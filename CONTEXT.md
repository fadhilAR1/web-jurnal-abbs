# Project Context: SMA ABBS Surakarta Jurnal & Monitoring Web Application

Welcome! This document provides a complete overview, tech stack detail, database architecture, and folder structure for the **SMA ABBS Surakarta Jurnal & Monitoring Web Application**. This file is created to help AI agents and developers quickly understand the codebase and build features seamlessly.

---

## 1. Overview
The **SMA ABBS Surakarta Jurnal & Monitoring Web Application** is a management system designed for school teachers (Guru) and administrators (Admin). It allows teachers to log and manage school journals, and administrators to manage master data (students, classes, subjects, extracurriculars, academic years), manage user accounts, and monitor all school journals.

### User Roles & Permissions
1. **Admin**:
   - Dashboard statistics overview.
   - **User Management**: CRUD operations, import/export accounts for teachers/admins.
   - **Student Management (Master Siswa)**: CRUD operations, export student data.
   - **Master Data Management**: CRUD operations for Classes, Subjects (Mapel), Extracurriculars (Ekskul), and Academic Years (Tahun Ajaran). Can manage students enrolled in a class or extracurricular.
   - **Monitoring**: Read and edit all journals (KBM, Ekskul, Piket, Kegiatan).
2. **Guru (Teacher)**:
   - Dashboard summary of filled journals.
   - **Journal Logging (CRUD)**:
     - **Jurnal KBM** (Kegiatan Belajar Mengajar): Teaching activities with student attendance.
     - **Jurnal Ekskul** (Ekstrakurikuler): Extracurricular activities, student attendance, and photo uploads.
     - **Jurnal Piket**: Logging teacher picket duties, teacher absences, class substitute information, school guests, and event notes.
     - **Jurnal Kegiatan**: Event/activity logs with descriptive text and file attachments.
   - **Profile Management**: Complete and update teacher biodata.
3. **Public (Students/Parents)**:
   - Read-only dashboard and journals.

---

## 2. Tech Stack & Dependencies

- **Backend Framework**: Laravel 12 (PHP 8.2+)
- **Database**: SQLite (default connection configured in `.env`)
- **Frontend Frameworks & Styling**:
  - **Bootstrap v5.3.0** (loaded via CDN in Blade layouts for layout structure and responsive grids)
  - **custom-ui.css** (located in `public/css/custom-ui.css` for custom colors, panels, sidebar transitions, and micro-interactions)
  - **Tailwind CSS v4.0.0** (devDependencies pre-configured in `package.json` and `resources/css/app.css` but not compiled directly into the main templates)
  - **Sass / SCSS**: Built using Vite (`resources/sass/app.scss` importing Bootstrap SCSS files)
- **Key PHP Packages** (in `composer.json`):
  - `barryvdh/laravel-dompdf` (for PDF report generation)
  - `maatwebsite/excel` (for Excel imports and exports)
  - `spatie/laravel-permission` (integrated for role management)
  - `google/apiclient` (for Google API integration)
  - `laravel/socialite` (for OAuth integration)
- **Notifications / Dialogs**: SweetAlert2 (via CDN in layout file)

---

## 3. Database Schema & Key Models

Here are the primary models in `app/Models/` and their respective attributes:

### 1. `User`
- **Fields**: `id`, `name`, `username`, `password`, `role` (`admin` | `guru` | `siswa`), `remember_token`, `timestamps`
- **Relations**: Has one `BiodataGuru` (`biodata()`)

### 2. `BiodataGuru`
- **Fields**: `id`, `user_id`, `nip`, `nuptk`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `telepon`, `alamat`, `timestamps`
- **Relations**: Belongs to `User`

### 3. `Siswa`
- **Fields**: `id`, `nis`, `nama`, `jenis_kelamin`, `kelas_id` (nullable), `timestamps`
- **Relations**: Belongs to `MasterKelas`

### 4. `MasterKelas`
- **Fields**: `id`, `nama_kelas`, `tingkat` (X, XI, XII), `program` (MIPA, IPS, dll), `kapasitas`, `timestamps`
- **Relations**: Has many `Siswa`

### 5. `MasterMapel`
- **Fields**: `id`, `nama_mapel`, `jenjang` (X, XI, XII), `program` (Umum, MIPA, IPS, dll), `timestamps`

### 6. `MasterEkskul`
- **Fields**: `id`, `nama_ekskul`, `kapasitas`, `timestamps`
- **Relations**: Belongs to many `Siswa` via pivot `SiswaEkstrakurikuler`

### 7. `MasterTahunAjaran`
- **Fields**: `id`, `tahun_ajaran` (e.g. "2025/2026"), `semester` (Ganjil/Genap), `status` (active / inactive), `timestamps`

### 8. `JurnalKBM`
- **Fields**: `id`, `guru_id`, `kelas_id`, `mapel_id`, `tanggal`, `jam_mulai`, `jam_selesai`, `materi`, `absensi` (JSON cast as array), `catatan`, `master_tahun_ajaran_id`, `timestamps`
- **Relations**: Belongs to `User` (as `guru`), `MasterKelas`, `MasterMapel`, `MasterTahunAjaran`

### 9. `JurnalEkskul`
- **Fields**: `id`, `guru_id`, `ekskul_id`, `tanggal`, `waktu_mulai`, `waktu_selesai`, `materi`, `absensi` (JSON cast as array), `foto_kegiatan` (JSON array of paths), `catatan`, `status`, `timestamps`
- **Relations**: Belongs to `User` (as `guru`), `MasterEkskul`

### 10. `JurnalPiket`
- **Fields**: `id`, `guru_id`, `hari`, `tanggal`, `guru_tidak_hadir` (JSON cast), `jumlah_siswa_tidak_hadir`, `siswa_tidak_hadir_detail` (JSON cast), `tamu_sekolah` (JSON cast), `kejadian_penting` (JSON cast), `catatan`, `status`, `timestamps`
- **Relations**: Belongs to `User` (as `guru`)

### 11. `JurnalKegiatan`
- **Fields**: `id`, `guru_id`, `jenis_kegiatan`, `nama_kegiatan`, `tanggal`, `waktu_mulai`, `waktu_selesai`, `lokasi`, `peserta` (JSON cast), `deskripsi`, `foto_kegiatan` (JSON cast), `dokumentasi` (JSON cast), `status`, `timestamps`
- **Relations**: Belongs to `User` (as `guru`)

---

## 4. Key Routes Layout (`routes/web.php`)

- **File Server Route**: `/file/{path}` (Dynamically serves assets from `storage/app/public/` to bypass local Windows symlink issues)
- **Public Routes**: `/public/dashboard`, `/public/jurnal/kbm`, `/public/jurnal/ekskul`, `/public/jurnal/piket`, `/public/jurnal/kegiatan`
- **Auth Routes**: `/login` (GET/POST), `/logout` (POST)
- **Guru Protected Routes** (`/jurnal/...`):
  - `/profile` (GET/PUT)
  - `/dashboard`
  - `/jurnal/kbm/*` (CRUD KBM logs + JSON helpers: `get-siswa/{kelasId}`, `get-mapel/{kelasId}`)
  - `/jurnal/ekskul/*` (CRUD Ekskul logs)
  - `/jurnal/piket/*` (CRUD Piket logs)
  - `/jurnal/kegiatan/*` (CRUD Kegiatan logs + download documentation)
- **Admin Protected Routes** (`/admin/...`):
  - `/admin/dashboard`
  - `/admin/users/*` (CRUD accounts + Excel exports)
  - `/admin/master-siswa/*` (CRUD + Excel exports)
  - `/admin/master-kelas/*` (CRUD + Manage enrolled students per class + Excel exports)
  - `/admin/master-mapel/*` (CRUD + Excel exports)
  - `/admin/master-ekskul/*` (CRUD + Manage participants + Excel exports)
  - `/admin/master-tahun-ajaran/*` (CRUD + Set active semester)
  - `/admin/monitoring/*` (Monitor and edit journal logs from all teachers)

---

## 5. File & Folder Structure

```text
├── app/
    ├── Console/
        ├── Commands/
            ├── CheckAdmin.php
            ├── FixEkskulPhotoPaths.php
            └── RecoverEkskulPhotos.php
        └── Kernel.php
    ├── Exceptions/
        └── Handler.php
    ├── Http/
        ├── Controllers/
            ├── Admin/
                ├── MasterEkskulController.php
                ├── MasterKelasController.php
                ├── MasterMapelController.php
                ├── MasterRuanganController.php
                ├── MasterSiswaController.php
                ├── MasterTahunAjaranController.php
                └── MonitoringController.php
            ├── Auth/
            ├── AdminController.php
            ├── AuthController.php
            ├── Controller.php
            ├── DashboardController.php
            ├── JurnalController.php
            ├── ProfileController.php
            └── PublicController.php
        ├── Middleware/
            ├── AdminMiddleware.php
            ├── Authenticate.php
            ├── EncryptCookies.php
            ├── GuruApprovedMiddleware.php
            ├── GuruMiddleware.php
            ├── GuruProfileCompleted.php
            ├── PreventRequestsDuringMaintenance.php
            ├── RedirectIfAuthenticated.php
            ├── TrimStrings.php
            ├── TrustHosts.php
            ├── TrustProxies.php
            ├── ValidateSignature.php
            └── VerifyCsrfToken.php
        └── Kernel.php
    ├── Models/
        ├── BiodataGuru.php
        ├── JurnalEkskul.php
        ├── JurnalKBM.php
        ├── JurnalKegiatan.php
        ├── JurnalPiket.php
        ├── MasterEkskul.php
        ├── MasterKelas.php
        ├── MasterMapel.php
        ├── MasterRuangan.php
        ├── MasterTahunAjaran.php
        ├── Siswa.php
        ├── SiswaEkstrakurikuler.php
        └── User.php
    └── Providers/
        ├── AppServiceProvider.php
        ├── AuthServiceProvider.php
        ├── BroadcastServiceProvider.php
        ├── EventServiceProvider.php
        └── RouteServiceProvider.php
├── config/
    ├── app.php
    ├── auth.php
    ├── broadcasting.php
    ├── cache.php
    ├── cors.php
    ├── database.php
    ├── dompdf.php
    ├── filesystems.php
    ├── hashing.php
    ├── logging.php
    ├── mail.php
    ├── queue.php
    ├── services.php
    ├── session.php
    └── view.php
├── database/
    ├── factories/
        └── UserFactory.php
    ├── migrations/
        ├── [39 migration files for tables users, journals, master data, etc.]
    ├── seeders/
        ├── AdminSeeder.php
        ├── DatabaseSeeder.php
        ├── GuruSeeder.php
        ├── MasterDataSeeder.php
        ├── ResetAndReseedSeeder.php
        ├── SiswaEkstrakurikulerSeeder.php
        └── SiswaSeeder.php
    └── database.sqlite
├── public/
    ├── build/
        ├── assets/
            ├── app-B626If4D.js
            └── app-DRU6WNES.css
        └── manifest.json
    ├── css/
        └── custom-ui.css
    ├── images/
        └── Logo_SMA_ABBS.png
    ├── uploads/
        ├── ekskul-photos/
        └── kegiatan-photos/
    ├── .htaccess
    ├── favicon.ico
    ├── index.php
    └── robots.txt
├── resources/
    ├── css/
        └── app.css
    ├── js/
        ├── app.js
        └── bootstrap.js
    ├── sass/
        ├── _variables.scss
        └── app.scss
    └── views/
        ├── admin/
            ├── master/
            ├── monitoring/
            ├── siswa/
            ├── users/
            └── dashboard.blade.php
        ├── auth/
            ├── login.blade.php
            ├── pending.blade.php
            └── register.blade.php
        ├── components/
            └── alerts.blade.php
        ├── ekskul/
        ├── jurnal/
            ├── exports/
            ├── _piket-edit-modal.blade.php
            ├── _piket-scripts.blade.php
            ├── ekskul-show.blade.php
            ├── ekskul.blade.php
            ├── kbm-edit.blade.php
            ├── kbm-show.blade.php
            ├── kbm.blade.php
            ├── kegiatan-edit.blade.php
            ├── kegiatan-show.blade.php
            ├── kegiatan.blade.php
            ├── piket-edit.blade.php
            ├── piket-show.blade.php
            └── piket.blade.php
        ├── layouts/
            ├── app.blade.php
            └── public.blade.php
        ├── profile/
            └── edit.blade.php
        ├── public/
            ├── jurnal/
            └── dashboard.blade.php
        ├── dashboard.blade.php
        ├── profile.blade.php
        └── welcome.blade.php
├── routes/
    ├── api.php
    ├── channels.php
    ├── console.php
    ├── test.php
    └── web.php
├── scripts/
    └── dispatch_request.php
├── .editorconfig
├── .env
├── .env.example
├── .gitattributes
├── .gitignore
├── README.md
├── START_HERE.bat
├── artisan
├── composer.json
├── package.json
├── phpunit.xml
├── setup.bat
├── sma-abbs-surakarta.conf
└── vite.config.js
```

---

## 6. Developer Development Guide & Best Practices

- **Bypassing Windows Symlink Issues**:
  Avoid using PHP standard `storage:link` in Windows environments because it requires elevated admin privileges, which can fail or cause route mismatch errors on local servers (like Laragon/Apache). Use the file server endpoint `/file/{path}` defined in `routes/web.php` for displaying images and assets.
  - *Example*: Instead of `asset('storage/' . $path)`, use `url('/file/' . $path)`.

- **Handling JSON Fields**:
  Models like `JurnalKBM`, `JurnalEkskul`, `JurnalPiket`, and `JurnalKegiatan` cast fields such as `absensi`, `foto_kegiatan`, `peserta`, etc., as array elements. Be careful when updating database values to ensure you pass valid arrays that PHP casts properly into JSON strings.

- **Running Local Server**:
  Use `npm run dev` to compile frontend assets via Vite if modifying SCSS or JS files. To launch the server, run `php artisan serve` or access via local server URL (e.g. `sma-abbs-surakarta.test` in Laragon).
