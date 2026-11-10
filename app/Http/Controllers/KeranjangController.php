<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\StoreKeranjangRequest;
use App\Http\Requests\UpdateKeranjangRequest;
use App\Models\Jas;
use App\Models\Keranjang;
use App\Models\Pesanan;
use App\Enums\StatusPesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KeranjangController extends Controller
{
    public function index(): View
    {
        $items = Keranjang::with(['jas.kategori'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $total = $items->sum(fn (Keranjang $item) => $item->subtotal);

        return view('keranjang', compact('items', 'total'));
    }

    public function store(StoreKeranjangRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $item = Keranjang::firstOrNew([
            'user_id' => $request->user()->id,
            'jas_id' => $data['jas_id'],
            'ukuran' => strtoupper(trim($data['ukuran'])),
        ]);

        $item->jumlah = ($item->exists ? $item->jumlah : 0) + (int) $data['jumlah'];
        $item->tanggal_mulai = $data['tanggal_mulai'];
        $item->tanggal_selesai = $data['tanggal_selesai'];
        $item->save();

        return redirect()->route('keranjang.index')
            ->with('sukses', 'Jas berhasil ditambahkan ke keranjang.');
    }

    public function update(UpdateKeranjangRequest $request, Keranjang $keranjang): RedirectResponse
    {
        $keranjang->update(['jumlah' => $request->integer('jumlah')]);

        return back()->with('sukses', 'Jumlah item diperbarui.');
    }

    public function destroy(Keranjang $keranjang): RedirectResponse
    {
        abort_if($keranjang->user_id !== auth()->id(), 403);

        $keranjang->delete();

        return back()->with('sukses', 'Item dihapus dari keranjang.');
    }

    public function kosongkan(): RedirectResponse
    {
        Keranjang::where('user_id', auth()->id())->delete();

        return back()->with('sukses', 'Keranjang dikosongkan.');
    }

    public function checkout(CheckoutRequest $request): RedirectResponse
    {
        $items = Keranjang::with('jas')
            ->where('user_id', $request->user()->id)
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('keranjang.index')->with('error', 'Keranjang masih kosong.');
        }

        try {
            $pesananBaru = DB::transaction(function () use ($items, $request) {
                $dibuat = collect();

                foreach ($items as $item) {
                    /** @var Jas $jas */
                    $jas = Jas::lockForUpdate()->find($item->jas_id);

                    if (! $jas || ! $jas->is_aktif) {
                        throw new \RuntimeException('Jas "'.($jas->nama ?? 'tidak diketahui').'" sudah tidak tersedia.');
                    }

                    if ($jas->stok < $item->jumlah) {
                        throw new \RuntimeException("Stok {$jas->nama} tidak mencukupi. Sisa {$jas->stok}.");
                    }

                    $pesanan = Pesanan::create([
                        'user_id' => $request->user()->id,
                        'jas_id' => $jas->id,
                        'nama_pemesan' => $request->string('nama_pemesan')->trim()->toString(),
                        'nomor_hp' => $request->string('nomor_hp')->trim()->toString(),
                        'tanggal_pesan' => today(),
                        'tanggal_mulai' => $item->tanggal_mulai,
                        'tanggal_selesai' => $item->tanggal_selesai,
                        'ukuran' => $item->ukuran,
                        'jumlah' => $item->jumlah,
                        'total_harga' => $jas->harga_sewa * $item->jumlah * $item->lama_sewa,
                        'status' => StatusPesanan::Pending,
                        'catatan' => $request->input('catatan'),
                    ]);

                    $jas->decrement('stok', $item->jumlah);
                    $dibuat->push($pesanan);
                }

                Keranjang::where('user_id', $request->user()->id)->delete();

                return $dibuat;
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        // Satu pesanan: langsung tampilkan nota/detail dengan tombol "Bayar Sekarang".
        if ($pesananBaru->count() === 1) {
            return redirect()->route('pesanan.show', $pesananBaru->first())
                ->with('sukses', 'Pesanan berhasil dibuat. Silakan periksa nota lalu lanjut ke pembayaran.');
        }

        return redirect()->route('pesanan.index')
            ->with('sukses', $pesananBaru->count().' pesanan berhasil dibuat. Silakan pilih pesanan dan lakukan pembayaran.');
    }
}
