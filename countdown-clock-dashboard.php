
<?php
// Adds "All Countdowns" admin page
add_action('admin_menu', function() {
    add_submenu_page(
        'bse-countdown-clock',
        'All Countdowns',
        'All Countdowns',
        'manage_options',
        'countdown-clock-dashboard',
        'countdown_clock_render_dashboard'
    );
});

function countdown_clock_render_dashboard() {
    $data = get_option('countdown_clock_data', []);
    ?>
    <div class="wrap">
        <h1>📋 All Countdowns</h1>
        <?php if (empty($data)) : ?>
            <p>No countdowns found.</p>
        <?php else: ?>
        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Format</th>
                    <th>Target Date</th>
                    <th>Status</th>
                    <th>Shortcode</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $id => $settings) :
                    $target = $settings['target'] ?? '';
                    $format = $settings['format'] ?? 'digital';
                    $now = current_time('Y-m-d\TH:i');
                    $start = $settings['start'] ?? '';
                    $stop = $settings['stop'] ?? '';
                    $active = (!$start || $now >= $start) && (!$stop || $now <= $stop);
                ?>
                <tr>
                    <td><strong><?php echo esc_html($id); ?></strong></td>
                    <td><?php echo esc_html(ucfirst($format)); ?></td>
                    <td><?php echo esc_html($target); ?></td>
                    <td><?php echo $active ? '<span style="color:green;">Active</span>' : '<span style="color:red;">Inactive</span>'; ?></td>
                    <td><code>[countdown_clock id="<?php echo esc_attr($id); ?>"]</code></td>
                    <td>
                        <a href="<?php echo admin_url('options-general.php?page=bse-countdown-clock&clock_id=' . esc_attr($id)); ?>" class="button">Edit</a>
                        <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=delete_countdown_clock&id=' . esc_attr($id)), 'delete_countdown_' . $id); ?>" class="button delete-button" onclick="return confirm('Delete countdown <?php echo esc_js($id); ?>?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php
}

// Handle deletion
add_action('admin_post_delete_countdown_clock', function() {
    if (!current_user_can('manage_options') || !isset($_GET['id'])) wp_die('Access denied');
    $id = sanitize_text_field($_GET['id']);
    check_admin_referer('delete_countdown_' . $id);
    $data = get_option('countdown_clock_data', []);
    unset($data[$id]);
    update_option('countdown_clock_data', $data);
    wp_redirect(admin_url('admin.php?page=countdown-clock-dashboard'));
    exit;
});
