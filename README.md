# SI KENDIS

Aplikasi Laravel lokal untuk pendataan awal kendaraan dinas berdasarkan scan BPKB. OPD menginput kendaraan satu per satu, lalu admin memverifikasi dan mengecek potensi duplikat nomor rangka/nomor mesin.

## Akun awal

- Email: `admin@bpkb.local`
- Password: `password`

Segera ganti password setelah aplikasi dipakai.

## Instalasi Laragon/XAMPP

1. Buat database MySQL bernama `inputbpkb`.
2. Salin `.env.example` menjadi `.env`, lalu sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inputbpkb
DB_USERNAME=root
DB_PASSWORD=
```

3. Jalankan perintah berikut dari folder proyek:

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

4. Buka `http://127.0.0.1:8000`.

Untuk development UI, jalankan `npm run dev` di terminal terpisah.

## Struktur utama

- `opds`: master OPD.
- `users`: akun admin dan User OPD, memakai kolom `role` dan `opd_id`.
- `kendaraans`: data kendaraan, scan BPKB, scan STNK, foto kendaraan, status verifikasi, dan catatan admin.
- `mutasi_kendaraans`: pengajuan mutasi kendaraan antar-OPD, file BAST, status verifikasi admin, dan catatan admin.
- `referensi_kendaraans`: database awal kendaraan hasil import Excel/CSV yang dapat dipilih OPD saat input kendaraan.
- Admin dapat export data kendaraan ke file `.xls` dari menu Data Kendaraan. File bisa dibuka langsung di Microsoft Excel.
- Admin dapat import database awal kendaraan dari menu Import Database. Kolom yang dibaca: Plat Nomor, Merk, Tipe, Tahun, Nomor Rangka, Nomor Mesin, Nomor BPKB.
- File upload baru disimpan dengan pola nama `PLATjenisberkas-kodeunik.ext`, misalnya `G7Fbpkb-20260511ABC123.pdf`, `G7Fstnk-...pdf`, dan `G7Fbast-...pdf`.

Primary key seluruh tabel tetap `id` auto increment. Nomor rangka dan nomor mesin tidak dibuat unique agar data duplikat tetap bisa masuk dan diperiksa admin.

Pada dashboard admin, angka kendaraan utama hanya menghitung kendaraan berstatus `disetujui`.
