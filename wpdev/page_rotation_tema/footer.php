<?php wp_footer(); ?>

<script>
jQuery(document).ready(function($) {
    // Hent og set baggrundsfarve
    var bgColor = '<?php echo get_option('pr_theme_bg_color', '#000000'); ?>';
    $('body').css('background-color', bgColor);
});
</script>

</body>
</html>
