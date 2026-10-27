# Courier Management API (Laravel 12)

API master data Kurir/Courier yang dibangun dengan Laravel 12, arsitektur Clean/Layered (Controller -> Service -> Repository), validasi lengkap di FormRequest terpisah, Database Transaction, dan Unit/Feature Tests (Pest).

---

## 📋 Fitur Utama
1. **Model & Migration Kurir**
   - Kolom: `name`, `email` (unique), `phone_number` (unique), `vehicle_type`, `license_plate`, `level` (Level 1 - 5), `is_active`, `timestamps`.
2. **RESTful API CRUD Kurir (`/api/couriers`)**
   - `GET /api/couriers`
     - Dilengkapi **Pagination** (default 15 data per halaman, konfigurabel via `?per_page=`).
     - Hasil urut berdasarkan nama kurir secara default (`name` ASC).
     - Opsi sorting tanggal didaftarkan (`?sort_by=created_at&sort_order=desc`).
     - Fitur multi-kata pencarian nama (`?search=budi+agung` cocok dengan `Budiono Hadi Agung`).
     - Opsi filter berdasarkan level kurir (`?level=2,3`).
   - `GET /api/couriers/{id}`: Detail kurir.
   - `POST /api/couriers`: Tambah data kurir dengan validasi ketat & pesan error kustom.
   - `PUT/PATCH /api/couriers/{id}`: Update kurir dengan validasi unique constraint yang mengabaikan ID kurir yang bersangkutan.
   - `DELETE /api/couriers/{id}`: Hapus kurir dari database.
3. **Penerapan SOP & Best Practices**
   - Lihat detail di [SOP.md](./SOP.md).
   - Selalu menggunakan `try-catch` di Controller.
   - Validasi eksklusif pada `FormRequest` terpisah (`StoreCourierRequest`, `UpdateCourierRequest`, `FilterCourierRequest`) lengkap dengan pesan error kustom bahasa Indonesia.
   - Pola **Repository Pattern** (`CourierRepositoryInterface` & `CourierRepository`) untuk semua query database.
   - **Service Layer** (`CourierService`) untuk seluruh logic aplikasi.
   - **DB Transaction** (`DB::transaction`) untuk mutasi data.

---

## 🚀 Setup & Instalasi

1. **Clone repository:**
   ```bash
   git clone https://github.com/putrapangestu/test-case-2.git
   cd test-case-2
   ```

2. **Install dependensi:**
   ```bash
   composer install
   ```

3. **Setup environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Jalankan Migrasi & Seeder:**
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan server aplikasi:**
   ```bash
   php artisan serve
   ```
   Aplikasi akan berjalan pada `http://127.0.0.1:8000`.

---

## 🧪 Menjalankan Automated Tests

Test dibuat menggunakan Pest PHP dengan cakupan lengkap skenario CRUD, validasi, pencarian multi-kata, filtering level, dan sorting:

```bash
php artisan test --compact
```
Atau:
```bash
vendor/bin/pest
```

---

## 📖 Contoh Penggunaan Endpoint API

### 1. Menampilkan Daftar Kurir (List & Filter)
- **URL**: `GET /api/couriers`
- **Query Parameters (Opsional)**:
  - `search`: Pencarian nama (contoh: `?search=budi+agung`)
  - `level`: Filter level kurir (contoh: `?level=2,3`)
  - `sort_by`: Kolom pengurutan (`name`, `created_at`, `level`, `id`)
  - `sort_order`: Urutan (`asc`, `desc`)
  - `per_page`: Jumlah per halaman (default 15)

Contoh request:
```bash
curl -X GET "http://127.0.0.1:8000/api/couriers?search=budi+agung&level=2,3&sort_by=created_at&sort_order=desc" \
  -H "Accept: application/json"
```

### 2. Menampilkan Detail Kurir
- **URL**: `GET /api/couriers/{id}`

```bash
curl -X GET "http://127.0.0.1:8000/api/couriers/1" \
  -H "Accept: application/json"
```

### 3. Tambah Kurir Baru
- **URL**: `POST /api/couriers`

```bash
curl -X POST "http://127.0.0.1:8000/api/couriers" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Budi Agung Santoso",
    "email": "budi.agung@example.com",
    "phone_number": "081234567890",
    "vehicle_type": "Motorcycle",
    "license_plate": "B 1234 XYZ",
    "level": 3,
    "is_active": true
  }'
```

### 4. Update Kurir
- **URL**: `PUT /api/couriers/{id}`

```bash
curl -X PUT "http://127.0.0.1:8000/api/couriers/1" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Budi Agung Update",
    "level": 4
  }'
```

### 5. Hapus Kurir
- **URL**: `DELETE /api/couriers/{id}`

```bash
curl -X DELETE "http://127.0.0.1:8000/api/couriers/1" \
  -H "Accept: application/json"
```
