<?php

namespace App\Http\Controllers;

use App\Enums\StatusPembayaran;
use App\Enums\StatusPesanan;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function index(): View
    {
        $pesanan = Pesanan::with(['jas.kategori', 'pembayaran'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(8);

        return view('pesanan', compact('pesanan'));
    }

    public function show(Pesanan $pesanan): View
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);

        $pesanan->load(['jas.kategori', 'pembayaran', 'admin']);

        return view('pesanan-detail', compact('pesanan'));
    }

    public function batal(Pesanan $pesanan): RedirectResponse
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);

        if (! $pesanan->status->bisaDibatalkan()) {
            return back()->with('error', 'Pesanan ini sudah diproses dan tidak dapat dibatalkan.');
        }

        DB::transaction(function () use ($pesanan) {
            $pesanan->load('pembayaran');

            $pesanan->update(['status' => StatusPesanan::Dibatalkan]);

            if ($pesanan->pembayaran && $pesanan->pembayaran->status === StatusPembayaran::Pending) {
                $pesanan->pembayaran->update([
                    'status' => StatusPembayaran::Ditolak,
                    'catatan_admin' => 'Pesanan dibatalkan oleh customer.',
                ]);
            }

            $pesanan->jas()->increment('stok', $pesanan->jumlah);
        });

        return back()->with('sukses', 'Pesanan berhasil dibatalkan dan stok dikembalikan.');
    }
}
