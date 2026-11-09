<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJasRequest;
use App\Http\Requests\Admin\UpdateJasRequest;
use App\Models\Jas;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class JasController extends Controller
{
    public function index(Request $request): View
    {
        $jas = Jas::with('kategori')
            ->cari($request->string('q')->trim()->toString())
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori_id', $request->integer('kategori')))
            ->when($request->filled('status'), fn ($q) => $q->where('is_aktif', $request->input('status') === 'aktif'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $kategori = Kategori::orderBy('nama')->get();

        return view('admin.jas.index', compact('jas', 'kategori'));
    }

    public function create(): View
    {
        return view('admin.jas.form', [
            'jas' => new Jas,
            'kategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function store(StoreJasRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_aktif'] = $request->boolean('is_aktif');
        $data['ukuran'] = $this->normalisasiUkuran($data['ukuran']);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('jas', 'public');
        }

        Jas::create($data);

        return redirect()->route('admin.jas.index')->with('sukses', 'Data jas berhasil ditambahkan.');
    }

    public function edit(Jas $jas): View
    {
        return view('admin.jas.form', [
            'jas' => $jas,
            'kategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function update(UpdateJasRequest $request, Jas $jas): RedirectResponse
    {
        $data = $request->validated();
        $data['is_aktif'] = $request->boolean('is_aktif');
        $data['ukuran'] = $this->normalisasiUkuran($data['ukuran']);

        if ($request->hasFile('gambar')) {
            if ($jas->gambar) {
                Storage::disk('public')->delete($jas->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('jas', 'public');
        }

        $jas->update($data);

        return redirect()->route('admin.jas.index')->with('sukses', 'Data jas berhasil diperbarui.');
    }

    public function destroy(Jas $jas): RedirectResponse
    {
        if ($jas->pesanan()->exists()) {
            return back()->with('error', 'Jas ini sudah memiliki riwayat pesanan sehingga tidak boleh dihapus. Nonaktifkan saja.');
        }

        if ($jas->gambar) {
            Storage::disk('public')->delete($jas->gambar);
        }

        $jas->delete();

        return back()->with('sukses', 'Data jas berhasil dihapus.');
    }

    private function normalisasiUkuran(string $ukuran): string
    {
        return collect(explode(',', $ukuran))
            ->map(fn ($u) => strtoupper(trim($u)))
            ->filter()
            ->unique()
            ->implode(',');
    }
}
