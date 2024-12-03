<?php

// Automatisk tilføj Page Rotator shortcode på forsiden
function rp_insert_shortcode() {
    if (is_front_page()) {
        echo do_shortcode('[page_rotator]');
    }
}
add_action('wp_footer', 'rp_insert_shortcode');

// Registrer script og styles fra plugin, hvis nødvendigt
function rp_enqueue_scripts() {
    wp_enqueue_script('pr-rotation-js', plugin_dir_url(__FILE__) . '../plugins/page-rotator/assets/js/rotation.js', array('jquery'), '1.0', true);
    wp_enqueue_style('pr-style-css', plugin_dir_url(__FILE__) . '../plugins/page-rotator/assets/css/style.css');
}
add_action('wp_enqueue_scripts', 'rp_enqueue_scripts');
