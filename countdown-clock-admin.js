
jQuery(document).ready(function($) {
    $('.color-field').wpColorPicker();

    $('.copy-shortcode').click(function() {
        const shortcode = $(this).data('shortcode');
        navigator.clipboard.writeText(shortcode).then(() => {
            $('.copy-confirmation').fadeIn().delay(1000).fadeOut();
        });
    });
});
