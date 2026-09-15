<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>รายงานผู้บริหาร {{ $filters['start_date'] }} ถึง {{ $filters['end_date'] }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600&family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('admin.reports.styles')
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f4f3ea; font-family: 'Sarabun', sans-serif; line-height: 1.6; }
        h2, h3 { font-family: 'Kanit', sans-serif; font-weight: 500; }
        .print-toolbar { max-width: 1000px; margin: 20px auto; padding: 0 20px; display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
        .print-toolbar button, .print-toolbar a { font: inherit; padding: 10px 16px; border-radius: 8px; border: 1px solid #1a3323; cursor: pointer; text-decoration: none; }
        .print-toolbar button { background: #1a3323; color: white; }
        .print-toolbar a { background: white; color: #1a3323; }
        .print-toolbar p { width: 100%; margin: 0; }
        .print-toolbar :focus-visible { outline: 3px solid #a6740e; outline-offset: 3px; }
        .print-page { background: #fff; max-width: 1000px; padding: 40px; margin: 0 auto 30px; }
        @media (max-width: 700px) { .print-page { padding: 20px; } }
        @page { size: A4 portrait; margin: 14mm; }
        @media print {
            body { background: #fff; font-size: 11pt; }
            .print-toolbar { display: none; }
            .print-page { margin: 0; padding: 0; max-width: none; }
            .report-heading h2 { font-size: 19pt; }
            .report-grid, .report-analysis { display: block; }
            .report-grid > section { margin-bottom: 18px; }
            .report-kpis { grid-template-columns: repeat(4, 1fr); gap: 8px; break-inside: avoid; }
            .report-kpi { padding: 12px; border-bottom: 0; }
            .report-kpi dd { font-size: 23pt; }
            .report-kpi dt, .report-table, .report-meta, .report-note { font-size: 10pt; }
            .report-table-wrap { overflow: visible; border-radius: 0; }
            .report-table th, .report-table td { padding: 7px 10px; }
            thead { display: table-header-group; }
            tr, .report-notice { break-inside: avoid; }
            h3 { break-after: avoid; }
            .report-section { margin-bottom: 18px; }
            .report-panel { padding: 12px; margin-bottom: 14px; }
            .report .report-table { min-width: 0; }
            .report .report-table th, .report .report-table td { padding: 6px 9px; font-size: 9.5pt; }
            .report .report-table .report-unit { min-width: 0; }
            .report-followup { margin-top: 12px; break-inside: avoid; padding: 12px; }
            .report-renewal { padding: 12px; break-inside: avoid; }
            .report-document-icon { display: none; }
            .report-bar-track { print-color-adjust: exact; }
            .report-bar { background: #1a3323; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="print-toolbar">
        <button type="button" onclick="window.print()"><x-admin.icon name="print" size="18" /> พิมพ์ / บันทึก PDF</button>
        <a href="{{ route('admin.reports.index', $filters) }}"><x-admin.icon name="back" size="18" /> กลับหน้ารายงาน</a>
        <p>เลือก “บันทึกเป็น PDF” ในหน้าต่างพิมพ์เพื่อจัดเก็บรายงานและส่งให้ผู้บริหาร</p>
    </div>
    <main class="report print-page">
        @include('admin.reports.summary')
    </main>
</body>
</html>
