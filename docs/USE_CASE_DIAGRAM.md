# Use Case Diagram - Sistem Pendaftaran Anggota FOSJABAR

## Use Case Diagram

```
                                 ┌─────────────────────────────────────────────────┐
                                 │                                                 │
                                 │      SISTEM PENDAFTARAN ANGGOTA FOSJABAR        │
                                 │                                                 │
    ┌──────────┐                 │                                                 │                 ┌──────────┐
    │          │                 │                                                 │                 │          │
    │          │                 │      ( Buat Position Guest )                    │                 │          │
    │          │────────────────────────────────o                                  │                 │          │
    │          │                 │                                                 │                 │          │
    │          │                 │      ( Buat Generasi )                          │                 │          │
    │  ADMIN/  │────────────────────────────────o                                  │                 │          │
    │ PENGURUS │                 │                 │                               │                 │          │
    │          │                 │                 │                               │                 │  GUEST   │
    │          │                 │                 │ <<include>>                   │                 │          │
    │          │                 │                 └────────►( Buka Pendaftaran )  │                 │          │
    │          │                 │                                                 │                 │          │
    │          │                 │                                                 │                 │          │
    │          │                 │                           ( Register )──────────────────────────────│          │
    │          │                 │                                 o               │                 │          │
    │          │                 │                                 │               │                 │          │
    │          │                 │                                 │ <<include>>   │                 │          │
    │          │                 │                                 └───►( Login )──────────────────────│          │
    │          │                 │                                         o       │                 │          │
    │          │                 │                                         │       │                 │          │
    │          │                 │                                         │       │                 │          │
    │          │                 │                          ( Dashboard Tamu )─────────────────────────│          │
    │          │                 │                                         │       │                 │          │
    │          │                 │                                         │       │                 │          │
    │          │                 │                                         │       │                 │          │
    │          │                 │                    ( Ajukan Jadi Anggota )──────────────────────────│          │
    │          │                 │                                 o               │                 │          │
    │          │                 │                                 │               │                 │          │
    │          │                 │                                 │ <<create>>    │                 │          │
    │          │                 │              ( Buat Participant Position Request )                 │          │
    │          │                 │                                 │               │                 │          │
    │          │                 │      ( Lihat Pengajuan )        │               │                 │          │
    │          │────────────────────────────────o                  │               │                 │          │
    │          │                 │              │                  │               │                 │          │
    │          │                 │              │<<include>>       │               │                 │          │
    │          │                 │              └─────►( Review & Approve )        │                 │          │
    │          │────────────────────────────────────────o                          │                 │          │
    │          │                 │                      │                          │                 │          │
    │          │                 │                      │ <<update>>               │                 │          │
    │          │                 │                      └────►( Update Status )    │                 │          │
    │          │                 │                                                 │                 │          │
    │          │                 │                                                 │                 └──────────┘
    │          │                 │                                                 │
    │          │                 │                                                 │                 ┌──────────┐
    │          │                 │                                                 │                 │          │
    │          │                 │           ( Ajukan Jadi Pengurus )──────────────────────────────────│ ANGGOTA │
    │          │                 │                                 o               │                 │          │
    │          │                 │                                 │               │                 │          │
    │          │                 │                                 │ <<create>>    │                 │          │
    │          │                 │      ( Buat Request Pengurus )  │               │                 │          │
    │          │────────────────────────────────o                  │               │                 │          │
    │          │                 │              │                  │               │                 │          │
    │          │                 │              │<<include>>       │               │                 │          │
    │          │                 │              └────►( Approve Pengurus )         │                 │          │
    │          │────────────────────────────────────────o                          │                 │          │
    │          │                 │                                                 │                 │          │
    └──────────┘                 │                                                 │                 └──────────┘
                                 │                                                 │
                                 └─────────────────────────────────────────────────┘
```

---

## Penjelasan Role

### Role (Peran):
1. **Admin/Pengurus** (kiri) - Mengelola sistem, membuka pendaftaran, menyetujui pengajuan
2. **Guest** (kanan atas) - User baru yang mendaftar, belum menjadi anggota
3. **Anggota** (kanan bawah) - User yang sudah disetujui menjadi anggota resmi

---

## Deskripsi Use Case

### Use Case Admin/Pengurus:
1. **Buat Position Guest** - Admin membuat posisi untuk guest (persiapan awal)
2. **Buat Generasi** - Admin membuat generasi baru yang akan dibuka
3. **Buka Pendaftaran** - Otomatis include setelah membuat generasi
4. **Lihat Pengajuan** - Admin melihat semua pengajuan yang masuk
5. **Review & Approve** - Admin mereview dan menyetujui/menolak pengajuan
6. **Approve Pengurus** - Admin menyetujui pengajuan menjadi pengurus

### Use Case Guest:
1. **Register** - User mendaftar akun baru (otomatis status: guest)
2. **Login** - Login masuk ke sistem (include setelah register)
3. **Dashboard Tamu** - Halaman dashboard untuk guest
4. **Ajukan Jadi Anggota** - Guest mengajukan diri untuk menjadi anggota

### Use Case Anggota:
1. **Ajukan Jadi Pengurus** - Anggota mengajukan diri untuk menjadi pengurus

### Use Case Sistem:
1. **Buat Participant Position Request** - Sistem membuat data pengajuan
2. **Update Status** - Sistem update status user setelah di-approve

---

## Alur Proses Sederhana

### Fase 1: Persiapan (Admin)
```
Admin → Buat Position Guest → Buat Generasi → Pendaftaran Terbuka
```

### Fase 2: Pendaftaran (Guest)
```
Guest → Register → Login → Dashboard Tamu → Ajukan Jadi Anggota
                                                      ↓
                                            [Menunggu Approval]
```

### Fase 3: Approval (Admin)
```
Admin → Lihat Pengajuan → Review & Approve → Status: Anggota
```

### Fase 4: Pengurus (Opsional)
```
Anggota → Ajukan Jadi Pengurus → Admin Approve → Status: Pengurus
```

---

## Relasi:
- **<<include>>** - Proses yang pasti terjadi (misal: registrasi pasti set status guest)
- **<<extend>>** - Proses tambahan yang bisa terjadi (misal: buka pendaftaran setelah buat generasi)
- **<<create>>** - Membuat data baru
- **<<update>>** - Update status/data

---

## Alur Sederhana:

```
ADMIN                           GUEST/USER                      SISTEM
  |                                 |                              |
  |--[1] Buat Position Guest------->|                              |
  |                                 |                              |
  |--[2] Buat Generation----------->|                              |
  |                                 |                              |
  |                                 |--[3] Registrasi------------->|
  |                                 |                              |
  |                                 |<--Set Status: Guest----------|
  |                                 |                              |
  |                                 |--[4] Login------------------>|
  |                                 |                              |
  |                                 |<--Dashboard Tamu-------------|
  |                                 |                              |
  |                                 |--[5] Ajukan Anggota--------->|
  |                                 |                              |
  |<--[6] Notif Pengajuan Baru------|                              |
  |                                 |                              |
  |--[7] Review & Approve---------->|                              |
  |                                 |                              |
  |                                 |<--Status: Anggota------------|
  |                                 |                              |
  |                                 |--[8] Ajukan Pengurus-------->|
  |                                 |                              |
  |<--[9] Notif Pengajuan-----------|                              |
  |                                 |                              |
  |--[10] Approve Pengurus--------->|                              |
  |                                 |                              |
  |                                 |<--Status: Pengurus-----------|
```

---

## Status Flow:

```
[Guest] ──(ajukan)──> [Pending Anggota] ──(admin approve)──> [Anggota]
                                                                  │
                                                                  │
                                                           (ajukan pengurus)
                                                                  │
                                                                  ▼
                                              [Pending Pengurus] ──(admin approve)──> [Pengurus]
```