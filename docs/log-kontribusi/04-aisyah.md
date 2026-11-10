# Log Kontribusi Individu — Aisyah

**Peran:** RAT & Use Case + Back end
**Progres dilaporkan:** ±35% (back end: skema database, register, login)
**Fokus saat ini:** tipe data tiap kolom RAT, login gabungan (Role + Kode_Unik), use case Kelola Stok Jas

---

## 1. Ringkasan progres yang dilaporkan

- Menyelesaikan **tipe data tiap kolom di RAT**.
- Merevisi RAT agar cocok dengan **login gabungan User dan Admin**
  (satu tabel, kolom `Role` dan `Kode_Unik`).
- Ingin bertanya ke dosen: **tabel Admin tetap dipisah atau digabung saja?**
- Menambahkan **use case Kelola Stok Jas**.
- Back end bersama Ripa: skema database, register, login.

## 2. Lanjutan yang sudah tersedia di aplikasi

### 2.1 Tipe data aktual (siap disalin ke RAT)

**users**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED | PK, auto increment |
| name | VARCHAR(255) | wajib |
| email | VARCHAR(255) | UNIQUE, wajib |
| email_verified_at | TIMESTAMP | NULL |
| password | VARCHAR(255) | hash |
| role | ENUM('admin','customer') | DEFAULT 'customer', INDEX |
| nomor_hp | VARCHAR(20) | NULL |
| remember_token | VARCHAR(100) | NULL |
| created_at, updated_at | TIMESTAMP | NULL |

**kategori**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED | PK |
| nama | VARCHAR(100) | UNIQUE |
| slug | VARCHAR(120) | UNIQUE |
| created_at, updated_at | TIMESTAMP | NULL |

**jas**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED | PK |
| kategori_id | BIGINT UNSIGNED | FK → kategori.id, RESTRICT delete, CASCADE update |
| nama | VARCHAR(120) | INDEX |
| deskripsi | TEXT | NULL |
| ukuran | VARCHAR(60) | DEFAULT 'S,M,L,XL' |
| harga_sewa | INT UNSIGNED | wajib |
| stok | INT UNSIGNED | DEFAULT 0 |
| gambar | VARCHAR(255) | NULL |
| is_aktif | BOOLEAN | DEFAULT 1 |
| created_at, updated_at | TIMESTAMP | NULL |

**keranjang**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED | PK |
| user_id | BIGINT UNSIGNED | FK → users.id, CASCADE |
| jas_id | BIGINT UNSIGNED | FK → jas.id, CASCADE |
| ukuran | VARCHAR(10) | wajib |
| jumlah | INT UNSIGNED | DEFAULT 1 |
| tanggal_mulai | DATE | wajib |
| tanggal_selesai | DATE | wajib |
| created_at, updated_at | TIMESTAMP | NULL |
| — | UNIQUE(user_id, jas_id, ukuran) | satu baris per kombinasi |

**pesanan**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED | PK |
| kode_pesanan | VARCHAR(30) | UNIQUE |
| user_id | BIGINT UNSIGNED | FK → users.id, RESTRICT |
| jas_id | BIGINT UNSIGNED | FK → jas.id, RESTRICT |
| admin_id | BIGINT UNSIGNED | FK → users.id, NULL, SET NULL |
| nama_pemesan | VARCHAR(100) | NULL |
| nomor_hp | VARCHAR(20) | NULL |
| tanggal_pesan | DATE | wajib |
| tanggal_mulai | DATE | wajib |
| tanggal_selesai | DATE | wajib |
| tanggal_dikembalikan | DATE | NULL |
| ukuran | VARCHAR(10) | wajib |
| jumlah | INT UNSIGNED | DEFAULT 1 |
| total_harga | BIGINT UNSIGNED | wajib |
| status | ENUM 5 nilai | DEFAULT 'pending', INDEX |
| catatan | TEXT | NULL |
| catatan_pengembalian | TEXT | NULL |
| created_at, updated_at | TIMESTAMP | NULL |

Nilai `status`: `pending`, `menunggu_verifikasi`, `disewa`, `selesai`, `dibatalkan`.

**pembayaran**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED | PK |
| pesanan_id | BIGINT UNSIGNED | FK → pesanan.id, UNIQUE, CASCADE |
| metode | ENUM('qris','cash') | DEFAULT 'qris' |
| bukti_pembayaran | VARCHAR(255) | NULL |
| status | ENUM('pending','diverifikasi','ditolak') | DEFAULT 'pending', INDEX |
| tanggal_bayar | DATETIME | NULL |
| tanggal_verifikasi | DATETIME | NULL |
| diverifikasi_oleh | BIGINT UNSIGNED | FK → users.id, NULL, SET NULL |
| catatan_admin | TEXT | NULL |
| created_at, updated_at | TIMESTAMP | NULL |

**testimoni**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED | PK |
| user_id | BIGINT UNSIGNED | FK → users.id, CASCADE |
| jas_id | BIGINT UNSIGNED | FK → jas.id, CASCADE |
| rating | TINYINT UNSIGNED | 1–5 |
| komentar | TEXT | wajib |
| created_at, updated_at | TIMESTAMP | NULL |
| — | UNIQUE(user_id, jas_id) | satu testimoni per jas per user |

### 2.2 Catatan `Kode_Unik`

Skema saat ini **belum** memuat `kode_unik`. Jika dosen menyetujui login
gabungan dengan kode unik, tambahkan:

```php
$table->string('kode_unik', 50)->nullable()->after('role');
```

Aturan pemakaian: wajib diisi **saat registrasi** hanya untuk `role = admin`,
divalidasi di `app/Http/Requests/Auth/RegisterRequest.php`; login tetap
email + password.

### 2.3 Use case Kelola Stok Jas (sudah terimplementasi)

| Aksi | Lokasi kode | Efek stok |
|---|---|---|
| Admin ubah stok manual | `Admin\JasController@store/update` | set nilai `stok` |
| Checkout pesanan | `KeranjangController@checkout` | `decrement('stok', jumlah)` setelah cek ketersediaan |
| Customer batalkan | `PesananController@batal` | `increment('stok', jumlah)` |
| Admin tandai selesai/pengembalian | `Admin\PesananController@selesai` | `increment('stok', jumlah)` |
| Admin batalkan | `Admin\PesananController@batal` | `increment('stok', jumlah)` |
| Peringatan stok rendah | `Admin\DashboardController` | daftar `stok <= 2` |

**Jawaban untuk pertanyaan tabel Admin:** **digabung** ke tabel `users` dengan
kolom `role` (dan `kode_unik` bila disetujui).

Alasan:
- Proposal memakai **satu form login**; tabel terpisah memaksa dua alur autentikasi.
- Relasi `pesanan.user_id`, `pesanan.admin_id`, `pembayaran.diverifikasi_oleh`
  semuanya cukup menunjuk ke `users`.
- Middleware `admin`/`customer` jadi sederhana dan sudah berjalan.
- Tidak ada duplikasi kolom email/password.

Konsekuensi yang harus ditulis di RAT: kolom khusus admin harus **nullable**,
dan `role` diberi index. Pemisahan tabel baru relevan jika admin punya banyak
atribut khusus (jabatan, shift, dsb.) — saat ini tidak ada.

## 3. Tugas berikutnya

1. Sinkronkan RAT: tambahkan `kode_unik` (atau hapus jika tidak dipakai) dan
   pastikan tipe data sama dengan migrasi di atas.
2. Perbarui ERD agar `admin_id` dan `diverifikasi_oleh` terlihat menunjuk ke `users`.
3. Lengkapi use case **Kelola Stok Jas** (aktor Admin, include: lihat stok,
   ubah stok, validasi stok tidak negatif).
4. Back end: register + login sudah jalan; lanjutkan validasi dan pengujian.

## 4. Log kontribusi individu

| No | Tanggal | Aktivitas | Artefak / Bukti | Output | Status |
|---|---|---|---|---|---|
| 1 | 2026-11-03 | Tentukan tipe data tiap kolom RAT | `database/migrations/*` | Tabel RAT bertipe | Selesai |
| 2 | 2026-11-04 | Revisi RAT untuk login gabungan (Role) | `0001_01_01_000000_create_users_table.php` | RAT v2 | Selesai |
| 3 | 2026-11-05 | Tambah use case Kelola Stok Jas | `Admin/JasController.php`, `KeranjangController.php` | Use case baru | Selesai |
| 4 | 2026-11-06 | Skema kategori & jas | `2026_01_01_000001_create_kategori_dan_jas_tables.php` | Tabel kategori, jas | Selesai |
| 5 | 2026-11-07 | Skema keranjang, pesanan, pembayaran | `2026_01_01_000002_*`, `2026_01_01_000003_*` | Tabel transaksi | Selesai |
| 6 | 2026-11-08 | Back end register | `AuthController@register`, `RegisterRequest.php` | Registrasi customer | Selesai |
| 7 | 2026-11-09 | Back end login + sesi | `AuthController@login`, `LoginRequest.php` | Login gabungan | Selesai |
| 8 | 2026-11-10 | Seeder & uji alur | `DatabaseSeeder.php`, `tests/Feature/AlurPenyewaanTest.php` | 11 test lulus | Selesai |
| 9 | — | Sisa: `kode_unik`, ERD, uji use case stok | — | Back end 100% | Berjalan |

**Pertanyaan untuk dosen (dibawa ke konsultasi):**
Tabel Admin tetap dipisah atau digabung ke tabel User?

**Rekomendasi jawaban:** digabung (`users` + `role` + `kode_unik` nullable),
karena login gabungan dan relasi pesanan/pembayaran sudah menunjuk ke `users`.
