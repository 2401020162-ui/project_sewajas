<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPembayaran;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LaporanRequest;
use App\Models\Pesanan;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(LaporanRequest $request): View
    {
        $awal = $request->filled('tanggal_awal')
            ? Carbon::parse($request->input('tanggal_awal'))->startOfDay()
            : now()->startOfMonth();

        $akhir = $request->filled('tanggal_akhir')
            ? Carbon::parse($request->input('tanggal_akhir'))->endOfDay()
            : now()->endOfMonth();

        $data = Pesanan::with(['user', 'jas.kategori', 'pembayaran'])
            ->whereBetween('tanggal_pesan', [$awal->toDateString(), $akhir->toDateString()])
            ->orderBy('tanggal_pesan')
            ->get();

        $totalPendapatan = $data
            ->filter(fn (Pesanan $p) => $p->pembayaran?->status === StatusPembayaran::Diverifikasi)
            ->sum('total_harga');

        $totalMenunggu = $data
            ->filter(fn (Pesanan $p) => $p->pembayaran?->status === StatusPembayaran::Pending)
            ->sum('total_harga');

        $rekapStatus = collect(\App\Enums\StatusPesanan::cases())
            ->mapWithKeys(fn ($status) => [$status->label() => $data->where('status', $status)->count()]);

        return view('admin.laporan', compact(
            'data',
            'awal',
            'akhir',
            'totalPendapatan',
            'totalMenunggu',
            'rekapStatus',
        ));
    }
}
