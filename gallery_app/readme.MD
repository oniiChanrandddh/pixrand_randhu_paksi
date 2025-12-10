# Pixrand – Aplikasi Galeri Berbasis Web

Pixrand adalah aplikasi web untuk pengelolaan foto dan album dengan dua peran utama, yaitu User dan Admin. Aplikasi ini dibangun dengan PHP, MySQL, dan struktur folder yang terorganisir agar mudah dikembangkan dan dipelajari.

## Akun Admin
Untuk mengakses halaman Admin, gunakan akun berikut:

Username: admin  
Password: admin123

## Fitur untuk User
- Upload foto ke album publik atau album pribadi.
- Membuat album pribadi dan melihat album publik yang dibuat pengguna lain.
- Sistem like dan komentar pada foto.
- Halaman detail foto dengan informasi lengkap (judul, tanggal upload, komentar, dan jumlah likes).
- Fitur download foto.
- Fitur pencarian cepat (CSR Search) untuk mencari album dan foto tanpa reload halaman.
- Tampilan responsif dengan desain gelap yang konsisten dan modern.

## Fitur untuk Admin
- Dashboard admin berisi ringkasan jumlah user, album, foto, dan aktivitas lain.
- Admin dapat membuat album baru yang otomatis berstatus publik.
- Manajemen album: melihat, menghapus, atau mengedit album.
- Manajemen foto: melihat semua foto pengguna dan menghapus foto jika diperlukan.
- Manajemen komentar: menghapus komentar yang melanggar.
- Sistem proteksi admin_guard agar halaman admin tidak bisa diakses user biasa.

## Fitur Keamanan
- Role-based access control (User dan Admin dipisahkan secara ketat).
- Validasi session dan login.
- Pembatasan akses album dan foto pribadi hanya untuk pemiliknya.
- Proteksi download dan akses langsung terhadap file.

## Desain dan Pengalaman Pengguna
Aplikasi menggunakan tema gelap dengan aksen ungu serta struktur layout yang konsisten pada seluruh halaman. Setiap komponen seperti album, foto, card, navigasi, dan grid disusun untuk terlihat rapi di layar besar maupun layar ponsel. Efek hover, bayangan, dan animasi ringan ditambahkan untuk memberi pengalaman penggunaan yang lebih modern.

## Ringkasan
Pixrand dirancang sebagai aplikasi galeri lengkap yang memiliki fitur utama sebuah platform berbagi foto, sekaligus panel admin untuk pengawasan konten. Proyek ini dapat dijadikan bahan pembelajaran, tugas sekolah, maupun dasar untuk pengembangan aplikasi yang lebih besar.

