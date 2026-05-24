<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use Illuminate\Http\Request;

class HariLiburController extends Controller
{
    public function index()
    {
        $items = HariLibur::orderByDesc('tanggal')->get();

        return view('admin.libur.index', ['items' => $items]);
    }

    public function store(Request $request)
    {
        HariLibur::create($this->validateData($request));

        return redirect()->route('admin.libur.index')
            ->with('status', 'Hari libur berhasil ditambahkan.');
    }

    public function update(Request $request, HariLibur $libur)
    {
        $libur->update($this->validateData($request));

        return redirect()->route('admin.libur.index')
            ->with('status', 'Hari libur berhasil diperbarui.');
    }

    public function destroy(HariLibur $libur)
    {
        $libur->delete();

        return redirect()->route('admin.libur.index')
            ->with('status', 'Hari libur berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request): array
    {
        return $request->validate([
            'tanggal' => ['required', 'date'],
            'keterangan' => ['required', 'string', 'max:255'],
        ]);
    }
}
