# Booking Badminton - API Documentation

Aplikasi REST API untuk sistem pemesanan lapangan badminton dengan fitur autentikasi JWT, verifikasi pembayaran, dan activity logging.

## 📋 Daftar Isi

- [Tech Stack](#tech-stack)
- [Instalasi](#instalasi)
- [Database](#database)
- [API Endpoints](#api-endpoints)
- [Alur Penggunaan](#alur-penggunaan)
- [Troubleshooting](#troubleshooting)

## 🛠️ Tech Stack

- **Backend**: Laravel 10.x
- **Database**: MySQL
- **Authentication**: JWT (Tymon/JWT-Auth)
- **PHP**: 8.1+
- **Composer**: Latest

## 📦 Instalasi

### 1. Clone Repository
```bash
git clone <repo-url>
cd booking_badminton
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database di `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking_badminton
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan Migrations & Seed
```bash
php artisan migrate:fresh --seed
```

Ini akan:
- Drop dan recreate semua tabel
- Seed 2 user (admin & member) dengan password: `password123`
- Seed 2 courts dan 2 equipment

### 6. Jalankan Server
```bash
php artisan serve
```

Server berjalan di `http://127.0.0.1:8000`

## 📊 Database

### User
- `id` (Primary Key)
- `name` (String)
- `email` (Unique)
- `password` (Hashed)
- `role` (admin/member) - Default: member
- `timestamps`

### Court (Lapangan)
- `id` (Primary Key)
- `name` (String)
- `floor_type` (String: Karpet/Kayu)
- `price_per_hour` (Integer)
- `photo` (String - nullable)
- `timestamps`

### Booking
- `id` (Primary Key)
- `user_id` (FK → users)
- `court_id` (FK → courts)
- `booking_date` (Date)
- `start_time` (Time)
- `end_time` (Time)
- `total_price` (Integer) - Otomatis dihitung
- `status` (Enum: pending/verified/cancelled)
- `timestamps`

### Payment
- `id` (Primary Key)
- `booking_id` (FK → bookings)
- `proof_image` (String)
- `status` (Enum: pending/verified/rejected)
- `timestamps`

### Equipment
- `id` (Primary Key)
- `name` (String)
- `price` (Integer)
- `stock` (Integer)
- `timestamps`

### ActivityLog
- `id` (Primary Key)
- `user_name` (String)
- `activity` (String)
- `description` (Text)
- `timestamps`

## 🔐 API Endpoints

### Authentication (Public)

**Register User**
```
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "role": "member"
}
```

**Login**
```
POST /api/login
Content-Type: application/json

{
  "email": "member@test.com",
  "password": "password123"
}

Response: { "token": "jwt_token_here", "user": {...} }
```

### Courts (Authenticated)

**List Courts**
```
GET /api/courts
Authorization: Bearer <token>
```

**Create Court** (Admin only)
```
POST /api/courts
Authorization: Bearer <token>
Content-Type: application/json

{
  "name": "Lapangan A",
  "floor_type": "Karpet",
  "price_per_hour": 60000,
  "photo": "lapangan.jpg"
}
```

**Update Court** (Admin only)
```
PUT /api/courts/{id}
Authorization: Bearer <token>
Content-Type: application/json

{
  "name": "Lapangan A Updated",
  "price_per_hour": 70000
}
```

**Delete Court** (Admin only)
```
DELETE /api/courts/{id}
Authorization: Bearer <token>
```

### Bookings (Authenticated)

**List Bookings**
```
GET /api/bookings
Authorization: Bearer <token>
```

**Create Booking** (Member)
```
POST /api/bookings
Authorization: Bearer <token>
Content-Type: application/json

{
  "court_id": 1,
  "booking_date": "2026-01-11",
  "start_time": "08:00",
  "end_time": "09:00"
}

Response: 
{
  "message": "Booking berhasil dibuat",
  "total_bayar": 60000,
  "data": {...}
}
```

**Delete Booking** (Admin only)
```
DELETE /api/bookings/{id}
Authorization: Bearer <token>
```

### Payments (Authenticated)

**Upload Bukti Bayar** (Member)
```
POST /api/payments/upload
Authorization: Bearer <token>
Content-Type: multipart/form-data

Form Data:
- booking_id: 1 (Text)
- image: <file.jpg> (File, max 5MB)

Response: 
{
  "message": "Bukti bayar berhasil diunggah",
  "data": { "id": 1, "status": "pending", ... }
}
```

**Verifikasi Pembayaran** (Admin only)
```
PUT /api/payments/{id}/validate
Authorization: Bearer <token>
Content-Type: application/json

{
  "status": "verified"
}

Status bisa: "verified" atau "rejected"
```

### Logs (Authenticated)

**View Activity Logs** (Admin only)
```
GET /api/logs
Authorization: Bearer <token>
```

### General

**Logout**
```
POST /api/logout
Authorization: Bearer <token>
```

**Refresh Token**
```
POST /api/refresh
Authorization: Bearer <token>
```

## 🔄 Alur Penggunaan

### Untuk Member

1. **Register / Login**
   ```
   POST /api/login
   email: member@test.com
   password: password123
   ```
   → Dapatkan `token`

2. **Lihat Lapangan**
   ```
   GET /api/courts
   Header: Authorization: Bearer <token>
   ```

3. **Buat Booking**
   ```
   POST /api/bookings
   Body: { court_id, booking_date, start_time, end_time }
   ```
   → Dapatkan `booking_id` dan `total_price`

4. **Upload Bukti Pembayaran**
   ```
   POST /api/payments/upload
   Body: { booking_id, image }
   ```
   → Dapatkan `payment_id`

5. **Tunggu Verifikasi Admin**
   - Status booking akan berubah dari `pending` → `verified` setelah admin verifikasi

### Untuk Admin

1. **Login sebagai Admin**
   ```
   POST /api/login
   email: admin@test.com
   password: password123
   ```

2. **Kelola Lapangan**
   ```
   POST/PUT/DELETE /api/courts/{id}
   ```

3. **Verifikasi Pembayaran**
   ```
   PUT /api/payments/{id}/validate
   Body: { status: "verified" }
   ```

4. **Lihat Activity Logs**
   ```
   GET /api/logs
   ```

## 🐛 Troubleshooting

### Migration Error: Foreign Key Constraint
**Solusi**: Pastikan urutan migrasi benar - `courts` & `users` harus sebelum `bookings`, `bookings` sebelum `payments`

### 401 Unauthorized
**Solusi**: 
- Pastikan token ada di header `Authorization: Bearer <token>`
- Token sudah expired? Gunakan `POST /api/refresh`

### 422 Validation Error pada Upload Image
**Solusi**:
- File harus image format (jpg/png/gif/bmp/webp)
- Ukuran max 5MB
- Pastikan file valid (tidak corrupted)

### 405 Method Not Allowed
**Solusi**: Cek HTTP method - pastikan menggunakan GET/POST/PUT/DELETE yang benar

## 📝 Catatan

- Default password seeder: `password123`
- JWT token lifetime: 60 menit (configurable di `config/jwt.php`)
- Image storage: `storage/app/public/proofs/`
- Booking hanya bisa dibuat untuk tanggal hari ini atau lebih (after_or_equal:today)
- Anti-bentrok: Sistem otomatis cek jadwal yang overlap

---

**Dibuat dengan ❤️ untuk Booking Badminton System**

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
