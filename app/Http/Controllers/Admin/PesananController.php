<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPembayaran;
use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status');

        $pesanan = Pesanan::with(['user', 'jas', 'pembayaran'])
            ->when(in_array($status, StatusPesanan::values(), true), fn ($q) => $q->where('status', $status))
            ->when($request->filled('q'), function ($q) use ($request) {
                $cari = $request->string('q')->trim()->toString();
                $q->where(function ($w) use ($cari) {
                    $w->where('kode_pesanan', 'like', "%{$cari}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$cari}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pesanan.index', compact('pesanan'));
    }

    public function show(Pesanan $pesanan): View
    {
        $pesanan->load(['user', 'jas.kategori', 'pembayaran.verifikator', 'admin']);

        return view('admin.pesanan.show', compact('pesanan'));
    }

    public function kirim(Pesanan $pesanan): RedirectResponse
    {
        if ($pesanan->status !== StatusPesanan::Disetujui) {
            return back()->with('error', 'Hanya pesanan yang pembayarannya sudah disetujui yang dapat dikirim.');
        }

        $pesanan->update([
            'status' => StatusPesanan::Dikirim,
            'admin_id' => auth()->id(),
        ]);

        return back()->with('sukses', 'Pesanan ditandai sedang disewa/dikirim.');
    }

    public function selesai(Pesanan $pesanan): RedirectResponse
    {
        if ($pesanan->status !== StatusPesanan::Dikirim) {
            return back()->with('error', 'Hanya pesanan yang sedang disewa yang dapat diselesaikan.');
        }

        DB::transaction(function () use ($pesanan) {
            $pesanan->update([
                'status' => StatusPesanan::Selesai,
                'tanggal_dikembalikan' => today(),
                'admin_id' => auth()->id(),
            ]);

            $pesanan->jas()->increment('stok', $pesanan->jumlah);
        });

        return back()->with('sukses', 'Pesanan selesai dan stok jas sudah dikembalikan.');
    }

    public function batal(Pesanan $pesanan): RedirectResponse
    {
        if (! $pesanan->status->bisaDibatalkan()) {
            return back()->with('error', 'Pesanan ini tidak dapat dibatalkan.');
        }

        DB::transaction(function () use ($pesanan) {
            $pesanan->load('pembayaran');

            $pesanan->update([
                'status' => StatusPesanan::Dibatalkan,
                'admin_id' => auth()->id(),
            ]);

            if ($pesanan->pembayaran && $pesanan->pembayaran->status === StatusPembayaran::Pending) {
                $pesanan->pembayaran->update([
                    'status' => StatusPembayaran::Ditolak,
                    'catatan_admin' => 'Pesanan dibatalkan oleh admin.',
                    'diverifikasi_oleh' => auth()->id(),
                    'tanggal_verifikasi' => now(),
                ]);
            }

            $pesanan->jas()->increment('stok', $pesanan->jumlah);
        });

        return back()->with('sukses', 'Pesanan dibatalkan dan stok dikembalikan.');
    }
}
