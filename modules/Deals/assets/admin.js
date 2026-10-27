/* Al Aosaf: Offer Page admin. */
jQuery(function ($) {
    'use strict';
    var $list = $('[data-rules]'), tpl = document.getElementById('dl-tpl'), seq = 0;

    function enhance() { $(document.body).trigger('wc-enhanced-select-init'); }

    function sync($rule) {
        var target = $rule.find('[data-target]:checked').val() || 'products';
        $rule.find('[data-show]').each(function () { $(this).toggle($(this).attr('data-show') === target); });
        var type = $rule.find('[data-type]').val(), v = parseFloat($rule.find('[data-value]').val()) || 0;
        $rule.find('[data-unit]').text(type === 'percent' ? '%' : '৳');
        var ex = '';
        if (v > 0) {
            var base = 1500, now;
            if (type === 'percent') now = Math.round(base * (1 - Math.min(v, 95) / 100));
            else if (type === 'amount') now = base - v;
            else now = v;
            ex = now > 0 && now < base
                ? 'Example: a ৳1,500 product → ৳' + now.toLocaleString('en') + (type === 'price' ? ' (every product in this rule gets this price)' : '')
                : 'Example: a ৳1,500 product → no change (the offer price must be lower than the current price)';
        }
        $rule.find('[data-example]').text(ex);
        $rule.toggleClass('is-off', !$rule.find('[data-on]').is(':checked'));
    }

    function empty() { $('[data-empty]').prop('hidden', $list.children('[data-rule]').length > 0); }

    $list.children('[data-rule]').each(function () { sync($(this)); });
    $list.on('change input', '[data-target], [data-type], [data-value], [data-on]', function () { sync($(this).closest('[data-rule]')); });
    $list.on('click', '[data-del]', function () {
        if (!window.confirm('Remove this rule?')) return;
        $(this).closest('[data-rule]').remove();
        empty();
    });

    $('[data-add]').on('click', function () {
        var html = tpl.innerHTML.replace(/__i__/g, 'n' + (++seq));
        var $r = $(html).appendTo($list);
        sync($r);
        enhance();
        empty();
        $r.find('.dl-rule__name').trigger('focus');
    });

    $list.sortable({ handle: '.dl-rule__drag', items: '> [data-rule]', placeholder: 'dl-ph', forcePlaceholderSize: true });

    // Number the rules in the order they are shown, so the saved priority matches the screen.
    $('#dl-form').on('submit', function () {
        $list.children('[data-rule]').each(function (i) {
            $(this).find('[name^="deals[rules]["]').each(function () {
                this.name = this.name.replace(/^deals\[rules\]\[[^\]]+\]/, 'deals[rules][' + i + ']');
            });
        });
    });
});
