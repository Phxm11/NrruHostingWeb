{{--
    Shared "confirm popup" component.
    Replaces native window.confirm() dialogs. Any <form data-confirm="ข้อความ...">
    on the page is automatically intercepted: submit is paused, a styled popup
    asks the user to confirm, and the form only submits if they click "ยืนยัน".
    Include once per page (e.g. in admin/layout.blade.php).
--}}
<div class="site-confirm-overlay" id="siteConfirmOverlay">
    <div class="site-confirm-card" role="alertdialog" aria-modal="true" aria-labelledby="siteConfirmTitle">
        <div class="site-confirm-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.6 1.9 18a2 2 0 0 0 1.7 3h16.8a2 2 0 0 0 1.7-3L14.7 3.6a2 2 0 0 0-3.4 0Z"/></svg>
        </div>
        <div class="site-confirm-title" id="siteConfirmTitle">ยืนยันการทำรายการ</div>
        <p class="site-confirm-text" id="siteConfirmText"></p>
        <div class="site-confirm-actions">
            <button type="button" class="site-confirm-btn site-confirm-btn-danger" id="siteConfirmOk">ยืนยัน</button>
            <button type="button" class="site-confirm-btn site-confirm-btn-ghost" id="siteConfirmCancel">ยกเลิก</button>
        </div>
    </div>
</div>

<style>
    .site-confirm-overlay {
        position: fixed; inset: 0; z-index: 2100;
        background: rgba(20, 30, 18, .45);
        display: flex; align-items: center; justify-content: center;
        padding: 20px;
        opacity: 0; visibility: hidden;
        transition: opacity .18s ease, visibility 0s linear .18s;
    }
    .site-confirm-overlay.show {
        opacity: 1; visibility: visible;
        transition: opacity .18s ease, visibility 0s;
    }
    .site-confirm-card {
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
    .site-confirm-overlay.show .site-confirm-card { transform: translateY(0) scale(1); opacity: 1; }
    .site-confirm-icon {
        width: 54px; height: 54px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 14px;
        background: #f6e1d8; color: #ae4830;
    }
    .site-confirm-title {
        font-family: 'Kanit', sans-serif; font-weight: 600; font-size: 17.5px;
        color: #15231a; margin-bottom: 6px;
    }
    .site-confirm-text {
        font-size: 14px; color: #5c6659; margin: 0 0 20px; line-height: 1.6;
    }
    .site-confirm-actions { display: flex; gap: 10px; }
    .site-confirm-btn {
        flex: 1;
        border: none; border-radius: 12px; padding: 11px;
        font-family: 'Sarabun', sans-serif; font-size: 14.5px; font-weight: 600;
        cursor: pointer; transition: filter .15s ease, transform .15s ease;
    }
    .site-confirm-btn:hover { filter: brightness(1.05); transform: translateY(-1px); }
    .site-confirm-btn-ghost { background: #eef0e8; color: #244430; }
    .site-confirm-btn-danger { background: linear-gradient(135deg, #c65a3c, #ae4830); color: #fff; }
</style>

<script>
    (function () {
        function initSiteConfirm() {
            var overlay = document.getElementById('siteConfirmOverlay');
            if (!overlay) return;

            var textEl    = document.getElementById('siteConfirmText');
            var okBtn     = document.getElementById('siteConfirmOk');
            var cancelBtn = document.getElementById('siteConfirmCancel');
            var pendingForm = null;

            function open(message, form) {
                pendingForm = form;
                textEl.textContent = message;
                overlay.classList.add('show');
            }
            function close() {
                overlay.classList.remove('show');
                pendingForm = null;
            }

            cancelBtn.addEventListener('click', close);
            overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });

            okBtn.addEventListener('click', function () {
                var form = pendingForm;
                close();
                // HTMLFormElement.submit() does NOT re-dispatch the 'submit' event,
                // so this safely bypasses the listener below without looping.
                if (form) form.submit();
            });

            document.querySelectorAll('form[data-confirm]').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    open(form.dataset.confirm, form);
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSiteConfirm);
        } else {
            initSiteConfirm();
        }
    })();
</script>
