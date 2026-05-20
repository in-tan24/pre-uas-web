# Setup (XAMPP / Localhost)

## Prerequisites
- XAMPP (Apache + MySQL) running
- PHP 7.4+ (bundled with XAMPP)

## 1) Buat database & tabel

### Opsi A: phpMyAdmin
1. Buka phpMyAdmin
2. Tab **Import**
3. Pilih file `database/schema.sql`
4. Klik **Go**

### Opsi B: MySQL CLI (jika tersedia)
Jalankan dari folder project:

```bash
mysql -u root -p < database/schema.sql
```

Jika password MySQL kosong:

```bash
mysql -u root < database/schema.sql
```

### Opsi C (Windows/XAMPP): mysql.exe bawaan XAMPP
PowerShell dari folder project:

```powershell
Get-Content .\database\schema.sql | & "C:\xampp\mysql\bin\mysql.exe" -u root
```

## 2) Seed data (opsional)
Import `database/seeds/sample_data.sql` lewat phpMyAdmin atau CLI:

```bash
mysql -u root < database/seeds/sample_data.sql
```

PowerShell (XAMPP):

```powershell
Get-Content .\database\seeds\sample_data.sql | & "C:\xampp\mysql\bin\mysql.exe" -u root
```

## 2a) Akun admin default (jika pakai seed)
- `superadmin` / `admin123`
- `admin` / `admin123`
- `finance` / `admin123`

## Superadmin (akses penuh)
- Superadmin bisa akses semua fitur admin (applications, exams, results, payments, ospek, master data).
- Menu tambahan: `Users` untuk membuat akun admin/finance/superadmin: `http://localhost/pre-uas/public/admin/users`

## Catatan untuk instalasi lama
Jika database sudah terlanjur dibuat sebelum ada kolom password candidate, jalankan migration:

```powershell
Get-Content .\database\migrations\011_alter_candidates_add_password.sql | & "C:\xampp\mysql\bin\mysql.exe" -u root pre_uas_admission
```

Jika database sudah terlanjur dibuat sebelum login admin pakai username, jalankan migration:

```powershell
Get-Content .\database\migrations\012_alter_users_add_username.sql | & "C:\xampp\mysql\bin\mysql.exe" -u root pre_uas_admission
```

## 3) Konfigurasi env
1. Copy `.env.example` jadi `.env`
2. Sesuaikan `DB_HOST`, `DB_USER`, `DB_PASS` bila perlu

## 4) Akses web
- Pastikan project berada di `c:\xampp\htdocs\pre-uas`
- Buka `http://localhost/pre-uas/public/`
