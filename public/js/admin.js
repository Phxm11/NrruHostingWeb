(() => {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('menuBtn');
    const backdrop = document.getElementById('sidebarBackdrop');
    const mobile = window.matchMedia('(max-width: 991px)');
    function closeSidebar(restoreFocus = false) {
        sidebar.classList.remove('open');
        document.body.classList.remove('sidebar-is-open');
        backdrop.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        if (restoreFocus) toggle.focus();
    }
    toggle.addEventListener('click', () => {
        sidebar.classList.add('open');
        document.body.classList.add('sidebar-is-open');
        backdrop.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
        document.getElementById('sidebarClose').focus();
    });
    document.getElementById('sidebarClose').addEventListener('click', () => closeSidebar(true));
    backdrop.addEventListener('click', () => closeSidebar(true));
    mobile.addEventListener('change', () => closeSidebar());

    let currentMenu = null;
    let currentButton = null;
    function closeMenu(restoreFocus = false) {
        if (!currentMenu) return;
        currentMenu.classList.remove('is-open');
        currentButton.classList.remove('menu-open');
        currentButton.setAttribute('aria-expanded', 'false');
        if (restoreFocus) currentButton.focus();
        currentMenu = currentButton = null;
    }
    document.querySelectorAll('.menu-wrap > .menu-btn').forEach((button, index) => {
        const menu = button.nextElementSibling;
        menu.id = `row-actions-${index}`;
        button.setAttribute('aria-controls', menu.id);
        button.setAttribute('aria-expanded', 'false');
        button.setAttribute('aria-label', 'เมนูจัดการรายการ');
        button.addEventListener('click', () => {
            const wasOpen = currentMenu === menu;
            closeMenu();
            if (wasOpen) return;
            currentMenu = menu;
            currentButton = button;
            menu.classList.add('is-open');
            button.classList.add('menu-open');
            button.setAttribute('aria-expanded', 'true');
            const rect = button.getBoundingClientRect();
            const height = menu.getBoundingClientRect().height;
            menu.style.left = `${Math.max(8, Math.min(rect.right - 220, innerWidth - 228))}px`;
            menu.style.top = `${Math.max(8, rect.bottom + height + 8 > innerHeight ? rect.top - height - 6 : rect.bottom + 6)}px`;
            menu.querySelector('a, button')?.focus();
        });
    });
    document.addEventListener('click', event => {
        if (currentMenu && !currentMenu.contains(event.target) && !currentButton.contains(event.target)) closeMenu();
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            closeMenu(true);
            if (sidebar.classList.contains('open')) closeSidebar(true);
        }
        if (event.key === 'Tab' && mobile.matches && sidebar.classList.contains('open')) {
            const items = [...sidebar.querySelectorAll('a, button')].filter(el => el.getClientRects().length);
            const first = items[0], last = items[items.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
    });
    document.addEventListener('focusin', event => {
        if (currentMenu && !currentMenu.contains(event.target) && event.target !== currentButton) closeMenu();
    });
    window.addEventListener('resize', () => closeMenu());
    document.addEventListener('scroll', () => closeMenu(), true);

    document.querySelectorAll('.table-responsive, .acc-table-wrap').forEach(region => {
        region.tabIndex = 0;
        region.setAttribute('role', 'region');
        region.setAttribute('aria-label', 'ตารางข้อมูล เลื่อนแนวนอนเพื่อดูข้อมูลทั้งหมด');
    });
})();
