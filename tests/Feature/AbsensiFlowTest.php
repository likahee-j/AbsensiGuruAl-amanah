<?php

use App\Models\Attendance;
use App\Models\QrToken;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

function makeSchool(): SchoolSetting
{
    return SchoolSetting::updateOrCreate(['id' => 1], [
        'school_name' => 'Test School',
        'latitude' => -6.2000000,
        'longitude' => 106.8166660,
        'radius_meters' => 200,
        'check_in_start' => '06:00:00',
        'check_in_end' => '09:00:00',
        'late_threshold' => '07:15:00',
        'check_out_start' => '14:00:00',
        'check_out_end' => '18:00:00',
    ]);
}

function makeGuru(string $email = 'guru.coba@example.com', ?string $phone = '081234567890'): User
{
    return User::create([
        'name' => 'Guru Coba',
        'email' => $email,
        'password' => Hash::make('password'),
        'role' => 'guru',
        'phone' => $phone,
    ]);
}

function makeAdmin(): User
{
    return User::create([
        'name' => 'Admin',
        'email' => 'admin.coba@example.com',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);
}

function makeToken(): QrToken
{
    return QrToken::create([
        'token' => (string) Str::uuid(),
        'expires_at' => Carbon::now()->addSeconds(30),
        'is_used' => false,
    ]);
}

test('admin can generate qr token via api endpoint', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 07:00:00'));
    $admin = makeAdmin();
    makeSchool();

    $response = $this->actingAs($admin)->getJson('/admin/qr/generate');

    $response->assertOk()
        ->assertJsonStructure(['token', 'expires_at', 'ttl_seconds', 'qr_svg_base64']);

    expect(QrToken::count())->toBe(1);
});

test('guru gets 403 when accessing admin qr', function () {
    $guru = makeGuru();
    $this->actingAs($guru)->get('/admin/qr')->assertForbidden();
});

test('full check-in flow: hadir status when on time and within radius', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 07:00:00'));
    makeSchool();
    $guru = makeGuru();
    $token = makeToken();

    $scan = $this->actingAs($guru)->postJson('/absensi/scan', ['token' => $token->token, 'mode' => 'checkin']);
    $scan->assertOk()->assertJson(['ok' => true]);

    $submit = $this->actingAs($guru)->postJson('/absensi/checkin', [
        'token' => $token->token,
        'latitude' => -6.2000010,
        'longitude' => 106.8166670,
    ]);

    $submit->assertOk()->assertJson(['ok' => true]);
    expect($submit->json('data.status'))->toBe('hadir');
    expect(Attendance::where('user_id', $guru->id)->count())->toBe(1);
    expect(QrToken::where('token', $token->token)->first()->is_used)->toBeTrue();
});

test('late status when checking in after threshold', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 08:00:00'));
    makeSchool();
    $guru = makeGuru();
    $token = makeToken();

    $submit = $this->actingAs($guru)->postJson('/absensi/checkin', [
        'token' => $token->token,
        'latitude' => -6.2000010,
        'longitude' => 106.8166670,
    ]);

    $submit->assertOk();
    expect($submit->json('data.status'))->toBe('terlambat');
});

test('reject when outside radius', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 07:00:00'));
    makeSchool();
    $guru = makeGuru();
    $token = makeToken();

    $submit = $this->actingAs($guru)->postJson('/absensi/checkin', [
        'token' => $token->token,
        'latitude' => -6.3000000,
        'longitude' => 106.9000000,
    ]);

    $submit->assertStatus(422)->assertJson(['ok' => false]);
    expect(Attendance::count())->toBe(0);
});

test('reject expired token', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 07:00:00'));
    makeSchool();
    $guru = makeGuru();

    $token = QrToken::create([
        'token' => (string) Str::uuid(),
        'expires_at' => Carbon::now()->subSeconds(5),
        'is_used' => false,
    ]);

    $scan = $this->actingAs($guru)->postJson('/absensi/scan', ['token' => $token->token]);
    $scan->assertStatus(422);
});

test('check-out flow: success when checked in and within hours', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 07:00:00'));
    makeSchool();
    $guru = makeGuru();

    // check-in pagi
    $tokenIn = makeToken();
    $this->actingAs($guru)->postJson('/absensi/checkin', [
        'token' => $tokenIn->token,
        'latitude' => -6.2000010,
        'longitude' => 106.8166670,
    ])->assertOk();

    // jam pulang
    Carbon::setTestNow(Carbon::parse('2026-05-19 15:00:00'));
    $tokenOut = makeToken();

    $out = $this->actingAs($guru)->postJson('/absensi/checkout', [
        'token' => $tokenOut->token,
        'latitude' => -6.2000010,
        'longitude' => 106.8166670,
    ]);

    $out->assertOk()->assertJson(['ok' => true]);
    $att = Attendance::where('user_id', $guru->id)->first();
    expect($att->check_out_time)->not->toBeNull();
});

test('check-out rejected when not yet checked in', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 15:00:00'));
    makeSchool();
    $guru = makeGuru();
    $token = makeToken();

    $out = $this->actingAs($guru)->postJson('/absensi/checkout', [
        'token' => $token->token,
        'latitude' => -6.2000010,
        'longitude' => 106.8166670,
    ]);

    $out->assertStatus(422);
});

test('admin can record manual attendance with izin status', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 09:00:00'));
    $admin = makeAdmin();
    $guru = makeGuru();
    makeSchool();

    $resp = $this->actingAs($admin)->post('/admin/manual-attendance', [
        'user_id' => $guru->id,
        'date' => '2026-05-19',
        'status' => 'izin',
        'notes' => 'Acara keluarga',
    ]);

    $resp->assertRedirect();

    $att = Attendance::where('user_id', $guru->id)->first();
    expect($att->status)->toBe('izin');
    expect($att->notes)->toBe('Acara keluarga');
    expect($att->recorded_by)->toBe($admin->id);
});

test('admin bulk mark alpa for missing gurus', function () {
    $admin = makeAdmin();
    $g1 = makeGuru('a@example.com');
    $g2 = makeGuru('b@example.com');
    $g3 = makeGuru('c@example.com');

    // g1 sudah hadir
    Attendance::create([
        'user_id' => $g1->id,
        'date' => '2026-05-19',
        'status' => 'hadir',
        'check_in_time' => '07:00:00',
    ]);

    $resp = $this->actingAs($admin)->post('/admin/manual-attendance/bulk-alpa', ['date' => '2026-05-19']);
    $resp->assertRedirect();

    expect(Attendance::count())->toBe(3);
    expect(Attendance::where('user_id', $g2->id)->first()->status)->toBe('alpa');
    expect(Attendance::where('user_id', $g3->id)->first()->status)->toBe('alpa');
    expect(Attendance::where('user_id', $g1->id)->first()->status)->toBe('hadir');
});

test('whatsapp link normalizes phone and generates wa.me url', function () {
    $admin = makeAdmin();
    $guru = makeGuru('wa@example.com', '081299887766');

    $resp = $this->actingAs($admin)->postJson('/admin/wa/link', [
        'user_id' => $guru->id,
        'message' => 'Halo guru',
    ]);

    $resp->assertOk();
    expect($resp->json('phone'))->toBe('6281299887766');
    expect($resp->json('wa_url'))->toStartWith('https://wa.me/6281299887766?text=');
});

test('laporan PDF endpoint returns a pdf', function () {
    $admin = makeAdmin();
    $guru = makeGuru();
    Attendance::create(['user_id' => $guru->id, 'date' => '2026-05-19', 'status' => 'hadir', 'check_in_time' => '07:00:00']);

    $resp = $this->actingAs($admin)->get(route('admin.laporan.pdf', ['guru' => $guru, 'from' => '2026-05-01', 'to' => '2026-05-31']));
    $resp->assertOk();
    expect($resp->headers->get('content-type'))->toBe('application/pdf');
});

test('guru is forbidden from admin pages', function () {
    $guru = makeGuru();
    $this->actingAs($guru)->get('/admin')->assertForbidden();
    $this->actingAs($guru)->get('/admin/manual-attendance')->assertForbidden();
    $this->actingAs($guru)->get('/admin/laporan')->assertForbidden();
});
