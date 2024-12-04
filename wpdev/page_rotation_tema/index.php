<?php get_header(); ?>

<div id="main-content">
    <?php
    $selected_set = pr_theme_get_selected_set();
    echo do_shortcode('[page_rotator set="' . esc_attr($selected_set) . '"]');
    ?>
</div>

<?php get_footer(); ?>
