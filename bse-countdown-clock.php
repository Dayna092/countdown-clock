<?php
/**
 * Plugin Name: BSE Countdown Clock
 * Plugin URI: https://brokensoundentertainment.com
 * Description: A customizable countdown timer plugin by Broken Sound Entertainment featuring circle, text, and digital formats with live preview and GitHub auto-updates.
 * Version: 3.2.1
 * Author: Broken Sound Entertainment
 * Author URI: https://brokensoundentertainment.com
 * License: GPLv2 or later
 * Text Domain: bse-countdown-clock
 * GitHub Plugin URI: https://github.com/dayna092/bse-countdown-clock
 * Primary Branch: main
 */



// GitHub update checker using plugin-update-checker library.
// For public repositories, no token is required. For private repositories,
// define COUNTDOWN_CLOCK_GITHUB_TOKEN in wp-config.php (optional).
if ( ! class_exists( 'Puc_v5_Factory' ) ) {
    require_once plugin_dir_path(__FILE__) . 'plugin-update-checker/plugin-update-checker.php';
}
if ( class_exists( 'Puc_v5_Factory' ) ) {
    $bse_countdown_clock_update_checker = Puc_v5_Factory::buildUpdateChecker(
        'https://github.com/dayna092/bse-countdown-clock/',
        __FILE__,
        'bse-countdown-clock'
    );
    if ( defined( 'COUNTDOWN_CLOCK_GITHUB_TOKEN' ) && COUNTDOWN_CLOCK_GITHUB_TOKEN ) {
        $bse_countdown_clock_update_checker->setAuthentication( COUNTDOWN_CLOCK_GITHUB_TOKEN );
    }
    if ( method_exists( $bse_countdown_clock_update_checker, 'setBranch' ) ) {
        $bse_countdown_clock_update_checker->setBranch( 'main' );
    }
}

add_action('admin_menu', 'countdown_clock_add_admin_menu');
add_action('admin_init', 'countdown_clock_register_settings');
add_action('admin_enqueue_scripts', 'countdown_clock_enqueue_admin_scripts');


function countdown_clock_add_admin_menu() {
    add_menu_page(
        'Countdown Clock Settings',
        'Countdown Clock',
        'manage_options',
        'bse-countdown-clock',
        'countdown_clock_settings_page',
        'dashicons-clock',
        30
    );
}


function countdown_clock_register_settings() {
    register_setting('countdown_clock_group', 'countdown_clock_data');
}

function countdown_clock_enqueue_admin_scripts($hook) {
    if ($hook !== 'toplevel_page_bse-countdown-clock') return;
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('countdown-clock-preview', plugin_dir_url(__FILE__) . 'countdown-clock-preview.js', array('jquery'), null, true);
    wp_enqueue_script('countdown-clock-admin', plugin_dir_url(__FILE__) . 'countdown-clock-admin.js', array('jquery', 'wp-color-picker'), null, true);
}

function countdown_clock_settings_page() {
    $data = get_option('countdown_clock_data', []);
    $current_id = isset($_GET['clock_id']) ? sanitize_text_field($_GET['clock_id']) : '';
    $settings = $current_id && isset($data[$current_id]) ? $data[$current_id] : [];

    ?>
    <div class="wrap">
        <h1>Countdown Clock Settings</h1>
        
        <form method="get" style="margin-bottom:20px;">
            <input type="hidden" name="page" value="bse-countdown-clock">
            <label><strong>Select or Create Countdown ID:</strong></label>
            <input type="text" name="clock_id" value="<?php echo esc_attr($current_id); ?>" placeholder="e.g. launch2024" required />
            <button class="button button-secondary">Load Settings</button>
        
<script>
document.addEventListener("DOMContentLoaded", function () {
    const formatSelect = document.querySelector('[name*="[format]"]');
    const circleSettings = document.getElementById("circle-format-settings");
    function toggleCircleSettings() {
        if (formatSelect && circleSettings) {
            circleSettings.style.display = formatSelect.value === "circle" ? "block" : "none";
        }
    }
    if (formatSelect) {
        formatSelect.addEventListener("change", toggleCircleSettings);
        toggleCircleSettings(); // init
    }
});
</script>

</form>


        <?php if ($current_id): ?>
        <form method="post" action="options.php">
            <?php settings_fields('countdown_clock_group'); ?>
            <?php
                if (!isset($data[$current_id])) $data[$current_id] = [];

    // Handle JSON import (overwrite)
    if (!empty($_POST['countdown_clock_data'][$current_id]['import_json'])) {
        $import_json_raw = stripslashes($_POST['countdown_clock_data'][$current_id]['import_json']);
        $import_array = json_decode($import_json_raw, true);
        if (is_array($import_array)) {
            foreach ($import_array as $k => $v) {
                $data[$current_id][$k] = sanitize_text_field($v);
            }
        }
    }
    
    if (!empty($_POST['countdown_clock_data'][$current_id]['import_json'])) {
        $import_json = json_decode(stripslashes($_POST['countdown_clock_data'][$current_id]['import_json']), true);
        if (is_array($import_json)) {
            $data[$current_id] = array_merge($data[$current_id], $import_json);
        }
    }

            ?>

            <input type="hidden" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][id]" value="<?php echo esc_attr($current_id); ?>" />

            
<h2>🎨 Design Settings</h2>
            <h3>Title</h3>
            <label>Countdown Title:</label><br><p class="description">Optional title displayed above the countdown.</p>
            <input type="text" style="width:100%;" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][title_text]" value="<?php echo esc_attr($settings['title_text'] ?? ''); ?>"><br><br>

            <label>Title Font:</label><br><p class="description">Font used for the countdown title.</p>
            <select name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][title_font]">
                <?php
                    $fonts = ['Roboto', 'Orbitron', 'Lobster', 'Montserrat', 'Open Sans'];
                    foreach ($fonts as $font) {
                        $selected = ($settings['title_font'] ?? '') === $font ? 'selected' : '';
                        echo "<option value='$font' $selected>$font</option>";
                    }
                ?>
            </select><br><br>

            <label>Title Color:</label><br><p class="description">Color of the countdown title text.</p>
            <input type="text" class="color-field" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][title_color]" value="<?php echo esc_attr($settings['title_color'] ?? '#ffffff'); ?>"><br><br>

            <label>Title Styles:</label><br>
            <label><input type="checkbox" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][title_bold]" value="1" <?php checked(!empty($settings['title_bold'])); ?>> Bold</label>&nbsp;&nbsp;
            <label><input type="checkbox" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][title_italic]" value="1" <?php checked(!empty($settings['title_italic'])); ?>> Italic</label>&nbsp;&nbsp;
            <label><input type="checkbox" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][title_underline]" value="1" <?php checked(!empty($settings['title_underline'])); ?>> Underline</label><br><br>

            <h3>Block Background & Text</h3>
            <label>Background Color:</label><br><p class="description">Background color of the countdown area for digital/text formats.</p>

            <input type="text" class="color-field" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][bg_color]" value="<?php echo esc_attr($settings['bg_color'] ?? '#ffffff'); ?>"><br><br>

            <label>Text Color:</label><br><p class="description">Color of the countdown text.</p>
            <input type="text" class="color-field" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][text_color]" value="<?php echo esc_attr($settings['text_color'] ?? '#000000'); ?>"><br><br>

            <label>Google Font:</label><br><p class="description">Font used for digital and text formats.</p>
            <select name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][font]">
                <?php
                    $fonts = ['Roboto', 'Orbitron', 'Lobster', 'Montserrat', 'Open Sans'];
                    foreach ($fonts as $font) {
                        $selected = ($settings['font'] ?? '') === $font ? 'selected' : '';
                        echo "<option value='$font' $selected>$font</option>";
                    }
                ?>
            </select><br><br>

            <label>Animation Style:</label><br><p class="description">Entrance animation for the countdown block.</p>
            <select name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][animation]">
                <?php
                    $animations = ['Pulse', 'Bounce', 'Fade', 'Shake', 'Zoom'];
                    foreach ($animations as $anim) {
                        $selected = ($settings['animation'] ?? '') === $anim ? 'selected' : '';
                        echo "<option value='$anim' $selected>$anim</option>";
                    }
                ?>
            </select><br><br>

            <label>Display Format:</label><br><p class="description">Choose how the countdown will be displayed: Digital, Circle, or Text-only.</p>
            <select name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][format]">
                <option value="digital" <?php selected($settings['format'] ?? '', 'digital'); ?>>Digital</option>
                <option value="circle" <?php selected($settings['format'] ?? '', 'circle'); ?>>Circle</option>
                <option value="text" <?php selected($settings['format'] ?? '', 'text'); ?>>Text Only</option>
            </select><br><br>

            
            <div id="circle-format-settings" style="display:none;"><h2>⚙️ Circle Format Settings</h2>
            <label>Donut Thickness (px):</label><br><p class="description">Controls the width of the donut ring.</p>
            <input type="range" min="1" max="30" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][circle_thickness]" value="<?php echo esc_attr($settings['circle_thickness'] ?? 10); ?>"><br><br>

            
<label>Donut Size (px):</label><br><p class="description">Controls the overall size of each donut circle.</p>
<input type="range" min="60" max="220" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][circle_size]" value="<?php echo esc_attr($settings['circle_size'] ?? 100); ?>"><br><br>

<label>Glow Effect:</label><br><p class="description">Add a soft glow around the active donut ring.</p>
<label><input type="checkbox" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][circle_glow]" value="1" <?php checked(!empty($settings['circle_glow'])); ?>> Enable glow</label><br><br>

<label>Pulse Animation:</label><br><p class="description">Subtle pulsing animation to draw attention to the countdown.</p>
<label><input type="checkbox" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][circle_pulse]" value="1" <?php checked(!empty($settings['circle_pulse'])); ?>> Enable pulse</label><br><br>

<label>Update Speed (ms):</label><br><p class="description">How often the donut values update (1000 = once per second).</p>
<input type="number" min="250" max="5000" step="250" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][circle_speed]" value="<?php echo esc_attr($settings['circle_speed'] ?? 1000); ?>"><br><br>

<label>Donut Background Color:</label><br><p class="description">Sets the background color of the donut ring. Use "transparent" for no fill.</p>
            <input type="text" class="color-field" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][circle_bg]" value="<?php echo esc_attr($settings['circle_bg'] ?? '#eeeeee'); ?>"><br><br>

            <label>Donut Progress Color:</label><br><p class="description">Color of the animated countdown ring.</p>
            <input type="text" class="color-field" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][circle_color]" value="<?php echo esc_attr($settings['circle_color'] ?? '#2196f3'); ?>"><br><br>

            <label>Donut Font Family:</label><br><p class="description">Font used inside the donut ring (value + label).</p>
            <select name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][circle_font]">
                <?php
                    foreach (['Roboto', 'Orbitron', 'Lobster', 'Montserrat', 'Open Sans'] as $font) {
                        $selected = ($settings['circle_font'] ?? '') === $font ? 'selected' : '';
                        echo "<option value='$font' $selected>$font</option>";
                    }
                ?>
            </select><br><br>

            <label>Donut Font Color:</label><br><p class="description">Color of the countdown numbers and labels inside the circle.</p>
            <input type="text" class="color-field" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][circle_font_color]" value="<?php echo esc_attr($settings['circle_font_color'] ?? '#000000'); ?>"><br><br>


<h3>Donut Label (Days / Hours / Minutes / Seconds)</h3>
<label>Label Font:</label><br><p class="description">Font for the labels under each donut.</p>
<select name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][label_font]">
    <?php
        foreach (['Roboto', 'Orbitron', 'Lobster', 'Montserrat', 'Open Sans'] as $font) {
            $selected = ($settings['label_font'] ?? '') === $font ? 'selected' : '';
            echo "<option value='$font' $selected>$font</option>";
        }
    ?>
</select><br><br>

<label>Label Color:</label><br><p class="description">Text color for the donut labels.</p>
<input type="text" class="color-field" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][label_color]" value="<?php echo esc_attr($settings['label_color'] ?? '#ffffff'); ?>"><br><br>

<label>Label Styles:</label><br>
<label><input type="checkbox" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][label_bold]" value="1" <?php checked(!empty($settings['label_bold'])); ?>> Bold</label>&nbsp;&nbsp;
<label><input type="checkbox" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][label_italic]" value="1" <?php checked(!empty($settings['label_italic'])); ?>> Italic</label>&nbsp;&nbsp;
<label><input type="checkbox" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][label_underline]" value="1" <?php checked(!empty($settings['label_underline'])); ?>> Underline</label><br><br>

</div>
<h2>⏱ Timer Settings</h2>
            <label>Redirect URL after countdown ends (optional):</label><br><p class="description">Enter a URL to automatically redirect the user when the countdown finishes.</p>
            <input type="url" style="width:100%;" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][redirect]" value="<?php echo esc_attr($settings['redirect'] ?? ''); ?>"><br><br>

            <label>Target Date & Time:</label><br><p class="description">The countdown will count down to this date and time.</p>
            <input type="datetime-local" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][target]" value="<?php echo esc_attr($settings['target'] ?? ''); ?>"><br><br>

            <label>Start Showing:</label><br><p class="description">The countdown will only appear after this date/time.</p>
            <input type="datetime-local" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][start]" value="<?php echo esc_attr($settings['start'] ?? ''); ?>"><br><br>

            <label>Stop Showing:</label><br><p class="description">The countdown will be hidden after this date/time.</p>
            <input type="datetime-local" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][stop]" value="<?php echo esc_attr($settings['stop'] ?? ''); ?>"><br><br>

            <p class="description">If checked, the countdown will keep counting up after it ends.</p><label><input type="checkbox" name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][countup]" value="1" <?php checked($settings['countup'] ?? '', '1'); ?>> Enable count-up after countdown ends</label><br><br>

            <h2>🧩 Shortcode</h2>
            <code>[countdown_clock id="<?php echo esc_attr($current_id); ?>"]</code>
            <button type="button" class="button copy-shortcode" data-shortcode='[countdown_clock id="<?php echo esc_attr($current_id); ?>"]'>Copy Shortcode</button>
            <span class="copy-confirmation" style="display:none;">✅ Copied!</span>

            
            <h2>📤 Export / Import Settings</h2>
            <label>Export:</label><br>
            <textarea rows="5" readonly style="width:100%"><?php echo esc_textarea(json_encode($settings)); ?></textarea><br><br>

            <label>Import (paste JSON here):</label><br>
            <textarea name="countdown_clock_data[<?php echo esc_attr($current_id); ?>][import_json]" rows="5" style="width:100%"></textarea>
            <p class="description">Pasting JSON here will overwrite current settings on save.</p>


            
            
            
<h2>👁️ Live Preview</h2>
<div id="countdown-preview-container">
    <iframe id="countdown-preview" style="width:100%;height:300px;border:1px solid #ccc;"></iframe>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const preview = document.getElementById("countdown-preview");
    const form = document.querySelector("form[action='options.php']");

    function extractValue(data, key) {
        for (const [k, v] of data.entries()) {
            if (k.includes(key)) return v;
        }
        return '';
    }

    const updatePreview = () => {
        const data = new FormData(form);
        const format = extractValue(data, '[format]') || 'digital';
        const font = extractValue(data, '[font]') || 'Roboto';
        const fontColor = extractValue(data, '[text_color]') || '#000';
        const bgColor = extractValue(data, '[bg_color]') || '#fff';

        const htmlStart = '<div style="padding:20px;background:' + bgColor + ';color:' + fontColor + ';font-family:' + font + ';text-align:center;">';
        let html = htmlStart;

        if (format === 'circle') {
            const ringColor = extractValue(data, '[circle_color]') || '#2196f3';
            const circleBg = extractValue(data, '[circle_bg]') || '#eee';
            const thickness = extractValue(data, '[circle_thickness]') || '10';
            html += '<div style="display:flex;gap:20px;justify-content:center;">';
            ['Days','Hours','Minutes','Seconds'].forEach(unit => {
                html += `
                    <div style="position:relative;width:100px;height:100px;text-align:center;">
                        <svg viewBox="0 0 100 100" style="width:100%;height:100%;transform:rotate(-90deg);">
                            <circle cx="50" cy="50" r="45" fill="none" stroke="${circleBg}" stroke-width="${thickness}"></circle>
                            <circle cx="50" cy="50" r="45" fill="none" stroke="${ringColor}" stroke-width="${thickness}" stroke-dasharray="282.74" stroke-dashoffset="70"></circle>
                        </svg>
                        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-family:${font};color:${fontColor};">
                            <span class="value">12</span><br><span class="unit">${unit}</span>
                        </div>
                    </div>`;
            });
            html += '</div>';
        } else if (format === 'text') {
            html += '<div style="font-size:24px;">Only 1 day left until the big event!</div>';
        } else {
            html += '<div style="font-size:32px;">1d 23h 59m 59s</div>';
        }

        html += '</div>';
        const doc = preview.contentDocument || preview.contentWindow.document;
        doc.open();
        doc.write('<body style="margin:0;">' + html + '</body>');
        doc.close();
    };

    if (form && preview) {
        form.addEventListener("change", updatePreview);
        updatePreview();
    }
});
</script>




<p><input type="submit" class="button button-primary" value="Save Settings"></p>
        
<script>
document.addEventListener("DOMContentLoaded", function () {
    const formatSelect = document.querySelector('[name*="[format]"]');
    const circleSettings = document.getElementById("circle-format-settings");
    function toggleCircleSettings() {
        if (formatSelect && circleSettings) {
            circleSettings.style.display = formatSelect.value === "circle" ? "block" : "none";
        }
    }
    if (formatSelect) {
        formatSelect.addEventListener("change", toggleCircleSettings);
        toggleCircleSettings(); // init
    }
});
</script>

</form>
        <?php endif; ?>
    </div>
<?php
}


// Shortcode Renderer
add_shortcode('countdown_clock', 'countdown_clock_render_shortcode');



function countdown_clock_render_shortcode($atts) {
    $atts = shortcode_atts(['id' => ''], $atts);
    $data = get_option('countdown_clock_data', []);
    $id = sanitize_text_field($atts['id']);

    if (!$id || !isset($data[$id])) return '<p>Countdown not found.</p>';

    $settings = $data[$id];
    $now      = current_time('Y-m-d\TH:i');
    $start    = $settings['start'] ?? '';
    $stop     = $settings['stop'] ?? '';

    if (($start && $now < $start) || ($stop && $now > $stop)) return '';

    $format = $settings['format'] ?? 'digital';

    // Common settings
    $bg_color       = $settings['bg_color'] ?? '#ffffff';
    $bg_transparent = !empty($settings['bg_transparent']);
    $text_color     = $settings['text_color'] ?? '#000000';
    $font_family    = $settings['font'] ?? 'Roboto';

    // Title settings
    $title_text      = $settings['title_text'] ?? '';
    $title_color     = $settings['title_color'] ?? $text_color;
    $title_font      = $settings['title_font'] ?? $font_family;
    $title_bold      = !empty($settings['title_bold']);
    $title_italic    = !empty($settings['title_italic']);
    $title_underline = !empty($settings['title_underline']);

    $title_style_parts   = [];
    $title_style_parts[] = 'color:' . esc_attr($title_color);
    $title_style_parts[] = "font-family:'" . esc_attr($title_font) . "'";
    if ($title_bold) {
        $title_style_parts[] = 'font-weight:bold';
    }
    if ($title_italic) {
        $title_style_parts[] = 'font-style:italic';
    }
    if ($title_underline) {
        $title_style_parts[] = 'text-decoration:underline';
    }
    $title_style = implode(';', $title_style_parts);

    // Label (Days / Hours / Minutes / Seconds) settings for circle format
    $label_color     = $settings['label_color'] ?? '#ffffff';
    $label_font      = $settings['label_font'] ?? $circle_font ?? 'Roboto';
    $label_bold      = !empty($settings['label_bold']);
    $label_italic    = !empty($settings['label_italic']);
    $label_underline = !empty($settings['label_underline']);

    $label_style_parts   = [];
    $label_style_parts[] = 'color:' . esc_attr($label_color);
    $label_style_parts[] = "font-family:'" . esc_attr($label_font) . "'";
    if ($label_bold) {
        $label_style_parts[] = 'font-weight:bold';
    }
    if ($label_italic) {
        $label_style_parts[] = 'font-style:italic';
    }
    if ($label_underline) {
        $label_style_parts[] = 'text-decoration:underline';
    }
    $label_style = implode(';', $label_style_parts);

    $container_bg = $bg_transparent ? 'transparent' : $bg_color;


ob_start();

// CIRCLE / DONUT FORMAT
if ($format === 'circle') {
    $circle_thickness  = intval($settings['circle_thickness'] ?? 10);
    $circle_bg         = $settings['circle_bg'] ?? '#eeeeee';
    $circle_color      = $settings['circle_color'] ?? '#2196f3';
    $circle_font       = $settings['circle_font'] ?? 'Roboto';
    $circle_font_color = $settings['circle_font_color'] ?? '#000000';
    $circle_size       = intval($settings['circle_size'] ?? 100);
    $circle_glow       = !empty($settings['circle_glow']);
    $circle_pulse      = !empty($settings['circle_pulse']);
    $circle_speed      = intval($settings['circle_speed'] ?? 1000);
    ?>
    <div class="countdown-clock-circle<?php echo $circle_pulse ? ' countdown-circle-pulse' : ''; ?>"
        data-id="<?php echo esc_attr($id); ?>"
        data-target="<?php echo esc_attr($settings['target'] ?? ''); ?>"
        data-countup="<?php echo !empty($settings['countup']) ? '1' : '0'; ?>"
        data-bg="<?php echo esc_attr($circle_bg); ?>"
        data-color="<?php echo esc_attr($circle_color); ?>"
        data-font="<?php echo esc_attr($circle_font); ?>"
        data-fontcolor="<?php echo esc_attr($circle_font_color); ?>"
        data-thickness="<?php echo esc_attr($circle_thickness); ?>"
        data-size="<?php echo esc_attr($circle_size); ?>"
        data-glow="<?php echo $circle_glow ? '1' : '0'; ?>"
        data-speed="<?php echo esc_attr($circle_speed); ?>"
        data-redirect="<?php echo esc_attr($settings['redirect'] ?? ''); ?>"
        style="background: <?php echo esc_attr($container_bg); ?>; text-align:center;"
    >
        <?php if (!empty($title_text)) : ?>
            <div class="countdown-title" style="<?php echo esc_attr($title_style); ?>; margin-bottom:10px;">
                <?php echo esc_html($title_text); ?>
            </div>
        <?php endif; ?>
        <div class="countdown-circle-units" style="display:flex;gap:20px;justify-content:center;flex-wrap:wrap;">
            <?php
            $units = [
                'days'    => 'Days',
                'hours'   => 'Hours',
                'minutes' => 'Minutes',
                'seconds' => 'Seconds',
            ];
            foreach ($units as $unit_key => $unit_label) : ?>
                <div class="circle-unit" data-size="<?php echo esc_attr($circle_size); ?>">
                    <svg viewBox="0 0 100 100">
                        <circle class="bg" cx="50" cy="50" r="40"></circle>
                        <circle class="progress" cx="50" cy="50" r="40"></circle>
                    </svg>
                    <span class="circle-value value" data-unit="<?php echo esc_attr($unit_key); ?>">0</span>
                    <div class="circle-label">
                        <span class="unit label" style="<?php echo esc_attr($label_style); ?>">
                            <?php echo esc_html($unit_label); ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
} elseif ($format === 'text') {
    ?>
    <div class="countdown-clock"
        data-id="<?php echo esc_attr($id); ?>"
        data-target="<?php echo esc_attr($settings['target'] ?? ''); ?>"
        data-countup="<?php echo !empty($settings['countup']) ? '1' : '0'; ?>"
        data-redirect="<?php echo esc_attr($settings['redirect'] ?? ''); ?>"
        style="background: <?php echo esc_attr($container_bg); ?>; color: <?php echo esc_attr($text_color); ?>; font-family: '<?php echo esc_attr($font_family); ?>'; text-align:center;"
    >
        <?php if (!empty($title_text)) : ?>
            <div class="countdown-title" style="<?php echo esc_attr($title_style); ?>; margin-bottom:10px;">
                <?php echo esc_html($title_text); ?>
            </div>
        <?php endif; ?>
        <div class="countdown-timer" id="countdown-<?php echo esc_attr($id); ?>">Loading...</div>
    </div>
    <?php
} else {
    ?>
    <div class="countdown-clock"
        data-id="<?php echo esc_attr($id); ?>"
        data-target="<?php echo esc_attr($settings['target'] ?? ''); ?>"
        data-countup="<?php echo !empty($settings['countup']) ? '1' : '0'; ?>"
        data-redirect="<?php echo esc_attr($settings['redirect'] ?? ''); ?>"
        style="background: <?php echo esc_attr($container_bg); ?>; color: <?php echo esc_attr($text_color); ?>; font-family: '<?php echo esc_attr($font_family); ?>'; text-align:center;"
    >
        <?php if (!empty($title_text)) : ?>
            <div class="countdown-title" style="<?php echo esc_attr($title_style); ?>; margin-bottom:10px;">
                <?php echo esc_html($title_text); ?>
            </div>
        <?php endif; ?>
        <div class="countdown-timer" id="countdown-<?php echo esc_attr($id); ?>">Loading...</div>
    </div>
    <?php
}

    return ob_get_clean();
}




 
 // Enqueue frontend JS & styles
add_action('wp_enqueue_scripts', function() {
    // Digital / text formats
    wp_enqueue_script(
        'countdown-clock-frontend',
        plugin_dir_url(__FILE__) . 'countdown-clock-frontend.js',
        [],
        null,
        true
    );

    // Circle / donut format
    wp_enqueue_script(
        'countdown-clock-circle',
        plugin_dir_url(__FILE__) . 'countdown-clock-circle.js',
        [],
        null,
        true
    );

    // Circle / donut layout styles
    wp_enqueue_style(
        'countdown-clock-circle-style',
        plugin_dir_url(__FILE__) . 'countdown-clock.css',
        [],
        null
    );
});

// Load Google Fonts used by countdowns
add_action('wp_enqueue_scripts', function() {
    $data = get_option('countdown_clock_data', []);
    if (empty($data) || !is_array($data)) {
        return;
    }

    $families = [];
    foreach ($data as $id => $settings) {
        if (!is_array($settings)) {
            continue;
        }
        foreach (['font', 'circle_font', 'title_font', 'label_font'] as $key) {
            if (!empty($settings[$key])) {
                $families[$settings[$key]] = true;
            }
        }
    }

    if (empty($families)) {
        return;
    }

    $google_parts = [];
    foreach (array_keys($families) as $family) {
        $google_parts[] = str_replace(' ', '+', $family) . ':wght@400;700';
    }
    $url = 'https://fonts.googleapis.com/css2?family=' . implode('&family=', $google_parts) . '&display=swap';

    wp_enqueue_style(
        'countdown-clock-google-fonts',
        $url,
        [],
        null
    );
});

// Preview handler for admin live preview iframe
add_action('template_redirect', function() {
    if (!isset($_GET['preview_countdown'])) return;
    $id = sanitize_text_field($_GET['preview_countdown']);
    echo do_shortcode('[countdown_clock id="' . esc_attr($id) . '"]');
    exit;
});

// Register block (placeholder)
add_action('enqueue_block_editor_assets', function() {
    wp_enqueue_script('countdown-clock-block', plugin_dir_url(__FILE__) . 'countdown-clock-block.js', ['wp-blocks', 'wp-element'], null, true);
});

add_action('init', function () {
    if (isset($_GET['countdown_complete'])) {
        $id = sanitize_text_field($_GET['countdown_complete']);
        error_log("Countdown complete for ID: " . $id);
        wp_die(); // No output
    }
});
    
