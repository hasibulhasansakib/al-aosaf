/* Al Aosaf: Homepage Builder (admin) */
jQuery(function ($) {
    var $b = $('#at-builder');
    if (!$b.length) return;
    var $list = $('#at-list');
    var dirty = false;

    function uid() { return 's' + Math.random().toString(36).slice(2, 10); }
    function markDirty() { dirty = true; $('#at-saved').text('Unsaved changes'); }

    /* ---------- enhanced selects (categories / product search) ---------- */
    function enhance() { $(document.body).trigger('wc-enhanced-select-init'); }

    // A cloned node carries the select2 markup of its source; strip it so it can be enhanced again.
    function cleanClone($el) {
        $el.find('.select2-container').remove();
        $el.find('select').each(function () {
            $(this).removeClass('enhanced select2-hidden-accessible').removeAttr('data-select2-id aria-hidden tabindex')
                .find('option').removeAttr('data-select2-id');
        });
        return $el;
    }

    function cloneWithValues($src) {
        // Select values are not copied by cloneNode for multi selects in every browser: copy them by hand.
        var $c = $src.clone();
        var from = $src.find('select'), to = $c.find('select');
        from.each(function (i) { to.eq(i).val($(this).val()); });
        return cleanClone($c);
    }

    /* ---------- sections ---------- */
    function sortableList($el, handle) {
        $el.sortable({ handle: handle, axis: 'y', tolerance: 'pointer', placeholder: 'at-ph', forcePlaceholderSize: true, update: markDirty });
    }
    sortableList($list, '.at-s__drag');
    function initItems($scope) {
        $scope.find('.at-items').each(function () {
            if (!$(this).data('ui-sortable')) sortableList($(this), '.at-item__drag');
        });
        $scope.find('.at-rep[data-t="hotspots"]').each(function () { drawStage($(this)); });
    }
    initItems($b);

    $b.on('click', '.at-s__tog', function () {
        var $s = $(this).closest('.at-s').toggleClass('is-open');
        $(this).text($s.hasClass('is-open') ? 'Close' : 'Edit');
        if ($s.hasClass('is-open')) $s.find('.at-rep[data-t="hotspots"]').each(function () { drawStage($(this)); });
    });
    $b.on('change', '.at-s__onbox', function () { $(this).closest('.at-s').toggleClass('is-off', !this.checked); markDirty(); });
    $b.on('click', '.at-s__up', function () { var $s = $(this).closest('.at-s'); $s.prev('.at-s').before($s); markDirty(); });
    $b.on('click', '.at-s__down', function () { var $s = $(this).closest('.at-s'); $s.next('.at-s').after($s); markDirty(); });
    $b.on('click', '.at-s__del', function () {
        if (!confirm('Delete this section? (You can still discard the draft if it was a mistake.)')) return;
        $(this).closest('.at-s').remove(); markDirty();
    });
    $b.on('click', '.at-s__dup', function () {
        var $s = $(this).closest('.at-s');
        var $c = cloneWithValues($s).attr('data-id', uid()).removeClass('ui-sortable-handle');
        $c.find('.at-items').removeData('ui-sortable').removeClass('ui-sortable');
        $s.after($c); initItems($c); enhance(); markDirty();
    });
    $('#at-add').on('click', function () {
        var type = $('#at-add-type').val();
        var html = document.getElementById('at-tpl-' + type).innerHTML.replace(/__ID__/g, uid());
        var $s = $(html).addClass('is-open');
        $s.find('.at-s__tog').text('Close');
        $list.append($s); initItems($s); enhance(); markDirty();
        $('html, body').animate({ scrollTop: $s.offset().top - 60 }, 300);
    });

    /* ---------- repeater items ---------- */
    $b.on('click', '.at-rep__add', function () {
        var $rep = $(this).closest('.at-rep');
        var $item = $($rep.children('template.at-item-tpl').html()).addClass('is-open');
        $rep.children('.at-items').append($item); initItems($item); enhance(); markDirty();
        if ($rep.data('t') === 'hotspots') drawStage($rep);
    });
    $b.on('click', '.at-item__tog, .at-item__title', function () { $(this).closest('.at-item').toggleClass('is-open'); });
    $b.on('click', '.at-item__del', function () {
        var $rep = $(this).closest('.at-rep');
        $(this).closest('.at-item').remove(); markDirty();
        if ($rep.data('t') === 'hotspots') drawStage($rep);
    });
    $b.on('click', '.at-item__dup', function () {
        var $it = $(this).closest('.at-item');
        var $c = cloneWithValues($it); $it.after($c); initItems($c); enhance(); markDirty();
        var $rep = $it.closest('.at-rep'); if ($rep.data('t') === 'hotspots') drawStage($rep);
    });
    // Keep the item header in sync with its first text field.
    $b.on('input', '.at-item [data-k="title"], .at-item [data-k="text"], .at-item [data-k="label"], .at-item [data-k="q"], .at-item [data-k="name"]', function () {
        var $it = $(this).closest('.at-item');
        var $first = $it.find('[data-k="title"],[data-k="text"],[data-k="label"],[data-k="q"],[data-k="name"]').filter(function () { return $(this).closest('.at-item')[0] === $it[0] && this.value; }).first();
        $it.children('.at-item__bar').find('.at-item__title').text($first.length ? $first.val().replace(/<[^>]+>/g, ' ') : 'Item');
    });
    $b.on('input change', 'input, textarea, select', function () { if (!$(this).closest('.at-side form').length) markDirty(); });

    /* ---------- images ---------- */
    var frame;
    $b.on('click', '.at-img__pick', function (e) {
        e.preventDefault();
        var $w = $(this).closest('.at-img');
        frame = wp.media({ title: 'Choose image', library: { type: 'image' }, multiple: false, button: { text: 'Use this image' } });
        frame.on('select', function () {
            var a = frame.state().get('selection').first().toJSON();
            var src = (a.sizes && (a.sizes.medium || a.sizes.full) || a).url;
            $w.find('input').val(a.id).trigger('change');
            $w.addClass('has-img').find('.at-img__prev').html('<img src="' + src + '" alt="">');
            $w.closest('[data-scope]').find('.at-rep[data-t="hotspots"]').each(function () { drawStage($(this)); });
        });
        frame.open();
    });
    $b.on('click', '.at-img__clear', function () {
        var $w = $(this).closest('.at-img');
        $w.find('input').val(0).trigger('change');
        $w.removeClass('has-img').find('.at-img__prev').empty();
        $w.closest('[data-scope]').find('.at-rep[data-t="hotspots"]').each(function () { drawStage($(this)); });
    });

    /* ---------- hotspot editor ---------- */
    function drawStage($rep) {
        var $scope = $rep.closest('[data-scope]');
        var $img = $scope.find('.at-img').filter(function () { return $(this).find('[data-k="' + $rep.data('image') + '"]').length; }).first().find('img');
        var $stage = $rep.children('.at-stage');
        $stage.find('.at-stage__wrap').remove();
        if (!$img.length) { $stage.find('.at-stage__empty').show(); return; }
        $stage.find('.at-stage__empty').hide();
        var $wrap = $('<div class="at-stage__wrap"><img alt=""></div>');
        $wrap.find('img').attr('src', $img.attr('src'));
        $rep.children('.at-items').children('.at-item').each(function (i) {
            var x = parseFloat($(this).find('[data-k="x"]').val()) || 0, y = parseFloat($(this).find('[data-k="y"]').val()) || 0;
            $('<span class="at-dot"></span>').text(i + 1).css({ left: x + '%', top: y + '%' }).attr('data-i', i).appendTo($wrap);
        });
        $stage.append($wrap);
    }
    function pct($wrap, e) {
        var r = $wrap[0].getBoundingClientRect();
        return {
            x: Math.round(Math.min(100, Math.max(0, (e.clientX - r.left) / r.width * 100)) * 10) / 10,
            y: Math.round(Math.min(100, Math.max(0, (e.clientY - r.top) / r.height * 100)) * 10) / 10
        };
    }
    var drag = null;
    $b.on('mousedown', '.at-dot', function (e) { e.preventDefault(); e.stopPropagation(); drag = { $dot: $(this), $wrap: $(this).parent(), moved: false }; });
    $(document).on('mousemove', function (e) {
        if (!drag) return;
        var p = pct(drag.$wrap, e); drag.moved = true;
        drag.$dot.css({ left: p.x + '%', top: p.y + '%' });
        var $it = drag.$wrap.closest('.at-rep').children('.at-items').children('.at-item').eq(drag.$dot.data('i'));
        $it.find('[data-k="x"]').val(p.x); $it.find('[data-k="y"]').val(p.y);
    }).on('mouseup', function () { if (drag && drag.moved) markDirty(); drag = null; });
    $b.on('click', '.at-stage__wrap', function (e) {
        if ($(e.target).hasClass('at-dot')) return;
        var $rep = $(this).closest('.at-rep'), p = pct($(this), e);
        var $item = $($rep.children('template.at-item-tpl').html()).addClass('is-open');
        $item.find('[data-k="x"]').val(p.x); $item.find('[data-k="y"]').val(p.y);
        $rep.children('.at-items').append($item); enhance(); drawStage($rep); markDirty();
    });
    $b.on('input change', '.at-rep[data-t="hotspots"] [data-k="x"], .at-rep[data-t="hotspots"] [data-k="y"]', function () { drawStage($(this).closest('.at-rep')); });

    /* ---------- collect + save ---------- */
    function own($scope, sel) { return $scope.find(sel).filter(function () { return $(this).parent().closest('[data-scope]')[0] === $scope[0]; }); }
    function collect($scope) {
        var o = {};
        own($scope, '[data-k]').each(function () {
            var $f = $(this), k = $f.data('k'), t = $f.data('t');
            if (t === 'repeater' || t === 'hotspots') {
                o[k] = $f.children('.at-items').children('.at-item').map(function () { return collect($(this)); }).get();
            } else if (t === 'toggle') {
                o[k] = this.checked ? 1 : 0;
            } else if (t === 'categories' || t === 'products') {
                o[k] = ($f.val() || []).map(Number);
            } else {
                o[k] = $f.val();
            }
        });
        return o;
    }
    function doc() {
        return {
            sections: $list.children('.at-s').map(function () {
                var $s = $(this);
                return { id: $s.attr('data-id'), type: $s.data('type'), on: $s.find('.at-s__onbox').first().is(':checked') ? 1 : 0, name: $s.find('.at-s__name').first().val(), d: collect($s) };
            }).get(),
            settings: collect($('#at-settings'))
        };
    }
    function save(mode, win) {
        var $btns = $('#at-save, #at-preview, #at-publish').prop('disabled', true);
        $('#at-saved').text(mode === 'publish' ? 'Publishing…' : 'Saving…');
        return $.post(aaAtelier.ajax, { action: 'aa_atelier_save', nonce: aaAtelier.nonce, mode: mode, doc: JSON.stringify(doc()) })
            .done(function (r) {
                if (!r || !r.success) { $('#at-saved').text('Not saved: ' + ((r && r.data && r.data.message) || 'error')); if (win) win.close(); return; }
                dirty = false;
                $('#at-saved').text((mode === 'publish' ? 'Published ' : 'Draft saved ') + r.data.time);
                $('#at-state').toggleClass('is-changed', r.data.changed).text(r.data.changed ? 'Draft has unpublished changes' : 'Draft = live page');
                if (win && r.data.preview) win.location = r.data.preview;
                else if (win) win.close();
            })
            .fail(function () { $('#at-saved').text('Not saved: connection error'); if (win) win.close(); })
            .always(function () { $btns.prop('disabled', false); });
    }
    $('#at-save').on('click', function () { save('draft'); });
    $('#at-preview').on('click', function () { var w = window.open('', '_blank'); save('draft', w); });
    $('#at-publish').on('click', function () { if (confirm('Publish this version to the live page?')) save('publish'); });
    $('#at-export').on('click', function () {
        var blob = new Blob([JSON.stringify(doc(), null, 2)], { type: 'application/json' });
        var a = document.createElement('a'); a.href = URL.createObjectURL(blob);
        a.download = 'homepage-builder-' + new Date().toISOString().slice(0, 10) + '.json'; a.click();
    });
    $(window).on('beforeunload', function () { if (dirty) return 'You have unsaved changes.'; });
    $(document).on('keydown', function (e) { if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); save('draft'); } });
});
