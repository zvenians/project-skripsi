# UNAI Upgrade — keputusan dan validasi

Tanggal: 17 November 2026

## Pilihan kompatibilitas

Bootstrap 6.0.0-alpha.1 belum stabil. Situs tetap memakai **Bootstrap 5.3.9 stable** sebagai baseline; V6 memerlukan pengujian regresi tersendiri, khususnya modal, dropdown, sidebar, DataTables, dan kelas utilitas CSS. Foto, logo, warna gelap dan style Bootstrap asli dipertahankan.

Konfigurasi Composer dan bootstrap aplikasi menargetkan **Laravel 13**. Ini merupakan *source migration*, bukan klaim pemasangan runtime. Tanpa Composer dan akses ke registry paket, `composer.lock`, `vendor/`, `node_modules/`, dan hasil build tidak dapat diproduksi. Jalankan dependency resolution dan tests sebelum deployment.

## Perubahan yang sudah dibuat

- Blade public/admin dikonsolidasikan menjadi layout yang memuat satu Bootstrap bundle (bukan versi ganda).
- Memisahkan CSS tema dari salinan Bootstrap 5.1 yang dulu dibundel dalam `landing.css`.
- Vue 3 untuk filter fasilitas; GSAP untuk motion ringan; Vite 8 menjadi entrypoint build. Ada CDN fallback bila build belum disiapkan.
- Memperbaiki form pencarian navbar dan responsivitas kartu/tabel, gambar menggunakan file yang sama.
- Menghapus query `mysqli_*` langsung dari dua Blade kalender dan menggantinya dengan Eloquent service + serialisasi JavaScript aman.
- Membatasi dashboard dengan auth, halaman admin dengan policy, rate-limit login/registrasi, hash password ketika user dibuat admin.
- Menutup kebocoran API key yang pernah ditanam di `wa.php` dan controller; mengaktifkan TLS verification, mengambil kunci dari `.env`.
- Migrasi tambahan idempotent untuk field yang dipakai kode tetapi tidak hadir di 2022 migrations.
- Menyederhanakan halaman review pesanan tanpa menghilangkan inspect/edit/delete dan formulir keputusan admin.

## Perubahan akses demo (tambahan)

- `DEMO_MODE=true` pada `APP_ENV=local` atau `APP_ENV=demo` mempertahankan tampilan `/login`, tetapi pengunjung dapat menekan tombol **Masuk ke Demo** tanpa mengisi kredensial.
- Dashboard deep link otomatis memakai satu akun khusus **Pengunjung Demo** (hak admin) dari database demo. Email akun internal berekstensi `.invalid` dan password acak, bukan password yang diketahui publik.
- Di lingkungan `production`, jalur demo selalu mati; gunakan `DEMO_MODE=false` untuk login normal.
- Akses publik ke dashboard demo akan memberi hak edit pada **database demo**. Jangan pernah menggunakan database produksi; akun demo dilindungi dari perubahan/hapus, dan WhatsApp outbound tidak aktif saat demo.
- Cache provider legacy dari versi lama (`bootstrap/cache/services.php` dan `bootstrap/cache/packages.php`) dibersihkan untuk menghindari pemuatan paket Laravel 8 yang sudah tidak ada.
- Validasi runtime alur demo tetap membutuhkan Composer dan database staging yang belum tersedia di lingkungan ini.

## Pekerjaan wajib sebelum go-live

1. **Rotasi kunci NusaSMS:** kredensial lama pernah ada di source yang diunggah, sehingga harus dianggap bocor. Jangan gunakan ulang di production.
2. Jalankan `composer update` dengan PHP 8.3+ dan selesaikan perubahan 8→9→10→11→12→13 terhadap konfigurasi lama. Periksa paket pihak ketiga (`botman`, `chatify`, `laravel/ui`, Sanctum, kalender, Pusher, Twilio). Berkas integrasi lama disimpan sebagai rujukan, tetapi paket belum dinyatakan compatible / aktif.
3. Jalankan `npm install && npm run build` lalu simpan lockfile hasil dependency resolution pada repositori.
4. Pindahkan atau petakan **data order lama**: kode lama menulis `users.name` ke kolom `orders.user_id` (foreignId), sedangkan kode baru menggunakan `users.id`. Backup dan konversi data harus dilakukan berbasis inspeksi database aktual; jangan mengasumsikan data bisa dipetakan otomatis.
5. Uji sistem secara nyata dengan database staging: autentikasi, role admin, Create/Read/Update/Delete facilities, users, orders, schedules, news, suggestions; upload foto; pemakaian kalender; review status, modal, email/WA integration, mobile, aksesibilitas, dan cache/config laravel.
6. Validasi `APP_KEY`, session driver, CSRF, storage permissions, queue/mail, HTTPS, index database, dan CORS saat deployment.
7. Persiapkan migrasi login lama: password yang sebelumnya disimpan plaintext oleh admin lama perlu reset password terarah, bukan dibaca/migrasi secara plaintext.
8. Perbaiki template legacy `resources/views/auth/*` hanya bila ingin menghidupkan kembali flow password reset dan verifikasi, sebab proyek menggunakan login/register custom.

## Pengujian yang tersedia di lingkungan ini

- Sintaks PHP (`php -l`) untuk source yang diekspor.
- Sintaks JavaScript (`node --check`) untuk entrypoint baru.
- Pembandingan file hash aset publik dengan ZIP asli.
- Pemeriksaan statis route/view dan deteksi sisa kredensial di source aplikasi.

**Sudah dilakukan:** dependency install dan audit NPM, build produksi showcase Vite, pemeriksaan sintaks JavaScript, serta smoke test browser untuk login demo, dashboard, modal reservasi, penyimpanan sandbox, dan tampilan responsif.

**Belum dilakukan:** Composer dependency install, koneksi MySQL staging, migrasi nyata, PHPUnit, serta end-to-end browser test yang terhubung ke runtime Laravel. Keterbatasan ini hanya berlaku pada backend PHP; showcase Vercel dibangun dan diuji secara terpisah.
