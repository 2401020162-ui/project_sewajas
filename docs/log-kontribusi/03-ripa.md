# Log Kontribusi Individu — Ripa

**Peran:** Front end (UI login gabungan & menu stok) + Back end
**Progres dilaporkan:** ±35% (back end)
**Fokus saat ini:** struktur folder, koneksi database, alur login sesi, sinkron nama field UI ↔ RAT

---

## 1. Ringkasan progres yang dilaporkan

- Menyesuaikan desain UI dengan **login gabungan** User dan Admin.
- Menambahkan **menu untuk mengatur stok jas**.
- Ingin bertanya ke dosen: **kode unik diisi saat login atau saat registrasi?**
- Back end: menyiapkan struktur folder, koneksi database, alur login pakai sesi,
  dan menyamakan nama field UI dengan RAT.

## 2. Lanjutan yang sudah tersedia di aplikasi

| Artefak | Isi yang sudah ada |
|---|---|
| `app/Http/Controllers/AuthController.php` | Register (role otomatis `customer`), login dengan `Auth::attempt`, `RateLimiter` 5 percobaan/60 detik, `session()->regenerate()`, redirect berdasarkan role |
| `app/Http/Requests/Auth/LoginRequest.php`, `RegisterRequest.php` | Validasi field login & registrasi |
| `app/Http/Middleware/EnsureAdmin.php`, `EnsureCustomer.php` | Penjaga area admin/customer |
| `app/Enums/Role.php` | Enum `admin` / `customer` |
| `routes/web.php` | Grup `guest`, `auth`, `auth+customer`, prefix `admin` + `auth+admin` |
| `config/session.php` + tabel `sessions` | Sesi berbasis database (siap untuk login gabungan) |
| `.env.example` | Template koneksi DB & `SESSION_DRIVER` |
| `resources/views/layouts/admin.blade.php` | Menu sidebar, termasuk **Kelola Jas** dan **Verifikasi Pembayaran** (badge) |
| `resources/views/admin/jas/index.blade.php`, `form.blade.php` | UI pengaturan stok (field `stok`, badge stok rendah) |
| `app/Http/Controllers/Admin/JasController.php` | CRUD jas termasuk perubahan `stok` |

**Pemetaan nama field UI ↔ RAT (yang sudah dipakai konsisten):**

| UI (form) | RAT / kolom DB | Tipe |
|---|---|---|
| Nama | `users.name` | VARCHAR(255) |
| Email | `users.email` | VARCHAR(255) UNIQUE |
| Nomor HP | `users.nomor_hp` | VARCHAR(20) NULL |
| Kata sandi | `users.password` | VARCHAR(255) (hash) |
| Peran | `users.role` | ENUM('admin','customer') |
| Nama jas | `jas.nama` | VARCHAR(120) |
| Harga sewa/hari | `jas.harga_sewa` | INT UNSIGNED |
| Stok | `jas.stok` | INT UNSIGNED DEFAULT 0 |
| Ukuran | `jas.ukuran` | VARCHAR(60) |
| Status pesanan | `pesanan.status` | ENUM 5 nilai |
| Metode bayar | `pembayaran.metode` | ENUM('qris','cash') |

**Jawaban untuk pertanyaan kode unik:** isi **saat registrasi**, bukan saat login.

- Kode unik adalah atribut pembuatan akun (gerbang pendaftaran admin), jadi
  disimpan sekali di tabel `users` saat registrasi.
- Login tetap sederhana: **email + password saja**, sesuai keputusan Christine
  bahwa sequence Login cukup satu pengecekan.
- Jika kode unik dicek saat login, ia berubah menjadi faktor kedua; form login
  gabungan jadi tidak konsisten (customer tidak punya kode) dan sequence diagram
  harus menambah satu pengecekan lagi.

**Catatan penting:** kolom `kode_unik` **belum ada** di skema saat ini
(`database/migrations/0001_01_01_000000_create_users_table.php` hanya punya
`role` dan `nomor_hp`). Jadi ada dua opsi yang harus diputuskan bersama Aisyah:

1. Tambah kolom `kode_unik VARCHAR(50) NULL` di `users`, wajib hanya saat
   `role = admin`, divalidasi di `RegisterRequest` (nilai dari config/env).
2. Hapus `kode_unik` dari RAT jika dosen menilai tidak perlu.

## 3. Tugas berikutnya

1. Putuskan `kode_unik` (tambah migrasi atau hapus dari RAT) bersama Aisyah.
2. Lengkapi alur login sesi: pastikan `SESSION_DRIVER=database`, uji
   "remember me", dan uji logout menginvalidasi sesi.
3. Tambahkan dokumentasi setup di `README.md` (masih bawaan Laravel):
   clone → `composer install` → salin `.env.example` → `php artisan key:generate`
   → `migrate --seed` → `serve`.
4. Rapikan menu stok: aksi cepat tambah/kurangi stok dari tabel Kelola Jas.

## 4. Log kontribusi individu

| No | Tanggal | Aktivitas | Artefak / Bukti | Output | Status |
|---|---|---|---|---|---|
| 1 | 2026-11-03 | Siapkan struktur folder & konfigurasi proyek | `app/`, `routes/`, `config/`, `.env.example` | Kerangka back end | Selesai |
| 2 | 2026-11-04 | Koneksi database & migrasi awal | `database/migrations/*`, `.env` lokal | DB `pw_sewajas` terhubung | Selesai |
| 3 | 2026-11-05 | Alur login pakai sesi + redirect per role | `AuthController.php`, `config/session.php` | Login gabungan berfungsi | Selesai |
| 4 | 2026-11-06 | Middleware pembatas area admin/customer | `EnsureAdmin.php`, `EnsureCustomer.php`, `routes/web.php` | Otorisasi role | Selesai |
| 5 | 2026-11-07 | Desain UI login gabungan (tab Masuk/Daftar) | `resources/views/auth.blade.php` | UI login gabungan | Selesai |
| 6 | 2026-11-08 | Menu & UI pengaturan stok jas | `layouts/admin.blade.php`, `admin/jas/index.blade.php` | Menu Kelola Jas | Selesai |
| 7 | 2026-11-09 | Samakan nama field UI dengan RAT | `RegisterRequest.php`, `admin/jas/form.blade.php` | Pemetaan field | Selesai |
| 8 | 2026-11-10 | Dokumentasi setup & uji sesi | `README.md` (draft), `.env.example` | Panduan instalasi | Berjalan |
| 9 | — | Sisa: keputusan `kode_unik`, aksi cepat stok | — | Back end 100% | Belum |

**Pertanyaan untuk dosen (dibawa ke konsultasi):**
Kode unik diisi saat login atau saat registrasi?

**Rekomendasi jawaban:** saat registrasi (disimpan di `users.kode_unik`),
login tetap email + password.
