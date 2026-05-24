<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = User::where('role', 'guru')->orderBy('name')->get();

        return view('admin.guru.index', compact('gurus'));
    }

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => 'guru',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.guru.index')->with('status', 'Guru berhasil ditambahkan.');
    }

    public function edit(User $guru)
    {
        abort_if($guru->role !== 'guru', 404);

        return view('admin.guru.edit', ['guru' => $guru]);
    }

    public function update(Request $request, User $guru)
    {
        abort_if($guru->role !== 'guru', 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($guru->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ]);

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $guru->update($payload);

        return redirect()->route('admin.guru.index')->with('status', 'Data guru berhasil diperbarui.');
    }

    public function destroy(User $guru)
    {
        abort_if($guru->role !== 'guru', 404);

        $guru->delete();

        return redirect()->route('admin.guru.index')->with('status', 'Guru berhasil dihapus.');
    }
}
