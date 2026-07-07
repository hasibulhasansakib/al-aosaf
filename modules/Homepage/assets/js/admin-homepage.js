jQuery(document).ready(function($) {

    // Hero Slides Repeater Logic
    const $wrapper = $('#aa-hero-slides-wrapper');
    const $addButton = $('#aa-add-hero-slide');
    
    // Make slides sortable
    if (typeof $.fn.sortable !== 'undefined') {
        $wrapper.sortable({
            handle: '.aa-slide-handle',
            update: function() {
                updateSlideIndices();
            }
        });
    }

    function updateSlideIndices() {
        $wrapper.find('.aa-slide-item').each(function(index) {
            $(this).find('.aa-slide-number').text(index + 1);
            
            // Update input names
            $(this).find('input').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    name = name.replace(/\[\d+\]/, '[' + index + ']');
                    $(this).attr('name', name);
                }
            });
        });
    }

    // Toggle slide content
    $wrapper.on('click', '.aa-slide-header', function(e) {
        if ($(e.target).hasClass('aa-slide-handle')) return; // Don't toggle when dragging
        const $content = $(this).siblings('.aa-slide-content');
        const $icon = $(this).find('.aa-slide-toggle');
        $content.slideToggle(300);
        if ($icon.text() === '▼') {
            $icon.text('▲');
        } else {
            $icon.text('▼');
        }
    });

    // Add new slide
    $addButton.on('click', function(e) {
        e.preventDefault();
        var template = $('#aa-slide-template').html();
        var index = $wrapper.find('.aa-slide-item').length;
        template = template.replace(/__INDEX__/g, index);
        template = template.replace(/__NUM__/g, index + 1);
        $wrapper.append(template);
    });

    // Remove slide
    $wrapper.on('click', '.aa-remove-slide', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to remove this slide?')) {
            $(this).closest('.aa-slide-item').slideUp(300, function() {
                $(this).remove();
                updateSlideIndices();
            });
        }
    });

    // WP Media Uploader
    let mediaUploader;
    $wrapper.on('click', '.aa-upload-image-btn', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const $input = $btn.siblings('.aa-image-url-input');
        const $preview = $btn.closest('.aa-slide-item').find('.aa-image-preview');

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Slide Image',
            button: { text: 'Choose Image' },
            multiple: false
        });

        mediaUploader.on('select', function() {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            $input.val(attachment.url);
            
            if ($preview.length === 0) {
                $btn.after('<div class="aa-image-preview" style="margin-top: 10px;"><img src="'+attachment.url+'" style="max-width: 150px; height: auto;" /></div>');
            } else {
                $preview.find('img').attr('src', attachment.url);
            }
        });

        mediaUploader.open();
    });

    // Dynamic Sliders Repeater Logic
    const $dsWrapper = $('#aa-dynamic-sliders-wrapper');
    const $dsAddButton = $('#aa-add-dynamic-slider');
    
    // Make slides sortable
    if (typeof $.fn.sortable !== 'undefined') {
        $dsWrapper.sortable({
            handle: '.aa-slide-handle',
            update: function() {
                updateDynamicSliderIndices();
            }
        });
    }

    function updateDynamicSliderIndices() {
        $dsWrapper.find('.aa-slide-item').each(function(index) {
            var shortcodeId = index + 1;
            var titleInput = $(this).find('.aa-dynamic-title-input').val();
            $(this).find('.aa-slide-number').text(titleInput ? titleInput : 'Slider ' + shortcodeId);
            $(this).find('code').text('[aa_dynamic_slider id="' + shortcodeId + '"]');
            
            // Update input and select names
            $(this).find('input, select').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    name = name.replace(/\[\d+\]/, '[' + index + ']');
                    $(this).attr('name', name);
                }
            });
        });
    }

    // Toggle slide content
    $dsWrapper.on('click', '.aa-slide-header', function(e) {
        if ($(e.target).hasClass('aa-slide-handle')) return; // Don't toggle when dragging
        const $content = $(this).siblings('.aa-slide-content');
        const $icon = $(this).find('.aa-slide-toggle');
        $content.slideToggle(300);
        if ($icon.text() === '▼') {
            $icon.text('▲');
        } else {
            $icon.text('▼');
        }
    });

    // Update title dynamically in header
    $dsWrapper.on('keyup', '.aa-dynamic-title-input', function() {
        var val = $(this).val();
        var $item = $(this).closest('.aa-slide-item');
        var index = $item.index() + 1;
        $item.find('.aa-slide-number').text(val ? val : 'Slider ' + index);
    });

    // Add new slide
    $dsAddButton.on('click', function(e) {
        e.preventDefault();
        var template = $('#aa-dynamic-slider-template').html();
        var index = $dsWrapper.find('.aa-slide-item').length;
        template = template.replace(/__INDEX__/g, index);
        template = template.replace(/__NUM__/g, index + 1);
        $dsWrapper.append(template);
    });

    // Remove slide
    $dsWrapper.on('click', '.aa-remove-slide', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to remove this slider?')) {
            $(this).closest('.aa-slide-item').slideUp(300, function() {
                $(this).remove();
                updateDynamicSliderIndices();
            });
        }
    });

});
