(() => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (reducedMotion.matches || !('IntersectionObserver' in window) || !Element.prototype.animate) {
        return;
    }

    const targets = document.querySelectorAll([
        '#steps .section-heading',
        '#steps .step-item',
        '.policy-layout > div',
        '.policy-layout > .policy-list',
        '.cta-band',
        '.domain-owner-group',
        '.request-container .form-card',
        '.admin-app .stat-row',
        '.admin-app .dm-stats',
        '.admin-app .panel',
        '.admin-app .detail-section',
    ].join(', '));

    const activeAnimations = new Set();
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) {
                return;
            }

            observer.unobserve(entry.target);

            if (entry.target.contains(document.activeElement)) {
                return;
            }

            const stepIndex = entry.target.matches('#steps .step-item')
                ? [...entry.target.parentElement.children].indexOf(entry.target)
                : 0;

            const animation = entry.target.animate([
                { opacity: 0, transform: 'translateY(20px)' },
                { opacity: 1, transform: 'none' },
            ], {
                duration: 520,
                delay: stepIndex * 85,
                easing: 'cubic-bezier(.2, .7, .2, 1)',
                fill: 'backwards',
            });

            activeAnimations.add(animation);
            animation.addEventListener('finish', () => activeAnimations.delete(animation), { once: true });
            animation.addEventListener('cancel', () => activeAnimations.delete(animation), { once: true });
        });
    }, { threshold: .08, rootMargin: '0px 0px -5% 0px' });

    targets.forEach(target => {
        const bounds = target.getBoundingClientRect();

        if (bounds.height > 0 && bounds.top >= window.innerHeight * .85) {
            observer.observe(target);
        }
    });

    reducedMotion.addEventListener('change', event => {
        if (!event.matches) {
            return;
        }

        observer.disconnect();
        activeAnimations.forEach(animation => animation.cancel());
    });
})();
