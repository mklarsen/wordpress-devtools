<?php
/*
Plugin Name: Page Rotator
Description: Vis et antal sider i rotation
Version: 1.2
Author: MKLarsen
*/

if (!defined('ABSPATH')) {
    exit; // Sikrer, at filen ikke kan tilgås direkte
}

// Inkluder administrationssiden
require_once plugin_dir_path(__FILE__) . 'admin/admin-page.php';

// Registrer shortcodes og scripts
function pr_register_assets() {
    wp_enqueue_script('pr-rotation-js', plugin_dir_url(__FILE__) . 'assets/js/rotation.js', array('jquery'), '1.0', true);
    wp_enqueue_style('pr-style-css', plugin_dir_url(__FILE__) . 'assets/css/style.css');
}
add_action('wp_enqueue_scripts', 'pr_register_assets');

// Opret shortcode
function pr_display_rotating_pages() {
    $selected_pages = get_option('pr_selected_pages', []);
    $page_times = get_option('pr_page_times', []);

    if (empty($selected_pages)) {
        return '<p>No pages selected for rotation.</p>';
    }

    $current_time = current_time('H:i'); // Få den aktuelle tid
    $output = '<div id="pr-rotating-pages">';

    foreach ($selected_pages as $page_id) {
        $page = get_post($page_id);
        $start_time = isset($page_times[$page_id]['start']) ? $page_times[$page_id]['start'] : '00:00';
        $end_time = isset($page_times[$page_id]['end']) ? $page_times[$page_id]['end'] : '23:59';

        // Kontrollér om den aktuelle tid falder inden for intervallet
        if ($current_time >= $start_time && $current_time <= $end_time) {
            $output .= '<div class="pr-page" data-page-id="' . esc_attr($page_id) . '">';
            $output .= '<h2>' . esc_html($page->post_title) . '</h2>';
            $output .= '<div>' . wp_kses_post(apply_filters('the_content', $page->post_content)) . '</div>';
            $output .= '</div>';
        }
    }

    $output .= '</div>';

    return $output;
}
add_shortcode('page_rotator', 'pr_display_rotating_pages');
