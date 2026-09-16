(() => {

    const navbar = document.getElementById('navbar');
    const toggle = document.getElementById('mobileToggle');
    const menu = document.getElementById('mobileMenu');
    const desktop = window.matchMedia('(min-width: 1200px)');

    function setMenu(open, restoreFocus = false) {
        menu.hidden = !open;
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'ปิดเมนูหลัก' : 'เปิดเมนูหลัก');
        if (restoreFocus) toggle.focus();
    }

    toggle.addEventListener('click', () => setMenu(menu.hidden));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !menu.hidden) setMenu(false, true);
    });
    document.addEventListener('click', event => {
        if (!menu.hidden && !navbar.contains(event.target)) setMenu(false);
    });
    navbar.addEventListener('focusout', () => {
        requestAnimationFrame(() => {
            if (!menu.hidden && !navbar.contains(document.activeElement)) setMenu(false);
        });
    });
    desktop.addEventListener('change', () => {
        const focusWasInMenu = menu.contains(document.activeElement);
        setMenu(false);
        if (desktop.matches && focusWasInMenu) navbar.querySelector('.brand').focus();
    });
    menu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => setMenu(false)));

    const sectionLinks = [...navbar.querySelectorAll('a[href^="#"]')];
    const sections = [...new Set(sectionLinks.map(link => document.querySelector(link.getAttribute('href'))))];
    function updateNavigation() {
        navbar.classList.toggle('scrolled', window.scrollY > 8);
        let current = 'top';
        const position = navbar.getBoundingClientRect().bottom + 32;
        sections.forEach(section => {
            if (section.getBoundingClientRect().top <= position) current = section.id;
        });
        sectionLinks.forEach(link => {
            if (link.getAttribute('href') === '#' + current) link.setAttribute('aria-current', 'location');
            else link.removeAttribute('aria-current');
        });
    }
    let scrollPending = false;
    window.addEventListener('scroll', () => {
        if (scrollPending) return;
        scrollPending = true;
        requestAnimationFrame(() => { updateNavigation(); scrollPending = false; });
    }, { passive: true });
    window.addEventListener('resize', updateNavigation);
    sectionLinks.forEach(link => link.addEventListener('click', event => {
        event.preventDefault();
        setMenu(false);
        const target = document.querySelector(link.getAttribute('href'));
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        target.setAttribute('tabindex', '-1');
        target.focus({ preventScroll: true });
        target.scrollIntoView({ behavior: reduceMotion ? 'instant' : 'smooth', block: 'start' });
        history.replaceState(null, '', link.getAttribute('href'));
        updateNavigation();
    }));
    updateNavigation();

})();
