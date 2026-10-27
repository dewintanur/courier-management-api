# Courier Management API

REST API untuk mengelola data kurir menggunakan Laravel 11 dan database MySQL.

## Teknologi
- PHP 8.2+
- Laravel 11
- MySQL
- PHPUnit
- SQLite in-memory untuk automated testing

## Fitur
- Menambahkan kurir
- Menampilkan daftar dan detail kurir
- Memperbarui dan menghapus kurir
- Validasi input, termasuk level 1–5
- Pencarian nama berdasarkan beberapa kata
- Filter berdasarkan beberapa level
- Sorting berdasarkan nama atau tanggal pendaftaran
- Pagination

## Instalasi

Clone repository:

```bash
git clone https://github.com/USERNAME/courier-management-api.git
cd courier-management-api
```

Install dependency:

```bash
composer install
```

Salin konfigurasi environment:

```bash
cp .env.example .env
php artisan key:generate
```

Buat database MySQL bernama `courier_management`, kemudian sesuaikan konfigurasi database di `.env`.

Jalankan migration:

```bash
php artisan migrate
```

Jalankan aplikasi:

```bash
php artisan serve
```

API tersedia di `http://127.0.0.1:8000/api/couriers`.

## Dokumentasi API

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/api/couriers` | Daftar kurir |
| GET | `/api/couriers/{id}` | Detail kurir |
| POST | `/api/couriers` | Tambah kurir |
| PUT/PATCH | `/api/couriers/{id}` | Update kurir |
| DELETE | `/api/couriers/{id}` | Hapus kurir |

### Query Parameters

| Parameter | Contoh | Keterangan |
|---|---|---|
| search | `budi agung` | Cari nama berdasarkan beberapa kata |
| level | `2,3` | Filter level kurir |
| sort | `name` atau `created_at` | Urut nama A–Z atau tanggal terbaru |
| per_page | `5` | Jumlah data per halaman |
| page | `2` | Nomor halaman |

Contoh:

```http
GET /api/couriers?search=budi+agung&level=2,3
```

### Contoh Request POST

```json
{
  "name": "Budiono Hadi Agung",
  "phone": "081234567890",
  "email": "budiono@example.com",
  "level": 2,
  "is_active": true
}
```

## Automated Testing

Pengujian menggunakan PHPUnit dan database SQLite in-memory.

Jalankan:

```bash
php artisan test --filter=CourierApiTest
```

Hasil pengujian:

- 12 tests passed
- 38 assertions

Pengujian mencakup penyimpanan data, validasi, update, delete, pencarian, filter, pagination, dan sorting.

## Catatan Implementasi

- Menggunakan Form Request untuk memisahkan validasi dari controller.
- Menggunakan Eloquent ORM dan route model binding.
- Pencarian memecah input menjadi beberapa kata dan mencocokkan seluruh kata pada nama kurir.
- Default sorting berdasarkan nama secara ascending.
- Sorting `created_at` menampilkan kurir terbaru terlebih dahulu.
- Menggunakan database terpisah untuk pengujian agar data development tidak terpengaruh.
