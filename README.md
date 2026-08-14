# NRRU Hosting Web

ระบบยื่นคำขอใช้บริการ **Hosting / Virtual Server** ของมหาวิทยาลัย (NRRU) พัฒนาด้วย [Laravel](https://laravel.com) — ผู้ใช้งาน (บุคลากร/นักศึกษา) กรอกแบบฟอร์มขอใช้บริการผ่านหน้าเว็บสาธารณะ ส่วนเจ้าหน้าที่ (แอดมิน) เข้าสู่ระบบเพื่อตรวจสอบคำขอ อนุมัติ และสร้างบัญชีให้ผู้ขอใช้บริการ

---

## สารบัญ

- [ภาพรวมระบบ](#ภาพรวมระบบ)
- [ฟีเจอร์หลัก](#ฟีเจอร์หลัก)
- [เทคโนโลยีที่ใช้](#เทคโนโลยีที่ใช้)
- [โครงสร้างโปรเจกต์](#โครงสร้างโปรเจกต์)
- [โครงสร้างฐานข้อมูล (ย่อ)](#โครงสร้างฐานข้อมูล-ย่อ)
- [เริ่มต้นใช้งาน (Installation)](#เริ่มต้นใช้งาน-installation)
- [การตั้งค่าไฟล์แนบ (Storage)](#การตั้งค่าไฟล์แนบ-storage)
- [เส้นทาง (Routes) หลัก](#เส้นทาง-routes-หลัก)
- [บทบาทผู้ใช้ (Roles & Permissions)](#บทบาทผู้ใช้-roles--permissions)
- [การพัฒนาต่อ](#การพัฒนาต่อ)

---

## ภาพรวมระบบ

ระบบแบ่งการทำงานออกเป็น 2 ฝั่ง:

1. **ฝั่งผู้ขอใช้บริการ (Public)** — ไม่ต้องล็อกอิน กรอกแบบฟอร์มขอใช้บริการออนไลน์ ระบุข้อมูลผู้ขอ, วัตถุประสงค์, ทรัพยากรที่ต้องการ (Virtual Server / Web Hosting), บริการที่ต้องการเปิดใช้งาน (SSH, HTTP/HTTPS, Database ฯลฯ), โดเมนที่ต้องการ, แนบเอกสาร/รูปภาพประกอบ, รับรองค่าใช้จ่าย และเซ็นยอมรับข้อกำหนด
2. **ฝั่งเจ้าหน้าที่ (Admin)** — ต้องล็อกอิน ใช้ตรวจสอบรายการคำขอทั้งหมด ดูรายละเอียดคำขอแต่ละรายการแบบเต็ม (รวมรูปภาพ/เอกสารที่ผู้ใช้แนบมา), อนุมัติคำขอ, สร้าง/แก้ไข/ปิดการใช้งานบัญชีให้ผู้ขอใช้บริการ, และจัดการผู้ใช้งาน/บทบาท/สิทธิ์ของเจ้าหน้าที่เอง

## ฟีเจอร์หลัก

### ฝั่งผู้ขอใช้บริการ
- แบบฟอร์มขอใช้บริการแบบหลายส่วน (ข้อมูลผู้ขอ → ทรัพยากร/บริการ → โดเมน → เอกสารแนบ → ยอมรับข้อกำหนด)
- แนบผู้พัฒนาระบบร่วมได้หลายคน
- เลือกแพ็กเกจทรัพยากรสำเร็จรูป หรือระบุสเปกแบบกำหนดเอง (Custom)
- แนบเอกสารประกอบ/ภาพหน้าจอ และลายเซ็นยืนยัน

### ฝั่งเจ้าหน้าที่ (Admin)
- แดชบอร์ดรายการคำขอ พร้อมค้นหา/กรองสถานะ
- **หน้ารายละเอียดคำขอ** — แสดงข้อมูลที่ผู้ใช้กรอกมาทั้งหมดในหน้าเดียว (ผู้ขอใช้บริการ, ผู้พัฒนาระบบ, ทรัพยากรที่ขอ, บริการที่เปิดใช้, รายละเอียดทางเทคนิค, โดเมน, ประวัติการอนุมัติ, บัญชีที่สร้างให้แล้ว)
- **แกลเลอรีรูปภาพที่ผู้ใช้อัปโหลด** พร้อม lightbox ดูภาพขนาดเต็ม และเอกสารอื่น ๆ ที่ดาวน์โหลดได้
- อนุมัติคำขอ / ลบคำขอ
- สร้างและจัดการบัญชี (Username/Password) ให้ผู้ขอใช้บริการ พร้อมเปิด/ปิดการใช้งานบัญชี
- จัดการผู้ใช้งานเจ้าหน้าที่ บทบาท (Roles) และสิทธิ์ (Permissions) แบบ Role-based access control

## เทคโนโลยีที่ใช้

| ส่วนประกอบ        | เวอร์ชัน / รายละเอียด                     |
|--------------------|--------------------------------------------|
| PHP                | ^8.1                                        |
| Laravel Framework  | ^10.10                                      |
| Laravel Sanctum    | ^3.3 (session/token auth)                   |
| Database           | MySQL (ปรับเป็น DB อื่นได้ผ่าน `.env`)      |
| Frontend           | Blade Templates + Vite                      |
| Testing            | PHPUnit                                     |

## โครงสร้างโปรเจกต์

จุดที่สำคัญสำหรับผู้ที่จะพัฒนาต่อ:

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── HomeController.php                 หน้าแรก
│   │   ├── ServiceRequestController.php        รับ/บันทึกแบบฟอร์มคำขอ (ฝั่งผู้ใช้)
│   │   ├── Auth/AuthController.php              ล็อกอิน/ลืมรหัสผ่าน/รีเซ็ตรหัสผ่าน
│   │   └── Admin/
│   │       ├── ServiceAccountController.php     รายการคำขอ, รายละเอียดคำขอ, อนุมัติ, จัดการบัญชี
│   │       ├── UserController.php               จัดการผู้ใช้งานเจ้าหน้าที่
│   │       ├── RoleController.php                จัดการบทบาท
│   │       └── PermissionController.php          จัดการสิทธิ์
│   ├── Middleware/
│   │   ├── RoleMiddleware.php
│   │   └── PermissionMiddleware.php
│   └── Requests/
│       └── StoreServiceRequestRequest.php        กฎ validate ของแบบฟอร์มคำขอ
├── Models/                                       Eloquent models (ดูหัวข้อฐานข้อมูลด้านล่าง)
resources/views/
├── service_requests/create.blade.php             แบบฟอร์มขอใช้บริการ (public)
├── admin/
│   ├── layout.blade.php                          เลย์เอาต์หลักของฝั่งแอดมิน (ดีไซน์ระบบ/สี/คอมโพเนนต์ร่วม)
│   ├── requests/
│   │   ├── index.blade.php                       รายการคำขอทั้งหมด
│   │   └── show.blade.php                        รายละเอียดคำขอรายตัว (รวมแกลเลอรีรูปภาพ)
│   ├── accounts/                                  สร้าง/แก้ไขบัญชี
│   ├── users/ roles/ permissions/                 จัดการผู้ใช้งาน/บทบาท/สิทธิ์
routes/
└── web.php                                        เส้นทางทั้งหมดของระบบ
database/migrations/
└── 2026_07_06_000001_create_dc_hosting_service_tables.php   ตารางหลักของระบบคำขอบริการ
```

## โครงสร้างฐานข้อมูล (ย่อ)

ตารางหลักที่เกี่ยวกับคำขอใช้บริการ (สร้างใน migration เดียว) และความสัมพันธ์คร่าว ๆ:

```
applicants            ข้อมูลผู้ขอใช้บริการ
service_requests       คำขอ 1 ใบ (เลขที่คำขอ, สถานะ, วันที่ยื่น) — belongsTo applicant
developers              ผู้พัฒนาระบบร่วม (hasMany ของ service_request)
resource_plans          แพ็กเกจทรัพยากรสำเร็จรูป (Virtual Server / Web Hosting)
request_resources       ทรัพยากรที่เลือกในคำขอ (อ้างอิง resource_plan หรือระบุ custom spec)
request_enabled_services บริการที่ต้องการเปิดใช้ (SSH, HTTP/HTTPS, Database, อื่น ๆ)
tech_details            รายละเอียดทางเทคนิค (ภาษา/เฟรมเวิร์ก, ฐานข้อมูล, พอร์ต ฯลฯ)
department_codes        รหัสหน่วยงานสำหรับใช้ตั้งชื่อโดเมน
domains                 โดเมนที่ขอเปิดใช้งาน
attachments             ไฟล์/รูปภาพที่แนบมากับคำขอ
fee_certifications      การรับรองค่าใช้จ่าย/ขอยกเว้นค่าธรรมเนียม
policy_acceptances      การยอมรับข้อกำหนดและลายเซ็น
approvals               ประวัติการอนุมัติ/ปฏิเสธคำขอ
service_accounts        บัญชี (username/password) ที่เจ้าหน้าที่สร้างให้ผู้ขอใช้บริการ
```

ระบบสิทธิ์ผู้ใช้งานเจ้าหน้าที่ (เพิ่มภายหลัง): `roles`, `permissions`, `role_user`, `permission_role`

## เริ่มต้นใช้งาน (Installation)

### สิ่งที่ต้องมีก่อน
- PHP >= 8.1 พร้อม extension ที่ Laravel ต้องการ
- Composer
- Node.js + npm (สำหรับ build asset ด้วย Vite)
- MySQL (หรือฐานข้อมูลอื่นที่รองรับ)

### ขั้นตอน

```bash
# 1) ติดตั้ง dependency ฝั่ง PHP
composer install

# 2) ติดตั้ง dependency ฝั่ง frontend
npm install

# 3) คัดลอกไฟล์ environment แล้วสร้าง APP_KEY
cp .env.example .env
php artisan key:generate

# 4) ตั้งค่าฐานข้อมูลใน .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD ฯลฯ)

# 5) รัน migration เพื่อสร้างตาราง
php artisan migrate

# 6) เชื่อมโฟลเดอร์ storage ให้เข้าถึงไฟล์แนบ/รูปภาพผ่านเว็บได้ (สำคัญมาก ดูหัวข้อถัดไป)
php artisan storage:link

# 7) build asset (หรือใช้ npm run dev ระหว่างพัฒนา)
npm run build

# 8) รันเซิร์ฟเวอร์สำหรับพัฒนา
php artisan serve
```

จากนั้นเข้าใช้งานได้ที่ `http://localhost:8000`

- แบบฟอร์มขอใช้บริการ (public): `http://localhost:8000/service-requests/create`
- เข้าสู่ระบบเจ้าหน้าที่: `http://localhost:8000/login`

> หากยังไม่มีบัญชีเจ้าหน้าที่ ให้สร้างผ่าน `php artisan tinker` หรือเพิ่ม seeder สำหรับสร้างผู้ใช้ตั้งต้นและกำหนดบทบาทให้เรียบร้อยก่อนใช้งานหน้าแอดมิน

## การตั้งค่าไฟล์แนบ (Storage)

ไฟล์แนบและรูปภาพที่ผู้ใช้อัปโหลด (เอกสารประกอบ, ภาพหน้าจอ, ลายเซ็น) จะถูกเก็บไว้ที่ `storage/app/public` และเสิร์ฟผ่าน URL รูปแบบ `/storage/...` ดังนั้น**ต้องรันคำสั่งนี้เสมอหลังติดตั้งโปรเจกต์ใหม่หรือย้ายเซิร์ฟเวอร์**:

```bash
php artisan storage:link
```

หากไม่รันคำสั่งนี้ รูปภาพในหน้า "รายละเอียดคำขอ" ของแอดมินจะไม่แสดงผล (ลิงก์ไฟล์เสีย)

## เส้นทาง (Routes) หลัก

| Method | Path                                          | คำอธิบาย                                   |
|--------|-----------------------------------------------|----------------------------------------------|
| GET    | `/service-requests/create`                    | แบบฟอร์มขอใช้บริการ (public)                 |
| POST   | `/service-requests`                           | บันทึกคำขอใช้บริการ                          |
| GET    | `/login`                                      | หน้าเข้าสู่ระบบเจ้าหน้าที่                    |
| GET    | `/admin/requests`                             | รายการคำขอทั้งหมด                            |
| GET    | `/admin/requests/{serviceRequest}`            | **รายละเอียดคำขอรายตัว** (รวมรูปภาพที่แนบมา) |
| PATCH  | `/admin/requests/{serviceRequest}/approve`    | อนุมัติคำขอ                                  |
| DELETE | `/admin/requests/{serviceRequest}`            | ลบคำขอ                                       |
| GET    | `/admin/requests/{serviceRequest}/accounts/create` | ฟอร์มสร้างบัญชีให้คำขอที่เลือก           |
| GET    | `/admin/accounts`                             | รายการบัญชีทั้งหมด                           |
| GET    | `/admin/users`, `/admin/roles`, `/admin/permissions` | จัดการผู้ใช้งาน/บทบาท/สิทธิ์ของเจ้าหน้าที่ |

ดูรายการเส้นทางทั้งหมดแบบละเอียดได้ที่ `routes/web.php` หรือรันคำสั่ง:

```bash
php artisan route:list
```

## บทบาทผู้ใช้ (Roles & Permissions)

ระบบรองรับการควบคุมสิทธิ์แบบ Role-based ผ่านตาราง `roles`, `permissions` และตารางเชื่อม `role_user`, `permission_role` โดยมี middleware `RoleMiddleware` และ `PermissionMiddleware` ไว้ตรวจสอบสิทธิ์การเข้าถึงเส้นทางของแอดมิน สามารถจัดการได้ที่เมนู "ผู้ใช้งาน / บทบาท / สิทธิ์" ในหน้าแอดมิน

## การพัฒนาต่อ

- ดีไซน์ของหน้าแอดมินทั้งหมดกำหนดไว้ที่ `resources/views/admin/layout.blade.php` (สี, ตัวแปร CSS, คอมโพเนนต์ปุ่ม/ป้ายสถานะ ฯลฯ) — หน้าใหม่ควรอ้างอิงคลาสจากไฟล์นี้เพื่อให้ดีไซน์สอดคล้องกัน
- กฎ validate ของแบบฟอร์มคำขอบริการอยู่ที่ `app/Http/Requests/StoreServiceRequestRequest.php`
- โครงสร้างความสัมพันธ์ระหว่างตารางทั้งหมดดูได้จาก Eloquent relationship ในโฟลเดอร์ `app/Models/`
