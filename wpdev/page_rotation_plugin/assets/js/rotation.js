jQuery(document).ready(function($) {
    const $pages = $('#pr-rotating-pages .pr-page');
    let currentIndex = 0;

    let d = $pages.map(function() {
        console.info( $(this).data() ); // Print all data attributes    
    }).get();
    
    let durations = $pages.map(function() {
        return $(this).data('duration') || 5000; // Default 5 sec
    }).get();

    function showNextPage() {
        $pages.removeClass('active');
        $pages.eq(currentIndex).addClass('active');
        let remainingTime = durations[currentIndex];

        const intervalId = setInterval(() => {
            if (remainingTime > 1) {
                console.info('Current pageID ',$pages.eq(currentIndex).data('pageId'), ' Next in:', remainingTime, 'Sec.');
            } else if (remainingTime === 1) {
                console.info('Switching page...');
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

    function fetchCookieValue() {
        const cookieName = 'pr_rotation_checksum';
        const cookieValue = document.cookie.split('; ').find(row => row.startsWith(cookieName)).split('=')[1];
        console.info('Cookie value:', cookieValue);
    }

    setInterval(fetchCookieValue, 20000); // Fetch cookie value every 20 seconds

    if ($pages.length > 0) {
        showNextPage(); // Start rotationen
    }
});
