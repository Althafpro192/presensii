# Sistem Presensi Berbasis RFID (Multi-Tenant)

Sistem Informasi Presensi Siswa multi-tenant yang menggunakan pemindai RFID (ESP32) untuk merekam kehadiran siswa. Sistem ini memiliki peran Super Admin (Platform), Admin Sekolah, Kurikulum, Guru, dan Orang Tua/Siswa.

## Fitur Utama

- **Multi-tenant / Multi-school**: Mendukung banyak sekolah dalam satu aplikasi (Super Admin).
- **RFID Presensi**: Menggunakan perangkat keras ESP32 dan RFID MFRC522 untuk *tap in* dan *tap out*.
- **Real-time Notifikasi**: Menggunakan Laravel Reverb (WebSockets) untuk notifikasi kehadiran langsung ke layar (Toast).
- **Grafik & Laporan**: Visualisasi kehadiran dengan Chart.js dan ekspor data dalam format Excel/PDF.
- **Sistem Pengaturan Hari Libur**: Penentuan libur nasional dan *override* libur sekolah.

## Dokumentasi Perangkat Keras ESP32

Kode untuk perangkat keras (mikrokontroler ESP32) dapat ditemukan di direktori `docs/ESP32/main.ino`.
Perangkat ESP32 berfungsi sebagai pemindai *smart card* RFID dan akan mengirimkan API request ke sistem pusat.

### Endpoint API Presensi RFID

**POST** `/api/rfid/scan`

**Headers:**
- `Content-Type: application/json`
- `X-Device-API-Key: YOUR_API_KEY_HERE`

**Body:**
```json
{
  "card_uid": "A1B2C3D4"
}
```

**Response (Success):**
```json
{
  "status": "success",
  "message": "Check-in berhasil. Budi Santoso - Hadir",
  "student_name": "Budi Santoso",
  "type": "check_in"
}
```

## Instalasi

1. Clone repositori ini.
2. Salin `.env.example` ke `.env` dan sesuaikan koneksi database.
3. Jalankan `composer install` dan `npm install`.
4. Jalankan `php artisan key:generate`.
5. Jalankan `php artisan migrate --seed` untuk membuat database beserta *dummy data* awal.
6. Mulai server dengan `php artisan serve`, WebSocket dengan `php artisan reverb:start`, dan Frontend Vite dengan `npm run dev`.
