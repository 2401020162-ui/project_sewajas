# Log Kontribusi Individu — PW_SEWAJAS (Kelompok 6)

Dokumen ini memetakan progres tiap anggota ke artefak nyata di repositori
`https://github.com/2401020162-ui/project_sewajas` (branch `main`).

## Status aplikasi saat ini (baseline bersama)

| Aspek | Kondisi |
|---|---|
| Framework | Laravel 12.69.2, PHP 8.2.12, MySQL `pw_sewajas` |
| Autentikasi | Satu form masuk/daftar untuk User & Admin, satu tabel `users` + kolom `role` |
| Otorisasi | Middleware `admin` dan `customer` (`app/Http/Middleware/`) |
| Alur utama | Daftar → Masuk → Katalog → Detail → Keranjang → Checkout → Pembayaran (QRIS/Cash) → Verifikasi Admin → Disewa → Pengembalian → Selesai |
| Status pesanan | `pending`, `menunggu_verifikasi`, `disewa`, `selesai`, `dibatalkan` |
| Stok | Berkurang saat checkout, kembali saat batal atau selesai/pengembalian |
| Pengujian | `php artisan test` → 11 passed (32 assertions) |
| Commit terakhir | `20e397a` — penyesuaian alur pesanan & pembayaran |

## Catatan penting soal bukti kontribusi

Seluruh commit di repo saat ini tercatat atas satu identitas Git (`Nabiilah`),
karena pekerjaan masih dijalankan di satu mesin. Agar log individu kuat sebagai
bukti, lakukan ini mulai sekarang:

1. Setiap anggota mengatur identitas Git sendiri sebelum commit:
   `git config user.name "Nama"` dan `git config user.email "email@kampus"`.
2. Setiap anggota commit pada file yang menjadi tanggung jawabnya
   (contoh: Christine → `resources/views/admin/**`, `resources/views/layouts/admin.blade.php`).
3. Simpan tautan commit di kolom "Bukti" pada tabel log masing-masing.

## Daftar file log

- `01-christine.md` — Sequence diagram + front end Admin
- `02-salsadilla.md` — Activity diagram + front end User
- `03-ripa.md` — UI login gabungan, menu stok, back end
- `04-aisyah.md` — RAT, use case Kelola Stok, skema & back end
