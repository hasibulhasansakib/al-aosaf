jQuery(document).ready(function($){
    // Initialize color picker
    $('.aa-color-picker').wpColorPicker();

    // Media uploader
    var file_frame;
    $('.aa-upload-media').on('click', function(e) {
        e.preventDefault();
        var button = $(this);
        var targetInput = $(button.data('target'));

        if (file_frame) {
            file_frame.open();
            return;
        }

        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select or Upload an Image',
            button: { text: 'Use this image' },
            multiple: false
        });

        file_frame.on('select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            targetInput.val(attachment.url);
        });

        file_frame.open();
    });

    // Logo Type conditional logic
    var logoTypeSelect = $('.aa-logo-type-select');
    var logoTextRow = $('.aa-logo-text-row');

    function toggleLogoFields() {
        var val = logoTypeSelect.val();
        if (val === 'text' || val === 'image_text') {
            logoTextRow.show();
        } else {
            logoTextRow.hide();
        }
    }

    if (logoTypeSelect.length) {
        logoTypeSelect.on('change', toggleLogoFields);
        toggleLogoFields(); // trigger on load
    }
});
