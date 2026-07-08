<?php

namespace Database\Seeders;

use App\Models\DepartmentCode;
use App\Models\ResourcePlan;
use Illuminate\Database\Seeder;

class DcHostingSeeder extends Seeder
{
    public function run(): void
    {
        ResourcePlan::insert([
            ['service_type' => 'virtual_server', 'size_label' => 'ขนาดเล็ก', 'cpu_vcpu' => 3, 'ram_gb' => 4, 'storage_gb' => 10, 'fee_per_year' => 5000, 'suitable_for' => 'ระบบเว็บไซต์/ระบบงานทั่วไป'],
            ['service_type' => 'virtual_server', 'size_label' => 'ขนาดกลาง', 'cpu_vcpu' => 3, 'ram_gb' => 4, 'storage_gb' => 20, 'fee_per_year' => 7500, 'suitable_for' => 'ระบบฐานข้อมูลขนาดเล็ก-กลาง'],
            ['service_type' => 'virtual_server', 'size_label' => 'ขนาดใหญ่', 'cpu_vcpu' => 4, 'ram_gb' => 8, 'storage_gb' => 50, 'fee_per_year' => 12000, 'suitable_for' => 'ระบบงานที่มีผู้ใช้งานมากขึ้น'],
            ['service_type' => 'web_hosting', 'size_label' => 'ขนาดเล็ก', 'cpu_vcpu' => null, 'ram_gb' => null, 'storage_gb' => 5, 'fee_per_year' => 1200, 'suitable_for' => 'แบบไม่ต้องการฐานข้อมูล'],
            ['service_type' => 'web_hosting', 'size_label' => 'ขนาดใหญ่', 'cpu_vcpu' => null, 'ram_gb' => null, 'storage_gb' => 10, 'fee_per_year' => 1620, 'suitable_for' => 'ระบบบริหารจัดการเนื้อหา (CMS)'],
        ]);

        DepartmentCode::insert([
            ['code' => '-edu', 'department_name' => 'คณะครุศาสตร์'],
            ['code' => '-fms', 'department_name' => 'คณะวิทยาการจัดการ'],
            ['code' => '-nurse', 'department_name' => 'คณะพยาบาลศาสตร์'],
            ['code' => '-ph', 'department_name' => 'คณะสาธารณสุขศาสตร์'],
            ['code' => '-sci', 'department_name' => 'คณะวิทยาศาสตร์และเทคโนโลยี'],
            ['code' => '-human', 'department_name' => 'คณะมนุษยศาสตร์และสังคมศาสตร์'],
            ['code' => '-fit', 'department_name' => 'คณะเทคโนโลยีอุตสาหกรรม'],
        ]);
    }
}
