<?php
function countdown_clock_shortcode($atts) {
    $atts = shortcode_atts(array('id' => ''), $atts);
    $id = sanitize_text_field($atts['id']);
    if (!$id) return '<p>No countdown ID provided.</p>';

    $data = get_option('countdown_clock_data_' . $id);
    if (!$data || !is_array($data)) return '<p>Countdown not found.</p>';

    $title = esc_html($data['title'] ?? '');
    $target_date = esc_html($data['target_date'] ?? '');
    $format = esc_html($data['format'] ?? 'digital');

    ob_start(); ?>
    <div class="countdown-clock-frontend" data-id="<?php echo esc_attr($id); ?>" data-date="<?php echo $target_date; ?>" data-format="<?php echo $format; ?>">
        <?php if ($title): ?>
            <div class="countdown-title"><?php echo $title; ?></div>
        <?php endif; ?>
        <div class="countdown-output">Loading countdown...</div>
    </div>
    <?php return ob_get_clean();
}
add_shortcode('countdown_clock', 'countdown_clock_shortcode');

function countdown_clock_enqueue_assets() {
    if (!is_admin()) {
        wp_enqueue_script('countdown-clock-frontend', plugins_url('assets/countdown-frontend.js', __FILE__), [], null, true);
        wp_enqueue_style('countdown-clock-style', plugins_url('css/frontend-style.css', __FILE__));
    }
}
add_action('wp_enqueue_scripts', 'countdown_clock_enqueue_assets');