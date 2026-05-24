<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TahunPelajaranController extends Controller
{
    public function index()
    {
        $items = TahunPelajaran::orderByDesc('mulai')->get();

        return view('admin.tapel.index', ['items' => $items]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $this->save(new TahunPelajaran(), $data);

        return redirect()->route('admin.tapel.index')
            ->with('status', 'Tahun pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, TahunPelajaran $tapel)
    {
        $data = $this->validateData($request);
        $this->save($tapel, $data);

        return redirect()->route('admin.tapel.index')
            ->with('status', 'Tahun pelajaran berhasil diperbarui.');
    }

    public function destroy(TahunPelajaran $tapel)
    {
        $tapel->delete();

        return redirect()->route('admin.tapel.index')
            ->with('status', 'Tahun pelajaran berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request): array
    {
        return $request->validate([
            'tahun' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['required', Rule::in(['Ganjil', 'Genap'])],
            'mulai' => ['required', 'date'],
            'selesai' => ['required', 'date', 'after_or_equal:mulai'],
            'is_aktif' => ['nullable', 'boolean'],
        ], [
            'tahun.regex' => 'Format tahun harus seperti 2024/2025.',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function save(TahunPelajaran $tapel, array $data): void
    {
        $aktif = (bool) ($data['is_aktif'] ?? false);

        $tapel->fill([
            'tahun' => $data['tahun'],
            'semester' => $data['semester'],
            'mulai' => $data['mulai'],
            'selesai' => $data['selesai'],
            'is_aktif' => $aktif,
        ])->save();

        if ($aktif) {
            TahunPelajaran::where('id', '!=', $tapel->id)->update(['is_aktif' => false]);
        }
    }
}
