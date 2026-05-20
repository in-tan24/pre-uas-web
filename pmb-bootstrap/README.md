# PMB CampusFlow

Aplikasi latihan seleksi penerimaan mahasiswa baru berbasis PHP dan Bootstrap 5.

## Fitur

- Landing page alur PMB sesuai gambar referensi.
- Pendaftaran online calon mahasiswa.
- Login peserta dengan email pendaftaran.
- Dashboard peserta dengan tahapan seleksi berkas, ujian, pengumuman, pembayaran, dan OSPEK.
- Panel admin sederhana untuk memantau pendaftar.
- UI Bootstrap dengan nuansa kartu login seperti contoh lampiran.

## Cara Menjalankan di XAMPP

1. Pastikan folder ini berada di `htdocs`.
2. Jalankan Apache dari XAMPP Control Panel.
3. Buka `http://localhost/WEBPRE/pmb-bootstrap/index.php`.
4. Login admin memakai:
   - Username: `admin`
   - Password: `admin123`

Data demo disimpan di session browser agar alur bisa langsung diuji. File `database/schema.sql` disediakan jika ingin dikembangkan menjadi versi database MySQL.
