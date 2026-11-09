<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKategoriRequest;
use App\Http\Requests\Admin\UpdateKategoriRequest;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(Request $request): View
    {
        $kategori = Kategori::withCount('jas')->orderBy('nama')->get();
        $edit = $request->filled('edit') ? Kategori::find($request->integer('edit')) : null;

        return view('admin.kategori', compact('kategori', 'edit'));
    }

    public function store(StoreKategoriRequest $request): RedirectResponse
    {
        Kategori::create($request->validated());

        return redirect()->route('admin.kategori.index')->with('sukses', 'Kategori berhasil ditambahkan.');
    }

    public function update(UpdateKategoriRequest $request, Kategori $kategori): RedirectResponse
    {
        $kategori->update($request->validated());

        return redirect()->route('admin.kategori.index')->with('sukses', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        if ($kategori->jas()->exists()) {
            return back()->with('error', 'Kategori masih digunakan oleh data jas.');
        }

        $kategori->delete();

        return back()->with('sukses', 'Kategori berhasil dihapus.');
    }
}
