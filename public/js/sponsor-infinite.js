document.addEventListener('DOMContentLoaded', function () {
    const track = document.querySelector('[data-ragen-carousel="sponsor"]');

    if (!track) return;

    const originalCards = Array.from(track.children).filter(function (element) {
        return element.classList.contains('sponsor-card');
    });

    if (originalCards.length < 2) return;

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const controls = document.querySelectorAll(
        '[data-ragen-prev="sponsor"], [data-ragen-next="sponsor"]'
    );

    controls.forEach(function (button) {
        button.style.display = 'none';
    });

    track.style.justifyContent = 'flex-start';
    track.style.flexWrap = 'nowrap';
    track.style.scrollSnapType = 'none';
    track.style.scrollBehavior = 'auto';

    originalCards.forEach(function (card) {
        const clone = card.cloneNode(true);
        clone.setAttribute('aria-hidden', 'true');
        clone.setAttribute('tabindex', '-1');

        clone.querySelectorAll('a, button, input, select, textarea').forEach(function (element) {
            element.setAttribute('tabindex', '-1');
        });

        track.appendChild(clone);
    });

    let paused = false;
    let previousTime = null;
    let animationId = null;

    function getLoopDistance() {
        const first = originalCards[0];
        const firstClone = track.children[originalCards.length];

        if (!first || !firstClone) return 0;

        return firstClone.offsetLeft - first.offsetLeft;
    }

    function animate(time) {
        if (previousTime === null) previousTime = time;

        const elapsed = Math.min(time - previousTime, 50);
        previousTime = time;

        if (!paused && !reducedMotion.matches) {
            const distance = getLoopDistance();

            if (distance > 0) {
                track.scrollLeft += elapsed * 0.035;

                if (track.scrollLeft >= distance) {
                    track.scrollLeft -= distance;
                }
            }
        }

        animationId = requestAnimationFrame(animate);
    }

    track.addEventListener('mouseenter', function () {
        paused = true;
    });

    track.addEventListener('mouseleave', function () {
        paused = false;
    });

    track.addEventListener('focusin', function () {
        paused = true;
    });

    track.addEventListener('focusout', function (event) {
        if (!track.contains(event.relatedTarget)) {
            paused = false;
        }
    });

    document.addEventListener('visibilitychange', function () {
        previousTime = null;
    });

    animationId = requestAnimationFrame(animate);
});
