<?php get_header(); ?>


<?php
    if(!isset($_COOKIE['pr_rotation_checksum'])) {
        console("Cookie named pr_rotation_checksum is not set", "warn");
    } else {
        console("Cookie pr_rotation_checksum is set value is: " . $_COOKIE['pr_rotation_checksum'], "info");
    }
?>

<div id="main-content">
    <?php
        $selected_set = pr_theme_get_selected_set();
        echo do_shortcode('[page_rotator set="' . esc_attr($selected_set) . '"]');
    ?>
</div>


<?php get_footer(); ?>
