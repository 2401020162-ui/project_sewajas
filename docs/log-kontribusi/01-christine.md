# Log Kontribusi Individu — Christine

**Peran:** Sequence diagram & Front end Admin
**Progres dilaporkan:** ±40% (front end Admin)
**Fokus saat ini:** revisi sequence diagram terdampak, front end dashboard & Kelola Jas

---

## 1. Ringkasan progres yang dilaporkan

- Merevisi sequence diagram yang terdampak perubahan alur, terutama **Login** dan
  **Kelola Jas (alur stok)**.
- Ingin mengonfirmasi ke dosen: **apakah sequence Login cukup satu pengecekan
  beserta perannya?**
- Mulai mengerjakan front end Admin: **layout dashboard** dan **Kelola Jas**.

## 2. Lanjutan yang sudah tersedia di aplikasi

Pekerjaan front end Admin sudah punya kerangka nyata yang bisa dilanjutkan
(tidak perlu mulai dari nol):

| Artefak | Isi yang sudah ada |
|---|---|
| `resources/views/layouts/admin.blade.php` | Sidebar menu berbasis array `$menu`, badge jumlah pembayaran menunggu verifikasi, tombol "Lihat Situs", form logout, toggle sidebar responsif (`sidebarToggle` / `sidebarClose`) |
| `resources/views/admin/dashboard.blade.php` | Kartu ringkasan + daftar stok rendah (`stok <= 2`) |
| `resources/views/admin/jas/index.blade.php` | Pencarian `q`, filter kategori, filter status aktif/nonaktif, tabel dengan badge stok (merah <1, kuning ≤2, hijau >2), pagination |
| `resources/views/admin/jas/form.blade.php` | Field nama, kategori, ukuran, harga sewa/hari, stok, deskripsi, `is_aktif`, upload gambar |
| `resources/views/admin/kategori.blade.php` | CRUD kategori dalam satu halaman |
| `resources/views/admin/verifikasi.blade.php` | Antrean verifikasi + aksi setujui/tolak |
| `resources/views/admin/pesanan/index.blade.php`, `show.blade.php` | Daftar & detail pesanan, form pengembalian |
| `resources/views/admin/pengguna/index.blade.php`, `show.blade.php` | Daftar pengguna (read-only) |
| `resources/views/admin/laporan.blade.php` | Filter tanggal + cetak (`window.print()`) |
| `app/Http/Controllers/Admin/JasController.php` | CRUD jas, normalisasi ukuran, upload/hapus gambar, blokir hapus jika sudah punya riwayat pesanan |
| `public/css/style.css` | Style admin (sidebar, tabel, badge, toolbar, print) |

**Jawaban untuk pertanyaan sequence Login:** cukup **satu pengecekan
kredensial**, lalu **satu percabangan peran**. Implementasi di
`app/Http/Controllers/AuthController.php` memang begitu:

1. `Auth::attempt()` dipanggil **sekali** untuk email + password.
2. Session diregenerasi.
3. `arahkanBerdasarkanRole()` mengarahkan ke `admin.dashboard` atau `beranda`.

Jadi diagramnya: `Aktor → Form Login → AuthController → users (DB) → Session`,
lalu satu blok `alt` untuk `role = admin` / `role = customer`. Tidak perlu dua
alur pengecekan terpisah.

## 3. Tugas berikutnya (sisa ±60%)

1. **Sequence diagram final** (3 diagram minimum):
   - Login (satu cek + percabangan role) — sertakan `RateLimiter` 5 percobaan/60 detik.
   - Kelola Jas (Admin → Form → `StoreJasRequest` → `Jas` → DB, termasuk upload gambar).
   - Alur stok: checkout mengurangi stok (`KeranjangController`), batal/selesai mengembalikan stok (`PesananController`, `Admin\PesananController`).
2. **Front end Admin**: samakan pola komponen (toolbar, tabel, modal konfirmasi hapus),
   lengkapi empty state & pesan error, rapikan tampilan mobile, dan konsistenkan
   badge status dengan `App\Enums\StatusPesanan`.
3. **Halaman Kelola Jas**: tambahkan aksi cepat "tambah stok" dan indikator stok
   rendah langsung di tabel (data `stok <= 2` sudah tersedia di dashboard).

## 4. Log kontribusi individu

| No | Tanggal | Aktivitas | Artefak / Bukti | Output | Status |
|---|---|---|---|---|---|
| 1 | 2026-11-03 | Identifikasi sequence diagram terdampak perubahan alur | Daftar diagram (Login, Kelola Jas, Pembayaran, Pengembalian) | Ruang lingkup revisi jelas | Selesai |
| 2 | 2026-11-04 | Revisi sequence Login jadi satu pengecekan + percabangan role | `app/Http/Controllers/AuthController.php`, `resources/views/auth.blade.php` | Diagram Login versi 2 | Selesai |
| 3 | 2026-11-05 | Susun alur stok pada sequence Kelola Jas | `KeranjangController.php`, `Admin/JasController.php` | Alur stok terverifikasi | Selesai |
| 4 | 2026-11-06 | Bangun layout dashboard Admin (sidebar, badge, responsif) | `resources/views/layouts/admin.blade.php`, `public/css/style.css` | Layout dashboard | Selesai |
| 5 | 2026-11-07 | Bangun halaman daftar Kelola Jas (pencarian, filter, badge stok) | `resources/views/admin/jas/index.blade.php` | Halaman daftar jas | Selesai |
| 6 | 2026-11-08 | Bangun form tambah/edit jas + upload gambar | `resources/views/admin/jas/form.blade.php` | Form jas | Selesai |
| 7 | 2026-11-09 | Rapikan dashboard & kartu stok rendah | `resources/views/admin/dashboard.blade.php` | Dashboard v2 | Selesai |
| 8 | 2026-11-10 | Konsistensi badge status & komponen tabel | `resources/views/admin/**` | UI admin konsisten | Berjalan |
| 9 | — | Sisa: modal konfirmasi, empty state, uji mobile | — | Front end Admin 100% | Belum |

**Pertanyaan untuk dosen (dibawa ke konsultasi):**
Apakah sequence Login cukup satu pengecekan kredensial dengan satu percabangan
peran, atau perlu dipisah menjadi dua sequence (User dan Admin)?

**Rekomendasi jawaban:** satu sequence dengan satu `alt` peran sudah benar dan
sesuai implementasi, karena form login memang gabungan.
