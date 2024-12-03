<?php
// Inkluder header (valgfrit, afhængigt af behov)
get_header();
?>
<div id="main-content">
    <!-- Hovedindholdet fra Page Rotator plugin -->
    <?php echo do_shortcode('[page_rotator]'); ?>
</div>
<!-- Fjern standard footer -->
<!-- Foderen er nu fjernet -->
<?php
// Luk dokumentet ordentligt, hvis nødvendigt
?>
