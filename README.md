# Fasilitas UNAI

Sistem reservasi fasilitas Universitas Advent Indonesia. Peningkatan ini mempertahankan identitas visual, foto, logo, Blade views, dan basis data proyek asli. Komponen frontend dibuat lebih interaktif secara progresif tanpa memindahkan seluruh aplikasi menjadi SPA.

**Demo publik:** [project-skripsi-kevin.vercel.app](https://project-skripsi-kevin.vercel.app)

Demo Vercel adalah showcase Vue dengan tiga role, aset asli, dan sandbox `localStorage`. Backend Laravel tetap tersedia lengkap di repository untuk instalasi PHP/database. Showcase tidak mengirim perubahan ke data operasional UNAI.

## Teknologi

- PHP 8.3+ dan **Laravel 13**
- **Bootstrap 5.3.9 stable**. Bootstrap 6.0.0 masih alpha per 17 November 2026; tidak diaktifkan agar UI lama tidak rusak
- **Vue 3.5**, **GSAP 3.15**, **Vite 8**, dan **FullCalendar 6**
- Blade, MySQL/MariaDB, Vitest, PHPUnit, dan Playwright

## Mode demo tanpa login

- File `.env.example` mengaktifkan `DEMO_MODE=true` dalam `APP_ENV=local`. Pada pratinjau publik, gunakan `APP_ENV=demo` **dengan database demo terpisah**, bukan database operasional.
- Halaman demo menyediakan pilihan **Superadmin**, **Admin Organisasi**, dan **Admin Pengelola** tanpa kredensial.
- Pengunjung juga dapat membuka `/dashboard/utama`, `/dashboard/order`, atau bagian admin secara langsung. Middleware membuat sesi demo dan akun demo secara otomatis saat tabel `users` tersedia; tidak memerlukan registrasi.
- Mode demo sengaja dinonaktifkan untuk `APP_ENV=production`, bahkan jika `DEMO_MODE=true`. Untuk login normal, set `DEMO_MODE=false`.
- Setiap role memakai identitas terpisah. Jangan menghubungkan situs demo ke database asli. Notifikasi WhatsApp dinonaktifkan ketika mode demo aktif, dan seluruh akun demo dilindungi dari edit/hapus.
- Perubahan ini tidak menghilangkan autentikasi normal atau kredensial aplikasi apabila kelak dibuat menjadi aplikasi operasional.

### Menjalankan showcase Vercel

```bash
npm install
npm run dev:demo
```

Build produksi showcase dibuat dengan `npm run build:demo` ke folder `dist/`. Konfigurasi `vercel.json` memakai build ini karena Vercel tidak menyediakan runtime Laravel/PHP tradisional. Fitur reservasi showcase—buat, edit, setujui, tolak, hapus, kalender, dan reset—berjalan di sandbox browser.

## Instalasi

1. Pastikan PHP 8.3+, Composer 2, Node 20.19+ (atau versi Node 22/24 yang kompatibel), dan MySQL/MariaDB tersedia.
2. Jalankan `composer install`, kemudian `npm ci` dan `npm run build`.
3. Salin `.env.example` ke `.env`, atur koneksi database, lalu jalankan `php artisan key:generate` **hanya untuk instalasi baru**. Jika memigrasikan database yang sudah berisi data, **pertahankan APP_KEY lama**.
4. Cadangkan database sebelum menjalankan `php artisan migrate`. Periksa data lama, terutama kolom `orders.user_id` dan `schedules.user_id`, karena implementasi 2022 pernah mengirim nama organisasi sebagai ID.
5. Jalankan `php artisan storage:link` bila perlu dan `php artisan serve` untuk uji lokal. Atur virtual host production ke folder `public/`.
6. Isi `NUSASMS_API_KEY` hanya bila pengiriman WhatsApp memang diaktifkan dan kuncinya telah diganti. Jangan simpan kunci dalam source code.

## Fitur frontend

- Tema editorial charcoal/hitam dengan permukaan ivory dan warna semantik khusus status/notifikasi.
- Sidebar, menu, data, dan tindakan yang berbeda untuk tiga role.
- Kalender bulan/minggu/hari, filter, detail event, serta CRUD pemesanan sandbox.
- Validasi anti-bentrok di frontend dan transaksi database dengan row locking di backend.
- Efek masuk yang halus, dukungan `prefers-reduced-motion`, tabel responsif, ekspor laporan, kritik/saran, dan reset sandbox.

## Status migrasi

Dependency Laravel dikunci melalui `composer.lock`. Suite PHPUnit menguji akses demo multi-role serta aturan anti-bentrok; Vitest menguji logika interval dan Playwright menguji alur desktop/mobile. Tetap cadangkan database dan baca [UPGRADE_NOTES.md](UPGRADE_NOTES.md) sebelum memigrasikan instalasi lama.

Aset `public/photo/` dan `public/storage/` tetap disertakan tanpa perubahan pada berkas asal. ZIP tidak menyertakan `.env`, database pribadi, `vendor/`, atau `node_modules/`.
