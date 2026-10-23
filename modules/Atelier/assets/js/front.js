/* Al Aosaf: Atelier homepage (no dependencies) */
(function () {
    'use strict';
    document.documentElement.classList.add('at-js');
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var bn = function (s) { return String(s).replace(/\d/g, function (d) { return '০১২৩৪৫৬৭৮৯'[d]; }); };
    var pad = function (n) { return n < 10 ? '0' + n : String(n); };

    function ready(fn) { if (document.readyState !== 'loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }

    /* ---------- announcement ribbon ---------- */
    function announce(el) {
        var items = el.querySelectorAll('.at-announce__item');
        if (items.length < 2) return;
        var i = 0;
        setInterval(function () {
            var cur = items[i];
            i = (i + 1) % items.length;
            cur.classList.remove('is-on'); cur.classList.add('is-out');
            items[i].classList.remove('is-out'); items[i].classList.add('is-on');
            setTimeout(function () { cur.classList.remove('is-out'); }, 700);
        }, 4200);
    }

    /* ---------- hero ---------- */
    function hero(el) {
        var slides = el.querySelectorAll('.at-hero__slide');
        var bars = el.querySelectorAll('.at-hero__dot');
        var now = el.querySelector('[data-hero-now]');
        var n = slides.length, cur = 0, timer = null, paused = false;
        var ms = Math.max(3, parseFloat(el.getAttribute('data-interval')) || 6) * 1000;
        el.style.setProperty('--int', (ms / 1000) + 's');
        // Start the reveal only once the first photo is there, so it never wipes in an empty frame.
        var first = slides[0] && slides[0].querySelector('img');
        var ready = function () { el.classList.add('is-ready'); };
        if (!first || (first.complete && first.naturalWidth)) ready();
        else { first.addEventListener('load', ready); first.addEventListener('error', ready); setTimeout(ready, 4000); }
        if (n < 2) return;

        function show(i) {
            i = (i + n) % n;
            if (i === cur) return;
            var prev = slides[cur];
            prev.classList.remove('is-on'); prev.classList.add('is-leaving'); prev.setAttribute('aria-hidden', 'true');
            prev.querySelectorAll('a').forEach(function (a) { a.setAttribute('tabindex', '-1'); });
            setTimeout(function () { prev.classList.remove('is-leaving'); }, 1300);
            var next = slides[i];
            next.classList.add('is-on'); next.removeAttribute('aria-hidden');
            next.querySelectorAll('a').forEach(function (a) { a.removeAttribute('tabindex'); });
            next.querySelectorAll('img[loading="lazy"]').forEach(function (img) { img.loading = 'eager'; });
            bars.forEach(function (b) { b.classList.remove('is-on'); b.removeAttribute('aria-current'); });
            void el.offsetWidth; // restart the progress animation
            if (bars[i]) { bars[i].classList.add('is-on'); bars[i].setAttribute('aria-current', 'true'); }
            if (now) now.textContent = bn(pad(i + 1));
            cur = i;
            schedule();
        }
        function schedule() {
            clearTimeout(timer);
            if (!paused && !reduce) timer = setTimeout(function () { show(cur + 1); }, ms);
        }
        // Preload the next slide image once the page has settled.
        window.addEventListener('load', function () {
            slides.forEach(function (s) { s.querySelectorAll('img[loading="lazy"]').forEach(function (img) { img.loading = 'eager'; }); });
        });
        el.addEventListener('click', function (e) {
            var t = e.target.closest('[data-go],[data-prev],[data-next]');
            if (!t) return;
            if (t.hasAttribute('data-prev')) show(cur - 1);
            else if (t.hasAttribute('data-next')) show(cur + 1);
            else show(parseInt(t.getAttribute('data-go'), 10));
        });
        var media = window.matchMedia('(hover: hover)');
        if (media.matches) {
            el.addEventListener('mouseenter', function () { paused = true; el.classList.add('is-paused'); clearTimeout(timer); });
            el.addEventListener('mouseleave', function () { paused = false; el.classList.remove('is-paused'); show(cur + 1); });
        }
        // Swipe on touch screens.
        var x0 = null, y0 = null;
        el.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; y0 = e.touches[0].clientY; }, { passive: true });
        el.addEventListener('touchend', function (e) {
            if (x0 === null) return;
            var dx = e.changedTouches[0].clientX - x0, dy = e.changedTouches[0].clientY - y0;
            if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)) show(cur + (dx < 0 ? 1 : -1));
            x0 = null;
        });
        document.addEventListener('visibilitychange', function () { if (document.hidden) clearTimeout(timer); else schedule(); });
        if (bars[0]) bars[0].classList.add('is-on');
        // The first slide's timer starts with the reveal, not before the photo has loaded.
        if (el.classList.contains('is-ready')) schedule();
        else new MutationObserver(function (m, o) { if (el.classList.contains('is-ready')) { o.disconnect(); schedule(); } }).observe(el, { attributes: true, attributeFilter: ['class'] });
    }

    /* ---------- rails (product + review carousels) ---------- */
    function rail(wrap) {
        var track = wrap.querySelector('[data-rail]');
        if (!track) return;
        var bar = wrap.querySelector('[data-rail-bar]');
        var prev = wrap.querySelector('[data-rail-prev]'), next = wrap.querySelector('[data-rail-next]');
        function step() { var c = track.firstElementChild; return c ? c.getBoundingClientRect().width + parseFloat(getComputedStyle(track).columnGap || 0) : 300; }
        function update() {
            var max = track.scrollWidth - track.clientWidth;
            var ratio = track.scrollWidth ? track.clientWidth / track.scrollWidth : 1;
            if (bar) {
                bar.style.width = Math.min(100, ratio * 100) + '%';
                bar.style.transform = 'translateX(' + (max > 0 ? (track.scrollLeft / max) * (1 / ratio - 1) * 100 : 0) + '%)';
            }
            if (prev) prev.disabled = track.scrollLeft < 4;
            if (next) next.disabled = track.scrollLeft > max - 4;
            if (bar) bar.parentNode.style.visibility = max > 4 ? '' : 'hidden';
        }
        if (prev) prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: reduce ? 'auto' : 'smooth' }); });
        if (next) next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: reduce ? 'auto' : 'smooth' }); });
        track.addEventListener('scroll', function () { window.requestAnimationFrame(update); }, { passive: true });
        window.addEventListener('resize', update);
        update();

        // Drag with the mouse on desktop.
        var down = false, sx = 0, sl = 0, moved = false;
        track.addEventListener('pointerdown', function (e) {
            if (e.pointerType !== 'mouse' || e.target.closest('button, .at-card__actions, .at-card__thumbs')) return;
            down = true; moved = false; sx = e.clientX; sl = track.scrollLeft;
        });
        window.addEventListener('pointermove', function (e) {
            if (!down) return;
            var dx = e.clientX - sx;
            if (!moved && Math.abs(dx) > 6) { moved = true; track.classList.add('is-drag'); }
            if (moved) track.scrollLeft = sl - dx;
        });
        window.addEventListener('pointerup', function () {
            if (!down) return;
            down = false;
            if (moved) {
                track.classList.remove('is-drag');
                var w = step(); track.scrollTo({ left: Math.round(track.scrollLeft / w) * w, behavior: 'smooth' });
            }
        });
        track.addEventListener('click', function (e) { if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; } }, true);
        track.addEventListener('dragstart', function (e) { e.preventDefault(); });
    }

    /* ---------- tabs ---------- */
    function tabs(el) {
        var btns = el.querySelectorAll('[data-tab]');
        var panels = el.querySelectorAll('.at-tabpanel');
        function go(i) {
            btns.forEach(function (b, k) { b.classList.toggle('is-on', k === i); b.setAttribute('aria-selected', k === i ? 'true' : 'false'); b.tabIndex = k === i ? 0 : -1; });
            panels.forEach(function (p, k) { p.hidden = k !== i; p.classList.toggle('is-on', k === i); });
        }
        btns.forEach(function (b, k) {
            b.addEventListener('click', function () { go(k); });
            b.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                    var j = (k + (e.key === 'ArrowRight' ? 1 : -1) + btns.length) % btns.length;
                    go(j); btns[j].focus();
                }
            });
        });
    }

    /* ---------- countdown (only rendered for a real end date) ---------- */
    function countdown(el) {
        var end = parseInt(el.getAttribute('data-ends'), 10) * 1000;
        var u = {}; el.querySelectorAll('[data-u]').forEach(function (b) { u[b.getAttribute('data-u')] = b; });
        function tick() {
            var s = Math.max(0, Math.floor((end - Date.now()) / 1000));
            if (u.d) u.d.textContent = bn(pad(Math.floor(s / 86400)));
            if (u.h) u.h.textContent = bn(pad(Math.floor(s % 86400 / 3600)));
            if (u.m) u.m.textContent = bn(pad(Math.floor(s % 3600 / 60)));
            if (u.s) u.s.textContent = bn(pad(s % 60));
            if (s <= 0) { el.style.display = 'none'; return; }
            setTimeout(tick, 1000);
        }
        tick();
    }

    /* ---------- shop the look ---------- */
    function look(el) {
        var spots = el.querySelectorAll('[data-spot]');
        function open(i, on) {
            spots.forEach(function (s, k) {
                var o = on && k === i;
                s.classList.toggle('is-open', o);
                s.querySelector('.at-spot__dot').setAttribute('aria-expanded', o ? 'true' : 'false');
            });
            el.querySelectorAll('[data-spot-link]').forEach(function (a) { a.classList.toggle('is-hot', on && +a.getAttribute('data-spot-link') === i); });
        }
        spots.forEach(function (s, i) {
            s.querySelector('.at-spot__dot').addEventListener('click', function (e) {
                e.stopPropagation();
                if (window.matchMedia('(max-width: 560px)').matches) {
                    // Small screens: no popover, highlight + scroll to the list entry instead.
                    var link = el.querySelector('[data-spot-link="' + i + '"]');
                    open(i, true);
                    if (link) link.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'center' });
                    return;
                }
                open(i, !s.classList.contains('is-open'));
            });
        });
        el.querySelectorAll('[data-spot-link]').forEach(function (a) {
            var i = +a.getAttribute('data-spot-link');
            a.addEventListener('mouseenter', function () { open(i, true); });
            a.addEventListener('mouseleave', function () { open(i, false); });
        });
        document.addEventListener('click', function (e) { if (!e.target.closest('.at-spot')) open(-1, false); });
    }

    /* ---------- product card film dock ---------- */
    function cards(root) {
        var hover = window.matchMedia('(hover: hover)').matches;
        function show(t) {
            var card = t.closest('[data-card]'), img = card && card.querySelector('[data-main]');
            if (!img || t.classList.contains('is-on')) return;
            card.querySelectorAll('.at-card__thumb').forEach(function (b) { b.classList.toggle('is-on', b === t); });
            var src = t.getAttribute('data-src'), set = t.getAttribute('data-srcset');
            var pre = new Image();
            // "auto" sizes only works for images in the page; give the preloader the real card width.
            if (set) { pre.sizes = Math.ceil(img.getBoundingClientRect().width || 300) + 'px'; pre.srcset = set; }
            pre.src = src;
            img.classList.add('is-swapping');
            var swap = function () {
                if (!t.classList.contains('is-on')) return; // another photo was chosen meanwhile
                if (set) img.srcset = set; else img.removeAttribute('srcset');
                img.src = src;
                img.classList.remove('is-swapping');
            };
            if (pre.decode) pre.decode().then(swap, swap); else pre.onload = swap;
        }
        root.addEventListener('click', function (e) {
            var t = e.target.closest('.at-card__thumb');
            if (t) { e.preventDefault(); show(t); }
        });
        if (!hover) return;
        root.addEventListener('mouseover', function (e) {
            var t = e.target.closest('.at-card__thumb');
            if (t) show(t);
        });
        root.querySelectorAll('[data-card]').forEach(function (card) {
            card.addEventListener('mouseleave', function () {
                var first = card.querySelector('.at-card__thumb');
                if (first) show(first);
            });
        });
    }

    /* ---------- reveal on scroll ---------- */
    function reveal(root) {
        var els = root.querySelectorAll('[data-reveal]');
        if (reduce || !('IntersectionObserver' in window)) { els.forEach(function (e) { e.classList.add('is-in'); }); return; }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (!en.isIntersecting) return;
                var el = en.target, sibs = el.parentNode ? [].filter.call(el.parentNode.children, function (c) { return c.hasAttribute('data-reveal'); }) : [];
                var idx = Math.max(0, sibs.indexOf(el));
                el.style.transitionDelay = Math.min(idx, 6) * 0.08 + 's';
                el.classList.add('is-in');
                io.unobserve(el);
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
        els.forEach(function (e) { io.observe(e); });
    }

    ready(function () {
        document.querySelectorAll('[data-atelier]').forEach(function (root) {
            root.querySelectorAll('[data-announce]').forEach(announce);
            root.querySelectorAll('[data-hero]').forEach(hero);
            root.querySelectorAll('[data-rail-wrap]').forEach(rail);
            root.querySelectorAll('[data-tabs]').forEach(tabs);
            root.querySelectorAll('.at-count[data-ends]').forEach(countdown);
            root.querySelectorAll('[data-look]').forEach(look);
            cards(root);
            reveal(root);
        });
    });
})();
