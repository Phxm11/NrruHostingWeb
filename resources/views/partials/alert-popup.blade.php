{{--
    Shared "alert popup" component.
    Renders session('success') / session('status') / session('error') / validation
    errors / one-time account credentials as a centered popup dialog instead of an
    inline banner. Drop `@include('partials.alert-popup')` anywhere in the page body.
--}}
@php
    $__popupType    = null;
    $__popupTitle   = null;
    $__popupMessage = null;
    $__popupList    = [];

    if (session('new_username')) {
        $__popupType  = 'credential';
        $__popupTitle = 'สร้างบัญชีสำเร็จ';
    } elseif (session('success')) {
        $__popupType    = 'success';
        $__popupTitle   = 'สำเร็จ';
        $__popupMessage = session('success');
    } elseif (session('status')) {
        $__popupType    = 'success';
        $__popupTitle   = 'แจ้งเตือน';
        $__popupMessage = session('status');
    } elseif (session('error')) {
        $__popupType    = 'error';
        $__popupTitle   = 'เกิดข้อผิดพลาด';
        $__popupMessage = session('error');
    } elseif ($errors->any()) {
        $__popupType  = 'error';
        $__popupTitle = $errorTitle ?? 'ไม่สามารถดำเนินการได้';
        $__popupList  = $errors->all();
    }
@endphp

@if ($__popupType)
    <div class="site-popup-overlay" id="sitePopupOverlay">
        <div class="site-popup-card site-popup-{{ $__popupType }}" role="dialog" aria-modal="true" aria-labelledby="sitePopupTitle">
            <button type="button" class="site-popup-close" id="sitePopupClose" aria-label="ปิด">&times;</button>

            <div class="site-popup-icon">
                @if ($__popupType === 'success')
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg>
                @elseif ($__popupType === 'credential')
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2 3 6v6c0 5 4 9 9 10 5-1 9-5 9-10V6l-9-4Z"/><path d="M9 12l2 2 4-4"/></svg>
                @else
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><path d="M12 8v5M12 16h.01"/></svg>
                @endif
            </div>

            <div class="site-popup-title" id="sitePopupTitle">{{ $__popupTitle }}</div>

            @if ($__popupType === 'credential')
                <p class="site-popup-text">คัดลอกและส่งข้อมูลนี้ให้ผู้ใช้บริการทันที ระบบจะไม่แสดงรหัสผ่านนี้อีก</p>
                <div class="site-popup-credentials">
                    <div class="site-popup-cred-row"><span>Username</span><code id="popupCredUsername">{{ session('new_username') }}</code></div>
                    <div class="site-popup-cred-row"><span>Password</span><code id="popupCredPassword">{{ session('new_password') }}</code></div>
                </div>
                <button type="button" class="site-popup-btn" id="sitePopupCopyBtn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
                    คัดลอกทั้งหมด
                </button>
            @elseif (count($__popupList))
                <ul class="site-popup-list">
                    @foreach ($__popupList as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="site-popup-btn site-popup-btn-ghost" id="sitePopupOkBtn">ตกลง</button>
            @else
                <p class="site-popup-text">{{ $__popupMessage }}</p>
                <button type="button" class="site-popup-btn site-popup-btn-ghost" id="sitePopupOkBtn">ตกลง</button>
            @endif
        </div>
    </div>

    @once
        <style>
            .site-popup-overlay {
                position: fixed; inset: 0; z-index: 2000;
                background: rgba(20, 30, 18, .45);
                display: flex; align-items: center; justify-content: center;
                padding: 20px;
                opacity: 0; transition: opacity .18s ease;
            }
            .site-popup-overlay.show { opacity: 1; }
            .site-popup-card {
                position: relative;
                width: 100%; max-width: 380px;
                background: #fff;
                border-radius: 20px;
                padding: 28px 26px 24px;
                text-align: center;
                box-shadow: 0 32px 70px -28px rgba(21,35,26,.45);
                transform: translateY(10px) scale(.97); opacity: 0;
                transition: transform .2s ease, opacity .2s ease;
            }
            .site-popup-overlay.show .site-popup-card { transform: translateY(0) scale(1); opacity: 1; }
            .site-popup-close {
                position: absolute; top: 10px; right: 12px;
                background: none; border: none; font-size: 20px; line-height: 1;
                color: #9aa596; cursor: pointer; padding: 6px;
            }
            .site-popup-close:hover { color: #15231a; }
            .site-popup-icon {
                width: 54px; height: 54px; border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                margin: 0 auto 14px;
            }
            .site-popup-success .site-popup-icon { background: #e8f0dc; color: #244430; }
            .site-popup-error .site-popup-icon   { background: #f6e1d8; color: #ae4830; }
            .site-popup-credential .site-popup-icon { background: #faf0d3; color: #8a6408; }
            .site-popup-title {
                font-family: 'Kanit', sans-serif; font-weight: 600; font-size: 17.5px;
                color: #15231a; margin-bottom: 6px;
            }
            .site-popup-text {
                font-size: 14px; color: #5c6659; margin: 0 0 18px; line-height: 1.6;
            }
            .site-popup-list {
                text-align: left; font-size: 13.5px; color: #ae4830;
                background: #f6e1d8; border-radius: 12px; padding: 12px 16px 12px 30px;
                margin: 0 0 18px;
            }
            .site-popup-list li { margin-bottom: 4px; }
            .site-popup-credentials {
                text-align: left; background: #faf0d3; border: 1px solid #eecf88;
                border-radius: 12px; padding: 12px 14px; margin-bottom: 14px;
            }
            .site-popup-cred-row {
                display: flex; align-items: center; justify-content: space-between; gap: 10px;
                font-size: 13.5px; padding: 5px 0;
            }
            .site-popup-cred-row span { color: #6b5a2e; font-weight: 600; }
            .site-popup-cred-row code {
                background: #fff; border: 1px solid #eecf88; padding: 2px 8px;
                border-radius: 6px; font-weight: 700; color: #6b4c05;
            }
            .site-popup-btn {
                display: inline-flex; align-items: center; justify-content: center; gap: 8px;
                width: 100%; border: none; border-radius: 12px; padding: 11px;
                font-family: 'Sarabun', sans-serif; font-size: 14.5px; font-weight: 600;
                cursor: pointer; transition: filter .15s ease, transform .15s ease;
                background: linear-gradient(135deg, #d79a2c, #a6740e); color: #2c1e05;
            }
            .site-popup-btn:hover { filter: brightness(1.05); transform: translateY(-1px); }
            .site-popup-btn-ghost {
                background: #244430; color: #eef2e6;
            }
        </style>

        <script>
            (function () {
                function initSitePopup() {
                    var overlay = document.getElementById('sitePopupOverlay');
                    if (!overlay) return;

                    requestAnimationFrame(function () { overlay.classList.add('show'); });

                    function close() {
                        overlay.classList.remove('show');
                        setTimeout(function () { overlay.remove(); }, 200);
                    }

                    var closeBtn = document.getElementById('sitePopupClose');
                    if (closeBtn) closeBtn.addEventListener('click', close);

                    var okBtn = document.getElementById('sitePopupOkBtn');
                    if (okBtn) okBtn.addEventListener('click', close);

                    overlay.addEventListener('click', function (e) {
                        if (e.target === overlay) close();
                    });

                    document.addEventListener('keydown', function onKey(e) {
                        if (e.key === 'Escape') { close(); document.removeEventListener('keydown', onKey); }
                    });

                    if (overlay.querySelector('.site-popup-success')) {
                        setTimeout(close, 4000);
                    }

                    var copyBtn = document.getElementById('sitePopupCopyBtn');
                    if (copyBtn) {
                        copyBtn.addEventListener('click', function () {
                            var u = document.getElementById('popupCredUsername').textContent;
                            var p = document.getElementById('popupCredPassword').textContent;
                            var text = 'Username: ' + u + '\nPassword: ' + p;
                            var done = function () {
                                var label = copyBtn.querySelector('svg') ? copyBtn.lastChild : copyBtn;
                                copyBtn.innerHTML = 'คัดลอกแล้ว ✓';
                                setTimeout(function () {
                                    copyBtn.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg> คัดลอกทั้งหมด';
                                }, 1800);
                            };
                            if (navigator.clipboard && navigator.clipboard.writeText) {
                                navigator.clipboard.writeText(text).then(done);
                            } else {
                                done();
                            }
                        });
                    }
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initSitePopup);
                } else {
                    initSitePopup();
                }
            })();
        </script>
    @endonce
@endif
