# UI/UX Design Guidelines — Sistem Presensi RFID Sekolah

> Dokumen ini adalah **single source of truth** untuk desain visual seluruh halaman.
> Setiap kali membangun atau mengubah tampilan, ikuti pedoman ini tanpa pengecualian.

---

## 1. Identitas Visual

**Karakter desain:** Flat, minimalis, modern, institutional — terasa dibangun oleh developer berpengalaman, bukan template AI generik.

**Referensi inspirasi:** Vercel Dashboard, Linear.app, Notion — kepadatan konten yang tepat, whitespace yang terencana, tidak ada elemen dekoratif berlebihan.

---

## 2. Warna

### Palet Utama

| Peran                  | Nama          | Hex       | Tailwind Class  |
|------------------------|---------------|-----------|-----------------|
| Teks & sidebar         | Slate 900     | `#0F172A` | `slate-900`     |
| Aksen / tombol primer  | Indigo 500    | `#6366F1` | `indigo-500`    |
| Hover aksen            | Indigo 600    | `#4F46E5` | `indigo-600`    |
| Background halaman     | Slate 100     | `#F1F5F9` | `slate-100`     |
| Background card        | Putih         | `#FFFFFF` | `white`         |
| Background row hover   | Slate 50      | `#F8FAFC` | `slate-50`      |
| Border semua elemen    | Slate 200     | `#E2E8F0` | `slate-200`     |
| Teks utama             | Slate 900     | `#0F172A` | `slate-900`     |
| Teks sekunder / label  | Slate 500     | `#64748B` | `slate-500`     |
| Placeholder / hint     | Slate 400     | `#94A3B8` | `slate-400`     |

### Warna Status Kehadiran

| Status      | Background  | Teks      | Tailwind BG    | Tailwind Text  |
|-------------|-------------|-----------|----------------|----------------|
| Hadir       | `#DCFCE7`   | `#166534` | `green-100`    | `green-800`    |
| Alfa        | `#FEE2E2`   | `#991B1B` | `red-100`      | `red-800`      |
| Izin        | `#FEF9C3`   | `#854D0E` | `yellow-100`   | `yellow-800`   |
| Sakit       | `#E0E7FF`   | `#3730A3` | `indigo-100`   | `indigo-800`   |
| Terlambat   | `#FFEDD5`   | `#9A3412` | `orange-100`   | `orange-800`   |
| Menunggu    | `#F1F5F9`   | `#475569` | `slate-100`    | `slate-600`    |
| Disetujui   | `#DCFCE7`   | `#166534` | `green-100`    | `green-800`    |
| Ditolak     | `#FEE2E2`   | `#991B1B` | `red-100`      | `red-800`      |
| Terdaftar   | `#DCFCE7`   | `#166534` | `green-100`    | `green-800`    |
| Belum       | `#FEE2E2`   | `#991B1B` | `red-100`      | `red-800`      |

### Aturan Warna

- Warna aksen `indigo-500` **hanya** untuk: tombol primer, active state navigasi, focus ring, link penting.
- Jangan pakai warna di luar daftar ini tanpa alasan yang jelas.
- **Tidak ada gradient.** Tidak ada shadow berat. Semua flat.
- Background halaman selalu `slate-100`, bukan putih murni.
- Sidebar menggunakan background `slate-900` (gelap).

---

## 3. Tipografi

**Font:** `Plus Jakarta Sans` — dari Google Fonts.
**Weight yang digunakan: 400 (Regular) dan 500 (Medium) SAJA.**

### Skala Teks

| Elemen                    | Ukuran | Weight | Warna default |
|---------------------------|--------|--------|---------------|
| Heading halaman (h1)      | 22px   | 500    | `slate-900`   |
| Heading card / section    | 16px   | 500    | `slate-900`   |
| Body / konten             | 14px   | 400    | `slate-900`   |
| Label input / kolom tabel | 12px   | 500    | `slate-700`   |
| Teks sekunder / deskripsi | 13px   | 400    | `slate-500`   |
| Hint / placeholder        | 12px   | 400    | `slate-400`   |
| Angka statistik besar     | 28px   | 500    | `slate-900`   |
| Badge / pill teks         | 12px   | 500    | (sesuai status)|

### Aturan Tipografi

- Selalu gunakan **sentence case**. Tidak ada Title Case, tidak ada ALL CAPS.
- Hanya dua weight: `400` untuk body, `500` untuk heading dan label.
- Jangan pakai bold untuk penekanan di tengah kalimat.
- Line height body: `1.6`. Heading: `1.3`.

---

## 4. Spacing & Ukuran

Semua nilai spacing menggunakan **kelipatan 4px**: `4 · 8 · 12 · 16 · 20 · 24 · 32 · 40 · 48`.

| Kegunaan                        | Nilai   |
|---------------------------------|---------|
| Padding dalam card              | 20–24px |
| Padding konten halaman          | 24–32px |
| Gap antar card                  | 16–24px |
| Gap dalam form (antar field)    | 16px    |
| Gap ikon dengan teks di tombol  | 8px     |
| Padding tombol horizontal       | 16px    |
| Padding tombol vertikal         | 8px     |
| Tinggi navbar                   | 56px    |
| Lebar sidebar desktop           | 240px   |

### Border Radius

| Elemen                   | Radius |
|--------------------------|--------|
| Card                     | 12px   |
| Tombol, input, dropdown  | 8px    |
| Badge / pill status      | 999px  |
| Avatar inisial           | 50%    |
| Banner / alert           | 8px    |

---

## 5. Layout Shell

### Struktur Halaman Desktop

```
┌─────────────────────────────────────────────────┐
│  NAVBAR  56px  bg-white  border-bottom          │
├──────────┬──────────────────────────────────────┤
│          │                                      │
│ SIDEBAR  │  KONTEN UTAMA                        │
│ 240px    │  bg-slate-100   padding 24–32px      │
│ bg-slate │                                      │
│ -900     │                                      │
│          │                                      │
└──────────┴──────────────────────────────────────┘
```

### Sidebar

- Background: `slate-900`
- Pemisah dari konten: perbedaan warna sudah cukup, tidak perlu border.
- Item normal: teks `slate-400`, hover `bg-slate-800 text-white`, radius 8px.
- Item aktif: `bg-indigo-600 text-white`, radius 8px.
- Padding item: `px-3 py-2`.

### Navbar

- Background: `white`, border bawah `slate-200` 1px.
- Kiri: judul halaman aktif (16px medium slate-900).
- Kanan: ikon notifikasi + avatar user (inisial, bg indigo-100 text indigo-700).

### Konten Utama

- Diawali **Page Header**: judul halaman + breadcrumb kiri, tombol aksi kanan.
- Stat cards (jika ada) langsung di bawah Page Header.
- Grid stat: 4 kolom desktop, 2 kolom tablet, 1 kolom mobile.

---

## 6. Komponen Visual

### Card

- Background `white`, border `1px solid slate-200`, radius `12px`, padding `20–24px`.
- Tidak ada shadow. Tidak ada gradient.

### Stat Card

- Label atas: 12px weight 500 `slate-500`
- Angka: 28px weight 500 `slate-900` (atau warna status jika relevan)
- Keterangan bawah opsional: 12px `slate-400`

### Tabel

- Container: card putih dengan border dan radius 12px, `overflow-hidden`.
- Header row: background `slate-50`, border bawah `slate-200`.
- Header teks: 12px weight 500 `slate-500`.
- Body row: hover background `slate-50`, transisi 100ms.
- Pemisah baris: border bawah `slate-100` per baris — tidak ada border vertikal antar kolom.
- Padding sel: horizontal 16px, vertikal 12px.

### Badge / Pill Status

- Border radius `999px`, padding `px-2.5 py-0.5`, font 12px weight 500.
- Warna mengikuti tabel status di bagian 2 — konsisten di semua halaman.

### Tombol

| Varian    | Tampilan                                           | Kapan dipakai              |
|-----------|----------------------------------------------------|----------------------------|
| Primer    | `bg-indigo-500 text-white` hover `indigo-600`      | Aksi utama per halaman     |
| Sekunder  | `border border-slate-200 text-slate-700` hover `bg-slate-50` | Aksi pendukung / batal |
| Danger    | `bg-red-500 text-white` hover `red-600`            | Hapus / tolak              |
| Ghost     | Transparan, hover `bg-slate-100 text-slate-600`    | Ikon atau aksi tersier     |

- Semua tombol: radius 8px, font 14px weight 500, transisi 150ms ease.
- Ikon di kiri teks jika ada, gap 8px.
- Loading state: spinner kecil di kiri, opacity 50%.

### Input & Form

- Border `slate-200`, radius `8px`, padding `px-3 py-2`, font 14px.
- Focus: ring `indigo-500` 2px, border transparan.
- Placeholder: `slate-400`.
- Label di atas: 12px weight 500 `slate-700`, margin bawah 4px.
- Pesan error di bawah: 12px `red-600`, margin atas 4px.
- Gap antar field: 16px.

### Modal

- Overlay: `rgba(0,0,0,0.4)`.
- Card: `white`, radius `12px`, padding `32px`.
- Lebar: `480px` (form) atau `640px` (konten luas).
- Judul: 16px weight 500 `slate-900`.
- Tombol aksi di bawah rata kanan: [Batal sekunder] [Aksi primer/danger].

### Avatar Inisial

- Lingkaran penuh, ukuran `32px` (tabel) atau `56px` (profil).
- Isi: 2 huruf inisial nama, diambil otomatis.
- Warna bervariasi berdasarkan huruf pertama nama:

| Huruf awal | Background     | Teks          |
|------------|----------------|---------------|
| A – D      | `indigo-100`   | `indigo-700`  |
| E – H      | `teal-100`     | `teal-700`    |
| I – L      | `rose-100`     | `rose-700`    |
| M – P      | `amber-100`    | `amber-700`   |
| Q – T      | `violet-100`   | `violet-700`  |
| U – Z      | `cyan-100`     | `cyan-700`    |

### Banner / Alert

- Border kiri 4px sebagai aksen warna, radius 8px, padding 16px.
- Warning: `bg-yellow-50` border `yellow-400` teks `yellow-800`
- Info: `bg-indigo-50` border `indigo-400` teks `indigo-800`
- Danger: `bg-red-50` border `red-400` teks `red-800`
- Success: `bg-green-50` border `green-400` teks `green-800`

---

## 7. Halaman Mobile (Siswa & Orang Tua)

Viewport target: **375px** (WebView).

- Padding kiri-kanan: `16px`
- Tidak ada sidebar — header atas `slate-900` tinggi `56px` dengan teks putih
- Tombol aksi: full width, minimum tinggi `48px`
- Font minimum: `14px` body, `12px` label
- Gap antar section: `16px`
- Minimum tap target: `44x44px`

### Kalender Kehadiran (Mobile)

- Grid 7 kolom (Sen–Min)
- Setiap hari: lingkaran kecil berisi tanggal
- Warna per status: hijau hadir, merah alfa, kuning izin, abu-abu belum/libur
- Navigasi bulan: panah kiri-kanan di atas grid

---

## 8. Microinteraction & Animasi

**Prinsip: sedikit tapi bermakna.**

- Hover tombol: perubahan warna background, `150ms ease`
- Hover baris tabel: background `slate-50`, `100ms ease`
- Modal masuk: fade + slide naik kecil, `200ms ease-out`
- Toast/flash: slide dari kanan atau atas, auto-dismiss `3 detik`
- Loading tombol: spinner kecil + opacity 50%

**Tidak diperbolehkan:** animasi bouncing, pulse berlebihan, efek yang mengalihkan perhatian dari konten.

---

## 9. Larangan Desain

```
❌ Tidak ada gradient (background, teks, border)
❌ Tidak ada box-shadow dekoratif
❌ Tidak ada glassmorphism atau blur effect
❌ Tidak ada warna di luar palet yang sudah ditentukan
❌ Tidak ada font selain Plus Jakarta Sans
❌ Tidak ada font weight selain 400 dan 500
❌ Tidak ada ALL CAPS atau Title Case di konten antarmuka
❌ Tidak ada border radius di bawah 8px (kecuali pill 999px)
❌ Tidak ada elemen dekoratif yang tidak membawa informasi
❌ Sidebar tidak boleh putih atau berwarna terang
❌ Background halaman tidak boleh putih murni
```

---

## 10. Prompt Pembuka untuk AI

Tempelkan teks berikut di awal setiap sesi sebelum meminta halaman atau komponen baru:

```
Ikuti design system dari UI_UX.md ini secara ketat:

Font: Plus Jakarta Sans, weight 400 (body) dan 500 (heading/label) saja.
Warna aksen: indigo-500 (#6366F1), hover indigo-600 (#4F46E5).
Background halaman: slate-100 (#F1F5F9).
Background card: white, border slate-200 (1px), radius 12px, tanpa shadow.
Sidebar: bg-slate-900. Item aktif: bg-indigo-600 text-white radius-lg.
Teks utama: slate-900. Teks sekunder: slate-500. Placeholder: slate-400.

Status badge selalu pill (radius 999px):
- hadir → green-100 / green-800
- alfa → red-100 / red-800
- izin → yellow-100 / yellow-800
- sakit → indigo-100 / indigo-800
- terlambat → orange-100 / orange-800
- menunggu → slate-100 / slate-600

Tombol primer: bg-indigo-500 text-white radius-lg.
Tombol sekunder: border border-slate-200 text-slate-700.
Input: border-slate-200 radius-lg, focus ring indigo-500.
Avatar: lingkaran inisial 2 huruf, warna bervariasi per huruf awal nama.
Tabel: header bg-slate-50, row hover bg-slate-50, border bawah slate-100 per baris.
Semua teks sentence case. Tidak ada gradient. Tidak ada shadow dekoratif.
Transisi hover: 150ms ease, perubahan warna background saja.
```

---

*Versi: 1.0 — Perbarui dokumen ini sebelum mengubah design system.*
