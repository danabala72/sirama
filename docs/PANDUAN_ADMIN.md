# Panduan Admin - Manajemen Data Akademik

## Ringkasan
Panduan ini untuk Admin Jurusan / Admin dalam mengelola data: **Jurusan → Skema → Mata Kuliah → Mahasiswa**.

---

## 1. Membuat Jurusan

### Langkah:
1. Buka menu **Jurusan**
2. Klik **Tambah Jurusan**
3. Isi form:
   - **Kode Jurusan** (contoh: `AKM`, `TI`, `SI`)
   - **Nama Jurusan** (contoh: `Akuntansi Manajerial`, `Teknik Informatika`)
   - **Ketua Jurusan** (opsional)
4. Klik **Simpan**

### Catatan:
- Kode jurusan harus unik, tidak boleh duplikat
- Jurusan adalah dasar untuk semua data lain (skema, MK, mahasiswa)

---

## 2. Membuat Skema

### Kapan Membuat Skema?
- Saat jurusan memiliki **beberapa jalur/kelompok mata kuliah**
- Contoh: Skema A (5 MK), Skema B (7 MK) untuk 1 jurusan
- Jika jurusan hanya memiliki 1 set MK, bisa **lewati tahap ini**

### Langkah:
1. Buka menu **Jurusan**
2. Klik **Edit** pada jurusan yang ingin ditambahkan skema
3. Klik tombol **+ Tambah Skema** (biru)
4. Isi form:
   - **Nama Skema** (contoh: `Skema A`, `Skema B`)
   - **Deskripsi** (opsional, untuk catatan admin)
5. Klik **Simpan**

### Catatan:
- Skema **harus dibuat sebelum** mata kuliah di-assign ke skema
- Setiap skema milik **1 jurusan**
- Skema bisa diedit atau dihapus kapan saja
- Jika skema dihapus, MK yang sudah di-assign ke skema tersebut menjadi **tanpa skema**

---

## 3. Menambahkan Mata Kuliah

### Kapan Menggunakan Skema?
- Saat membuat MK, pilih **Skema** dari dropdown
- Jika MK **tidak masuk skema manapun**, pilih **"-- Tanpa Skema --"**

### Langkah:
1. Buka menu **Jurusan**
2. Klik **Edit** pada jurusan yang ingin ditambahkan MK
3. Klik tombol **+ Tambah MK** (hijau)
4. Isi form:
   - **Kode Mata Kuliah** (contoh: `AKB101`, `TI201`)
   - **Nama Mata Kuliah** (contoh: `Akuntansi Biaya`, `Algoritma Pemrograman`)
   - **Skema** (pilih dari dropdown, atau "-- Tanpa Skema --")
   - **Tawarkan pada Semester** (pilih semester dimana MK ditawarkan)
   - **SKS** (contoh: 2, 3, 4)
   - **Nilai Minimum** (default: 60)
5. Klik **Simpan**

### Catatan:
- MK dengan **status aktif** akan muncul di Form 3 mahasiswa
- MK **tanpa skema** akan dilihat oleh **semua mahasiswa** jurusan tersebut
- MK dengan skema hanya dilihat oleh mahasiswa yang di-assign skema tersebut
- Satu MK bisa ditawarkan di **beberapa semester** melalui relasi many-to-many

---

## 4. Membuat Mahasiswa

### Kapan Memilih Skema untuk Mahasiswa?
- Saat **membuat mahasiswa baru**
- Saat **mengedit data mahasiswa**

### Langkah:
1. Buka menu **Mahasiswa**
2. Klik **Tambah Mahasiswa**
3. Isi form:
   - **Username / NIM**
   - **Password**
   - **Konfirmasi Password**
   - **Skema** (pilih dari dropdown, atau "-- Tanpa Skema --")
4. Klik **Buat User**

### Catatan:
- Mahasiswa dengan **skema tertentu** hanya melihat MK dari skema tersebut + MK tanpa skema
- Mahasiswa **tanpa skema** melihat **semua MK** jurusannya
- Skema mahasiswa bisa **diubah kapan saja** melalui Edit Mahasiswa

---

## 5. Alur Kerja yang Disarankan

```
1. Admin membuat Jurusan
   ↓
2. Admin membuat Skema (jika diperlukan)
   ↓
3. Admin menambahkan Mata Kuliah dan assign ke Skema
   ↓
4. Admin membuat Mahasiswa dan pilih Skema untuk mahasiswa
   ↓
5. Mahasiswa login dan mengisi Form 3
```

---

## 6. Skema Visual

```
Jurusan: Akuntansi Manajerial (AKM)
├── Skema A
│   ├── MK: Akuntansi Biaya (AKB101) - Semester 1
│   └── MK: Akuntansi Manajemen (AKB102) - Semester 1
├── Skema B
│   ├── MK: Akuntansi Keuangan (AKB201) - Semester 2
│   └── MK: Akuntansi Syariah (AKB202) - Semester 2
└── Tanpa Skema
    └── MK: Kewirausahaan (MPB76404) - Semester 2
```

---

## 7. Pertanyaan Umum

**Q: Apakah mahasiswa bisa melihat MK dari skema lain?**
A: Tidak. Mahasiswa hanya melihat MK dari skema yang di-assign + MK tanpa skema.

**Q: Jika saya hapus skema, apa yang terjadi pada MK?**
A: MK yang sebelumnya di-assign ke skema tersebut menjadi **tanpa skema** dan bisa dilihat oleh semua mahasiswa.

**Q: Bisakah satu MK masuk ke beberapa skema?**
A: Tidak. Satu MK hanya bisa masuk ke **1 skema** atau **tanpa skema**.

**Q: Mahasiswa tanpa skema bisa melihat MK apa saja?**
A: Semua MK yang aktif di jurusan mahasiswa, tanpa filter skema.

**Q: Bagaimana jika jurusan tidak memiliki skema?**
A: Semua MK jurusan tersebut dianggap **tanpa skema** dan bisa dilihat oleh semua mahasiswa jurusan tersebut.

---

## 8. Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Mahasiswa tidak melihat MK tertentu | Cek apakah MK aktif, sesuai jurusan, dan sesuai skema mahasiswa |
| MK tidak muncul di Form 3 | Pastikan MK memiliki status aktif dan terhubung ke semester yang dipilih |
| Skema tidak muncul di dropdown | Pastikan skema sudah dibuat untuk jurusan tersebut |
| Error saat simpan skema | Pastikan nama skema tidak kosong dan jurusan valid |

---

## 9. Kontak
Jika ada pertanyaan atau masalah, hubungi tim pengembang.
