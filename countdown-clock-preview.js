
// Live preview for circle countdown in admin
jQuery(document).ready(function($) {
    const previewTarget = $('<div id="circle-preview" style="margin-top:40px;"></div>');
    $('form').last().append(previewTarget);

    $('select[name*="[format]"], input[name*="[target]"]').on('change input', function() {
        const format = $('select[name*="[format]"]').val();
        const target = $('input[name*="[target]"]').val();

        if (format === 'circle' && target) {
            previewTarget.html('<p><strong>Live Preview:</strong></p><div class="countdown-clock-circle" data-id="preview" data-target="' + target + '" data-countup="0"></div>');
        } else {
            previewTarget.empty();
        }
    });
});
