<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PenggunaController extends Controller
{
    /** @var array<string, string> */
    private const LABELS = [
        'admin' => 'Admin',
        'kepsek' => 'Kepsek',
        'guru' => 'Guru',
    ];

    public function index(string $role)
    {
        $users = User::where('role', $role)->orderBy('name')->get();

        return view('admin.pengguna.index', [
            'role' => $role,
            'roleLabel' => self::LABELS[$role],
            'users' => $users,
        ]);
    }

    public function create(string $role)
    {
        return view('admin.pengguna.create', [
            'role' => $role,
            'roleLabel' => self::LABELS[$role],
        ]);
    }

    public function store(Request $request, string $role)
    {
        $data = $this->validateData($request, $role);

        $payload = $this->payload($data, $role);
        $payload['password'] = Hash::make($data['password']);
        $payload['role'] = $role;
        $payload['email_verified_at'] = now();

        if ($request->hasFile('photo')) {
            $payload['photo'] = $request->file('photo')->store('photos', 'public');
        }

        User::create($payload);

        return redirect()
            ->route('admin.pengguna.index', $role)
            ->with('status', self::LABELS[$role].' berhasil ditambahkan.');
    }

    public function show(string $role, User $user)
    {
        $this->ensureRole($user, $role);

        return view('admin.pengguna.show', [
            'role' => $role,
            'roleLabel' => self::LABELS[$role],
            'user' => $user,
        ]);
    }

    public function edit(string $role, User $user)
    {
        $this->ensureRole($user, $role);

        return view('admin.pengguna.edit', [
            'role' => $role,
            'roleLabel' => self::LABELS[$role],
            'user' => $user,
        ]);
    }

    public function update(Request $request, string $role, User $user)
    {
        $this->ensureRole($user, $role);

        $data = $this->validateData($request, $role, $user);

        $payload = $this->payload($data, $role);

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $payload['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $user->update($payload);

        return redirect()
            ->route('admin.pengguna.show', [$role, $user])
            ->with('status', 'Data '.self::LABELS[$role].' berhasil diperbarui.');
    }

    public function destroy(Request $request, string $role, User $user)
    {
        $this->ensureRole($user, $role);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        return redirect()
            ->route('admin.pengguna.index', $role)
            ->with('status', self::LABELS[$role].' berhasil dihapus.');
    }

    public function qr(string $role, User $user)
    {
        $this->ensureRole($user, $role);

        $payload = $user->username ?: 'USER-'.$user->id;
        $svg = QrCode::format('svg')->size(320)->margin(1)->errorCorrection('H')->generate($payload);

        return response($svg)->header('Content-Type', 'image/svg+xml');
    }

    private function ensureRole(User $user, string $role): void
    {
        abort_if($user->role !== $role, 404);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request, string $role, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user?->id)],
            'gender' => ['nullable', Rule::in(['L', 'P'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'nip' => ['nullable', 'string', 'max:50'],
            'nuptk' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(6)],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data, string $role): array
    {
        return [
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'] ?? null,
            'gender' => $data['gender'] ?? null,
            'phone' => $data['phone'] ?? null,
            'nip' => $data['nip'] ?? null,
            'nuptk' => $data['nuptk'] ?? null,
            'address' => $data['address'] ?? null,
        ];
    }
}
