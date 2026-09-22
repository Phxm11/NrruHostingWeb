# NRRU Hosting Web

ระบบรับคำขอและบริหารทะเบียนบริการ **Web Hosting** และ **Virtual Server** สำหรับสำนักคอมพิวเตอร์ มหาวิทยาลัยราชภัฏนครราชสีมา พัฒนาด้วย Laravel 10 และออกแบบหน้าจอภาษาไทยสำหรับผู้ขอใช้บริการและเจ้าหน้าที่

ระบบครอบคลุมตั้งแต่การยื่นคำขอ การตรวจสอบและอนุมัติ ไปจนถึงการบันทึกบัญชีบริการ โดเมน และประวัติการต่ออายุ โดยการสร้างบริการบน Plesk, VM หรือ DNS จริงยังเป็นงานที่เจ้าหน้าที่ดำเนินการแยกจากระบบนี้

## ความสามารถของระบบ

### ผู้ขอใช้บริการ

- ดูรายละเอียดแพ็กเกจ ขั้นตอน และข้อกำหนดจากหน้าแรก
- ค้นหาโดเมนที่เปิดเผยในทะเบียนสาธารณะ
- ยื่นคำขอ Web Hosting หรือ Virtual Server โดยไม่ต้องเข้าสู่ระบบ
- ระบุผู้ขอ ผู้พัฒนา วัตถุประสงค์ ระยะเวลา ทรัพยากร และโดเมนที่ต้องการ
- แนบเอกสารรายละเอียดระบบ หลักฐานประกอบ และรูปลายเซ็น
- รับเลขที่คำขอหลังส่งสำเร็จเพื่อใช้อ้างอิงกับเจ้าหน้าที่

### เจ้าหน้าที่

- ตรวจสอบ ค้นหา แก้ไข อนุมัติ และลบคำขอ
- เปิดไฟล์แนบผ่านเส้นทางที่ตรวจสอบสิทธิ์แล้ว
- สร้างและดูแลทะเบียนบัญชีบริการหลังคำขอได้รับอนุมัติ
- จัดการโดเมน สถานะบัญชี วันหมดอายุ และประวัติการต่ออายุ
- ดูและพิมพ์รายงานสรุป
- สร้าง แก้ไข เปิด หรือระงับบัญชีเจ้าหน้าที่

> บัญชีเจ้าหน้าที่ที่เปิดใช้งานทุกบัญชีเข้าถึงส่วนหลังบ้านได้เท่ากัน ระบบยังไม่มีการแบ่ง role หรือ permission รายเมนู

## เทคโนโลยี

| ส่วน | เทคโนโลยี |
|---|---|
| Backend | PHP `^8.1`, Laravel 10, Composer 2 |
| Database | MySQL หรือ MariaDB, InnoDB, `utf8mb4` |
| Frontend | Blade, Bootstrap 5.3.3, JavaScript, CSS |
| Asset tooling | Vite 5 และ npm |
| Testing | PHPUnit 10, SQLite `:memory:` |

หน้าเว็บปัจจุบันโหลด Bootstrap และ Google Fonts ผ่าน CDN ส่วนไฟล์ CSS และ JavaScript หลักให้บริการจาก `public/` จึงไม่จำเป็นต้องเปิด Vite เพื่อใช้งานระบบทั่วไป

## เริ่มต้นใช้งาน

คำสั่งตัวอย่างต่อไปนี้ใช้ PowerShell บน Windows/XAMPP และเหมาะสำหรับ **การติดตั้งใหม่บนฐานข้อมูลเปล่า** หากกำลังอัปเกรดระบบที่มีข้อมูลอยู่แล้ว ให้ทำตาม [คู่มือการดูแลระบบ](docs/OPERATIONS.md) เพื่อรักษาข้อมูลและ `APP_KEY` เดิม

### 1. เตรียมเครื่อง

ติดตั้งเครื่องมือต่อไปนี้:

- PHP 8.1 ขึ้นไป พร้อม extension ที่ Laravel ต้องใช้ เช่น OpenSSL, PDO MySQL, mbstring, fileinfo และ DOM/XML
- Composer 2
- MySQL หรือ MariaDB
- Node.js และ npm เฉพาะเมื่อต้องพัฒนา assets ผ่าน Vite

ตรวจสอบความพร้อมของ PHP หลังติดตั้ง dependencies ได้ด้วย `composer check-platform-reqs`

### 2. ติดตั้งโปรเจกต์

```powershell
cd C:/xampp/htdocs/NrruHostingWeb
composer install

if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
}

php artisan key:generate
```

หาก `php` ไม่อยู่ใน PATH ให้ใช้ `C:/xampp/php/php.exe` แทนคำว่า `php`

### 3. เตรียมฐานข้อมูล

สร้างฐานข้อมูลผ่าน phpMyAdmin หรือ SQL client:

```sql
CREATE DATABASE dc_hosting_service
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

จากนั้นตรวจค่าใน `.env` ให้ตรงกับเครื่อง:

```dotenv
APP_NAME="NRRU Hosting Web"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dc_hosting_service
DB_USERNAME=root
DB_PASSWORD=
```

ไม่ควร commit ไฟล์ `.env` หรือข้อมูลรับรองของฐานข้อมูลขึ้น repository

### 4. สร้างตารางและบัญชีเจ้าหน้าที่

```powershell
php artisan config:clear
php artisan migrate --seed
php artisan staff:create
```

- Seeder หลักเพิ่มแพ็กเกจบริการและรหัสหน่วยงานตั้งต้น
- `staff:create` ให้กรอกชื่อ อีเมล และรหัสผ่านอย่างน้อย 12 ตัวอักษร
- ระบบไม่มีบัญชีหรือรหัสผ่านเริ่มต้นที่กำหนดตายตัว
- ควรตรวจสอบราคาแพ็กเกจกับหน่วยงานก่อนนำข้อมูล seed ไปใช้จริง

### 5. เปิดระบบ

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

เปิด `http://127.0.0.1:8000` สำหรับหน้าสาธารณะ หรือ `http://127.0.0.1:8000/login` สำหรับเจ้าหน้าที่

หากใช้ Apache ของ XAMPP ให้ตั้ง `DocumentRoot` ไปที่โฟลเดอร์ `public/` เปิด `mod_rewrite` และอนุญาต `.htaccess` ตัวอย่างการตั้งค่าอยู่ใน [OPERATIONS.md](docs/OPERATIONS.md#3-ตั้งค่า-xampp--apache)

## ข้อมูลสาธิต

ใช้คำสั่งนี้เฉพาะฐานข้อมูลสำหรับพัฒนาหรือสาธิตที่แยกจากข้อมูลจริง:

```powershell
php artisan db:seed --class=DemoSeeder
```

`DemoSeeder` ทำงานได้เฉพาะ environment `local` หรือ `testing` ไม่สร้างบัญชีเจ้าหน้าที่ และไม่สร้างไฟล์แนบจริง

## หน้าสำคัญ

| URL | ผู้ใช้ | รายละเอียด |
|---|---|---|
| `/` | ทุกคน | หน้าแรกและข้อมูลบริการ |
| `/domains` | ทุกคน | ค้นหาทะเบียนโดเมนสาธารณะ |
| `/service-requests/create` | ทุกคน | แบบฟอร์มขอใช้บริการ |
| `/login` | เจ้าหน้าที่ | เข้าสู่ระบบ |
| `/admin/requests` | เจ้าหน้าที่ | ตรวจสอบและอนุมัติคำขอ |
| `/admin/accounts` | เจ้าหน้าที่ | จัดการทะเบียนบัญชีบริการและการต่ออายุ |
| `/admin/domains` | เจ้าหน้าที่ | จัดการทะเบียนโดเมน |
| `/admin/reports` | เจ้าหน้าที่ | รายงานสรุปและหน้าพิมพ์ |
| `/admin/users` | เจ้าหน้าที่ | จัดการบัญชีเจ้าหน้าที่ |

เส้นทาง `/admin/*` ต้องเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่ที่มีสถานะเปิดใช้งาน

## ไฟล์แนบ

| รายการ | สถานะ | ชนิดไฟล์ | ขนาดสูงสุด |
|---|---|---|---:|
| เอกสารรายละเอียดระบบ | จำเป็น | PDF, DOC, DOCX, ZIP | 10 MB |
| หลักฐานประกอบ | ไม่จำเป็น | PDF, JPG, JPEG, PNG | 10 MB |
| รูปลายเซ็นผู้ขอ | จำเป็น | ไฟล์รูปภาพ | 2 MB |

ไฟล์ใหม่จัดเก็บใน `storage/app/private` และเปิดผ่าน controller ที่ตรวจสอบสิทธิ์เจ้าหน้าที่ จึงไม่ต้องใช้ `php artisan storage:link` สำหรับไฟล์คำขอเหล่านี้

บนเครื่องให้บริการจริง ควรตั้ง `upload_max_filesize` ไม่น้อยกว่า `10M` และ `post_max_size` ให้รองรับขนาดรวมของคำขอ เช่น `32M`

## พัฒนา Frontend

ติดตั้ง dependencies และเปิด Vite development server:

```powershell
npm.cmd ci
npm.cmd run dev
```

สร้าง production assets ด้วย:

```powershell
npm.cmd run build
```

บน macOS หรือ Linux ให้ใช้ `npm` แทน `npm.cmd` ทั้งนี้หน้า Blade ที่ต้องการใช้ไฟล์จาก Vite ต้องเรียกผ่าน `@vite` ก่อน

## ทดสอบ

ชุดทดสอบใช้ SQLite แบบ in-memory และกำหนด environment เป็น `testing` ใน `phpunit.xml`:

```powershell
php artisan config:clear
php artisan test
```

เครื่องที่รันทดสอบต้องเปิด extension `pdo_sqlite` หากต้องการทดสอบกับ MySQL/MariaDB แยกต่างหาก ให้ใช้:

```powershell
php artisan config:clear
php scripts/test-mysql.php
```

สคริปต์จะสร้างฐานข้อมูลชั่วคราวชื่อ `nrru_handover_test_*` และลบเมื่อทดสอบเสร็จ บัญชีฐานข้อมูลจึงต้องมีสิทธิ์สร้างและลบฐานข้อมูลบนเครื่องทดสอบ

เมื่อตรวจงาน Blade หรือ Markdown เพิ่มเติม สามารถใช้:

```powershell
php artisan view:cache
php artisan view:clear
git diff --check
```

## โครงสร้างโปรเจกต์

```text
app/
├── Console/Commands/        คำสั่งดูแลระบบและนำเข้าข้อมูล
├── Http/Controllers/        กระบวนการทำงานของหน้าเว็บ
├── Http/Requests/           กฎตรวจสอบแบบฟอร์ม
├── Models/                  Eloquent models
└── Support/RequestFiles.php การจัดการตำแหน่งไฟล์คำขอ
database/
├── migrations/              โครงสร้างฐานข้อมูลและการเปลี่ยนแปลง
└── seeders/                 ข้อมูลตั้งต้นและข้อมูลสาธิต
resources/views/             Blade templates
public/css/                  Stylesheet ที่ให้บริการโดยตรง
public/js/                   JavaScript ที่ให้บริการโดยตรง
routes/web.php               เส้นทางเว็บทั้งหมด
storage/app/private/         เอกสารแนบของผู้ขอ
tests/                       Unit และ feature tests
docs/                        คู่มือผู้ใช้ ผู้ดูแล และเอกสารส่งมอบ
scripts/                     สคริปต์ทดสอบ สำรอง และจัดชุดส่งมอบ
```

## การนำขึ้นใช้งานจริง

- ชี้ web root ไปที่ `public/` เท่านั้น
- ใช้ HTTPS และกำหนด `APP_ENV=production`, `APP_DEBUG=false`
- ตั้ง `APP_URL` และการเชื่อมต่อฐานข้อมูลให้ตรงกับระบบจริง
- ให้ process ของเว็บเขียน `storage/` และ `bootstrap/cache/` ได้
- สำรองฐานข้อมูล ไฟล์ private และ `.env` ก่อนอัปเกรด
- รักษา `APP_KEY` เดิมเสมอเมื่ออัปเกรดระบบที่มีข้อมูลอยู่แล้ว
- ตั้งค่า SMTP หากเพิ่มกระบวนการส่งอีเมลในอนาคต; ค่าเริ่มต้น `MAIL_MAILER=log` ไม่ส่งอีเมลออกจริง

รายละเอียดขั้นตอนติดตั้ง อัปเกรด สำรอง และกู้คืนอยู่ใน [คู่มือการดูแลระบบ](docs/OPERATIONS.md)

## ขอบเขตปัจจุบัน

- ไม่มี SSO และไม่มีการยืนยันตัวตนของผู้ยื่นคำขอ
- ไม่มีหน้าสำหรับผู้ยื่นติดตามสถานะคำขอด้วยตนเอง
- กระบวนการอนุมัติมีชั้นเดียว และเจ้าหน้าที่ที่เปิดใช้งานมีสิทธิ์เท่ากัน
- การเปิด ระงับ แก้โดเมน และต่ออายุเป็นการแก้ทะเบียนในระบบเท่านั้น ไม่ได้สั่งงาน Hosting, VM หรือ DNS อัตโนมัติ
- ไม่มีระบบรับชำระเงินหรือออกใบเสร็จยืนยันการชำระ
- ไม่มี job แจ้งเตือนหรือเปลี่ยนสถานะบัญชีตามวันหมดอายุอัตโนมัติ
- การกู้คืนรหัสผ่านผ่านหน้าเว็บถูกปิดไว้ใน route ปัจจุบัน

ดูข้อจำกัดและแนวทางพัฒนาต่อได้ใน [HANDOVER.md](docs/HANDOVER.md)

## แก้ปัญหาเบื้องต้น

| อาการ | จุดที่ควรตรวจสอบ |
|---|---|
| หน้าเว็บแสดง 500 | `storage/logs/laravel.log`, `APP_KEY`, ฐานข้อมูล และสิทธิ์เขียน storage/cache |
| ไม่พบตารางหรือแพ็กเกจ | ค่า `DB_DATABASE`, สถานะ migration และการ seed ข้อมูล |
| รูปแบบหน้าหรือฟอนต์ไม่โหลด | การเข้าถึง Bootstrap CDN และ Google Fonts |
| อัปโหลดไฟล์ไม่ได้ | ชนิด/ขนาดไฟล์, PHP upload limits และสิทธิ์เขียน private storage |
| เข้าหลังบ้านแล้วกลับหน้า login | สถานะบัญชี, session, `APP_URL` และ secure cookie |
| ทดสอบใช้ฐานข้อมูลผิด | ล้าง config cache แล้วตรวจค่าใน `phpunit.xml` |

## เอกสารเพิ่มเติม

- [คู่มือผู้ใช้งาน](docs/USER_GUIDE.md)
- [คู่มือติดตั้ง อัปเกรด สำรอง และกู้คืน](docs/OPERATIONS.md)
- [ขอบเขตและบันทึกส่งมอบ](docs/HANDOVER.md)
- [รายงานทดสอบและรายการตรวจรับเดิม](docs/TEST_REPORT.md) — ใช้อ้างอิงตามวันที่ในเอกสาร ไม่ใช่ผลทดสอบล่าสุดของทุกการเปลี่ยนแปลง
