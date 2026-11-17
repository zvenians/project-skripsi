# Fasilitas UNAI

Sistem reservasi fasilitas Universitas Advent Indonesia. Peningkatan ini mempertahankan identitas visual, foto, logo, Blade views, dan basis data proyek asli. Komponen frontend dibuat lebih interaktif secara progresif tanpa memindahkan seluruh aplikasi menjadi SPA.

**Demo publik:** [project-skripsi-kevin.vercel.app](https://project-skripsi-kevin.vercel.app)

Demo Vercel adalah showcase Vue yang memakai aset asli dan sandbox `localStorage`. Backend Laravel tetap tersedia lengkap di repository untuk instalasi PHP/database. Showcase tidak mengirim perubahan ke data operasional UNAI.

## Teknologi

- PHP 8.3+ dan **Laravel 13** (target manifest, perlu instalasi dan pengujian di lingkungan terhubung)
- **Bootstrap 5.3.9 stable**. Bootstrap 6.0.0 masih alpha per 17 November 2026; tidak diaktifkan agar UI lama tidak rusak
- **Vue 3.5** (pencarian/filter fasilitas) + **GSAP 3.15** (motion) + **Vite 8** (bundling modern)
- Blade, MySQL/MariaDB, FullCalendar 6

## Mode demo tanpa login

- File `.env.example` mengaktifkan `DEMO_MODE=true` dalam `APP_ENV=local`. Pada pratinjau publik, gunakan `APP_ENV=demo` **dengan database demo terpisah**, bukan database operasional.
- Halaman `/login` tetap tersedia dengan gaya visual asli. Kolom email dan password tetap terlihat tetapi tidak perlu diisi; klik **Masuk ke Demo** untuk masuk sebagai **Pengunjung Demo**.
- Pengunjung juga dapat membuka `/dashboard/utama`, `/dashboard/order`, atau bagian admin secara langsung. Middleware membuat sesi demo dan akun demo secara otomatis saat tabel `users` tersedia; tidak memerlukan registrasi.
- Mode demo sengaja dinonaktifkan untuk `APP_ENV=production`, bahkan jika `DEMO_MODE=true`. Untuk login normal, set `DEMO_MODE=false`.
- Pengunjung demo berbagi akun admin dan data demo yang sama: jangan menghubungkan situs demo ke database asli. Notifikasi WhatsApp dinonaktifkan ketika mode demo aktif, dan akun demo tidak bisa diedit atau dihapus dari dashboard.
- Perubahan ini tidak menghilangkan autentikasi normal atau kredensial aplikasi apabila kelak dibuat menjadi aplikasi operasional.

### Menjalankan showcase Vercel

```bash
npm install
npm run dev:demo
```

Build produksi showcase dibuat dengan `npm run build:demo` ke folder `dist/`. Konfigurasi `vercel.json` memakai build ini karena Vercel tidak menyediakan runtime Laravel/PHP tradisional. Fitur reservasi showcase—buat, edit, setujui, tolak, hapus, kalender, dan reset—berjalan di sandbox browser.

## Instalasi

1. Pastikan PHP 8.3+, Composer 2, Node 20.19+ (atau versi Node 22/24 yang kompatibel), dan MySQL/MariaDB tersedia.
2. Dari folder `fasilitasunai`, jalankan `composer install` setelah membuat lock file dengan `composer update` pada lingkungan pengembangan; kemudian jalankan `npm install` dan `npm run build`.
3. Salin `.env.example` ke `.env`, atur koneksi database, lalu jalankan `php artisan key:generate` **hanya untuk instalasi baru**. Jika memigrasikan database yang sudah berisi data, **pertahankan APP_KEY lama**.
4. Cadangkan database sebelum menjalankan `php artisan migrate`. Periksa data lama, terutama kolom `orders.user_id` dan `schedules.user_id`, karena implementasi 2022 pernah mengirim nama organisasi sebagai ID.
5. Jalankan `php artisan storage:link` bila perlu dan `php artisan serve` untuk uji lokal. Atur virtual host production ke folder `public/`.
6. Isi `NUSASMS_API_KEY` hanya bila pengiriman WhatsApp memang diaktifkan dan kuncinya telah diganti. Jangan simpan kunci dalam source code.

## Fitur frontend

- Navbar dan sidebar yang lebih rapi serta tetap responsif.
- Filter fasilitas berbasis Vue 3; kartu tetap dirender Blade sehingga konten terlihat ketika JavaScript gagal.
- Efek masuk yang halus, hover yang ringan, dan dukungan `prefers-reduced-motion`.
- Kalender jadwal yang mengambil data dari ORM, bukan koneksi database langsung dari template.
- Quick filter tabel dashboard, penanganan tabel lebar di ponsel, dan halaman pemesanan yang lebih mudah dipakai.

## Status migrasi

**Catatan penting:** berkas `composer.json` diarahkan ke Laravel 13, tetapi lingkungan implementasi tidak menyediakan PHP/Composer. Oleh karena itu dependency resolution, migrasi database asli, dan compatibility plugin legacy **belum dapat dinyatakan lulus**. Build showcase Vite telah dijalankan terpisah. Jangan langsung menggantikan instalasi backend production. Baca [UPGRADE_NOTES.md](UPGRADE_NOTES.md) sebelum instalasi.

Aset `public/photo/` dan `public/storage/` tetap disertakan tanpa perubahan pada berkas asal. ZIP tidak menyertakan `.env`, database pribadi, `vendor/`, atau `node_modules/`.
