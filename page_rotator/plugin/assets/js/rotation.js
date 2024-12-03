jQuery(document).ready(function($) {
    const $pages = $('#pr-rotating-pages .pr-page');
    let currentIndex = 0;

    function showNextPage() {
        $pages.hide();
        $pages.eq(currentIndex).fadeIn();
        currentIndex = (currentIndex + 1) % $pages.length;
    }

    if ($pages.length > 0) {
        showNextPage();
        setInterval(showNextPage, 5000); // Skift side hvert 5. sekund
    }
});
