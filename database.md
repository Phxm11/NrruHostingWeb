# โครงสร้างฐานข้อมูล `dc_hosting_service` 

ระบบขอใช้บริการ Hosting/Virtual Server ของ NRRU — เอกสารนี้ลงรายละเอียดทุกคอลัมน์ ชนิดข้อมูล ค่าเริ่มต้น คีย์ และความสัมพันธ์ระหว่างตาราง โดยอ้างอิงจาก SQL dump ล่าสุด

> **หมายเหตุ:** ตาราง `roles`, `permissions`, `permission_role` ยังปรากฏอยู่ในฐานข้อมูลจริง ณ ตอนนี้ แต่ไม่มีข้อมูลและไม่ถูกใช้งานในโค้ดแล้ว — อยู่ระหว่างเตรียมลบทิ้งตามที่ตัดสินใจว่าไม่ใช้ระบบสิทธิ์แยกระดับ เอกสารนี้จึงอธิบายไว้ท้ายสุดแยกเป็นหมวด "ตารางที่กำลังจะถูกลบ"

---

## ภาพรวมความสัมพันธ์ (Entity Relationship)

```
                                   ┌──────────────────┐
                                   │  resource_plans   │  (แพ็กเกจสำเร็จรูป — อ้างอิงคงที่)
                                   └─────────┬─────────┘
                                             │ plan_id (nullable FK)
                                             │
┌──────────────┐   applicant_id   ┌─────────▼──────────┐
│  applicants   │◄─────────────────┤  service_requests   │
│ (ผู้ขอใช้บริการ) │  (1 คน : หลายคำขอ) │   (คำขอใช้บริการ)    │
└───────┬───────┘                  └──┬───────┬───────┬──┘
        │                             │       │       │
        │ applicant_id (nullable)     │       │       │ request_id (CASCADE DELETE)
        │                             │       │       │
┌───────▼────────┐          request_id│       │       │
│ service_accounts│◄───────────────────┘       │       │
│  (บัญชีใช้งานจริง)  │                          │       │
└─────────────────┘                            │       │
                                                │       │
                          ┌─────────────────────▼┐  ┌───▼──────────┐  ┌──────────┐
                          │      developers       │  │   domains     │  │ approvals │
                          │  (ผู้พัฒนาระบบที่ระบุ)   │  │  (โดเมนที่ขอ)   │  │(ผลอนุมัติ) │
                          └────────────────────────┘  └──────┬────────┘  └───────────┘
                                                              │ department_code (nullable FK)
                                                       ┌──────▼──────────┐
                                                       │ department_codes │ (รหัสคณะ — อ้างอิงคงที่)
                                                       └───────────────────┘

users  (เจ้าหน้าที่แอดมิน — แยกอิสระจาก applicants ทั้งหมด ไม่มี FK เชื่อมกัน)
```

**Cardinality สรุป:**
- `applicants` 1 : N `service_requests` — ผู้ขอ 1 คนยื่นได้หลายคำขอ (ระบบ match ด้วย `staff_or_student_id`, ถ้ายื่นซ้ำจะอัปเดตข้อมูลเดิมแทนสร้างใหม่)
- `service_requests` 1 : N `developers`, 1 : N `domains`, 1 : N `approvals` — คำขอเดียวมีผู้พัฒนา/โดเมน/รอบอนุมัติได้หลายรายการ
- `service_requests` 1 : N `service_accounts` — คำขอเดียวสร้างบัญชีจริงได้หลายบัญชี (เช่น SSH แยกกับ Database)
- `resource_plans` 1 : N `service_requests` — แพ็กเกจหนึ่งถูกเลือกใช้ในหลายคำขอได้ (ความสัมพันธ์นี้ไม่บังคับ เพราะคำขอเลือกกำหนดสเปกเองได้โดยไม่ผูกกับแพ็กเกจ)
- `department_codes` 1 : N `domains` — รหัสคณะหนึ่งใช้ในหลายโดเมนได้

---

## 1. `applicants` — ผู้ขอใช้บริการ

| คอลัมน์ | ชนิดข้อมูล | Null ได้ | ค่าเริ่มต้น | คำอธิบาย |
|---|---|---|---|---|
| `applicant_id` | `bigint(20) UNSIGNED` | ไม่ | auto_increment | **PK** |
| `full_name` | `varchar(150)` | ไม่ | — | ชื่อ-สกุลผู้ขอใช้บริการ |
| `customer_name` | `varchar(150)` | ได้ | `NULL` | ชื่อบัญชีลูกค้าที่ใช้อ้างอิงใน Plesk (ไม่บังคับกรอก — เพิ่มเข้ามาภายหลังจากฟิลด์เดิม) |
| `staff_or_student_id` | `varchar(30)` | ไม่ | — | รหัสบุคลากร/นักศึกษา — **ใช้เป็นตัวระบุตัวตนหลักตอนบันทึกฟอร์ม** (ระบบใช้ `updateOrCreate` จับคู่ด้วยฟิลด์นี้) |
| `unit_name` | `varchar(150)` | ไม่ | — | หน่วยงาน |
| `affiliation` | `varchar(150)` | ไม่ | — | สังกัด |
| `position_title` | `varchar(150)` | ได้ | `NULL` | ตำแหน่ง |
| `phone` | `varchar(20)` | ได้ | `NULL` | เบอร์โทรศัพท์ |
| `email` | `varchar(150)` | ได้ | `NULL` | อีเมล |
| `created_at` | `timestamp` | ไม่ | `current_timestamp()` | วันที่สร้างเรคคอร์ด (ไม่มี `updated_at` — โมเดลนี้ `timestamps` ปิดไว้ ใช้แค่ `created_at`) |

**Keys:**
- PRIMARY KEY (`applicant_id`)
- UNIQUE KEY `applicants_staff_or_student_id_email_unique` (`staff_or_student_id`, `email`) — คู่นี้ห้ามซ้ำกันทั้งฐานข้อมูล

**จุดสำคัญ:** เพราะ unique key เป็นแบบ**คู่** (รหัส + อีเมล) ไม่ใช่รหัสอย่างเดียว ในทางทฤษฎีคนละอีเมลกับรหัสเดียวกันจะสร้างแถวใหม่แยกได้ — แต่โค้ดจริงตอนบันทึกฟอร์ม (`updateOrCreate`) จับคู่ด้วย `staff_or_student_id` อย่างเดียว จึงมีผลเหมือนใช้รหัสเป็นตัวระบุหลักในทางปฏิบัติ

---

## 2. `service_requests` — คำขอใช้บริการ (ตารางหลัก)

| คอลัมน์ | ชนิดข้อมูล | Null ได้ | ค่าเริ่มต้น | คำอธิบาย |
|---|---|---|---|---|
| `request_id` | `bigint(20) UNSIGNED` | ไม่ | auto_increment | **PK** |
| `form_no` | `varchar(30)` | ไม่ | — | เลขที่แบบฟอร์ม รูปแบบ `xxx/พ.ศ.` เช่น `001/2569` |
| `request_date` | `date` | ไม่ | — | วันที่ยื่นคำขอ |
| `receipt_no` | `varchar(30)` | ได้ | `NULL` | เลขที่ใบรับเรื่อง เช่น `RC-001/2569` |
| `receipt_date` | `date` | ได้ | `NULL` | วันที่รับเรื่อง |
| `receipt_time` | `time` | ได้ | `NULL` | เวลาที่รับเรื่อง |
| `applicant_id` | `bigint(20) UNSIGNED` | ไม่ | — | **FK →** `applicants.applicant_id` |
| `purpose_type` | `enum('1.1_teaching','1.2_academic_research_community','1.3_internal_admin','1.4_other')` | ไม่ | — | วัตถุประสงค์: การเรียนการสอน / บริการวิชาการ-วิจัย-ชุมชน / บริหารภายในหน่วยงาน / อื่น ๆ |
| `purpose_other_detail` | `text` | ได้ | `NULL` | รายละเอียดถ้าเลือก "อื่น ๆ" |
| `project_start_date` | `date` | ไม่ | — | วันเริ่มโครงการ |
| `project_end_date` | `date` | ไม่ | — | วันสิ้นสุดโครงการ (ต้อง ≥ วันเริ่ม) |
| `status` | `enum('draft','submitted','approved','rejected','expired')` | ไม่ | `'submitted'` | สถานะคำขอ |
| `service_type` | `enum('virtual_server','web_hosting')` | ได้ | `NULL` | ประเภทบริการที่ขอ |
| `plan_id` | `bigint(20) UNSIGNED` | ได้ | `NULL` | **FK →** `resource_plans.plan_id` (เว้นว่างได้ถ้ากำหนดสเปกเอง) |
| `custom_cpu_vcpu` | `int(11)` | ได้ | `NULL` | จำนวน vCPU ที่กำหนดเอง (ถ้าไม่ใช้แพ็กเกจสำเร็จรูป) |
| `custom_ram_gb` | `int(11)` | ได้ | `NULL` | RAM (GB) ที่กำหนดเอง |
| `custom_storage_gb` | `int(11)` | ได้ | `NULL` | พื้นที่จัดเก็บ (GB) ที่กำหนดเอง |
| `custom_fee` | `decimal(10,2)` | ได้ | `NULL` | ค่าบริการที่กำหนดเอง |
| `enabled_services` | `longtext` (JSON, ตรวจสอบด้วย `CHECK (json_valid())`) | ได้ | `NULL` | รายการบริการที่เปิดใช้ เก็บเป็น JSON array เช่น `["ssh","http_https"]` ค่าที่เป็นไปได้: `ssh`, `http_https`, `database_access`, `other` |
| `enabled_services_other_detail` | `varchar(255)` | ได้ | `NULL` | รายละเอียดถ้าเลือกบริการ "อื่น ๆ" |
| `language_framework` | `varchar(100)` | ได้ | `NULL` | ภาษา/เฟรมเวิร์กที่ใช้พัฒนา |
| `database_used` | `varchar(100)` | ได้ | `NULL` | ฐานข้อมูลที่ใช้ |
| `port_service_needed` | `varchar(255)` | ได้ | `NULL` | พอร์ต/บริการที่ต้องเปิดเพิ่มเติม |
| `needs_external_connection` | `tinyint(1)` | ไม่ | `0` | ต้องการเชื่อมต่อจากภายนอกเครือข่ายหรือไม่ (boolean) |
| `system_detail_doc_path` | `varchar(500)` | ได้ | `NULL` | path ไฟล์เอกสารรายละเอียดระบบ (เก็บใน storage disk `public`) |
| `screenshot_evidence_path` | `varchar(500)` | ได้ | `NULL` | path ไฟล์ภาพหน้าจอ/หลักฐานประกอบ |
| `agree_to_pay` | `tinyint(1)` | ไม่ | `0` | ยอมรับชำระค่าบริการ (boolean) |
| `request_fee_waiver` | `tinyint(1)` | ไม่ | `0` | ขอยกเว้นค่าบริการ (boolean) — ต้องเลือกอย่างใดอย่างหนึ่งกับ `agree_to_pay` |
| `waiver_reason` | `text` | ได้ | `NULL` | เหตุผลที่ขอยกเว้นค่าบริการ (บังคับกรอกถ้า `request_fee_waiver = 1`) |
| `accepted` | `tinyint(1)` | ไม่ | `0` | ยอมรับข้อกำหนด/นโยบายก่อนส่งฟอร์ม (boolean, บังคับต้องเป็น 1 ถึงส่งได้) |
| `signature_image_path` | `varchar(500)` | ได้ | `NULL` | path ไฟล์รูปลายเซ็นผู้ขอใช้บริการ |
| `accepted_date` | `date` | ได้ | `NULL` | วันที่ยอมรับข้อกำหนด |
| `source` | `enum('self_service','legacy_import')` | ไม่ | `'self_service'` | ที่มา: กรอกเองผ่านฟอร์ม หรือ นำเข้าจากระบบเก่า |
| `legacy_note` | `varchar(255)` | ได้ | `NULL` | หมายเหตุกรณีนำเข้าจากระบบเก่า |
| `created_at` | `timestamp` | ไม่ | `current_timestamp()` | วันที่สร้างเรคคอร์ด |

**Keys:**
- PRIMARY KEY (`request_id`)
- KEY `service_requests_applicant_id_foreign` (`applicant_id`)
- KEY `service_requests_plan_id_foreign` (`plan_id`)

**Foreign Keys:**
- `applicant_id` → `applicants.applicant_id` (ไม่มี ON DELETE ระบุ — ค่าเริ่มต้นคือ RESTRICT: ลบ applicant ไม่ได้ถ้ายังมีคำขอผูกอยู่)
- `plan_id` → `resource_plans.plan_id` (RESTRICT เช่นกัน)

---

## 3. `developers` — ผู้รับผิดชอบพัฒนาระบบ

| คอลัมน์ | ชนิดข้อมูล | Null ได้ | คำอธิบาย |
|---|---|---|---|
| `developer_id` | `bigint(20) UNSIGNED` | ไม่ | **PK**, auto_increment |
| `request_id` | `bigint(20) UNSIGNED` | ไม่ | **FK →** `service_requests.request_id` |
| `full_name` | `varchar(150)` | ไม่ | ชื่อ-สกุลผู้พัฒนา |
| `role_desc` | `varchar(150)` | ได้ | บทบาทหน้าที่ เช่น "ดูแลระบบ" |
| `phone` | `varchar(20)` | ได้ | เบอร์โทร |
| `email` | `varchar(150)` | ได้ | อีเมล |

**Keys:** PRIMARY KEY (`developer_id`), KEY (`request_id`)
**Foreign Key:** `request_id` → `service_requests.request_id` **ON DELETE CASCADE** (ลบคำขอ → ลบรายชื่อผู้พัฒนาที่ผูกอยู่ทั้งหมดทันที)

---

## 4. `domains` — โดเมนที่ขอใช้งาน

| คอลัมน์ | ชนิดข้อมูล | Null ได้ | คำอธิบาย |
|---|---|---|---|
| `domain_id` | `bigint(20) UNSIGNED` | ไม่ | **PK**, auto_increment |
| `request_id` | `bigint(20) UNSIGNED` | ไม่ | **FK →** `service_requests.request_id` |
| `domain_name` | `varchar(255)` | ไม่ | ชื่อโดเมนที่ต้องการ |
| `domain_format` | `varchar(255)` | ได้ | รูปแบบโดเมนเต็ม (เช่น `edu.nrru.ac.th`) |
| `department_code` | `varchar(20)` | ได้ | **FK →** `department_codes.code` |
| `department_other` | `varchar(150)` | ได้ | ระบุหน่วยงานเองถ้าไม่อยู่ในรายการ `department_codes` |

**Keys:** PRIMARY KEY (`domain_id`), KEY (`request_id`), KEY (`department_code`)
**Foreign Keys:**
- `request_id` → `service_requests.request_id` **ON DELETE CASCADE**
- `department_code` → `department_codes.code` (RESTRICT — ลบรหัสคณะไม่ได้ถ้ายังมีโดเมนอ้างอิงอยู่)

---

## 5. `department_codes` — รหัสคณะ/หน่วยงาน (อ้างอิงคงที่)

| คอลัมน์ | ชนิดข้อมูล | Null ได้ | คำอธิบาย |
|---|---|---|---|
| `code` | `varchar(20)` | ไม่ | **PK** — รหัสคณะ เช่น `-edu`, `-fit` |
| `department_name` | `varchar(150)` | ไม่ | ชื่อเต็มคณะ/หน่วยงาน |

**ข้อมูลปัจจุบัน (7 แถว):**

| code | department_name |
|---|---|
| `-edu` | คณะครุศาสตร์ |
| `-fit` | คณะเทคโนโลยีอุตสาหกรรม |
| `-fms` | คณะวิทยาการจัดการ |
| `-human` | คณะมนุษยศาสตร์และสังคมศาสตร์ |
| `-nurse` | คณะพยาบาลศาสตร์ |
| `-ph` | คณะสาธารณสุขศาสตร์ |
| `-sci` | คณะวิทยาศาสตร์และเทคโนโลยี |

---

## 6. `resource_plans` — แพ็กเกจทรัพยากรมาตรฐาน (อ้างอิงคงที่)

| คอลัมน์ | ชนิดข้อมูล | Null ได้ | คำอธิบาย |
|---|---|---|---|
| `plan_id` | `bigint(20) UNSIGNED` | ไม่ | **PK**, auto_increment |
| `service_type` | `enum('virtual_server','web_hosting')` | ไม่ | ประเภทบริการของแพ็กเกจนี้ |
| `size_label` | `varchar(30)` | ไม่ | ชื่อขนาด (เล็ก/กลาง/ใหญ่) |
| `cpu_vcpu` | `int(11)` | ได้ | จำนวน vCPU (ไม่มีสำหรับ web_hosting) |
| `ram_gb` | `int(11)` | ได้ | RAM หน่วย GB (ไม่มีสำหรับ web_hosting) |
| `storage_gb` | `int(11)` | ได้ | พื้นที่จัดเก็บ หน่วย GB |
| `fee_per_year` | `decimal(10,2)` | ได้ | ค่าบริการต่อปี (บาท) |
| `suitable_for` | `varchar(255)` | ได้ | คำแนะนำการใช้งานที่เหมาะสม |

**ข้อมูลปัจจุบัน (5 แถว):**

| plan_id | service_type | size_label | vCPU | RAM | Storage | ค่าบริการ/ปี | เหมาะกับ |
|---|---|---|---|---|---|---|---|
| 6 | virtual_server | เล็ก | 3 | 4GB | 10GB | 5,000 | เว็บไซต์/ระบบงานทั่วไป |
| 7 | virtual_server | กลาง | 3 | 4GB | 20GB | 7,500 | ฐานข้อมูลขนาดเล็ก-กลาง |
| 8 | virtual_server | ใหญ่ | 4 | 8GB | 50GB | 12,000 | ระบบผู้ใช้งานเยอะ |
| 9 | web_hosting | เล็ก | — | — | 5GB | 1,200 | ไม่ต้องการฐานข้อมูล |
| 10 | web_hosting | ใหญ่ | — | — | 10GB | 1,620 | ระบบ CMS |

---

## 7. `approvals` — ผลการอนุมัติแต่ละระดับ

| คอลัมน์ | ชนิดข้อมูล | Null ได้ | คำอธิบาย |
|---|---|---|---|
| `approval_id` | `bigint(20) UNSIGNED` | ไม่ | **PK**, auto_increment |
| `request_id` | `bigint(20) UNSIGNED` | ไม่ | **FK →** `service_requests.request_id` |
| `approver_level` | `enum('unit_head','computer_center_deputy_director','computer_center_director')` | ไม่ | ระดับผู้อนุมัติ: หัวหน้าหน่วยงาน / รองผอ.สำนักคอมฯ / ผอ.สำนักคอมฯ |
| `approver_name` | `varchar(150)` | ได้ | ชื่อผู้อนุมัติ |
| `decision` | `enum('certify_info_only','certify_and_waive_fee','acknowledge_assign_web_team','rejected')` | ได้ | ผลการพิจารณา: รับรองข้อมูลอย่างเดียว / รับรองและยกเว้นค่าบริการ / รับทราบและมอบทีมเว็บดำเนินการ / ปฏิเสธ |
| `signature_image_path` | `varchar(500)` | ได้ | path ไฟล์รูปลายเซ็นผู้อนุมัติ |
| `decision_date` | `date` | ได้ | วันที่ตัดสินใจ |

**Keys:** PRIMARY KEY (`approval_id`), KEY (`request_id`)
**Foreign Key:** `request_id` → `service_requests.request_id` **ON DELETE CASCADE**

---

## 8. `service_accounts` — บัญชีที่สร้างให้ใช้งานจริง

ตารางนี้เก็บ**ข้อมูลอ่อนไหวที่สุดในระบบ** เพราะเก็บรหัสผ่านบัญชีจริงของผู้ใช้บริการ

| คอลัมน์ | ชนิดข้อมูล | Null ได้ | ค่าเริ่มต้น | คำอธิบาย |
|---|---|---|---|---|
| `account_id` | `bigint(20) UNSIGNED` | ไม่ | auto_increment | **PK** |
| `request_id` | `bigint(20) UNSIGNED` | ไม่ | — | **FK →** `service_requests.request_id` |
| `applicant_id` | `bigint(20) UNSIGNED` | ได้ | `NULL` | **FK →** `applicants.applicant_id` |
| `username` | `varchar(100)` | ไม่ | — | ชื่อผู้ใช้ (**unique ทั้งระบบ**) |
| `password_hash` | `varchar(255)` | ไม่ | — | รหัสผ่าน (เก็บแบบ hash) |
| `account_type` | `enum('ssh','database','control_panel','ftp')` | ไม่ | `'control_panel'` | ประเภทบัญชี |
| `status` | `enum('active','disabled','expired')` | ไม่ | `'active'` | สถานะบัญชี |
| `created_by` | `varchar(150)` | ได้ | `NULL` | ชื่อเจ้าหน้าที่ผู้สร้างบัญชี (เก็บเป็นข้อความอิสระ **ไม่ผูกกับ** `users.id`) |
| `created_at` | `timestamp` | ไม่ | `current_timestamp()` | วันที่สร้างบัญชี |
| `expire_date` | `date` | ได้ | `NULL` | วันหมดอายุ |
| `last_login` | `datetime` | ได้ | `NULL` | เข้าสู่ระบบล่าสุด (ปัจจุบันยังไม่มีกลไกอัปเดตค่านี้อัตโนมัติในโค้ด) |

**Keys:**
- PRIMARY KEY (`account_id`)
- UNIQUE KEY `service_accounts_username_unique` (`username`)
- KEY (`request_id`), KEY (`applicant_id`)

**Foreign Keys:**
- `request_id` → `service_requests.request_id` **ON DELETE CASCADE** (ลบคำขอ → ลบบัญชีที่สร้างจากคำขอนั้นด้วย)
- `applicant_id` → `applicants.applicant_id` (RESTRICT)

---

## 9. `users` — บัญชีเจ้าหน้าที่ (ฝั่งแอดมิน)

**คนละกลุ่มกับ `applicants` โดยสิ้นเชิง** — ไม่มี foreign key เชื่อมกันเลย นี่คือบัญชีสำหรับล็อกอินเข้าหน้า `/admin`

| คอลัมน์ | ชนิดข้อมูล | Null ได้ | ค่าเริ่มต้น | คำอธิบาย |
|---|---|---|---|---|
| `id` | `bigint(20) UNSIGNED` | ไม่ | auto_increment | **PK** |
| `name` | `varchar(255)` | ไม่ | — | ชื่อ |
| `email` | `varchar(255)` | ไม่ | — | อีเมลล็อกอิน (**unique**) |
| `email_verified_at` | `timestamp` | ได้ | `NULL` | วันที่ยืนยันอีเมล |
| `password` | `varchar(255)` | ไม่ | — | รหัสผ่าน (hash แบบ bcrypt) |
| `is_active` | `tinyint(1)` | ไม่ | `1` | เปิด/ปิดการใช้งานบัญชี |
| `remember_token` | `varchar(100)` | ได้ | `NULL` | โทเคนจดจำการล็อกอิน |
| `created_at`, `updated_at` | `timestamp` | ได้ | `NULL` | วันที่สร้าง/แก้ไขล่าสุด |

**Keys:** PRIMARY KEY (`id`), UNIQUE KEY (`email`)

**ข้อมูลปัจจุบัน:** มีแอดมิน 2 คน (`admin@nrru.ac.th` และอีเมลของเจ้าหน้าที่อีกคน) — ทุกคนที่ล็อกอินได้จะมีสิทธิ์เท่ากันหมด (ไม่มีระบบแบ่งสิทธิ์)

---

## ตารางระบบของ Laravel  

| ตาราง | คอลัมน์สำคัญ | ใช้ทำอะไร |
|---|---|---|
| `migrations` | `id`, `migration`, `batch` | บันทึกประวัติว่า migration ไหนรันไปแล้วบ้าง (ใน batch ไหน) — Laravel ใช้ตัดสินใจว่าจะรัน migration ตัวไหนต่อตอนสั่ง `php artisan migrate` |
| `failed_jobs` | `id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at` | เก็บ background job (queue) ที่รันแล้วล้มเหลว พร้อม stack trace เพื่อ debug/สั่งรันซ้ำ |
| `password_reset_tokens` | `email`, `token`, `created_at` | โทเคนชั่วคราวสำหรับฟีเจอร์ "ลืมรหัสผ่าน" ของ `users` |

---

## ตารางที่กำลังจะถูกลบ (ไม่ได้ใช้งานแล้ว)

สร้างไว้สำหรับระบบแบ่งสิทธิ์แบบ role-based แต่ตัดสินใจไม่ใช้แล้ว เพราะใช้งานภายในทีมเล็ก ทุกคนที่ล็อกอินเข้า `users` ได้ก็ควรมีสิทธิ์เท่ากันหมดอยู่แล้ว — ปัจจุบัน**ไม่มีข้อมูลอยู่ในตารางเหล่านี้เลย** และโค้ดฝั่งแอปพลิเคชันไม่มีจุดไหนอ้างอิงตารางเหล่านี้แล้ว

| ตาราง | คอลัมน์ | เดิมออกแบบไว้ทำอะไร |
|---|---|---|
| `roles` | `role_id` (PK), `name` (unique), `label`, `description`, `is_system`, `created_at`, `updated_at` | เก็บชื่อบทบาท เช่น admin, staff |
| `permissions` | `permission_id` (PK), `name` (unique), `label`, `description`, `group`, `created_at`, `updated_at` | รายการสิทธิ์การทำงานที่กำหนดได้ |
| `permission_role` | `permission_role_id` (PK), `permission_id` (FK, CASCADE), `role_id` (FK, CASCADE), unique คู่ (`permission_id`,`role_id`) | ตารางเชื่อม many-to-many ระหว่าง role กับ permission |

มี migration พร้อมลบอยู่แล้ว (`2026_09_01_000001_drop_role_permission_tables.php`) แค่ยังไม่ได้รันบนเซิร์ฟเวอร์จริง

---

## สรุปจุดที่ควรระวัง

1. **CASCADE DELETE จาก `service_requests`:** ลบคำขอ 1 ใบ จะลบ `developers`, `domains`, `approvals`, `service_accounts` ที่ผูกอยู่ทั้งหมดทันทีแบบไม่ถามซ้ำ — ควรมีการยืนยันก่อนลบเสมอ
2. **`service_accounts.created_by` ไม่ผูกกับ `users.id`:** เก็บเป็นชื่อข้อความอิสระ ตรวจสอบย้อนหลังแบบเป็นระบบ (join) ไม่ได้ว่าแอดมินคนไหนสร้างบัญชีไหนจริง ๆ — ถ้าต้องการ audit ที่เชื่อถือได้ ควรเปลี่ยนเป็น FK ไปที่ `users.id` แทน
3. **`enabled_services` เป็น JSON string ใน longtext:** ต้อง decode ก่อนใช้งานทุกครั้ง (ไม่ใช่คอลัมน์แบบ relational ปกติ) — ถ้าต้องการ query/filter ตามบริการที่เปิดใช้บ่อยๆ อาจพิจารณาแยกเป็นตาราง pivot ในอนาคต
4. **`applicants` unique key เป็นคู่ (รหัส+อีเมล) แต่โค้ดจริง match แค่รหัส:** ถ้ามีคนกรอกรหัสประจำตัวซ้ำกันโดยไม่ได้ตั้งใจ (คนละคนจริง) ข้อมูลชื่อ-สกุลจะถูกเขียนทับกันไปมา (แก้ไปแล้วให้ใช้ข้อมูลล่าสุดเสมอ แต่ต้นตอเรื่องรหัสซ้ำยังต้องระวัง)
5. **ไม่มีระบบสิทธิ์แยกระดับ:** ทุกคนที่มีบัญชีใน `users` และล็อกอินได้ เข้าถึงทุกฟังก์ชันในหน้า `/admin` เท่ากันหมด (เป็นการตัดสินใจที่ตั้งใจไว้ เหมาะกับทีมขนาดเล็กที่ไว้ใจกัน)