jQuery(document).ready(function($) {
    const $pages = $('#pr-rotating-pages .pr-page');
    let currentIndex = 0;
    let durations = $pages.map(function() {
        return $(this).data('duration') || 5000; // Default 5 sec
    }).get();

    function showNextPage() {
        $pages.removeClass('active');
        $pages.eq(currentIndex).addClass('active');
        let remainingTime = durations[currentIndex];
        const intervalId = setInterval(() => {
            if (remainingTime > 1) {
                console.info('Current PageID:', currentIndex + 1, '> Next in:', remainingTime, 'Sec.');
            } else if (remainingTime === 1) {
                console.info('Switching to next page...');
            }
            remainingTime--;
            if (remainingTime < 0) {
                clearInterval(intervalId);
            }
        }, 1000);
        setTimeout(() => {
            currentIndex = (currentIndex + 1) % $pages.length;
            showNextPage();
        }, durations[currentIndex] * 1000);
    }

    if ($pages.length > 0) {
        showNextPage(); // Start rotationen
    }
});
