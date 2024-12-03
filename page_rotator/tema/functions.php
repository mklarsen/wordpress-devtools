<?php

// Fjern WordPress version meta-tag
remove_action('wp_head', 'wp_generator');

// Fjern WordPress-udskrivning af "Powered by WordPress" fra admin-footer
function rp_remove_admin_footer_text() {
    return '';
}
add_filter('admin_footer_text', 'rp_remove_admin_footer_text');

// Fjern standard widgets i footeren, hvis temaet understøtter widgets
function rp_remove_default_footer_widgets() {
    unregister_widget('WP_Widget_Meta');
}
add_action('widgets_init', 'rp_remove_default_footer_widgets');

// Fjern WordPress logo og link fra admin-bar
function rp_remove_wp_logo($wp_admin_bar) {
    $wp_admin_bar->remove_node('wp-logo');
}
add_action('admin_bar_menu', 'rp_remove_wp_logo', 999);

// Sørg for, at pluginets scripts og styles bliver indlæst
function rp_enqueue_plugin_assets() {
    wp_enqueue_script(
        'pr-rotation-js',
        plugin_dir_url(WP_PLUGIN_DIR . '/page-rotator/assets/js/rotation.js'),
        array('jquery'),
        '1.0',
        true
    );

    wp_enqueue_style(
        'pr-style-css',
        plugin_dir_url(WP_PLUGIN_DIR . '/page-rotator/assets/css/style.css')
    );
}
add_action('wp_enqueue_scripts', 'rp_enqueue_plugin_assets');

// Deaktiver RSS-feeds, hvis de ikke bruges
function rp_disable_feed() {
    wp_die(__('Feeds are disabled for this site.'));
}
add_action('do_feed', 'rp_disable_feed', 1);
add_action('do_feed_rdf', 'rp_disable_feed', 1);
add_action('do_feed_rss', 'rp_disable_feed', 1);
add_action('do_feed_rss2', 'rp_disable_feed', 1);
add_action('do_feed_atom', 'rp_disable_feed', 1);
