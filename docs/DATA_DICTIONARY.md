# Data Dictionary — NRRU Hosting Web

เอกสารพจนานุกรมข้อมูลระบบรับคำขอและบริหารทะเบียน Web Hosting / Virtual Server ของมหาวิทยาลัยราชภัฏนครราชสีมา

วันที่จัดทำ: **1 ตุลาคม 2569 (2026-10-01)**

## ขอบเขตและแหล่งอ้างอิง

อ้างอิงโครงสร้างหลังเรียก `up()` ของ migrations ทั้งหมดใน [database/migrations](../database/migrations/) จนถึง `2026_09_16_000001_add_server_details_to_domains_table.php` ประกอบกับ [Models](../app/Models/) และ [กฎตรวจสอบแบบฟอร์ม](../app/Http/Requests/StoreServiceRequestRequest.php) เป็นโครงสร้างที่โค้ดกำหนด ไม่ใช่ผลสำรวจฐานข้อมูลที่ติดตั้งจริง หากยัง migrate ไม่ครบ โครงสร้างฐานข้อมูลอาจต่างจากเอกสารนี้

ชนิดข้อมูลแสดงตาม MySQL/MariaDB: `id()` และ `foreignId()` เป็น `BIGINT UNSIGNED`, `string()` ที่ไม่ระบุขนาดเป็น `VARCHAR(255)`, `BOOLEAN` ใช้ค่า `0/1` (โดยทั่วไปจัดเก็บเป็น `TINYINT(1)`) ส่วน SQLite ที่ใช้ทดสอบอาจแสดงชนิดข้อมูลและบังคับข้อจำกัดต่างกัน

คำย่อ: **PK** = Primary Key, **FK** = Foreign Key, **UQ** = Unique, **AI** = Auto Increment ค่า `—` ในช่องค่าเริ่มต้นหมายถึงไม่มี default ที่ migration ระบุ; คอลัมน์ที่รับ NULL และไม่ได้กำหนดค่าโดยทั่วไปจะเป็น NULL ค่า `CURRENT_TIMESTAMP` มาจากเวลาของฐานข้อมูล

## รายการตาราง

| ตาราง | หน้าที่ | Primary Key |
|---|---|---|
| `users` | บัญชีเจ้าหน้าที่เข้าหลังบ้าน | `id` |
| `applicants` | ข้อมูลผู้ขอใช้บริการ ณ เวลายื่นคำขอ | `applicant_id` |
| `service_requests` | คำขอ ทรัพยากร รายละเอียดทางเทคนิค และการรับรอง | `request_id` |
| `developers` | ผู้รับผิดชอบพัฒนาระบบของคำขอ | `developer_id` |
| `resource_plans` | แพ็กเกจทรัพยากรและค่าบริการ | `plan_id` |
| `department_codes` | รหัสหน่วยงานมาตรฐาน | `code` |
| `domains` | โดเมนและชื่อเครื่อง Server ของคำขอ | `domain_id` |
| `approvals` | ประวัติการพิจารณาคำขอ | `approval_id` |
| `service_accounts` | ทะเบียนบัญชีบริการที่จัดให้ผู้ขอ | `account_id` |
| `service_renewals` | ประวัติการต่ออายุบัญชีบริการ | `id` |
| `password_reset_tokens` | โทเคนรีเซ็ตรหัสผ่านเจ้าหน้าที่ | `email` |
| `failed_jobs` | ข้อมูล queue job ที่ล้มเหลว | `id` |
| `migrations` | ประวัติ migrations ที่ Laravel เรียกใช้ | `id` |

## 1. users — บัญชีเจ้าหน้าที่

| คอลัมน์ | ชนิดข้อมูล | NULL | ค่าเริ่มต้น | คีย์/ข้อจำกัด | ความหมาย |
|---|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | ไม่ | AI | PK | รหัสเจ้าหน้าที่ |
| `name` | VARCHAR(255) | ไม่ | — | — | ชื่อเจ้าหน้าที่ |
| `email` | VARCHAR(255) | ไม่ | — | UQ | อีเมลที่ใช้เข้าสู่ระบบ |
| `email_verified_at` | TIMESTAMP | ได้ | — | — | วันเวลายืนยันอีเมล |
| `password` | VARCHAR(255) | ไม่ | — | — | รหัสผ่านที่ผ่านการ hash |
| `is_active` | BOOLEAN | ไม่ | 1 | — | เปิดหรือระงับการเข้าสู่ระบบ |
| `remember_token` | VARCHAR(100) | ได้ | — | — | โทเคนสำหรับจดจำการเข้าสู่ระบบ |
| `created_at` | TIMESTAMP | ได้ | — | — | วันเวลาสร้างข้อมูล |
| `updated_at` | TIMESTAMP | ได้ | — | — | วันเวลาแก้ไขข้อมูลล่าสุด |

บัญชีเจ้าหน้าที่แยกจาก `service_accounts` และปัจจุบันไม่มีตาราง role/permission ใน migrations ของโครงการ

## 2. applicants — ผู้ขอใช้บริการ

| คอลัมน์ | ชนิดข้อมูล | NULL | ค่าเริ่มต้น | คีย์/ข้อจำกัด | ความหมาย |
|---|---|---|---|---|---|
| `applicant_id` | BIGINT UNSIGNED | ไม่ | AI | PK | รหัสข้อมูลผู้ขอ |
| `full_name` | VARCHAR(150) | ไม่ | — | — | ชื่อและนามสกุลผู้ขอ |
| `customer_name` | VARCHAR(150) | ได้ | — | — | ชื่อบัญชีลูกค้าที่ใช้อ้างอิงใน Plesk |
| `staff_or_student_id` | VARCHAR(30) | ไม่ | — | INDEX | รหัสบุคลากรหรือนักศึกษา |
| `unit_name` | VARCHAR(150) | ไม่ | — | — | ชื่อหน่วยงาน |
| `affiliation` | VARCHAR(150) | ไม่ | — | — | สังกัด |
| `position_title` | VARCHAR(150) | ได้ | — | — | ตำแหน่ง |
| `phone` | VARCHAR(20) | ได้ | — | — | เบอร์โทรศัพท์ |
| `email` | VARCHAR(150) | ได้ | — | — | อีเมลติดต่อ |
| `created_at` | TIMESTAMP | ไม่ | CURRENT_TIMESTAMP | — | วันเวลาบันทึกข้อมูล |

ตั้งแต่ migration `2026_09_10_000002` ยกเลิก UQ คู่ `staff_or_student_id, email` เพื่อเก็บข้อมูลผู้ขอแต่ละครั้งเป็น snapshot ดังนั้นบุคคลเดียวกันอาจมีหลายแถว ไม่ควรถือว่ารหัสบุคลากรหรืออีเมลระบุแถวเดียวเสมอไป

## 3. service_requests — คำขอใช้บริการ

| คอลัมน์ | ชนิดข้อมูล | NULL | ค่าเริ่มต้น | คีย์/ข้อจำกัด | ความหมาย |
|---|---|---|---|---|---|
| `request_id` | BIGINT UNSIGNED | ไม่ | AI | PK | รหัสคำขอ |
| `form_no` | VARCHAR(30) | ไม่ | — | — | เลขที่แบบฟอร์ม/เลขอ้างอิงคำขอ |
| `request_date` | DATE | ไม่ | — | — | วันที่ยื่นคำขอ |
| `receipt_no` | VARCHAR(30) | ได้ | — | — | เลขที่เอกสารรับ/ใบเสร็จที่บันทึกประกอบคำขอ |
| `receipt_date` | DATE | ได้ | — | — | วันที่เอกสารรับ/ใบเสร็จ |
| `receipt_time` | TIME | ได้ | — | — | เวลาเอกสารรับ/ใบเสร็จ |
| `applicant_id` | BIGINT UNSIGNED | ไม่ | — | FK → applicants.applicant_id | ผู้ขอใช้บริการ |
| `purpose_type` | ENUM | ไม่ | — | ดูชุดค่า A | ประเภทวัตถุประสงค์ |
| `purpose_other_detail` | TEXT | ได้ | — | — | รายละเอียดวัตถุประสงค์อื่น |
| `project_start_date` | DATE | ไม่ | — | — | วันเริ่มต้นโครงการ |
| `project_end_date` | DATE | ไม่ | — | — | วันสิ้นสุดโครงการ |
| `status` | ENUM | ไม่ | submitted | ดูชุดค่า B | สถานะคำขอ |
| `source` | ENUM | ไม่ | self_service | ดูชุดค่า C | ที่มาของคำขอ |
| `legacy_note` | VARCHAR(255) | ได้ | — | — | หมายเหตุข้อมูลนำเข้าจากระบบเดิม |
| `service_type` | ENUM | ได้ | — | ดูชุดค่า D | ประเภทบริการที่ขอ |
| `plan_id` | BIGINT UNSIGNED | ได้ | — | FK → resource_plans.plan_id | แพ็กเกจที่เลือก |
| `custom_cpu_vcpu` | INT | ได้ | — | — | จำนวน vCPU ที่ระบุเอง |
| `custom_ram_gb` | INT | ได้ | — | — | RAM ที่ระบุเอง หน่วย GB |
| `custom_storage_gb` | INT | ได้ | — | — | พื้นที่จัดเก็บที่ระบุเอง หน่วย GB |
| `custom_fee` | DECIMAL(10,2) | ได้ | — | — | ค่าบริการที่ระบุเอง |
| `enabled_services` | JSON | ได้ | — | ตรวจชุดค่า E ในแอป | รายการบริการที่ขอเปิด เป็น JSON array |
| `enabled_services_other_detail` | VARCHAR(255) | ได้ | — | — | รายละเอียดบริการอื่น |
| `language_framework` | VARCHAR(100) | ได้ | — | — | ภาษาและ framework ของระบบ |
| `database_used` | VARCHAR(100) | ได้ | — | — | ฐานข้อมูลที่ใช้ |
| `port_service_needed` | VARCHAR(255) | ได้ | — | — | พอร์ตหรือบริการที่ต้องการ |
| `needs_external_connection` | BOOLEAN | ไม่ | 0 | — | ต้องเชื่อมต่อภายนอกหรือไม่ |
| `system_detail_doc_path` | VARCHAR(500) | ได้ | — | — | เส้นทางไฟล์รายละเอียดระบบ |
| `screenshot_evidence_path` | VARCHAR(500) | ได้ | — | — | เส้นทางไฟล์หลักฐานประกอบ |
| `agree_to_pay` | BOOLEAN | ไม่ | 0 | — | ยินยอมชำระค่าบริการ |
| `request_fee_waiver` | BOOLEAN | ไม่ | 0 | — | ขอรับการยกเว้นค่าธรรมเนียม |
| `waiver_reason` | TEXT | ได้ | — | — | เหตุผลขอยกเว้นค่าธรรมเนียม |
| `accepted` | BOOLEAN | ไม่ | 0 | — | ยอมรับข้อกำหนดการใช้บริการ |
| `signature_image_path` | VARCHAR(500) | ได้ | — | — | เส้นทางรูปลายเซ็นผู้ขอ |
| `accepted_date` | DATE | ได้ | — | — | วันที่ยอมรับข้อกำหนด |
| `created_at` | TIMESTAMP | ไม่ | CURRENT_TIMESTAMP | — | วันเวลาสร้างคำขอ |

`form_no` ไม่มี UNIQUE constraint ใน migrations แม้แอปใช้เป็นเลขอ้างอิงคำขอ ส่วน path ของไฟล์เก็บตำแหน่งไฟล์ ไม่ได้เก็บเนื้อหาไฟล์หรือ URL สาธารณะ

### ชุดค่าของคำขอ

| ชุด | ฟิลด์ | ค่าที่รองรับและความหมาย |
|---|---|---|
| A | `purpose_type` | `1.1_teaching` = การเรียนการสอน; `1.2_academic_research_community` = วิชาการ วิจัย และบริการชุมชน; `1.3_internal_admin` = บริหารงานภายใน; `1.4_other` = อื่น ๆ |
| B | `status` | `draft` = ฉบับร่าง; `submitted` = ส่งแล้ว; `approved` = อนุมัติ; `rejected` = ไม่อนุมัติ; `expired` = หมดอายุ |
| C | `source` | `self_service` = ผู้ขอยื่นผ่านระบบ; `legacy_import` = นำเข้าจาก Plesk เดิม |
| D | `service_type` | `virtual_server` = เครื่องแม่ข่ายเสมือน; `web_hosting` = พื้นที่ให้บริการเว็บไซต์ |
| E | สมาชิกของ `enabled_services` | `ssh`, `http_https`, `database_access`, `other` |

ตัวอย่าง `enabled_services`: `["ssh", "http_https"]` โมเดลแปลง JSON นี้เป็น array ส่วนฐานข้อมูลไม่ได้กำหนด enum สำหรับสมาชิกของ JSON

## 4. developers — ผู้พัฒนาระบบ

| คอลัมน์ | ชนิดข้อมูล | NULL | ค่าเริ่มต้น | คีย์/ข้อจำกัด | ความหมาย |
|---|---|---|---|---|---|
| `developer_id` | BIGINT UNSIGNED | ไม่ | AI | PK | รหัสผู้พัฒนา |
| `request_id` | BIGINT UNSIGNED | ไม่ | — | FK → service_requests.request_id; DELETE CASCADE | คำขอที่เกี่ยวข้อง |
| `full_name` | VARCHAR(150) | ไม่ | — | — | ชื่อและนามสกุล |
| `role_desc` | VARCHAR(150) | ได้ | — | — | หน้าที่หรือบทบาท |
| `phone` | VARCHAR(20) | ได้ | — | — | เบอร์โทรศัพท์ |
| `email` | VARCHAR(150) | ได้ | — | — | อีเมลติดต่อ |

## 5. resource_plans — แพ็กเกจทรัพยากร

| คอลัมน์ | ชนิดข้อมูล | NULL | ค่าเริ่มต้น | คีย์/ข้อจำกัด | ความหมาย |
|---|---|---|---|---|---|
| `plan_id` | BIGINT UNSIGNED | ไม่ | AI | PK | รหัสแพ็กเกจ |
| `service_type` | ENUM('virtual_server','web_hosting') | ไม่ | — | — | ประเภทบริการ |
| `size_label` | VARCHAR(30) | ไม่ | — | — | ชื่อขนาดแพ็กเกจ |
| `cpu_vcpu` | INT | ได้ | — | — | จำนวน vCPU |
| `ram_gb` | INT | ได้ | — | — | RAM หน่วย GB |
| `storage_gb` | INT | ได้ | — | — | พื้นที่จัดเก็บ หน่วย GB |
| `fee_per_year` | DECIMAL(10,2) | ได้ | — | — | ค่าบริการต่อปี |
| `suitable_for` | VARCHAR(255) | ได้ | — | — | ลักษณะงานที่เหมาะสม |

## 6. department_codes — รหัสหน่วยงาน

| คอลัมน์ | ชนิดข้อมูล | NULL | ค่าเริ่มต้น | คีย์/ข้อจำกัด | ความหมาย |
|---|---|---|---|---|---|
| `code` | VARCHAR(20) | ไม่ | — | PK | รหัสหน่วยงาน |
| `department_name` | VARCHAR(150) | ไม่ | — | — | ชื่อหน่วยงานมาตรฐาน |

## 7. domains — โดเมน

| คอลัมน์ | ชนิดข้อมูล | NULL | ค่าเริ่มต้น | คีย์/ข้อจำกัด | ความหมาย |
|---|---|---|---|---|---|
| `domain_id` | BIGINT UNSIGNED | ไม่ | AI | PK | รหัสโดเมน |
| `request_id` | BIGINT UNSIGNED | ไม่ | — | FK → service_requests.request_id; DELETE CASCADE | คำขอที่เกี่ยวข้อง |
| `domain_name` | VARCHAR(255) | ไม่ | — | — | ชื่อโดเมน |
| `domain_format` | VARCHAR(255) | ได้ | — | — | รูปแบบโดเมน |
| `department_code` | VARCHAR(20) | ได้ | — | FK → department_codes.code | รหัสหน่วยงานมาตรฐาน |
| `department_other` | VARCHAR(150) | ได้ | — | — | ชื่อหน่วยงานที่ระบุเอง |
| `server_name` | VARCHAR(150) | ได้ | — | — | ชื่อเครื่อง Server ที่ให้บริการ |

`domain_name` ไม่มี UNIQUE constraint ใน migrations และไม่มี `account_id` โดยตรง การเชื่อมโดเมนกับบัญชีบริการใช้คำขอร่วมกันผ่าน `request_id`

## 8. approvals — ประวัติการพิจารณา

| คอลัมน์ | ชนิดข้อมูล | NULL | ค่าเริ่มต้น | คีย์/ข้อจำกัด | ความหมาย |
|---|---|---|---|---|---|
| `approval_id` | BIGINT UNSIGNED | ไม่ | AI | PK | รหัสการพิจารณา |
| `request_id` | BIGINT UNSIGNED | ไม่ | — | FK → service_requests.request_id; DELETE CASCADE | คำขอที่พิจารณา |
| `approver_level` | ENUM | ไม่ | — | ดูชุดค่าด้านล่าง | ระดับผู้พิจารณา |
| `approver_name` | VARCHAR(150) | ได้ | — | — | ชื่อผู้พิจารณาที่บันทึกไว้ |
| `decision` | ENUM | ได้ | — | ดูชุดค่าด้านล่าง | ผลการพิจารณา |
| `signature_image_path` | VARCHAR(500) | ได้ | — | — | เส้นทางรูปลายเซ็นผู้พิจารณา |
| `decision_date` | DATE | ได้ | — | — | วันที่พิจารณา |

| ฟิลด์ | ค่า | ความหมาย |
|---|---|---|
| `approver_level` | `staff` | เจ้าหน้าที่ |
| `approver_level` | `unit_head` | หัวหน้าหน่วยงาน |
| `approver_level` | `computer_center_deputy_director` | รองผู้อำนวยการสำนักคอมพิวเตอร์ |
| `approver_level` | `computer_center_director` | ผู้อำนวยการสำนักคอมพิวเตอร์ |
| `decision` | `certify_info_only` | รับรองข้อมูล |
| `decision` | `certify_and_waive_fee` | รับรองข้อมูลและยกเว้นค่าธรรมเนียม |
| `decision` | `acknowledge_assign_web_team` | รับทราบและมอบหมายทีมเว็บไซต์ |
| `decision` | `rejected` | ไม่อนุมัติ |

ไม่มี FK ไป `users` และไม่มี UQ คู่ `request_id, approver_level` จึงรองรับหลายรายการของระดับเดียวกันได้ในระดับฐานข้อมูล

## 9. service_accounts — ทะเบียนบัญชีบริการ

| คอลัมน์ | ชนิดข้อมูล | NULL | ค่าเริ่มต้น | คีย์/ข้อจำกัด | ความหมาย |
|---|---|---|---|---|---|
| `account_id` | BIGINT UNSIGNED | ไม่ | AI | PK | รหัสบัญชีบริการ |
| `request_id` | BIGINT UNSIGNED | ไม่ | — | FK → service_requests.request_id; DELETE CASCADE | คำขอต้นทาง |
| `applicant_id` | BIGINT UNSIGNED | ได้* | — | FK → applicants.applicant_id | ผู้ขอที่เกี่ยวข้องกับบัญชี |
| `username` | VARCHAR(100) | ไม่ | — | UQ | ชื่อบัญชีบริการ |
| `password_hash` | VARCHAR(255) | ไม่ | — | — | รหัสผ่านที่ผ่านการ hash |
| `account_type` | ENUM('ssh','database','control_panel','ftp') | ไม่ | control_panel | — | ประเภทบัญชี |
| `status` | ENUM('active','disabled','expired') | ไม่ | active | — | สถานะที่จัดเก็บ |
| `created_by` | VARCHAR(150) | ได้ | — | — | ชื่อผู้สร้างบัญชี ไม่ใช่ FK |
| `created_at` | TIMESTAMP | ไม่ | CURRENT_TIMESTAMP | — | วันเวลาสร้างบัญชี |
| `expire_date` | DATE | ได้ | — | — | วันหมดอายุ; NULL คือไม่ได้กำหนด |

\* สำหรับ MySQL/MariaDB คอลัมน์ `applicant_id` ถูกลบแล้วสร้างใหม่เป็น nullable ใน migrations วันที่ 24 สิงหาคม 2569 ส่วน SQLite ข้ามการลบและคงคอลัมน์เดิมที่ NOT NULL ไว้

`password` เป็นชื่อ attribute ที่โมเดลรับเพื่อ hash ลง `password_hash` ไม่ใช่คอลัมน์ฐานข้อมูล ส่วน `effective_status` เป็นค่าคำนวณในโมเดล: บัญชี `active` ที่ `expire_date` ก่อนวันนี้แสดงเป็น `expired` โดยไม่จำเป็นต้องเปลี่ยนค่าคอลัมน์ `status` วันที่หมดอายุเท่ากับวันนี้ยังไม่ถือว่าหมดอายุจากการคำนวณนี้

## 10. service_renewals — ประวัติการต่ออายุ

| คอลัมน์ | ชนิดข้อมูล | NULL | ค่าเริ่มต้น | คีย์/ข้อจำกัด | ความหมาย |
|---|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | ไม่ | AI | PK | รหัสประวัติการต่ออายุ |
| `account_id` | BIGINT UNSIGNED | ไม่ | — | FK → service_accounts.account_id; DELETE CASCADE | บัญชีที่ต่ออายุ |
| `previous_expire_date` | DATE | ได้ | — | — | วันหมดอายุเดิม |
| `expire_date` | DATE | ไม่ | — | — | วันหมดอายุใหม่ |
| `previous_status` | VARCHAR(20) | ไม่ | — | — | สถานะก่อนต่ออายุ ไม่ใช่ ENUM ในฐานข้อมูล |
| `renewed_by` | BIGINT UNSIGNED | ได้ | — | FK → users.id; DELETE SET NULL | เจ้าหน้าที่ผู้ต่ออายุ |
| `renewed_by_name` | VARCHAR(255) | ไม่ | — | — | ชื่อเจ้าหน้าที่ ณ เวลาต่ออายุ |
| `note` | TEXT | ได้ | — | — | หมายเหตุ |
| `created_at` | TIMESTAMP | ไม่ | CURRENT_TIMESTAMP | — | วันเวลาต่ออายุ |

## 11. password_reset_tokens — โทเคนรีเซ็ตรหัสผ่าน

| คอลัมน์ | ชนิดข้อมูล | NULL | ค่าเริ่มต้น | คีย์/ข้อจำกัด | ความหมาย |
|---|---|---|---|---|---|
| `email` | VARCHAR(255) | ไม่ | — | PK | อีเมลบัญชีที่ขอรีเซ็ต ไม่มี FK ไป users |
| `token` | VARCHAR(255) | ไม่ | — | — | โทเคนที่ระบบ password broker จัดเก็บ |
| `created_at` | TIMESTAMP | ได้ | — | — | วันเวลาสร้างโทเคน |

## 12. failed_jobs — งานเบื้องหลังที่ล้มเหลว

| คอลัมน์ | ชนิดข้อมูล | NULL | ค่าเริ่มต้น | คีย์/ข้อจำกัด | ความหมาย |
|---|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | ไม่ | AI | PK | รหัสรายการ |
| `uuid` | VARCHAR(255) | ไม่ | — | UQ | UUID ของงาน |
| `connection` | TEXT | ไม่ | — | — | connection ของ queue |
| `queue` | TEXT | ไม่ | — | — | ชื่อ queue |
| `payload` | LONGTEXT | ไม่ | — | — | ข้อมูลงานที่ประมวลผล |
| `exception` | LONGTEXT | ไม่ | — | — | รายละเอียดข้อผิดพลาด |
| `failed_at` | TIMESTAMP | ไม่ | CURRENT_TIMESTAMP | — | วันเวลาที่งานล้มเหลว |

## 13. migrations — ประวัติการปรับโครงสร้าง

ตารางนี้ Laravel สร้างเพื่อจัดการ migration repository ไม่ได้ประกาศในไฟล์ migration ของแอป ตรวจสอบนิยาม framework ที่ติดตั้งใน [DatabaseMigrationRepository](../vendor/laravel/framework/src/Illuminate/Database/Migrations/DatabaseMigrationRepository.php) เมื่อต้องการยืนยันตามรุ่น

| คอลัมน์ | ชนิดข้อมูล | NULL | ค่าเริ่มต้น | คีย์/ข้อจำกัด | ความหมาย |
|---|---|---|---|---|---|
| `id` | INT UNSIGNED | ไม่ | AI | PK | รหัสรายการ migration |
| `migration` | VARCHAR(255) | ไม่ | — | — | ชื่อ migration ที่เรียกใช้แล้ว |
| `batch` | INT | ไม่ | — | — | หมายเลขชุดการ migrate |

## ความสัมพันธ์และพฤติกรรมเมื่อลบข้อมูล

| ตารางลูก → ตารางแม่ | ความสัมพันธ์จากแม่ไปลูก | เมื่อลบแม่ |
|---|---|---|
| service_requests.applicant_id → applicants.applicant_id | 1:N | ไม่ระบุ cascade; FK ป้องกันการลบเมื่อยังมีลูก |
| service_requests.plan_id → resource_plans.plan_id | 1:N; ลูกไม่เลือกแพ็กเกจได้ | ไม่ระบุ cascade; FK ป้องกันการลบเมื่อยังมีลูก |
| developers.request_id → service_requests.request_id | 1:N | CASCADE |
| domains.request_id → service_requests.request_id | 1:N | CASCADE |
| domains.department_code → department_codes.code | 1:N; ลูกไม่ระบุรหัสได้ | ไม่ระบุ cascade; FK ป้องกันการลบเมื่อยังมีลูก |
| approvals.request_id → service_requests.request_id | 1:N | CASCADE |
| service_accounts.request_id → service_requests.request_id | 1:N | CASCADE |
| service_accounts.applicant_id → applicants.applicant_id | 1:N | ไม่ระบุ cascade; FK ป้องกันการลบเมื่อยังมีลูก |
| service_renewals.account_id → service_accounts.account_id | 1:N | CASCADE |
| service_renewals.renewed_by → users.id | 1:N; ลูกไม่มีผู้ใช้อ้างอิงได้ | SET NULL; ชื่อที่บันทึกยังอยู่ |

FK ไม่ได้บังคับให้ `service_accounts.applicant_id` ตรงกับผู้ขอของ `request_id` โดยอัตโนมัติ และการลบแถวที่เก็บ path ไม่ได้หมายถึงฐานข้อมูลลบไฟล์บน storage ให้ด้วย

## กฎระดับแอปที่ต่างจากข้อจำกัดฐานข้อมูล

กฎต่อไปนี้อ้างอิงการยื่นแบบฟอร์มสาธารณะใน `StoreServiceRequestRequest` ไม่ใช่ CHECK constraint และอาจต่างจากการนำเข้าข้อมูลเดิมหรือการจัดการหลังบ้าน

- ต้องระบุประเภทบริการ และเมื่อเลือกแพ็กเกจ แพ็กเกจต้องตรงกับประเภทบริการ
- วันสิ้นสุดโครงการต้องไม่ก่อนวันเริ่มต้น และระยะเวลาไม่เกิน 1 ปีนับจากวันเริ่มต้น
- ต้องมีผู้พัฒนาอย่างน้อย 1 คน และบริการที่ขอเปิดอย่างน้อย 1 รายการ
- ทรัพยากรที่ระบุเองต้องเป็นจำนวนเต็มอย่างน้อย 1; ค่าบริการที่ระบุเองต้องไม่ติดลบ
- วัตถุประสงค์ `1.4_other` ต้องมีรายละเอียด และการขอยกเว้นค่าธรรมเนียมต้องมีเหตุผล
- ต้องยินยอมชำระค่าบริการหรือขอยกเว้นค่าธรรมเนียมอย่างน้อยหนึ่งรายการ พร้อมยอมรับข้อกำหนด
- ต้องแนบรายละเอียดระบบและรูปลายเซ็น แม้คอลัมน์ path จะรับ NULL ได้เพื่อรองรับข้อมูลในบริบทอื่น

## ตารางและคอลัมน์ที่เลิกใช้

| รายการเดิม | การเปลี่ยนแปลง | Migration |
|---|---|---|
| `personal_access_tokens` | ลบตาราง API token ที่ไม่ได้ใช้ | `2026_08_24_000001` |
| `request_resources` | ย้ายทรัพยากรและแพ็กเกจไป service_requests | `2026_08_24_000002` |
| `request_enabled_services` | ย้ายรายการบริการไป enabled_services แบบ JSON | `2026_08_24_000002` |
| `tech_details` | ย้ายรายละเอียดทางเทคนิคไป service_requests | `2026_08_24_000002` |
| `attachments` | ย้าย path เอกสารและหลักฐานไป service_requests | `2026_08_24_000002` |
| `fee_certifications` | ย้ายการรับรองค่าใช้จ่ายไป service_requests | `2026_08_24_000002` |
| `policy_acceptances` | ย้ายการยอมรับข้อกำหนดและลายเซ็นไป service_requests | `2026_08_24_000002` |
| `service_accounts.last_login` | ลบคอลัมน์เวลาล็อกอินล่าสุด | `2026_09_03_000001` |

เมื่อเพิ่มหรือแก้ไข migration ควรปรับชนิดข้อมูล ค่าเริ่มต้น ข้อจำกัด และความสัมพันธ์ในเอกสารนี้พร้อมกัน
