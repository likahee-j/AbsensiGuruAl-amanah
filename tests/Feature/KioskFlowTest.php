<?php

use App\Models\Attendance;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

function makeKioskSchool(): SchoolSetting
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

function makeKepsekUser(): User
{
    return User::create([
        'name' => 'Pak Kepsek',
        'email' => 'kepsek@example.com',
        'password' => Hash::make('password'),
        'role' => 'kepsek',
    ]);
}

function makeKioskGuru(string $username = 'guru.budi'): User
{
    return User::create([
        'name' => 'Budi Guru',
        'email' => $username.'@example.com',
        'username' => $username,
        'password' => Hash::make('password'),
        'role' => 'guru',
    ]);
}

test('kepsek can open kiosk page', function () {
    $kepsek = makeKepsekUser();
    makeKioskSchool();

    $this->actingAs($kepsek)->get('/admin/kiosk')->assertOk();
});

test('guru is forbidden from kiosk', function () {
    $guru = makeKioskGuru();
    makeKioskSchool();

    $this->actingAs($guru)->get('/admin/kiosk')->assertForbidden();
});

test('kiosk scan by username records check-in', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 07:00:00'));
    $kepsek = makeKepsekUser();
    $guru = makeKioskGuru('guru.budi');
    makeKioskSchool();

    $res = $this->actingAs($kepsek)->postJson('/admin/kiosk/scan', [
        'payload' => 'guru.budi',
    ]);

    $res->assertOk()->assertJson([
        'ok' => true,
        'mode' => 'checkin',
    ]);

    $att = Attendance::where('user_id', $guru->id)->first();
    expect($att)->not->toBeNull();
    expect($att->status)->toBe('hadir');
    expect($att->qr_token_used)->toBe('KIOSK');
    expect($att->recorded_by)->toBe($kepsek->id);
});

test('kiosk scan by USER-{id} fallback works', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 07:00:00'));
    $kepsek = makeKepsekUser();
    $guru = User::create([
        'name' => 'Guru Tanpa Username',
        'email' => 'nouser@example.com',
        'password' => Hash::make('password'),
        'role' => 'guru',
    ]);
    makeKioskSchool();

    $res = $this->actingAs($kepsek)->postJson('/admin/kiosk/scan', [
        'payload' => 'USER-'.$guru->id,
    ]);

    $res->assertOk()->assertJson(['ok' => true, 'mode' => 'checkin']);
});

test('kiosk second scan same day records check-out', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 07:00:00'));
    $kepsek = makeKepsekUser();
    $guru = makeKioskGuru('guru.ani');
    makeKioskSchool();

    $this->actingAs($kepsek)->postJson('/admin/kiosk/scan', ['payload' => 'guru.ani'])->assertOk();

    Carbon::setTestNow(Carbon::parse('2026-05-19 15:00:00'));
    $res = $this->actingAs($kepsek)->postJson('/admin/kiosk/scan', ['payload' => 'guru.ani']);

    $res->assertOk()->assertJson(['ok' => true, 'mode' => 'checkout']);

    $att = Attendance::where('user_id', $guru->id)->first();
    expect($att->check_in_time)->not->toBeNull();
    expect($att->check_out_time)->not->toBeNull();
});

test('kiosk rejects unknown qr payload', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 07:00:00'));
    $kepsek = makeKepsekUser();
    makeKioskSchool();

    $res = $this->actingAs($kepsek)->postJson('/admin/kiosk/scan', [
        'payload' => 'siapa-ini-entah',
    ]);

    $res->assertStatus(422)->assertJson(['ok' => false]);
});

test('kiosk rejects qr of admin role', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 07:00:00'));
    $kepsek = makeKepsekUser();
    $admin = User::create([
        'name' => 'Admin Test',
        'email' => 'admin.test@example.com',
        'username' => 'admin.test',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);
    makeKioskSchool();

    $res = $this->actingAs($kepsek)->postJson('/admin/kiosk/scan', [
        'payload' => 'admin.test',
    ]);

    $res->assertStatus(422)->assertJson(['ok' => false]);
});

test('kiosk records kepsek attendance too', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 07:00:00'));
    $kepsek = makeKepsekUser();
    $kepsek2 = User::create([
        'name' => 'Kepsek Lain',
        'email' => 'kepsek2@example.com',
        'username' => 'kepsek.lain',
        'password' => Hash::make('password'),
        'role' => 'kepsek',
    ]);
    makeKioskSchool();

    $res = $this->actingAs($kepsek)->postJson('/admin/kiosk/scan', [
        'payload' => 'kepsek.lain',
    ]);

    $res->assertOk()->assertJson(['ok' => true, 'mode' => 'checkin']);
    expect(Attendance::where('user_id', $kepsek2->id)->exists())->toBeTrue();
});

test('kiosk respects check-in time window', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 04:00:00'));
    $kepsek = makeKepsekUser();
    makeKioskGuru('guru.pagi');
    makeKioskSchool();

    $res = $this->actingAs($kepsek)->postJson('/admin/kiosk/scan', [
        'payload' => 'guru.pagi',
    ]);

    $res->assertStatus(422)->assertJson(['ok' => false]);
});

test('kiosk marks terlambat when past threshold', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 08:00:00'));
    $kepsek = makeKepsekUser();
    $guru = makeKioskGuru('guru.telat');
    makeKioskSchool();

    $this->actingAs($kepsek)->postJson('/admin/kiosk/scan', [
        'payload' => 'guru.telat',
    ])->assertOk();

    $att = Attendance::where('user_id', $guru->id)->first();
    expect($att->status)->toBe('terlambat');
});
