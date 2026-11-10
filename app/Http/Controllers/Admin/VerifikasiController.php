<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPembayaran;
use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TolakPembayaranRequest;
use App\Models\Pembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VerifikasiController extends Controller
{
    public function index(Request $request): View
    {
        $pembayaran = Pembayaran::with(['pesanan.user', 'pesanan.jas'])
            ->where('status', StatusPembayaran::Pending)
            ->when($request->filled('metode'), fn ($q) => $q->where('metode', $request->input('metode')))
            ->oldest('tanggal_bayar')
            ->paginate(10)
            ->withQueryString();

        return view('admin.verifikasi', compact('pembayaran'));
    }

    public function setujui(Pembayaran $pembayaran): RedirectResponse
    {
        if ($pembayaran->status !== StatusPembayaran::Pending) {
            return back()->with('error', 'Pembayaran ini sudah diproses.');
        }

        DB::transaction(function () use ($pembayaran) {
            $pembayaran->update([
                'status' => StatusPembayaran::Diverifikasi,
                'tanggal_verifikasi' => now(),
                'diverifikasi_oleh' => auth()->id(),
            ]);

            $pembayaran->pesanan->update([
                'status' => StatusPesanan::Disewa,
                'admin_id' => auth()->id(),
            ]);
        });

        return back()->with('sukses', 'Pembayaran berhasil diverifikasi. Pesanan sekarang berstatus Disewa.');
    }

    public function tolak(TolakPembayaranRequest $request, Pembayaran $pembayaran): RedirectResponse
    {
        if ($pembayaran->status !== StatusPembayaran::Pending) {
            return back()->with('error', 'Pembayaran ini sudah diproses.');
        }

        DB::transaction(function () use ($request, $pembayaran) {
            $pembayaran->update([
                'status' => StatusPembayaran::Ditolak,
                'catatan_admin' => $request->string('catatan_admin')->trim()->toString(),
                'tanggal_verifikasi' => now(),
                'diverifikasi_oleh' => auth()->id(),
            ]);

            $pembayaran->pesanan->update(['status' => StatusPesanan::Pending]);
        });

        return back()->with('sukses', 'Pembayaran ditolak. Customer dapat mengunggah bukti baru.');
    }
}
