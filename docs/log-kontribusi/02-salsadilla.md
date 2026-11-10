# Log Kontribusi Individu — Salsadilla

**Peran:** Activity diagram & Front end User
**Progres dilaporkan:** ±45% (front end User)
**Fokus saat ini:** revisi 5 activity diagram, front end Beranda/Katalog/Daftar-Masuk

---

## 1. Ringkasan progres yang dilaporkan

- Memperbaiki **lima activity diagram** sesuai masukan dosen:
  percabangan Ya/Tidak harus jelas, kolom maksimal dua.
- Ingin bertanya ke dosen: **bagaimana menangani diagram yang pelakunya ada tiga?**
- Mulai mengerjakan front end User: **Beranda, Katalog, form Daftar dan Masuk**.

## 2. Lanjutan yang sudah tersedia di aplikasi

| Artefak | Isi yang sudah ada |
|---|---|
| `resources/views/beranda.blade.php` | Hero, katalog unggulan, section "Lima momen spesial", section `#kontak` |
| `resources/views/katalog.blade.php` | Grid jas + filter/pencarian, pagination kustom |
| `resources/views/detail.blade.php` | Detail jas, pilihan ukuran, tanggal sewa, tombol tambah keranjang |
| `resources/views/partials/jas-card.blade.php` | Komponen kartu jas yang dipakai ulang |
| `resources/views/auth.blade.php` | Satu form gabungan **Masuk** dan **Daftar** (tab), field `name`, `email`, `nomor_hp`, `password` |
| `resources/views/keranjang.blade.php` | Tabel keranjang, field `nama_pemesan` + `nomor_hp`, baris deposit (informasi) |
| `resources/views/pembayaran.blade.php` | Pilihan QRIS (upload bukti + gambar QRIS) dan Cash |
| `resources/views/pesanan.blade.php`, `pesanan-detail.blade.php` | Daftar pesanan + timeline status + nota |
| `resources/views/layouts/app.blade.php`, `layouts/pesan.blade.php` | Layout publik, flash message |
| `public/css/style.css` | Style publik (hero, kartu, grid, form, timeline) |

**Jawaban untuk pertanyaan tiga pelaku:** aturan "maksimal dua kolom" dan
"tiga pelaku" memang bertabrakan, jadi jangan dipaksa dalam satu diagram.
Pilihan yang aman:

1. **Pecah per use case** menjadi diagram dengan 2 swimlane: `Aktor + Sistem`.
   - Login → `User` + `Sistem`
   - Katalog/Pesan → `Customer` + `Sistem`
   - Verifikasi Pembayaran → `Admin` + `Sistem`
   - Pengembalian → `Admin` + `Sistem`
   - Kelola Jas → `Admin` + `Sistem`
2. Jika satu use case benar-benar melibatkan Customer **dan** Admin (misal
   verifikasi pembayaran), buat **dua diagram berurutan**: sisi Customer
   (submit pembayaran) dan sisi Admin (verifikasi), masing-masing 2 kolom.
3. Sistem tidak dihitung sebagai "pelaku" pada swimlane; jadikan kolom
   `Sistem/Backend` atau cukup aksi di dalam kolom aktor.

Cara ini memenuhi aturan dosen tanpa mengorbankan kelengkapan alur.

## 3. Tugas berikutnya (sisa ±55%)

1. **Lima activity diagram final** dengan keputusan Ya/Tidak eksplisit di setiap
   percabangan (contoh: stok cukup? Ya → lanjut checkout, Tidak → tampilkan pesan).
2. **Front end User**: konsistenkan kartu katalog, tambahkan empty state
   ("jas tidak ditemukan"), rapikan validasi form Daftar/Masuk, dan uji tampilan mobile.
3. Samakan label status di halaman pesanan dengan `App\Enums\StatusPesanan`
   (`pending`, `menunggu_verifikasi`, `disewa`, `selesai`, `dibatalkan`).

## 4. Log kontribusi individu

| No | Tanggal | Aktivitas | Artefak / Bukti | Output | Status |
|---|---|---|---|---|---|
| 1 | 2026-11-03 | Kumpulkan masukan dosen untuk 5 activity diagram | Catatan revisi | Daftar perbaikan | Selesai |
| 2 | 2026-11-04 | Perbaiki percabangan Ya/Tidak pada AD Login & Katalog | Diagram AD-01, AD-02 | AD revisi v2 | Selesai |
| 3 | 2026-11-05 | Batasi diagram maksimal dua kolom (swimlane Aktor + Sistem) | Diagram AD-03 s.d. AD-05 | AD sesuai aturan | Selesai |
| 4 | 2026-11-06 | Bangun Beranda (hero, katalog unggulan, lima momen, kontak) | `resources/views/beranda.blade.php` | Halaman Beranda | Selesai |
| 5 | 2026-11-07 | Bangun Katalog + komponen kartu jas | `resources/views/katalog.blade.php`, `partials/jas-card.blade.php` | Halaman Katalog | Selesai |
| 6 | 2026-11-08 | Bangun form gabungan Daftar & Masuk | `resources/views/auth.blade.php` | Form auth | Selesai |
| 7 | 2026-11-09 | Rapikan detail jas + pemilihan ukuran/tanggal | `resources/views/detail.blade.php` | Halaman detail | Selesai |
| 8 | 2026-11-10 | Empty state & validasi form user | `resources/views/**` | UI user konsisten | Berjalan |
| 9 | — | Sisa: uji mobile, konsistensi label status | — | Front end User 100% | Belum |

**Pertanyaan untuk dosen (dibawa ke konsultasi):**
Bagaimana menangani activity diagram dengan tiga pelaku jika kolom maksimal dua?

**Rekomendasi jawaban:** pecah per use case menjadi 2 kolom (Aktor + Sistem);
untuk use case lintas aktor, buat dua diagram berurutan (sisi Customer dan sisi Admin).
