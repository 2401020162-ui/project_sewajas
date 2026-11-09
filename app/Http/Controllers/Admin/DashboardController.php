<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Enums\StatusPembayaran;
use App\Http\Controllers\Controller;
use App\Models\Jas;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalPesanan = Pesanan::count();
        $pesananBulanIni = Pesanan::whereBetween('tanggal_pesan', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $totalPengguna = User::where('role', Role::Customer)->count();
        $menungguVerifikasi = Pembayaran::where('status', StatusPembayaran::Pending)->count();

        $totalPendapatan = Pesanan::whereHas('pembayaran', fn ($q) => $q->where('status', StatusPembayaran::Diverifikasi))
            ->sum('total_harga');

        $pesananTerbaru = Pesanan::with(['user', 'jas', 'pembayaran'])
            ->latest()
            ->take(8)
            ->get();

        $stokRendah = Jas::with('kategori')
            ->aktif()
            ->where('stok', '<=', 2)
            ->orderBy('stok')
            ->take(5)
            ->get();

        $pendapatanBulanan = Pesanan::with('pembayaran')
            ->where('tanggal_pesan', '>=', now()->startOfMonth()->subMonths(5))
            ->get()
            ->filter(fn (Pesanan $p) => $p->pembayaran?->status === StatusPembayaran::Diverifikasi)
            ->groupBy(fn (Pesanan $p) => $p->tanggal_pesan->format('Y-m'))
            ->map(fn ($items) => $items->sum('total_harga'));

        $labelBulanan = collect(range(5, 0))
            ->map(fn ($i) => now()->startOfMonth()->subMonths($i))
            ->mapWithKeys(fn ($bulan) => [$bulan->format('Y-m') => $bulan->translatedFormat('M')]);

        return view('admin.dashboard', compact(
            'totalPesanan',
            'pesananBulanIni',
            'totalPengguna',
            'menungguVerifikasi',
            'totalPendapatan',
            'pesananTerbaru',
            'stokRendah',
            'pendapatanBulanan',
            'labelBulanan',
        ));
    }
}
