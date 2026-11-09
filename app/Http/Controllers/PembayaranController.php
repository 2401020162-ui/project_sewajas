<?php

namespace App\Http\Controllers;

use App\Enums\MetodePembayaran;
use App\Enums\StatusPembayaran;
use App\Enums\StatusPesanan;
use App\Http\Requests\StorePembayaranRequest;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function show(Pesanan $pesanan): View
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);

        $pesanan->load(['jas.kategori', 'pembayaran']);

        return view('pembayaran', compact('pesanan'));
    }

    public function store(StorePembayaranRequest $request, Pesanan $pesanan): RedirectResponse
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);

        if ($pesanan->status !== StatusPesanan::Pending) {
            return redirect()->route('pesanan.show', $pesanan)
                ->with('error', 'Pesanan ini tidak sedang menunggu pembayaran.');
        }

        $pembayaran = $pesanan->pembayaran;

        if ($pembayaran && $pembayaran->status === StatusPembayaran::Diverifikasi) {
            return redirect()->route('pesanan.show', $pesanan)
                ->with('error', 'Pembayaran pesanan ini sudah lunas.');
        }

        if ($pembayaran && $pembayaran->status === StatusPembayaran::Pending) {
            return redirect()->route('pesanan.show', $pesanan)
                ->with('error', 'Bukti pembayaran sudah dikirim dan sedang menunggu verifikasi admin.');
        }

        $data = $request->validated();
        $metode = MetodePembayaran::from($data['metode']);

        $bukti = $pembayaran?->bukti_pembayaran;

        if ($request->hasFile('bukti_pembayaran')) {
            if ($bukti) {
                Storage::disk('public')->delete($bukti);
            }

            $bukti = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');
        }

        $pesanan->pembayaran()->updateOrCreate(
            ['pesanan_id' => $pesanan->id],
            [
                'metode' => $metode,
                'bukti_pembayaran' => $metode->butuhBukti() ? $bukti : null,
                'status' => StatusPembayaran::Pending,
                'tanggal_bayar' => $request->input('tanggal_bayar') ?? now(),
                'tanggal_verifikasi' => null,
                'diverifikasi_oleh' => null,
                'catatan_admin' => null,
            ]
        );

        return redirect()->route('pesanan.show', $pesanan)
            ->with('sukses', 'Pembayaran berhasil dikirim. Mohon tunggu verifikasi admin.');
    }
}
