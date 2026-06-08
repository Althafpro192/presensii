Anda adalah AI coding expert. Buatkan aplikasi web **Sistem Presensi Multi-Sekolah (SaaS)** yang lengkap, siap pakai, dengan spesifikasi di bawah ini. Gunakan **Laravel 13**, **PHP 8.3**, **MySQL**, **Inertia.js** (Vue 3), **TailwindCSS**. Ikuti arsitektur **Clean Architecture**: Controller tipis, UseCase untuk logika bisnis, FormRequest untuk validasi. Sertakan **migration, seeder, model, controller, use case, service, middleware, event, listener, queue job, dan komponen Vue** yang diperlukan. Kode harus dapat dijalankan setelah `php artisan migrate --seed` dan `npm install && npm run build`. Tulis komentar dalam bahasa Inggris.

## TUJUAN APLIKASI
Aplikasi presensi berbasis RFID untuk multi-sekolah (model SaaS). Setiap sekolah memiliki subdomain sendiri (contoh: `sekolaha.domain.com`). Super admin mengelola semua sekolah. Role: Super Admin, Admin Sekolah, Kurikulum, Teacher (yang bisa ditugaskan sebagai Wali Kelas per tahun ajaran), Orang Tua. Fitur utama: presensi RFID dengan ESP32, manajemen libur nasional + override sekolah, kenaikan kelas otomatis, pelanggaran siswa (toggleable), integrasi WhatsApp via n8n (bot untuk orang tua), upload surat izin dengan kompresi, manajemen perangkat RFID per sekolah, bulk write kartu RFID, notifikasi real-time, audit trail, ekspor laporan (Excel/PDF), proteksi brute force, arsip data absensi tahunan.

## SPESIFIKASI LENGKAP

### A. DATABASE (SEMUA TABEL)

Buat migration untuk tabel-tabel berikut. Gunakan foreign key, indeks yang sesuai.

1. `schools`
   - id, name, slug (unique, untuk subdomain), logo (nullable), primary_color (default #4361ee), secondary_color (default #3f37c9), api_key (string 32 unique nullable) – untuk autentikasi ESP32 tingkat sekolah, settings (json) – untuk konfigurasi seperti late_threshold, absent_threshold, dll, subscription_ends_at (timestamp nullable), is_active (boolean default true), timestamps.

2. `school_feature` (pivot untuk fitur toggle per sekolah)
   - id, school_id (foreign), feature_name (string), is_enabled (boolean default false), timestamps.
   - Feature names: 'pelanggaran_siswa', 'n8n_whatsapp', 'dashboard_sakit', 'ekspor_excel'.

3. `users` (extends Authenticatable)
   - Kolom standar Laravel + school_id (foreign, nullable) – null untuk super admin, role (enum: super_admin, admin_sekolah, kurikulum, teacher, orang_tua), phone (string unique nullable) – untuk WhatsApp, classroom_id (foreign nullable) – untuk teacher yang menjadi wali kelas, email_verified_at, password, remember_token, timestamps.

4. `classrooms`
   - id, school_id (foreign), name, grade_level (integer 10,11,12), order (integer default 0), timestamps.

5. `students`
   - id, school_id (foreign), classroom_id (foreign nullable), nis (string unique), name, rfid (string unique nullable) – UID kartu utama, parent_phone (string nullable – alternatif), promotion_status (enum: pending, naik, tidak_naik, lulus default pending), graduated_at (timestamp nullable), softDeletes, timestamps.

6. `parent_student` (pivot)
   - id, parent_id (foreign ke users), student_id (foreign), timestamps.

7. `academic_years`
   - id, school_id (foreign), name (contoh: 2024/2025), start_date, end_date, is_current (boolean default false), timestamps.

8. `classroom_teachers` (penugasan wali kelas per tahun ajaran)
   - id, academic_year_id (foreign), classroom_id (foreign), teacher_id (foreign ke users dengan role teacher), timestamps.

9. `attendances`
   - id, school_id (foreign), student_id (foreign), date, check_in_time (time nullable), check_out_time (time nullable), status (enum: hadir, izin, sakit, alpa, terlambat default hadir), timestamps.

10. `leave_requests`
    - id, school_id (foreign), student_id (foreign), parent_id (foreign ke users nullable), date, type (enum: izin, sakit), reason (text), attachment (string nullable), status (enum: pending, approved, rejected default pending), approved_by (foreign ke users nullable), timestamps.

11. `government_holidays`
    - id, holiday_date (date unique), name, is_national (boolean), year (integer), timestamps.

12. `school_holidays`
    - id, school_id (foreign), event_date, type (enum: libur, masuk), name (string nullable), override_government (boolean default false), created_by (foreign ke users), timestamps.

13. `academic_calendars` (rentang panjang, misal libur semester, periode kenaikan kelas)
    - id, school_id (foreign), name, start_date, end_date, type (enum: libur_semester, uts, uas, psb, kenaikan_kelas), timestamps.

14. `student_violations`
    - id, school_id (foreign), student_id (foreign), recorded_by (foreign ke users), violation_type (string), description (text nullable), violation_date, sanction (enum: peringatan, skors, dikembalikan_ke_ortu nullable), timestamps.

15. `promotion_logs`
    - id, school_id (foreign), student_id (foreign), from_classroom_id (foreign nullable), to_classroom_id (foreign nullable), status (enum: naik, tidak_naik, lulus), processed_by (foreign ke users), timestamps.

16. `n8n_webhooks`
    - id, school_id (foreign), webhook_url (string), secret_token (string nullable), is_active (boolean default false), timestamps.

17. `rfid_devices` (perangkat ESP32 per sekolah)
    - id, school_id (foreign), device_name, api_key (string 64 unique), ip_address (string nullable), status (enum: active, inactive default active), last_activity_at (timestamp nullable), timestamps.

18. `rfid_card_assignments` (kartu RFID milik siswa)
    - id, student_id (foreign), rfid_device_id (foreign nullable), card_uid (string unique), status (enum: active, lost, damaged, inactive default active), assigned_at (timestamp default now), lost_reported_at (timestamp nullable), lost_note (text nullable), timestamps.
    - Catatan: Satu siswa bisa memiliki beberapa kartu (aktif hanya satu). Jika kartu hilang, status diubah menjadi lost, dan wali kelas dapat mengubah kehadiran siswa secara manual sampai kartu baru diterbitkan.

19. `rfid_write_jobs` (antrean penulisan kartu)
    - id, school_id (foreign), rfid_device_id (foreign), student_id (foreign), card_uid_to_write (string – data yang akan ditulis ke kartu, misal NIS), status (enum: pending, processing, success, failed default pending), error_message (text nullable), processed_at (timestamp nullable), timestamps.

20. `audit_logs` (untuk audit trail)
    - id, user_id (foreign), action (string), model_type (string), model_id (integer nullable), old_values (json nullable), new_values (json nullable), ip_address (string), user_agent (string), timestamps.

21. `attendance_archive` (tabel untuk arsip data absensi tahunan) – struktur sama dengan `attendances`, ditambah kolom `archived_at`. Gunakan partisi berdasarkan bulan atau tahun.

### B. ENUM DAN CONSTANTS

- Buat Enum `UserRole` (super_admin, admin_sekolah, kurikulum, teacher, orang_tua).
- Buat Enum `AttendanceStatus` (hadir, izin, sakit, alpa, terlambat).
- Buat konstanta default untuk batas waktu: `LATE_THRESHOLD = '07:00:00'` (bisa diubah per sekolah via settings JSON di tabel schools).
- Buat konstanta `ABSENT_THRESHOLD = '15:00:00'` (jika belum check_in sampai jam itu, dianggap alpa? opsional).

### C. FITUR-FITUR UTAMA

#### C.1. Multi-sekolah & Subdomain
- Middleware `IdentifySchoolFromSubdomain`: ambil subdomain dari host, cari school dengan slug tersebut, simpan `school_id` ke request dan session. Jika subdomain adalah `admin` atau kosong, akses super admin.
- Semua route (kecuali super admin) dilindungi middleware ini.
- Super admin diakses via `admin.domain.com` atau domain utama.

#### C.2. Autentikasi & Role
- Login menggunakan email/password.
- Setelah login, redirect sesuai role: super admin ke dashboard global, lainnya ke dashboard sekolah masing-masing.
- Gunakan Laravel Fortify atau Breeze untuk autentikasi.
- Implementasikan **brute force protection**: batasi 5 percobaan gagal per 15 menit (gunakan built-in rate limiter).

#### C.3. Manajemen Sekolah (Super Admin)
- CRUD sekolah: nama, slug, logo, warna, api_key (generate otomatis), subscription_ends_at, is_active.
- Lihat daftar semua sekolah, statistik (jumlah siswa, guru, dll).
- Backup database: perintah `php artisan backup:run` dan lihat daftar backup. Bisa backup per sekolah atau semua. (Gunakan spatie/laravel-backup).

#### C.4. Fitur Toggle per Sekolah (Admin Sekolah & Super Admin)
- Halaman pengaturan fitur: daftar semua fitur dengan toggle switch. Simpan ke `school_feature`.
- Backend: buat Gate/Policy `feature-enabled` yang mengecek `school_feature.is_enabled`.
- Frontend: menu sidebar hanya muncul jika fitur aktif.

#### C.5. Manajemen Perangkat RFID (Admin Sekolah)
- CRUD perangkat ESP32: nama, api_key (auto-generate), ip_address (optional), status.
- Tampilkan daftar perangkat, status last activity.
- Halaman untuk menguji koneksi (jika ip_address diisi, kirim ping).

#### C.6. Presensi RFID via ESP32 (Menggunakan Domain)
- Endpoint publik: `POST /api/rfid/scan` – dengan header `X-Device-API-Key`.
- Middleware `VerifyDeviceApiKey`: cari `rfid_devices` dengan api_key, ambil school_id, lalu lanjutkan.
- Proses: cari student dengan `rfid = card_uid` dan `school_id` tersebut. Jika tidak ditemukan, tolak. Jika ditemukan, catat attendance berdasarkan waktu (check_in atau check_out). Gunakan `HolidayService` untuk mengecek apakah hari libur; jika libur dan tidak ada override, tolak dengan pesan "Hari libur".
- Respons ke ESP32 dalam format JSON: `{ status: 'success', message: 'Presensi berhasil, Nama Siswa' }`.
- ESP32 akan menampilkan pesan di LCD.

#### C.7. Bulk Write Kartu RFID (Admin Sekolah)
- Halaman: pilih kelas, pilih perangkat ESP32 yang terdaftar.
- Tampilkan daftar siswa di kelas dengan checkbox, kolom status kartu (dari `rfid_card_assignments`).
- Tombol "Tulis Kartu untuk Terpilih" → membuat `rfid_write_jobs` untuk setiap siswa terpilih dengan status pending.
- Queue job `ProcessRfidWriteJob` akan mengambil job, kirim request ke ESP32 (POST ke `http://[ip_esp32]/write_rfid` atau jika ip tidak ada, gagal). Payload: `{ code: NIS_siswa, job_id }`.
- ESP32 (yang sudah dimodifikasi) akan menerima, masuk mode tulis, setelah kartu ditempel, kirim callback ke `POST /api/rfid/write-callback` dengan payload `{ job_id, status, card_uid }`.
- Server update status job dan simpan card_uid ke `rfid_card_assignments` (buat entri baru, set status active, nonaktifkan kartu lama jika ada).
- Admin melihat progress (polling atau via WebSocket).

#### C.8. Hari Libur & Kalender (Kurikulum)
- Command `php artisan holidays:sync` mengambil data dari API `https://api-harilibur.vercel.app/api?year=...` dan simpan ke `government_holidays`. Jadwalkan cron setiap awal bulan.
- Service `HolidayService` dengan method `isHoliday($date, $school_id)` prioritas:
  1. Override masuk: cek `school_holidays` type `masuk` override_government true → return false.
  2. Libur sekolah: cek `school_holidays` type `libur` → return true.
  3. Libur semester: cek `academic_calendars` type `libur_semester` dalam rentang → return true.
  4. Libur nasional: cek `government_holidays` → return true.
  5. Weekend: cek jika Sabtu/Minggu (bisa diatur di settings school) → return true.
  6. Default false.
- Kurikulum dapat mengelola libur via kalender interaktif (Vue + FullCalendar). Klik tanggal, pilih "Tambah Libur" atau "Ubah Menjadi Masuk". Simpan ke `school_holidays`.

#### C.9. Kenaikan Kelas (Kurikulum)
- Halaman daftar siswa dengan status promotion_status (pending/naik/tidak_naik/lulus). Kurikulum ubah status per siswa atau massal.
- Tombol "Terapkan Kenaikan Kelas": 
  - Untuk status `naik`: cari kelas berikutnya (grade_level+1, order terkecil). Pindahkan student.classroom_id ke kelas baru. Catat promotion_logs.
  - Untuk status `lulus`: set classroom_id null, graduated_at now.
  - Untuk status `tidak_naik`: tetap.
- Hanya bisa dijalankan jika tanggal hari ini dalam rentang `academic_calendars` type `kenaikan_kelas` (diatur kurikulum).

#### C.10. Manajemen Wali Kelas (Kurikulum & Super Admin)
- Halaman assign wali kelas: pilih tahun ajaran, pilih kelas, pilih teacher (user role teacher) sebagai wali kelas. Simpan ke `classroom_teachers`.
- Untuk menentukan wali kelas saat ini: cari berdasarkan academic_year is_current true.
- Wali kelas yang login akan melihat kelas yang ditugaskan.

#### C.11. Fitur Pelanggaran Siswa (Toggleable)
- Jika fitur aktif, Admin Sekolah dapat CRUD pelanggaran siswa. Wali Kelas hanya melihat (tidak bisa edit/edit? Boleh lihat).
- Halaman pelanggaran dengan filter kelas, tanggal, jenis.
- Tampilkan grafik di dashboard.

#### C.12. n8n WhatsApp Bot (Fitur Toggle)
- Admin sekolah (atau super admin) mengatur webhook URL dan secret token di `n8n_webhooks`.
- Buat endpoint API untuk n8n (prefix `/api/whatsapp`) dengan verifikasi header `X-Webhook-Secret`.
- **Endpoint:**
  - `POST /validate-parent` – cek nomor orang tua, kembalikan daftar anak.
  - `POST /attendance` – cek presensi anak hari ini.
  - `POST /homeroom` – cari wali kelas anak.
  - `POST /leave-request` – ajukan izin via WhatsApp.
- Setiap request harus menyertakan `phone_number` dan untuk attendance/homeroom juga `student_nis`. Pastikan nomor orang tua memiliki akses ke student_nis tersebut melalui `parent_student`.
- Jika nomor tidak terdaftar, balas: "Nomor tidak terdaftar. Silakan laporkan ke wali kelas untuk mendaftarkan nomor Anda."
- Pengajuan izin akan membuat `leave_requests` status pending. Wali kelas akan melihat di dashboard website.

#### C.13. Surat Izin Online (Website) – Kompresi Gambar
- Form untuk orang tua: pilih anak, tanggal, jenis izin/sakit, alasan, upload file (jpg,jpeg,png,pdf) max 2MB.
- Validasi: jika gambar > 2MB, kompres menggunakan Intervention Image (resize lebar 800px, kualitas 80%, simpan). Jika PDF > 2MB, izinkan tapi tampilkan peringatan.
- Simpan file di `storage/app/public/leave_attachments`.
- Tampilkan thumbnail gambar; untuk PDF tampilkan ikon dan tombol lihat.
- Wali kelas dapat approve/reject izin di halaman khusus (dengan melihat attachment).

#### C.14. Kartu Hilang & Peran Wali Kelas
- Jika siswa kehilangan kartu, wali kelas dapat melaporkan kehilangan (di halaman siswa). Sistem akan mengubah status kartu di `rfid_card_assignments` menjadi `lost`, mencatat lost_reported_at.
- Selama kartu baru belum didaftarkan, wali kelas dapat **mengubah status kehadiran siswa secara manual** (misal dari "alpa" menjadi "hadir" jika siswa benar-benar hadir tapi tidak bisa scan). Ini disediakan di halaman presensi kelas.
- Admin sekolah melihat rekap kartu hilang (per kelas, per periode) untuk evaluasi pembelian kartu baru.
- Setelah kartu baru diterbitkan, admin sekolah atau wali kelas dapat melakukan bulk write (atau tulis satu per satu) untuk siswa tersebut.

#### C.15. Pengaturan Batas Waktu Terlambat & Alpa (Admin Sekolah)
- Di halaman pengaturan sekolah (settings), admin sekolah dapat mengatur:
  - `late_threshold`: jam (misal 07:00:00) – jika check_in setelah jam ini, status = terlambat.
  - `late_tolerance_minutes`: berapa menit setelah late_threshold masih dianggap terlambat, bukan alpa? Opsional.
  - `absent_threshold`: jam (misal 15:00:00) – jika belum check_in sampai jam ini, status alpa.
- Simpan di kolom `settings` JSON di tabel `schools`.

#### C.16. Dashboard Orang Tua dengan Grafik
- Tampilkan grafik batang kehadiran anak per bulan (hadir, izin, sakit, alpa, terlambat). Gunakan Chart.js.
- Tampilkan daftar riwayat absensi dan izin.

#### C.17. Ekspor Laporan (Excel/PDF)
- Untuk Kurikulum dan Admin Sekolah: halaman rekap presensi dengan filter (kelas, bulan, tahun).
- Tombol ekspor Excel (gunakan `maatwebsite/excel`) dan PDF (gunakan `barryvdh/laravel-dompdf`). Format rapi dengan kop surat sekolah (logo, alamat).

#### C.18. Notifikasi Real-time
- Gunakan Laravel Reverb (atau Pusher). Buat event `NewLeaveRequest`, `AttendanceRecorded`, dll.
- Di Vue, gunakan `usePage` dan Echo untuk mendengarkan event. Tampilkan toast notification.
- Contoh: ketika orang tua mengajukan izin, wali kelas mendapat notifikasi.

#### C.19. Audit Trail
- Buat Observer untuk model `Student`, `Attendance`, `LeaveRequest`, `RfidCardAssignment`, `RfidDevice`, `SchoolFeature`. Setiap create/update/delete catat di `audit_logs`.
- Super admin dapat melihat log di halaman khusus (filter by user, model, tanggal).

#### C.20. Arsip Data Absensi
- Buat command `php artisan attendances:archive` yang memindahkan data `attendances` lebih dari 1 tahun ke tabel `attendance_archive` (struktur sama). Jalankan cron setiap bulan.
- Partisi tabel `attendances` secara native MySQL (opsional) namun arsip lebih sederhana.

### D. TAMPILAN VUE (MODERN, GLASSMORPHISM, EFEK MIKRO)
- Gunakan TailwindCSS, composables, dan library `@vueuse/motion` untuk efek hover.
- Komponen sidebar dinamis berdasarkan role dan fitur aktif (gunakan `$page.props.user.role` dan `$page.props.schoolFeatures`).
- Dashboard per role dengan statistik yang relevan (gunakan grafik).
- Layout: responsive, dark/light mode? opsional.
- Implementasikan skeleton loading untuk halaman yang butuh data.

### E. ESP32 – MODIFIKASI KODE (Disediakan dalam dokumentasi)
Buatkan contoh kode ESP32 (Arduino) yang sudah dimodifikasi agar:
- Menggunakan domain server (bukan IP) dan header `X-Device-API-Key`.
- Menerima perintah tulis via endpoint `/write_rfid` (POST) dengan payload JSON.
- Setelah menulis, mengirim callback ke `/api/rfid/write-callback`.
- Menyimpan konfigurasi (SSID, password, server domain, API Key) di Preferences.
- Menyediakan web interface untuk konfigurasi (mode AP).
- Tampilan LCD menampilkan status (siap tulis, berhasil, gagal).

### F. QUEUE & JOB
- Gunakan database queue driver (atau Redis). Buat job `ProcessRfidWriteJob` untuk menulis kartu.
- Job akan melakukan HTTP call ke ESP32, dengan retry 3 kali jika gagal.
- Setiap job selesai, update status.

### G. UNIT TESTING
- Buat unit test untuk `HolidayService`, `PromotionService`, `RfidWriteJob`.
- Buat feature test untuk endpoint API `/api/rfid/scan`, `/api/whatsapp/validate-parent`.

### H. DOKUMENTASI
- Sertakan `README.md` dengan langkah-langkah:
  - Clone repositori
  - `composer install`, `npm install`
  - Salin `.env.example` ke `.env`, atur database, subdomain, dll
  - `php artisan key:generate`, `php artisan migrate --seed`
  - `php artisan queue:work` (untuk job)
  - `php artisan reverb:start` (untuk websocket)
  - `php artisan schedule:work` (untuk cron sync libur & arsip)
- Sertakan dokumentasi API untuk ESP32 (endpoint, header, payload).

## TEKNOLOGI YANG HARUS DIGUNAKAN
- Laravel 13, PHP 8.3
- MySQL 8.0+
- Inertia.js (Vue 3) dengan SSR opsional
- TailwindCSS 3
- Laravel Fortify (atau Breeze) untuk autentikasi
- Laravel Reverb (WebSocket)
- Laravel Queue (database/redis)
- Spatie Laravel Backup
- Maatwebsite Excel
- Barryvdh DomPDF
- Intervention Image
- Guzzle HTTP
- FullCalendar (Vue component)

## KETENTUAN PENULISAN KODE
- Gunakan **Clean Architecture**: Controller hanya menerima request, memanggil FormRequest, lalu UseCase. UseCase berisi logika bisnis. Repository pattern opsional, tapi gunakan Eloquent dengan global scope.
- Semua model yang membutuhkan `school_id` harus memiliki trait `Tenantable` (bisa buat sendiri).
- Gunakan **Laravel Policies** untuk otorisasi (misal update student hanya jika admin sekolah atau wali kelas yang berwenang).
- Nama file dan kelas mengikuti standar PSR-4.
- Kode harus mudah diuji (dependency injection).

## OUTPUT YANG DIHARAPKAN
- Kode lengkap dalam bentuk file-file (dapat di-zip atau ditulis langsung).
- Semua migration, seeder, model, controller, use case, service, middleware, event, listener, job, command.
- Semua komponen Vue (dalam folder `resources/js/Pages` dan komponen reusable).
- File konfigurasi (config, routes, .env.example).
- README.md dengan instruksi instalasi dan konfigurasi.

Sekarang, tuliskan kode selengkap mungkin. Pastikan tidak ada yang terlewat.