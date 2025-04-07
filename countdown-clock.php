<?php
/*
Plugin Name: Countdown Clock
Description: A simple countdown clock with admin interface and GitHub auto-updates.
Version: 1.0
Author: Dayna092
Plugin URI: https://github.com/dayna092/countdown-clock/
*/

defined('ABSPATH') or die('No script kiddies please!');

// ─────────────────────────────────────────────────────
// Add Countdown Clock to Admin Menu
// ─────────────────────────────────────────────────────

add_action('admin_menu', 'countdown_clock_add_admin_menu');

function countdown_clock_add_admin_menu() {
    add_menu_page(
        'Countdown Clock',                    // Page title
        'Countdown Clock',                    // Menu label
        'manage_options',                     // Capability
        'countdown-clock',                    // Slug
        'countdown_clock_admin_page',         // Callback function
        'dashicons-clock',                    // Icon
        6                                     // Position
    );
}

// ─────────────────────────────────────────────────────
// Admin Page Callback
// ─────────────────────────────────────────────────────

function countdown_clock_admin_page() {
    echo '<div class="wrap">';
    echo '<h1>Countdown Clock</h1>';
    echo '<p>This is where your countdown timer preview or settings can go.</p>';
    echo '<div id="countdown-clock">';
    echo '<strong>Time Remaining:</strong> <span id="clock-display">Loading...</span>';
    echo '</div>';
    echo '</div>';
}

// ─────────────────────────────────────────────────────
// Enqueue Admin Styles & Scripts
// ─────────────────────────────────────────────────────

add_action('admin_enqueue_scripts', 'countdown_clock_enqueue_assets');

function countdown_clock_enqueue_assets($hook) {
    // Only load on this plugin’s page
    if ($hook !== 'toplevel_page_countdown-clock') {
        return;
    }

    wp_enqueue_style(
        'countdown-clock-style',
        plugin_dir_url(__FILE__) . 'assets/css/admin-style.css'
    );

    wp_enqueue_script(
        'countdown-clock-script',
        plugin_dir_url(__FILE__) . 'assets/js/countdown.js',
        array(),
        null,
        true
    );
}

// ─────────────────────────────────────────────────────
// GitHub Plugin Update Checker
// ─────────────────────────────────────────────────────

require plugin_dir_path(__FILE__) . 'plugin-update-checker/plugin-update-checker.php';

$updateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/dayna092/countdown-clock/', // GitHub repo
    __FILE__,                                       // Full path to this plugin file
    'countdown-clock'                               // Plugin slug
);

$updateChecker->setBranch('main'); // Or 'master' if that's your branch

