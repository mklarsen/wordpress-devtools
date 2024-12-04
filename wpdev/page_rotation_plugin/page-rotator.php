<?php
/*
Plugin Name: Page Rotator Plugin
Description: Administrations Page Rotator
Version: 5.7
Author: MKLarsen
*/

if (!defined('ABSPATH')) exit; // Beskyt mod direkte adgang

// Inkluder admin-siden
require_once plugin_dir_path(__FILE__) . 'admin/admin-page.php';

// Registrer assets
function pr_enqueue_assets() {
    wp_enqueue_script(
        'pr-rotation-js',
        plugin_dir_url(__FILE__) . 'assets/js/rotation.js',
        array('jquery'),
        '1.0',
        true
    );
    wp_enqueue_style(
        'pr-style-css',
        plugin_dir_url(__FILE__) . 'assets/css/style.css'
    );
}
add_action('wp_enqueue_scripts', 'pr_enqueue_assets');

// Shortcode til at vise roterende sider
function pr_display_rotating_pages($atts) {
    $atts = shortcode_atts(['set' => 'default'], $atts, 'page_rotator');
    $rotation_sets = get_option('pr_rotation_sets', []);


    if (!isset($rotation_sets[$atts['set']])) {
        return '<p>Intet rotationssæt fundet.</p>';
    }

    $set_data = $rotation_sets[$atts['set']];
    $pages = $set_data['pages'];

    uasort($pages, function($a, $b) use ($set_data) {
        return ($set_data['orders'][$a] ?? 0) <=> ($set_data['orders'][$b] ?? 0);
    });
    

    $page_times = $set_data['times'];
    $current_time = current_time('H:i');

    $output = '<div id="pr-rotating-pages">';
    foreach ($pages as $page_id) {
        $start_time = $page_times[$page_id]['start'] ?? '00:00';
        $end_time = $page_times[$page_id]['end'] ?? '23:59';

        if ($current_time >= $start_time && $current_time <= $end_time) {
            $page = get_post($page_id);
            $output .= '<div class="pr-page" data-page-id="' . esc_attr($page_id) . '" data-duration="' . esc_attr($set_data['durations'][$page_id] ?? 5) . '" data-orders="' . esc_attr($set_data['orders'][$page_id] ?? 5) . '"    >';
            $output .= '<div>' . wp_kses_post(apply_filters('the_content', $page->post_content)) . '</div>';
            $output .= '</div>';
        }
    }
    $output .= '</div>';

    return $output;
}
add_shortcode('page_rotator', 'pr_display_rotating_pages');
