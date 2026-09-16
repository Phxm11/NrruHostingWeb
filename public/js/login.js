(() => {
    const password = document.getElementById('password');
    const toggle = document.getElementById('password-toggle');
    if (!password || !toggle) return;

    toggle.hidden = false;
    toggle.addEventListener('click', () => {
        const visible = password.type === 'password';
        password.type = visible ? 'text' : 'password';
        toggle.classList.toggle('is-visible', visible);
        toggle.setAttribute('aria-label', visible ? 'ซ่อนรหัสผ่าน' : 'แสดงรหัสผ่าน');
    });
})();
