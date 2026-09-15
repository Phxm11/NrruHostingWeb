# NRRU Hosting Web

ระบบบริหารจัดการคำขอใช้บริการ **Data Center, Web Hosting และ Virtual Server** ของสำนักคอมพิวเตอร์ มหาวิทยาลัยราชภัฏนครราชสีมา พัฒนาด้วย Laravel และมีหน้าจอภาษาไทยสำหรับผู้ขอใช้บริการและเจ้าหน้าที่

ระบบรองรับการยื่นคำขอ พิจารณาอนุมัติ จัดการทะเบียนบัญชีและโดเมน รวมถึงบันทึกการต่ออายุบริการ การสร้างหรือเปลี่ยนแปลงบริการบน Plesk, VM และ DNS จริงยังดำเนินการโดยเจ้าหน้าที่ ไม่มีการเชื่อมต่อ API เพื่อจัดสรรบริการอัตโนมัติ

## ความสามารถหลัก

### ผู้ขอใช้บริการ

- ดูข้อมูลบริการ ขั้นตอน และข้อกำหนดจากหน้า Home
- ยื่นคำขอผ่านแบบฟอร์มสาธารณะ โดยระบุข้อมูลผู้ขอ ผู้พัฒนาระบบ วัตถุประสงค์ และระยะเวลาโครงการ
- เลือก Web Hosting หรือ Virtual Server พร้อมแพ็กเกจหรือทรัพยากรที่ต้องการ
- ระบุโดเมน แนบเอกสารรายละเอียดระบบและรูปลายเซ็น
- ระบุการรับรองค่าใช้จ่ายหรือขอยกเว้นค่าธรรมเนียม
- รับเลขที่คำขอหลังส่งสำเร็จเพื่อใช้อ้างอิงเมื่อติดต่อเจ้าหน้าที่

### เจ้าหน้าที่

- เข้าสู่ระบบและรีเซ็ตรหัสผ่าน
- ค้นหา ตรวจสอบ แก้ไข และอนุมัติคำขอ
- เปิดเอกสารแนบผ่านหน้าระบบที่ตรวจสอบสิทธิ์
- สร้างและจัดการทะเบียนบัญชีบริการหลังอนุมัติคำขอ
- จัดการโดเมน สถานะบัญชี วันหมดอายุ และประวัติการต่ออายุ
- จัดการบัญชีเจ้าหน้าที่ โดยบัญชีที่เปิดใช้งานทุกบัญชีมีสิทธิ์หลังบ้านเท่ากัน

## เทคโนโลยีและสิ่งที่ต้องเตรียม

| ส่วน | เครื่องมือ |
|---|---|
| Backend | PHP `^8.1`, Laravel 10, Composer 2 |
| ฐานข้อมูล | MySQL / MariaDB พร้อม InnoDB และ `utf8mb4` |
| Frontend | Blade, Bootstrap 5.3.3, JavaScript และ SVG icons |
| ฟอนต์ | Sarabun สำหรับเนื้อหา และ Kanit สำหรับหัวข้อ |
| Asset tooling | Vite 5 และ npm สำหรับงาน build assets |
| ทดสอบ | PHPUnit 10 และ SQLite แบบ `:memory:` |

เปิด PHP extensions ตามที่ dependencies ต้องการ เช่น PDO MySQL, mbstring, OpenSSL, fileinfo และ DOM/XML ส่วนการทดสอบต้องมี `pdo_sqlite` ด้วย หลังติดตั้ง dependencies ใช้ `composer check-platform-reqs` ตรวจความพร้อมของ PHP จริงบนเครื่อง

หน้า Blade ปัจจุบันโหลด Bootstrap และ Google Fonts ผ่าน CDN จึงต้องเข้าถึงเครือข่ายดังกล่าวได้ การเปิดเว็บปัจจุบันไม่จำเป็นต้องรัน Vite หากจะพัฒนา assets ผ่าน Vite ให้ใช้ Node.js ที่ตรงกับ `engines` ของแพ็กเกจในชุด dependencies

## เริ่มต้นใช้งานบนเครื่องพัฒนา

ตัวอย่างต่อไปนี้ใช้ PowerShell บน Windows/XAMPP รันจากโฟลเดอร์โปรเจกต์ และใช้สำหรับ **การติดตั้งใหม่บนฐานข้อมูลเปล่า** หากมีระบบเดิม ให้ใช้ขั้นตอนอัปเกรดใน [คู่มือการดูแลระบบ](docs/OPERATIONS.md) เพื่อรักษาข้อมูลและ `APP_KEY` เดิม

### 1. ติดตั้ง dependencies

ตรวจว่าเรียก `php` และ `composer` จาก terminal ได้ แล้วรัน:

```powershell
cd C:/xampp/htdocs/NrruHostingWeb
composer install
composer check-platform-reqs
```

หาก PHP ไม่อยู่ใน PATH สามารถใช้ `C:/xampp/php/php.exe` แทน `php` ในคำสั่งต่าง ๆ ได้

### 2. สร้างฐานข้อมูลและไฟล์ตั้งค่า

เปิด MySQL/MariaDB แล้วสร้างฐานข้อมูลผ่าน phpMyAdmin หรือ SQL client:

```sql
CREATE DATABASE nrru_hosting
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

สร้าง `.env` เฉพาะเมื่อยังไม่มีไฟล์:

```powershell
if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
}
```

แก้ค่าใน `.env` ให้ตรงกับเครื่องและบัญชีฐานข้อมูลที่เตรียมไว้:

```dotenv
APP_NAME="NRRU Hosting Web"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nrru_hosting
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

`your_database_user` และ `your_database_password` เป็นค่าตัวอย่าง ให้แทนด้วยข้อมูลของเครื่องพัฒนา

### 3. เตรียมตารางและสร้างบัญชีเจ้าหน้าที่

```powershell
php artisan key:generate
php artisan config:clear
php artisan migrate
php artisan db:seed
php artisan staff:create
```

- `db:seed` เพิ่มแพ็กเกจบริการและรหัสหน่วยงานตั้งต้น ไม่สร้างบัญชีพร้อมรหัสผ่านเริ่มต้น
- `staff:create` ถามชื่อ อีเมล และรหัสผ่านอย่างน้อย 12 ตัวอักษร พร้อมให้ยืนยันรหัสผ่าน
- ราคาแพ็กเกจใน seed เป็นข้อมูลตั้งต้นของโปรเจกต์ ควรตรวจสอบกับหน่วยงานก่อนนำไปใช้จริง

### 4. เปิดเว็บ

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

เปิดหน้าแรกที่ `http://127.0.0.1:8000` และเข้าสู่ระบบเจ้าหน้าที่ที่ `http://127.0.0.1:8000/login`

หากต้องการใช้ Apache ของ XAMPP ให้ตั้ง VirtualHost โดยชี้ `DocumentRoot` ไปที่ `public/` เปิด `mod_rewrite` และอนุญาต `.htaccess` ดูตัวอย่างพร้อมขั้นตอนตั้งค่าใน [OPERATIONS.md](docs/OPERATIONS.md#3-ตั้งค่า-xampp--apache)

### ข้อมูลสาธิต — เลือกใช้

บนฐานข้อมูลสำหรับพัฒนาหรือสาธิตที่แยกจากข้อมูลจริง สามารถเพิ่มข้อมูลสมมติได้ด้วย:

```powershell
php artisan db:seed --class=DemoSeeder
```

Seeder นี้รองรับเฉพาะ environment `local` หรือ `testing` ไม่เพิ่มบัญชีเจ้าหน้าที่และไม่มีไฟล์แนบจริง

## หน้าสำคัญของระบบ

| URL | การใช้งาน |
|---|---|
| `/` | หน้า Home และข้อมูลบริการ |
| `/service-requests/create` | แบบฟอร์มขอใช้บริการ |
| `/login` | เข้าสู่ระบบเจ้าหน้าที่ |
| `/forgot-password` | ขอรีเซ็ตรหัสผ่าน |
| `/admin/requests` | รายการคำขอ |
| `/admin/accounts` | ทะเบียนบัญชีบริการ |
| `/admin/domains` | ทะเบียนโดเมน |
| `/admin/users` | จัดการบัญชีเจ้าหน้าที่ |

เส้นทาง `/admin/*` ต้องเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่ที่เปิดใช้งาน ดูขั้นตอนยื่นคำขอ อนุมัติ สร้างบัญชี และต่ออายุใน [คู่มือผู้ใช้งาน](docs/USER_GUIDE.md)

## การตั้งค่าที่เกี่ยวข้อง

### ไฟล์แนบ

| ไฟล์ | ชนิดและขนาดที่รับ |
|---|---|
| รายละเอียดระบบ — จำเป็น | PDF, DOC, DOCX, ZIP ไม่เกิน 10 MB |
| หลักฐานการสแกนโค้ด | PDF, JPG, JPEG, PNG ไม่เกิน 10 MB |
| ลายเซ็นผู้ขอ — จำเป็น | ไฟล์รูปภาพ ไม่เกิน 2 MB |

ไฟล์ที่อัปโหลดใหม่เก็บใน `storage/app/private` และเปิดผ่าน controller ที่ตรวจสิทธิ์เจ้าหน้าที่ ไม่ต้องใช้ `storage:link` เพื่อเปิดเอกสารเหล่านี้ ตั้ง `upload_max_filesize` ให้รองรับไฟล์ 10 MB และ `post_max_size` ให้รองรับผลรวมไฟล์ในคำขอ เช่น `32M`

### อีเมล

`.env.example` กำหนด `MAIL_MAILER=log` ซึ่งบันทึกอีเมลลง log สำหรับการพัฒนา หากต้องการส่งอีเมลรีเซ็ตรหัสผ่านจริง ให้กำหนด SMTP และ `APP_URL` ที่ผู้รับเข้าถึงได้ตาม [คู่มือการตั้งค่าอีเมล](docs/OPERATIONS.md#4-การตั้งค่าอีเมล)

### นำขึ้นเซิร์ฟเวอร์

กำหนด web root เป็น `public/`, ใช้ HTTPS, ตั้ง `APP_ENV=production` และ `APP_DEBUG=false` ให้ process ของเว็บเขียน `storage/` กับ `bootstrap/cache/` ได้ และรักษา `.env` กับ `APP_KEY` ของระบบเดิม ดูการอัปเกรด สำรอง และกู้คืนใน [OPERATIONS.md](docs/OPERATIONS.md)

## โครงสร้างและจุดแก้ไข

```text
app/
├── Console/Commands/       คำสั่งสร้างเจ้าหน้าที่และดูแลข้อมูล
├── Http/Controllers/       หน้าเว็บและกระบวนการทำงาน
├── Http/Requests/          ตรวจสอบข้อมูลแบบฟอร์ม
├── Models/                 โมเดลฐานข้อมูล
└── Support/RequestFiles.php ตรวจตำแหน่งไฟล์เอกสาร
database/
├── migrations/            โครงสร้างและการเปลี่ยนแปลงฐานข้อมูล
└── seeders/                ข้อมูลตั้งต้นและข้อมูลสาธิต
resources/views/
├── home.blade.php          หน้า Home และ Navbar
├── service_requests/      แบบฟอร์มขอใช้บริการ
├── auth/                  หน้าเข้าสู่ระบบและรีเซ็ตรหัสผ่าน
├── admin/                 หน้าสำหรับเจ้าหน้าที่
└── partials/              ส่วนประกอบหน้าจอร่วม
routes/web.php             เส้นทางหน้าเว็บ
storage/app/private/       เอกสารและลายเซ็นของผู้ขอ
tests/                     ชุดทดสอบอัตโนมัติ
docs/                      คู่มือผู้ใช้และผู้ดูแล
scripts/                   สคริปต์ทดสอบ สำรอง และจัดชุดส่งมอบ
```

### พัฒนา UI

- แก้หน้า Home และ Navbar ที่ [home.blade.php](resources/views/home.blade.php)
- แก้หน้าฟอร์มที่ [create.blade.php](resources/views/service_requests/create.blade.php)
- แก้โครงหน้าหลังบ้านที่ [admin/layout.blade.php](resources/views/admin/layout.blade.php)
- หน้าเหล่านี้มี CSS และ JavaScript ภายใน Blade ให้ตรวจไฟล์หน้าที่เกี่ยวข้องก่อนแก้ `resources/css/app.css`
- คงธีมเขียว–ครีม–ทอง ฟอนต์ Sarabun สำหรับเนื้อหา และ Kanit สำหรับหัวข้อตามรูปแบบเดิม
- เมื่อแก้หน้าเว็บ ให้ตรวจทั้งเดสก์ท็อปและมือถือ รวมถึงเมนู คีย์บอร์ด และข้อความแจ้งข้อผิดพลาด

หากพัฒนา assets ผ่าน Vite:

```powershell
npm.cmd ci
npm.cmd run dev
```

สร้างไฟล์สำหรับนำไปใช้งานด้วย `npm.cmd run build` บนระบบที่ไม่ใช้ Windows ให้ใช้ `npm` แทน `npm.cmd` หน้า Blade ที่ต้องการใช้ไฟล์ build ต้องเชื่อม assets ผ่าน `@vite` ด้วย

## ตรวจสอบและทดสอบ

หลังแก้ Blade สามารถตรวจการคอมไพล์และล้าง cache ได้ด้วย:

```powershell
php artisan view:cache
php artisan view:clear
git diff --check
```

รันชุดทดสอบที่มีอยู่ใน checkout:

```powershell
php artisan config:clear
php artisan test
```

`phpunit.xml` กำหนด environment เป็น `testing` และฐานข้อมูล SQLite `:memory:` ชุดทดสอบจะหยุดหากพบ config cache หรือฐานข้อมูลที่ไม่ตรงเงื่อนไขการแยกข้อมูลทดสอบ

หากต้องทดสอบกับ MySQL/MariaDB บนเครื่องทดสอบ:

```powershell
php artisan config:clear
php scripts/test-mysql.php
```

สคริปต์ใช้ข้อมูลเชื่อมต่อ MySQL ที่ตั้งไว้ สร้างฐานข้อมูลชั่วคราวชื่อ `nrru_handover_test_*` และลบฐานข้อมูลที่ตัวเองสร้างเมื่อจบ จึงต้องใช้บัญชีสำหรับเครื่องทดสอบที่มีสิทธิ์สร้างและลบฐานข้อมูล

เมื่อเสนอการเปลี่ยนแปลง ให้สรุปสิ่งที่แก้และผลตรวจที่รันจริง สำหรับการเปลี่ยน UI ให้แนบภาพเดสก์ท็อปและมือถือ ส่วนการเปลี่ยน schema ให้ใช้ migration และทดสอบบนฐานข้อมูลแยก

## ขอบเขตปัจจุบัน

- ยังไม่มี SSO การยืนยันตัวตนผู้ยื่น หรือหน้าติดตามสถานะคำขอด้วยตนเอง
- การอนุมัติเป็นชั้นเดียว เจ้าหน้าที่ที่เปิดใช้งานมีสิทธิ์เท่ากัน
- การเปิด ระงับ แก้โดเมน และต่ออายุ เปลี่ยนข้อมูลทะเบียนในเว็บ ไม่ส่งคำสั่งไป Hosting จริง
- ยังไม่มีระบบรับชำระเงินหรือออกใบเสร็จที่ยืนยันการชำระแล้ว
- ยังไม่มีการแจ้งเตือนหรือเปลี่ยนสถานะบัญชีตามวันหมดอายุอัตโนมัติ

ดูรายละเอียดเพิ่มเติมและแนวทางพัฒนาต่อใน [HANDOVER.md](docs/HANDOVER.md)

## แก้ปัญหาเบื้องต้น

| อาการ | จุดตรวจสอบ |
|---|---|
| หน้าเว็บขึ้น 500 | `storage/logs/laravel.log`, `APP_KEY`, การเชื่อมต่อฐานข้อมูล และสิทธิ์เขียน storage/cache |
| ไม่พบตารางหรือแพ็กเกจ | ค่า `DB_DATABASE`, สถานะ migrations และการ seed ข้อมูลตั้งต้น |
| ฟอนต์หรือรูปแบบหน้าเว็บไม่โหลด | การเข้าถึง Bootstrap CDN และ Google Fonts |
| อัปโหลดไฟล์ไม่ได้ | ขนาด/ชนิดไฟล์, PHP upload limits และสิทธิ์เขียน private storage |
| เข้าหลังบ้านแล้วกลับหน้า login | สถานะบัญชีเจ้าหน้าที่, session และ secure cookie กับ HTTP/HTTPS |
| อีเมลรีเซ็ตรหัสผ่านไม่ถึง | `MAIL_MAILER`, SMTP และ `APP_URL` |
| ทดสอบหยุดเพราะ config cache | รัน `php artisan config:clear` แล้วตรวจค่าฐานข้อมูลทดสอบ |

## เอกสารเพิ่มเติม

- [คู่มือผู้ใช้งาน](docs/USER_GUIDE.md)
- [ติดตั้ง อัปเกรด สำรอง และกู้คืน](docs/OPERATIONS.md)
- [ขอบเขตและบันทึกส่งมอบ](docs/HANDOVER.md)
- [รายงานทดสอบและรายการตรวจรับเดิม](docs/TEST_REPORT.md) — ใช้อ้างอิงตามวันที่ในเอกสาร ไม่ใช่ผลทดสอบล่าสุดของทุกการเปลี่ยนแปลง
