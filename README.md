# NRRU Hosting Web

ระบบบริหารจัดการคำขอและทะเบียนบริการ Hosting / Virtual Server สำหรับส่งต่อมหาวิทยาลัย รุ่นส่งมอบวันที่ 10 กันยายน 2569

ระบบรองรับการยื่นและพิจารณาคำขอ จัดการทะเบียนบัญชีและโดเมน รวมถึงบันทึกการต่ออายุบริการ การดำเนินการบนเครื่อง Hosting จริงยังเป็นงานของเจ้าหน้าที่ ไม่มีการเชื่อม Plesk หรือระบบสร้าง VM ในรุ่นนี้

## เอกสารส่งมอบ

- [คู่มือติดตั้ง อัปเกรด สำรองและกู้คืน](docs/OPERATIONS.md)
- [คู่มือผู้ใช้และเจ้าหน้าที่](docs/USER_GUIDE.md)
- [ขอบเขต ข้อจำกัด และแนวทางพัฒนาต่อ](docs/HANDOVER.md)
- [ผลทดสอบและรายการตรวจรับ](docs/TEST_REPORT.md)
- [โครงสร้างฐานข้อมูลปัจจุบัน](database.md)

## ความสามารถที่มี

- แบบฟอร์มสาธารณะ: ข้อมูลผู้ขอ ผู้พัฒนา ทรัพยากร โดเมน เอกสารและลายเซ็น
- คำขอใหม่เก็บข้อมูลผู้ยื่นแยกจากคำขอก่อนหน้า และแสดงเลขที่คำขอหลังส่งสำเร็จ
- เจ้าหน้าที่ค้นหา ดู แก้ไข อนุมัติ และลบคำขอ
- สร้างบัญชีหลังอนุมัติ พร้อมป้องกันสร้างซ้ำในคำขอเดียวกัน
- แก้ไขบัญชีและโดเมน เปิด/ระงับสถานะทะเบียนบัญชี
- ต่ออายุรายบัญชี พร้อมวันเดิม วันใหม่ ผู้ดำเนินการและหมายเหตุ
- ไฟล์แนบใหม่เก็บนอก public และเปิดผ่านเจ้าหน้าที่ที่ยังใช้งานได้
- จัดการบัญชีเจ้าหน้าที่ เข้าสู่ระบบ ออกจากระบบ และรีเซ็ตรหัสผ่านผ่านอีเมลเมื่อกำหนด SMTP แล้ว

เจ้าหน้าที่ทุกบัญชีที่เปิดใช้งานมีสิทธิ์หลังบ้านเท่ากัน ไม่มีระบบแบ่งบทบาทหรืออนุมัติหลายชั้น การอนุมัติบริการไม่ใช่หลักฐานรับชำระเงินและไม่ออกใบเสร็จอัตโนมัติ

## ติดตั้งใหม่อย่างย่อ

ต้องมี PHP 8.1 ขึ้นไปตาม composer.json, Composer 2 และ MySQL/MariaDB ที่รองรับ InnoDB/utf8mb4 สภาพแวดล้อมที่ตรวจจริงและรายละเอียด extensions อยู่ในรายงานทดสอบ ใช้ Node.js/npm เฉพาะเมื่อต้อง build assets

สร้างฐานข้อมูลเปล่าสำหรับระบบนี้ก่อน แล้วเปิด terminal ในโฟลเดอร์โปรเจกต์:

```powershell
composer install
Copy-Item .env.example .env
```

แก้ `.env` ให้ตรงกับฐานข้อมูลและ URL ของเครื่อง โดยห้ามใช้ฐานข้อมูลที่มีข้อมูลของระบบอื่น จากนั้น:

```powershell
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan staff:create
php artisan serve --host=127.0.0.1 --port=8000
```

`staff:create` ถามชื่อ อีเมล รหัสผ่านและคำยืนยันผ่าน terminal ไม่มีบัญชีหรือรหัสผ่านแอดมินสำเร็จรูป `db:seed` เพิ่มเฉพาะแพ็กเกจและรหัสหน่วยงานที่ยังไม่มี ไม่เขียนทับราคาที่แก้ไว้แล้ว

เปิด `http://127.0.0.1:8000` และเข้าหลังบ้านที่ `/login` การให้บริการผ่าน Apache/Nginx ต้องตั้ง DocumentRoot ไปที่โฟลเดอร์ `public` เท่านั้น ดูตัวอย่าง XAMPP ในคู่มือปฏิบัติการ

หากต้องการข้อมูลสมมติในฐานข้อมูลสาธิตแยก:

```powershell
php artisan db:seed --class=DemoSeeder
```

คำสั่งนี้ใช้ได้เฉพาะ APP_ENV=local/testing สร้างคำขอสมมติ 2 รายการและทะเบียนบัญชี 1 รายการ ไม่สร้างบัญชีเจ้าหน้าที่และไม่สร้างบริการจริงใน Hosting

## อัปเกรดเครื่องเดิม

สำรองฐานข้อมูล ไฟล์แนบ และ .env ก่อน ห้ามรัน `migrate:fresh`, `db:wipe` หรือ `key:generate` บนระบบเดิม ขั้นตอนเต็มอยู่ใน [คู่มือปฏิบัติการ](docs/OPERATIONS.md)

```powershell
php artisan migrate
php artisan files:privatize --dry-run
php artisan files:privatize
php artisan view:clear
```

คำสั่งย้ายไฟล์คัดลอกและตรวจ SHA-256 ก่อนลบสำเนาสาธารณะ ถ้ารายงาน missing references หรือไฟล์ปลายทางไม่ตรง ต้องตรวจต่อก่อนถือว่าย้ายครบ

## ทดสอบและสร้างชุดส่งมอบ

```powershell
php artisan config:clear
php artisan test
```

ชุดทดสอบใช้ SQLite ในหน่วยความจำและห้ามใช้ฐานข้อมูลจริง หากต้องทดสอบกับ MySQL/MariaDB ด้วยบัญชีที่มีสิทธิ์สร้างฐานข้อมูลทดสอบ:

```powershell
php scripts/test-mysql.php
```

สคริปต์สร้างฐานข้อมูลชื่อ `nrru_handover_test_*` ชั่วคราว รันทดสอบ และลบเฉพาะฐานข้อมูลที่สคริปต์สร้างเอง

สร้าง ZIP ด้วย Python 3:

```powershell
python scripts/build-handover.py
```

ได้ `dist/NRRU-Hosting-Handover.zip` และไฟล์ SHA-256 มี manifest ตรวจซอร์ส ไม่รวม .env จริง, vendor, node_modules, ข้อมูลนำเข้า Plesk จริง, ฐานข้อมูล, ไฟล์ผู้ใช้, cache และ log สคริปต์ตรวจ UTF-8 และหยุดเมื่อพบข้อความที่อาจเสียจากการแปลงอักขระ

## โครงสร้างโค้ด

- `routes/web.php`: เส้นทางสาธารณะและหลังบ้าน
- `app/Http/Controllers/ServiceRequestController.php`: บันทึกคำขอและข้อมูลผู้ยื่น
- `app/Http/Controllers/Admin/ServiceAccountController.php`: อนุมัติและทะเบียนบัญชี
- `app/Http/Controllers/Admin/ServiceRenewalController.php`: ต่ออายุและประวัติ
- `app/Http/Controllers/Admin/RequestFileController.php`: ส่งไฟล์ให้เจ้าหน้าที่
- `app/Console/Commands`: สร้างเจ้าหน้าที่ ย้ายไฟล์เก่า และคำสั่งนำเข้าข้อมูล
- `resources/views/admin/layout.blade.php`: สี รูปแบบปุ่ม ตาราง และ layout ร่วม
- `database/migrations`: ลำดับ schema ที่ใช้ติดตั้งจริง
- `tests/Feature`: ทดสอบ HTTP workflow, การต่ออายุ, ไฟล์ และการติดตั้ง

หน้าเว็บปัจจุบันใช้ Bootstrap และ Google Fonts ผ่าน CDN การรีเซ็ตรหัสผ่านต้องตั้งค่าอีเมลก่อนใช้งานจริง
