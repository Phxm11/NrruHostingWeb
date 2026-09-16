<footer>
    <div class="container d-flex flex-wrap justify-content-between gap-3">
        <div>
            <div class="f-brand mb-1">สำนักคอมพิวเตอร์ มหาวิทยาลัยราชภัฏนครราชสีมา</div>
            340 ถ.สุรนารายณ์ ต.ในเมือง อ.เมือง จ.นครราชสีมา 30000
        </div>
        <div>
            <a href="{{ url('/service-requests/create') }}">ยื่นคำขอใช้บริการ</a> &nbsp;|&nbsp;
            <a href="{{ route('domains.index') }}">ตรวจสอบโดเมน</a> &nbsp;|&nbsp;
            <a href="{{ url('/admin/requests') }}">สำหรับเจ้าหน้าที่</a>
        </div>
    </div>
</footer>
