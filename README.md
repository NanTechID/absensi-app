<div align="center">

<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">

# 🗂️ Absensi App

**Sistem Manajemen Absensi Karyawan berbasis Web**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-5.x-FDB538?style=for-the-badge&logo=filament&logoColor=white)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

*Proyek UTS – Mata Kuliah Pemrograman Web Lanjut*

</div>

---

## 📋 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Arsitektur](#-arsitektur)
- [Diagram Database](#-diagram-database)
- [Role & Permission](#-role--permission)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Seeder & Demo Data](#-seeder--demo-data)
- [Struktur Direktori](#-struktur-direktori)
- [Screenshots](#-screenshots)
- [Kontribusi](#-kontribusi)

---

## 🎯 Tentang Proyek

**Absensi App** adalah sistem manajemen absensi karyawan berbasis web yang dibangun menggunakan **Laravel 12** dan **Filament v5**. Aplikasi ini dirancang untuk mengelola data karyawan, departemen, tim, dan catatan absensi secara terintegrasi dengan sistem otorisasi berbasis peran (RBAC).

Proyek ini merupakan tugas UTS mata kuliah **Pemrograman Web Lanjut** yang mengimplementasikan best practices modern dalam pengembangan aplikasi web enterprise.

---

## ✨ Fitur Utama

### 👥 Manajemen Organisasi
| Fitur | Deskripsi |
|-------|-----------|
| **Departemen** | CRUD departemen perusahaan |
| **Tim/Unit** | Pengelompokan karyawan per tim dalam departemen |
| **Karyawan** | Data master karyawan lengkap dengan posisi & jabatan |
| **User Management** | Manajemen akun pengguna dengan role assignment |

### 🕐 Manajemen Absensi
| Fitur | Deskripsi |
|-------|-----------|
| **Pencatatan Absensi** | Input waktu masuk & waktu pulang |
| **Deteksi Lembur Otomatis** | Sistem otomatis mendeteksi & menandai lembur |
| **Alasan Lembur** | Form pengisian alasan ketika lembur terdeteksi |
| **Filter & Pencarian** | Filter absensi berdasarkan tanggal, karyawan, status lembur |

### 📊 Laporan & Ekspor
| Fitur | Deskripsi |
|-------|-----------|
| **Laporan Absensi** | Rekap absensi dengan filter periode & departemen |
| **Ekspor PDF** | Generate laporan absensi dalam format PDF (DomPDF) |
| **Ekspor Excel** | Ekspor data ke format XLSX (OpenSpout) |

### 🔐 Keamanan & Akses
| Fitur | Deskripsi |
|-------|-----------|
| **RBAC** | Role-Based Access Control dengan 8 level peran |
| **Hierarki Peran** | Manajer hanya bisa melihat data bawahannya |
| **Filament Shield** | Manajemen permission granular per resource |
| **Policy** | Laravel Policy untuk kontrol akses di level model |

### ⚙️ Konfigurasi & Branding
| Fitur | Deskripsi |
|-------|-----------|
| **App Branding** | Kustomisasi nama, logo, dan warna aplikasi |
| **App Settings** | Pengaturan aplikasi tersimpan di database |

---

## 🛠️ Tech Stack

### Backend
- **[Laravel 12](https://laravel.com)** — PHP Framework
- **[Filament v5](https://filamentphp.com)** — Admin Panel & UI
- **[Spatie Laravel Permission v6](https://spatie.be/docs/laravel-permission)** — RBAC
- **[Filament Shield](https://filamentphp.com/plugins/bezhansalleh-shield)** — Permission UI
- **[barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf)** — PDF Generation
- **[OpenSpout](https://github.com/openspout/openspout)** — Excel Export

### Frontend
- **Tailwind CSS** (via Filament)
- **Alpine.js** (via Filament)
- **Vite** — Asset bundling

### Database
- **MySQL 8.0+**

### Development Tools
- **PHP 8.2+**
- **Composer**
- **Node.js & NPM**

---

## 🏗️ Arsitektur

```
absensi-app/
├── app/
│   ├── Filament/Admin/
│   │   ├── Pages/              # Halaman kustom (Laporan, Branding)
│   │   └── Resources/          # CRUD Resources
│   │       ├── Absensis/       # Manajemen Absensi
│   │       ├── Departemens/    # Manajemen Departemen
│   │       ├── Karyawans/      # Manajemen Karyawan
│   │       ├── Teams/          # Manajemen Tim
│   │       └── Users/          # Manajemen User
│   ├── Http/
│   │   ├── Controllers/        # AttendanceReportExportController
│   │   └── Responses/          # FilamentLoginResponse (redirect by role)
│   ├── Models/                 # Eloquent Models
│   ├── Policies/               # Authorization Policies
│   ├── Providers/              # Service Providers
│   └── Services/
│       └── RoleHierarchy.php   # Role hierarchy logic
├── database/
│   ├── migrations/             # Database schema
│   └── seeders/                # Demo data seeders
└── resources/views/
    └── filament/admin/pages/   # Blade views (Laporan, Branding)
```

---

## 🗄️ Diagram Database

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   departemens   │     │      teams      │     │    karyawans    │
├─────────────────┤     ├─────────────────┤     ├─────────────────┤
│ id              │────<│ id              │────<│ id              │
│ nama_departemen │     │ nama_tim        │     │ nama_karyawan   │
│ created_at      │     │ departemen_id   │     │ nik             │
│ updated_at      │     │ created_at      │     │ team_id         │
└─────────────────┘     │ updated_at      │     │ posisi          │
                        └─────────────────┘     │ no_telp         │
                                                │ alamat          │
                        ┌─────────────────┐     │ created_at      │
                        │     users       │     │ updated_at      │
                        ├─────────────────┤     └────────┬────────┘
                        │ id              │              │
                        │ name            │              │
                        │ email           │       ┌──────┘
                        │ password        │       │
                        │ position        │     ┌─▼───────────────┐
                        │ created_at      │     │    absensis     │
                        │ updated_at      │     ├─────────────────┤
                        └────────┬────────┘     │ id              │
                                 │              │ karyawan_id     │
                                 └─────────────>│ user_id         │
                                               │ tanggal         │
                                               │ waktu_masuk     │
                                               │ waktu_pulang    │
                                               │ is_overtime     │
                                               │ alasan_overtime │
                                               │ created_at      │
                                               │ updated_at      │
                                               └─────────────────┘
```

---

## 🔐 Role & Permission

### Hierarki Peran (8 Level)

```
admin
  └── direktur
        └── manajer_operasional
              └── manajer_hrd
                    └── supervisor
                          └── team_leader
                                └── karyawan_senior
                                      └── karyawan
```

### Matriks Permission

| Permission | admin | direktur | manajer_operasional | manajer_hrd | supervisor | team_leader | karyawan_senior | karyawan |
|-----------|:-----:|:--------:|:-------------------:|:-----------:|:----------:|:-----------:|:---------------:|:--------:|
| `attendance.view` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| `attendance.create` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| `attendance.edit` | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| `attendance.delete` | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| `attendance.view_all` | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |

> **Role Hierarchy:** Setiap peran hanya dapat melihat data absensi dari karyawan yang berada di bawah hierarkinya.

---

## 🚀 Instalasi

### Prasyarat

Pastikan sistem Anda memiliki:
- **PHP** >= 8.2
- **Composer** >= 2.x
- **Node.js** >= 18.x & **NPM**
- **MySQL** >= 8.0
- **Git**

### Langkah Instalasi

#### 1. Clone Repository

```bash
git clone https://github.com/NanTechID/absensi-app.git
cd absensi-app
```

#### 2. Install Dependencies PHP

```bash
composer install
```

#### 3. Install Dependencies Node.js

```bash
npm install
```

#### 4. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

#### 5. Konfigurasi Database

Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### 6. Jalankan Migrasi & Seeder

```bash
php artisan migrate
php artisan db:seed
```

#### 7. Build Assets

```bash
npm run build
```

#### 8. Jalankan Server

```bash
php artisan serve
```

Akses aplikasi di: **http://localhost:8000/admin**

---

## ⚙️ Konfigurasi

### Publish Config (Opsional)

```bash
# Publish Spatie Permission config
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Publish Filament Shield config
php artisan vendor:publish --tag="filament-shield-config"
```

### Storage Link

```bash
php artisan storage:link
```

### Cache Clear

```bash
php artisan optimize:clear
```

---

## 🌱 Seeder & Demo Data

Setelah menjalankan `php artisan db:seed`, data berikut akan tersedia:

### 👤 Akun Demo

| Email | Password | Role | Akses |
|-------|----------|------|-------|
| `admin@admin.com` | `password` | **Admin** | Full access — semua fitur |
| `direktur@example.com` | `password` | **Direktur** | Lihat semua absensi |
| `manajer.ops@example.com` | `password` | **Manajer Operasional** | Kelola & edit absensi |
| `manajer.hrd@example.com` | `password` | **Manajer HRD** | Kelola absensi HRD |
| `supervisor@example.com` | `password` | **Supervisor** | Lihat & edit tim sendiri |
| `teamlead@example.com` | `password` | **Team Leader** | Lihat tim sendiri |
| `senior@example.com` | `password` | **Karyawan Senior** | Absensi diri sendiri |
| `karyawan@example.com` | `password` | **Karyawan** | Absensi diri sendiri |

### 🏢 Data Master

- **3 Departemen**: Operasional, HRD, IT
- **6 Tim**: Masing-masing departemen memiliki 2 tim
- **8+ Karyawan**: Tersebar di semua departemen
- **30+ Record Absensi**: Termasuk data lembur untuk pengujian

---

## 📁 Struktur Direktori Lengkap

```
absensi-app/
├── app/
│   ├── Filament/
│   │   └── Admin/
│   │       ├── Pages/
│   │       │   ├── AppBrandingSettings.php
│   │       │   └── LaporanAbsensi.php
│   │       └── Resources/
│   │           ├── Absensis/
│   │           │   ├── Pages/
│   │           │   │   ├── CreateAbsensi.php
│   │           │   │   ├── EditAbsensi.php
│   │           │   │   └── ListAbsensis.php
│   │           │   ├── Schemas/
│   │           │   │   └── AbsensiForm.php
│   │           │   ├── Tables/
│   │           │   │   └── AbsensisTable.php
│   │           │   └── AbsensiResource.php
│   │           ├── Departemens/        # + Schemas/ Tables/ Pages/
│   │           ├── Karyawans/          # + Schemas/ Tables/ Pages/
│   │           ├── Teams/              # + Schemas/ Tables/ Pages/
│   │           └── Users/              # + Pages/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── AttendanceReportExportController.php
│   │   └── Responses/
│   │       └── FilamentLoginResponse.php
│   ├── Models/
│   │   ├── Absensi.php
│   │   ├── AppSetting.php
│   │   ├── Departemen.php
│   │   ├── Karyawan.php
│   │   ├── Team.php
│   │   └── User.php
│   ├── Policies/
│   │   ├── AbsensiPolicy.php
│   │   └── RolePolicy.php
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── Filament/
│   │       └── AdminPanelProvider.php
│   └── Services/
│       └── RoleHierarchy.php
├── database/
│   ├── migrations/
│   │   ├── create_departemens_table.php
│   │   ├── create_teams_table.php
│   │   ├── create_karyawans_table.php
│   │   ├── create_absensis_table.php
│   │   ├── create_permission_tables.php
│   │   ├── add_position_to_users_table.php
│   │   ├── add_user_id_to_absensis_table.php
│   │   ├── map_attendance_permissions_to_shield_resource.php
│   │   └── create_app_settings_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── DepartemenSeeder.php
│       ├── TeamSeeder.php
│       ├── KaryawanSeeder.php
│       ├── UserSeeder.php
│       ├── RolePermissionSeeder.php
│       └── AbsensiSeeder.php
├── resources/
│   └── views/filament/admin/pages/
│       ├── app-branding-settings.blade.php
│       ├── laporan-absensi.blade.php
│       └── laporan-absensi-pdf.blade.php
├── tests/
│   └── Feature/
│       ├── AppBrandingSettingsTest.php
│       └── AttendanceVisibilityTest.php
├── routes/
│   └── web.php
├── composer.json
└── README.md
```

---

## 🧪 Testing

```bash
# Jalankan semua test
php artisan test

# Jalankan test spesifik
php artisan test --filter=AttendanceVisibilityTest

# Jalankan dengan coverage (butuh Xdebug)
php artisan test --coverage
```

---

## 🔄 Perintah Artisan Berguna

```bash
# Reset & seed ulang database
php artisan migrate:fresh --seed

# Clear semua cache
php artisan optimize:clear

# Jalankan dev server lengkap (server + queue + logs + vite)
composer run dev

# Install & setup cepat
composer run setup
```

---

## 🤝 Kontribusi

Pull request sangat diterima! Untuk perubahan besar, harap buka issue terlebih dahulu untuk mendiskusikan apa yang ingin Anda ubah.

1. **Fork** repository ini
2. **Buat** branch fitur (`git checkout -b feature/fitur-baru`)
3. **Commit** perubahan (`git commit -m 'feat: tambah fitur baru'`)
4. **Push** ke branch (`git push origin feature/fitur-baru`)
5. **Buat** Pull Request

---

## 👨‍💻 Developer

<div align="center">

**Dibuat dengan ❤️ untuk UTS Pemrograman Web Lanjut**

[![GitHub](https://img.shields.io/badge/GitHub-NanTechID-181717?style=for-the-badge&logo=github)](https://github.com/NanTechID)

</div>

---

## 📄 Lisensi

Proyek ini menggunakan lisensi [MIT](LICENSE).

---

<div align="center">

**⭐ Jika proyek ini membantu, jangan lupa beri bintang!**

</div>
