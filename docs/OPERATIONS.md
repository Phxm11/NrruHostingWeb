# คู่มือติดตั้ง อัปเกรด สำรองและกู้คืน

สำหรับผู้ดูแลที่รับช่วงระบบ รุ่นส่งมอบ 10 กันยายน 2569

## 1. เตรียมเครื่อง

- PHP ตาม composer.json พร้อม PDO MySQL, mbstring, OpenSSL, fileinfo, DOM/XML, tokenizer, ctype และ extensions ที่ Composer ตรวจว่าจำเป็น
- Composer 2 และ MySQL/MariaDB พร้อม InnoDB และ utf8mb4
- PHP pdo_sqlite สำหรับรัน tests; PHP zip และ mysqldump สำหรับสคริปต์สำรองข้อมูล
- Web server ต้องเข้าถึง `storage` และ `bootstrap/cache` เพื่อเขียนไฟล์ได้ โดยกำหนดสิทธิ์ให้บัญชีที่รันเว็บตามระบบปฏิบัติการ
- ตรวจ `upload_max_filesize` ให้ไม่น้อยกว่า 10M และ `post_max_size` ให้มากกว่าผลรวมไฟล์และแบบฟอร์ม เช่น 32M ตามนโยบายเครื่อง
- ระบบใช้งาน Bootstrap และ Google Fonts ผ่าน CDN ถ้าใช้ในเครือข่ายปิดต้องจัดเก็บ assets เหล่านี้ในเครื่องเพิ่มเติม

ใช้ `composer check-platform-reqs` ตรวจ extensions จากชุด dependencies ที่ติดตั้งจริง การให้บริการ Virtual Server ในแบบฟอร์มเป็นข้อมูลคำขอ ไม่ได้สร้าง VM บนเครื่องนี้

## 2. ติดตั้งใหม่

1. แตกชุดส่งมอบลงโฟลเดอร์โปรเจกต์
2. สร้างฐานข้อมูลเปล่า เช่น `nrru_hosting` และบัญชีฐานข้อมูลสำหรับระบบนี้
3. รัน `composer install` และคัดลอก `.env.example` เป็น `.env` เฉพาะการติดตั้งใหม่
4. กำหนด DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD และ APP_URL ให้ตรงเครื่อง
5. รันคำสั่งต่อไปนี้จากโฟลเดอร์โปรเจกต์

```powershell
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan staff:create
php artisan serve --host=127.0.0.1 --port=8000
```

สร้างบัญชีเจ้าหน้าที่ด้วยอีเมลที่ใช้งานได้และรหัสผ่านของผู้รับระบบเอง รหัสผ่านที่กรอกผ่าน terminal ไม่ถูกแสดงกลับ ไม่มีค่าแอดมินเริ่มต้น `AdminUserSeeder` แจ้งให้ใช้คำสั่งนี้ ส่วน RolePermissionSeeder และ GrantAdminSeeder เป็นคำสั่งเก่าที่ปิดการทำงานแล้ว

แพ็กเกจและราคาใน DcHostingSeeder เป็นข้อมูลตั้งต้นจากโปรเจกต์ ต้องให้หน่วยงานรับรองก่อนนำไปใช้เป็นอัตราจริง การ seed ซ้ำไม่เขียนทับราคาหรือชื่อหน่วยงานที่แก้ไว้แล้ว

สำหรับสาธิตบนฐานข้อมูลแยก ใช้ `php artisan db:seed --class=DemoSeeder` เมื่อ APP_ENV=local เท่านั้น เป็นข้อมูลสมมติ ไม่มีไฟล์แนบจริงและไม่มีบัญชีเจ้าหน้าที่เพิ่ม

## 3. ตั้งค่า XAMPP / Apache

ตัวอย่าง VirtualHost สำหรับเครื่องพัฒนา:

```apache
<VirtualHost *:80>
    ServerName nrru-hosting.test
    DocumentRoot "C:/xampp/htdocs/NrruHostingWeb/public"
    <Directory "C:/xampp/htdocs/NrruHostingWeb/public">
        AllowOverride All
        Require all granted
        Options -Indexes
    </Directory>
</VirtualHost>
```

เพิ่ม `127.0.0.1 nrru-hosting.test` ใน hosts ของเครื่องพัฒนา เปิด mod_rewrite และ restart Apache ตามขั้นตอนของผู้ดูแล กำหนด APP_URL=http://nrru-hosting.test แล้วรัน `php artisan config:clear`

บนเซิร์ฟเวอร์จริงให้ชี้ DocumentRoot ที่ `public` เช่นเดียวกัน ใช้ HTTPS พร้อม APP_ENV=production, APP_DEBUG=false และ SESSION_SECURE_COOKIE=true โดยค่า secure cookie ใช้กับ HTTPS เท่านั้น ห้ามเปิดโฟลเดอร์โปรเจกต์ทั้งหมดเป็น web root เพราะมี .env, backup และซอร์สภายใน

ไฟล์ผู้ขอใหม่อยู่ที่ `storage/app/private` จึงไม่ต้องสร้าง storage symlink เพื่อแสดงเอกสาร เปิดผ่าน `/admin/requests/{id}/files/{file}` ซึ่งตรวจล็อกอินและสถานะเจ้าหน้าที่

## 4. การตั้งค่าอีเมล

`.env.example` ใช้ MAIL_MAILER=log สำหรับเครื่องพัฒนา ไม่ส่งอีเมลจริง หากใช้ลืมรหัสผ่านจริง ให้ตั้ง MAIL_MAILER=smtp, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_ENCRYPTION, MAIL_FROM_ADDRESS ตามระบบอีเมลของมหาวิทยาลัย แล้วทดสอบด้วยบัญชีทดสอบ

APP_URL ต้องเป็น URL ที่ผู้รับอีเมลเข้าถึงได้ ไม่ใช่ localhost การทดสอบ SMTP และการส่งจริงเป็นงานตรวจรับบนเครื่องปลายทาง

## 5. อัปเกรดระบบเดิม

ทำในช่วงที่ไม่มีผู้ยื่นคำขอหรือเจ้าหน้าที่แก้ข้อมูล และเก็บซอร์สรุ่นเดิมไว้คู่กับ backup

```powershell
php artisan down
php scripts/backup.php C:/xampp/mysql/bin/mysqldump.exe
```

สำหรับ Linux ที่มี mysqldump อยู่ใน PATH ใช้ `php scripts/backup.php` ได้ สคริปต์อ่านข้อมูลเชื่อมต่อจาก Laravel ไม่รับรหัสผ่านผ่าน command line บันทึก SQL, ZIP ของไฟล์แนบและ .env พร้อม SHA-256 ใน `storage/app/handover-backups/<เวลาสำรอง>` ต้องเก็บโฟลเดอร์นี้ให้เข้าถึงได้เฉพาะผู้ดูแลและคัดลอกไปที่สำรองของหน่วยงาน

เมื่อตรวจว่าสำรองสำเร็จแล้ว ลงซอร์สรุ่นใหม่โดยรักษา .env และ storage ของเครื่องเดิมไว้ จากนั้น:

```powershell
composer install --no-dev --optimize-autoloader
php artisan config:clear
php artisan migrate --force
php artisan files:privatize --dry-run
php artisan files:privatize
php artisan view:clear
php artisan route:clear
php artisan config:cache
php artisan view:cache
php artisan up
```

`files:privatize` ย้ายเฉพาะไฟล์ใต้ attachments/ และ signatures/ จาก public storage ไป private ตรวจเนื้อหาด้วย SHA-256 ก่อนลบสำเนาสาธารณะ รันซ้ำได้ ถ้าไฟล์ปลายทางต่างจากต้นฉบับจะหยุดโดยเก็บทั้งสองไฟล์ไว้ ถ้ามี missing references หมายถึงฐานข้อมูลอ้างไฟล์ที่ไม่พบ ต้องกู้ไฟล์จาก backup หรือตรวจประวัติ ก่อนลงนามว่าข้อมูลครบ

ตรวจ exit code ทุกคำสั่ง หาก migration หรือการย้ายไฟล์ล้มเหลว ให้ตรวจเหตุและกู้คืนตามแผนก่อนเปิดระบบอีกครั้ง ไม่ควรรันคำสั่งถัดไปโดยอัตโนมัติเมื่อคำสั่งก่อนหน้าล้มเหลว

ห้ามใช้ `migrate:fresh`, `db:wipe`, `legacy:import-plesk --fresh` หรือสร้าง APP_KEY ใหม่กับข้อมูลจริง การอัปเกรดนี้ไม่ลบตารางบทบาทเก่าหรือข้อมูลบุคลากรเดิม และไม่แก้เลขใบเสร็จที่ถูกสร้างโดยโค้ดเก่า

## 6. สำรองและกู้คืน

ให้หน่วยงานกำหนดความถี่และระยะเวลาเก็บ backup ตามความสำคัญของข้อมูล ต้องสำรองฐานข้อมูล ไฟล์แนบ .env/APP_KEY และซอร์สเวอร์ชันเดียวกัน ในช่วงหยุดการเขียนข้อมูลเพื่อให้เป็นชุดที่สอดคล้องกัน

ซ้อมกู้คืนลงฐานข้อมูลใหม่แยกจากระบบใช้งาน ตัวอย่างคำสั่ง MySQL client บน Windows:

```powershell
C:/xampp/mysql/bin/mysql.exe --user=restore_user --password --database=nrru_restore --execute="source C:/backups/database.sql"
```

ก่อนรัน ต้องสร้าง `nrru_restore` และตรวจว่าชื่อฐานข้อมูลนี้เป็นฐานข้อมูลทดสอบกู้คืนจริง รหัสผ่านจะถูกถามโดย client ไม่เขียนลงคำสั่ง

แตก files.zip ลงโฟลเดอร์โปรเจกต์กู้คืน ให้ `.env` และ `storage/app/...` อยู่ตำแหน่งเดิม รักษา APP_KEY ของ backup ไว้ แต่แก้ DB_DATABASE และ APP_URL ให้ชี้เครื่องกู้คืน ใช้ซอร์สเวอร์ชันที่ตรงกับ backup แล้วล้าง config/view cache

ตรวจจำนวนคำขอ บัญชีและประวัติเทียบข้อมูลอ้างอิง เปิดไฟล์แนบจากบัญชีเจ้าหน้าที่ และตรวจว่าผู้ไม่ได้ล็อกอินเปิดไฟล์ไม่ได้ การกู้คืนหลังย้ายไฟล์ต้องใช้ SQL และไฟล์จาก backup ชุดเดียวกัน เพราะตำแหน่งไฟล์อาจต่างกัน

การตรวจ ZIP และ checksum ยืนยันความสมบูรณ์ของไฟล์ แต่ไม่แทนการซ้อมกู้คืนฐานข้อมูลและใช้งานระบบ

## 7. ตรวจระบบหลังติดตั้ง

```powershell
php artisan migrate:status
php artisan route:list
php artisan view:cache
```

บนเครื่องทดสอบที่ติดตั้ง dependencies ฝั่งพัฒนาแล้ว:

```powershell
php artisan config:clear
php artisan test
php scripts/test-mysql.php
```

tests ปกติใช้ SQLite :memory: ส่วนสคริปต์ MySQL สร้างและลบเฉพาะฐานข้อมูลชั่วคราว nrru_handover_test_* ต้องมีสิทธิ์ CREATE/DROP DATABASE จึงใช้ได้ ไม่ต้องเพิ่มสิทธิ์ดังกล่าวให้บัญชีเว็บบน production

ถ้าต้อง build JavaScript/CSS ของ Vite ใช้ `npm ci` และ `npm run build` หน้า Blade หลักของรุ่นนี้โหลด Bootstrap/Fonts ผ่าน CDN จึงต้องตรวจเครือข่ายปลายทางด้วย

## 8. จุดแก้ปัญหาที่พบบ่อย

| อาการ | ตรวจอะไร |
|---|---|
| หน้าเว็บ 500 | storage/logs/laravel.log, APP_KEY, DB, สิทธิ์ storage/cache |
| CSS หรือฟอนต์ไม่โหลด | การเข้าถึง CDN และ Google Fonts |
| ไฟล์แนบ 404 | path ในคำขอ, private storage, รายงาน files:privatize และ backup |
| เข้า admin แล้วกลับ login | บัญชี is_active, session และ secure cookie กับ HTTP/HTTPS |
| รีเซ็ตรหัสผ่านไม่ถึงอีเมล | MAIL_MAILER ยังเป็น log, SMTP และ APP_URL |
| ชุดทดสอบหยุดก่อนรัน | ล้าง config cache และใช้ฐานข้อมูลทดสอบตามที่กำหนด |
| ติดตั้งบน Linux ไม่พบหน้าโดเมน | ใช้ไฟล์ show.blade.php ตัวพิมพ์เล็กจากชุดส่งมอบ |
