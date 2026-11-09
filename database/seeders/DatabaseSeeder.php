<?php

namespace Database\Seeders;

use App\Enums\MetodePembayaran;
use App\Enums\Role;
use App\Enums\StatusPembayaran;
use App\Enums\StatusPesanan;
use App\Models\Jas;
use App\Models\Kategori;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Testimoni;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin PW Sewa Jas',
            'email' => 'admin@sewajas.test',
            'password' => 'admin12345',
            'role' => Role::Admin,
            'nomor_hp' => '081234567890',
        ]);

        $budi = User::create([
            'name' => 'Budi Santoso',
            'email' => 'user@sewajas.test',
            'password' => 'user12345',
            'role' => Role::Customer,
            'nomor_hp' => '081200000001',
        ]);

        $siti = User::create([
            'name' => 'Siti Rahma',
            'email' => 'siti@sewajas.test',
            'password' => 'user12345',
            'role' => Role::Customer,
            'nomor_hp' => '081200000002',
        ]);

        $pengantin = Kategori::create(['nama' => 'Jas Pengantin']);
        $formal = Kategori::create(['nama' => 'Jas Formal']);
        $wisuda = Kategori::create(['nama' => 'Jas Wisuda']);
        $almamater = Kategori::create(['nama' => 'Jas Almamater']);

        $jas = collect([
            [
                'kategori_id' => $pengantin->id,
                'nama' => 'Tuxedo Pengantin Classic',
                'deskripsi' => 'Tuxedo hitam premium dengan aksen satin, cocok untuk resepsi pernikahan. Termasuk kemeja putih dan dasi kupu-kupu.',
                'ukuran' => 'M,L,XL,XXL',
                'harga_sewa' => 150000,
                'stok' => 4,
            ],
            [
                'kategori_id' => $pengantin->id,
                'nama' => 'Jas Pengantin Ivory',
                'deskripsi' => 'Jas warna ivory dengan potongan slim fit untuk acara akad dan resepsi. Bahan adem dan tidak mudah kusut.',
                'ukuran' => 'S,M,L,XL',
                'harga_sewa' => 125000,
                'stok' => 3,
            ],
            [
                'kategori_id' => $formal->id,
                'nama' => 'Jas Navy Formal',
                'deskripsi' => 'Jas navy untuk seminar, wisuda, dan wawancara kerja. Potongan rapi dengan bahan wol campuran.',
                'ukuran' => 'S,M,L,XL',
                'harga_sewa' => 75000,
                'stok' => 8,
            ],
            [
                'kategori_id' => $formal->id,
                'nama' => 'Jas Hitam Slim Fit',
                'deskripsi' => 'Jas hitam serbaguna dengan potongan slim fit. Cocok untuk acara formal maupun semi-formal.',
                'ukuran' => 'S,M,L,XL,XXL',
                'harga_sewa' => 65000,
                'stok' => 10,
            ],
            [
                'kategori_id' => $wisuda->id,
                'nama' => 'Jas Wisuda Hitam',
                'deskripsi' => 'Jas hitam untuk wisuda, bersih dan wangi. Sudah termasuk toga bila diperlukan.',
                'ukuran' => 'S,M,L,XL,XXL',
                'harga_sewa' => 50000,
                'stok' => 12,
            ],
            [
                'kategori_id' => $wisuda->id,
                'nama' => 'Jas Wisuda Abu Muda',
                'deskripsi' => 'Jas abu muda modern untuk foto wisuda. Bahan ringan dan nyaman dipakai seharian.',
                'ukuran' => 'S,M,L',
                'harga_sewa' => 55000,
                'stok' => 6,
            ],
            [
                'kategori_id' => $almamater->id,
                'nama' => 'Jas Almamater UMRAH',
                'deskripsi' => 'Jas almamater resmi untuk kegiatan kampus. Tersedia ukuran lengkap dan bisa sewa harian.',
                'ukuran' => 'S,M,L,XL,XXL',
                'harga_sewa' => 40000,
                'stok' => 15,
            ],
            [
                'kategori_id' => $formal->id,
                'nama' => 'Jas Cokelat Kasual',
                'deskripsi' => 'Jas cokelat dengan gaya kasual untuk acara santai, photoshoot, dan promnight.',
                'ukuran' => 'M,L,XL',
                'harga_sewa' => 60000,
                'stok' => 5,
            ],
        ])->map(fn ($data) => Jas::create($data));

        // ===== Contoh pesanan untuk mengisi dashboard & laporan =====
        $buatPesanan = function (
            User $user,
            Jas $item,
            StatusPesanan $status,
            Carbon $mulai,
            int $jumlah,
            string $ukuran,
            ?StatusPembayaran $statusBayar = null,
            ?MetodePembayaran $metode = null,
        ) use ($admin) {
            $selesai = $mulai->copy()->addDays(2);

            $pesanan = Pesanan::create([
                'user_id' => $user->id,
                'jas_id' => $item->id,
                'admin_id' => in_array($status, [StatusPesanan::Disetujui, StatusPesanan::Dikirim, StatusPesanan::Selesai], true) ? $admin->id : null,
                'tanggal_pesan' => $mulai->copy()->subDays(2),
                'tanggal_mulai' => $mulai,
                'tanggal_selesai' => $selesai,
                'tanggal_dikembalikan' => $status === StatusPesanan::Selesai ? $selesai : null,
                'ukuran' => $ukuran,
                'jumlah' => $jumlah,
                'total_harga' => $item->harga_sewa * $jumlah * 2,
                'status' => $status,
                'catatan' => null,
            ]);

            if ($statusBayar) {
                Pembayaran::create([
                    'pesanan_id' => $pesanan->id,
                    'metode' => $metode ?? MetodePembayaran::Transfer,
                    'bukti_pembayaran' => null,
                    'status' => $statusBayar,
                    'tanggal_bayar' => $mulai->copy()->subDay(),
                    'tanggal_verifikasi' => $statusBayar === StatusPembayaran::Diverifikasi ? $mulai->copy()->subDay() : null,
                    'diverifikasi_oleh' => $statusBayar === StatusPembayaran::Diverifikasi ? $admin->id : null,
                    'catatan_admin' => $statusBayar === StatusPembayaran::Ditolak ? 'Bukti transfer tidak terbaca, silakan unggah ulang.' : null,
                ]);
            }

            return $pesanan;
        };

        $selesai1 = $buatPesanan($budi, $jas[2], StatusPesanan::Selesai, now()->subDays(20), 1, 'L', StatusPembayaran::Diverifikasi);
        $selesai2 = $buatPesanan($budi, $jas[4], StatusPesanan::Selesai, now()->subDays(12), 1, 'M', StatusPembayaran::Diverifikasi);

        $buatPesanan($budi, $jas[0], StatusPesanan::Dikirim, now()->subDay(), 1, 'L', StatusPembayaran::Diverifikasi);
        $buatPesanan($budi, $jas[6], StatusPesanan::Pending, now()->addDays(3), 1, 'M', StatusPembayaran::Pending);
        $buatPesanan($siti, $jas[1], StatusPesanan::Disetujui, now()->addDays(5), 1, 'S', StatusPembayaran::Diverifikasi);
        $buatPesanan($siti, $jas[7], StatusPesanan::Pending, now()->addDays(7), 1, 'L');
        $buatPesanan($siti, $jas[3], StatusPesanan::Dibatalkan, now()->subDays(5), 1, 'M', StatusPembayaran::Ditolak);

        Testimoni::create([
            'user_id' => $budi->id,
            'jas_id' => $selesai1->jas_id,
            'rating' => 5,
            'komentar' => 'Jasnya rapi, wangi, dan ukurannya pas. Adminnya juga ramah saat pengambilan.',
        ]);

        Testimoni::create([
            'user_id' => $budi->id,
            'jas_id' => $selesai2->jas_id,
            'rating' => 4,
            'komentar' => 'Bahan nyaman dipakai seharian. Proses sewa cepat dan tidak ribet.',
        ]);
    }
}
