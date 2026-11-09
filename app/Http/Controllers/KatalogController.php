<?php

namespace App\Http\Controllers;

use App\Models\Jas;
use App\Models\Kategori;
use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KatalogController extends Controller
{
    public function beranda(): View
    {
        $kategori = Kategori::withCount(['jas' => fn ($q) => $q->aktif()])
            ->whereHas('jas', fn ($q) => $q->aktif())
            ->orderBy('nama')
            ->get();

        $populer = Jas::with('kategori')
            ->aktif()
            ->withCount('pesanan')
            ->withAvg('testimoni', 'rating')
            ->orderByDesc('pesanan_count')
            ->orderByDesc('testimoni_avg_rating')
            ->take(6)
            ->get();

        $testimoni = Testimoni::with(['user', 'jas'])
            ->latest()
            ->take(6)
            ->get();

        return view('beranda', compact('kategori', 'populer', 'testimoni'));
    }

    public function katalog(Request $request): View
    {
        $kategori = Kategori::withCount(['jas' => fn ($q) => $q->aktif()])->orderBy('nama')->get();

        $jas = Jas::with('kategori')
            ->aktif()
            ->cari($request->string('q')->trim()->toString())
            ->when($request->filled('kategori'), function ($query) use ($request) {
                $query->whereHas('kategori', fn ($q) => $q->where('slug', $request->input('kategori')));
            })
            ->when($request->input('urut') === 'termurah', fn ($q) => $q->orderBy('harga_sewa'))
            ->when($request->input('urut') === 'termahal', fn ($q) => $q->orderByDesc('harga_sewa'))
            ->when($request->input('urut') === 'populer', fn ($q) => $q->withCount('pesanan')->orderByDesc('pesanan_count'))
            ->when(! in_array($request->input('urut'), ['termurah', 'termahal', 'populer'], true), fn ($q) => $q->latest())
            ->withAvg('testimoni', 'rating')
            ->paginate(9)
            ->withQueryString();

        return view('katalog', compact('jas', 'kategori'));
    }

    public function detail(Jas $jas): View
    {
        abort_unless($jas->is_aktif, 404);

        $jas->load(['kategori', 'testimoni.user'])->loadCount('pesanan');

        $terkait = Jas::with('kategori')
            ->aktif()
            ->where('kategori_id', $jas->kategori_id)
            ->whereKeyNot($jas->id)
            ->take(4)
            ->get();

        $sudahSewa = false;
        $sudahUlas = false;

        if (auth()->check()) {
            $sudahSewa = $jas->pesanan()
                ->where('user_id', auth()->id())
                ->where('status', \App\Enums\StatusPesanan::Selesai)
                ->exists();
            $sudahUlas = $jas->testimoni()->where('user_id', auth()->id())->exists();
        }

        return view('detail', compact('jas', 'terkait', 'sudahSewa', 'sudahUlas'));
    }
}
