/* Al Aosaf: Offer page: category chips + "show more". */
(function () {
    'use strict';
    function ready(fn) { if (document.readyState !== 'loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }

    ready(function () {
        document.querySelectorAll('[data-offer]').forEach(function (root) {
            var per = parseInt(root.getAttribute('data-per'), 10) || 24;
            var cells = [].slice.call(root.querySelectorAll('.aa-offer__cell'));
            var more = root.querySelector('[data-offer-more]');
            var chips = [].slice.call(root.querySelectorAll('[data-chip]'));
            var cat = '', shown = per;

            function apply(animate) {
                var n = 0, total = 0;
                cells.forEach(function (c) {
                    var match = !cat || c.getAttribute('data-cat') === cat;
                    if (match) total++;
                    var show = match && n < shown;
                    if (show) n++;
                    if (show && c.hidden && animate) {
                        c.classList.remove('is-new'); void c.offsetWidth; c.classList.add('is-new');
                    }
                    c.hidden = !show;
                });
                if (more) more.parentNode.hidden = total <= shown;
            }

            chips.forEach(function (b) {
                b.addEventListener('click', function () {
                    chips.forEach(function (x) { x.classList.toggle('is-on', x === b); x.setAttribute('aria-pressed', x === b ? 'true' : 'false'); });
                    cat = b.getAttribute('data-chip');
                    shown = per;
                    apply(true);
                });
            });
            if (more) more.addEventListener('click', function () { shown += per; apply(true); });
            apply(false);
        });
    });
})();
