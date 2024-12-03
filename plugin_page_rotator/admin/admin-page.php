<?php
if (!defined('ABSPATH')) {
    exit;
}

function pr_add_admin_menu() {
    add_menu_page(
        'Page Rotator', 
        'Page Rotator', 
        'manage_options', 
        'page-rotator', 
        'pr_admin_page',
        'dashicons-images-alt2', 
        20
    );
}
add_action('admin_menu', 'pr_add_admin_menu');

function pr_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        update_option('pr_selected_pages', $_POST['pr_pages']);
    }

    $selected_pages = get_option('pr_selected_pages', []);
    $all_pages = get_posts(['post_type' => 'page', 'numberposts' => -1]);
    ?>
    <div class="wrap">
        <h1>Page Rotator Settings</h1>
        <form method="post">
            <h2>Vælg sider til rotation:</h2>
            <ul>
                <?php foreach ($all_pages as $page): ?>
                    <li>
                        <label>
                            <input type="checkbox" name="pr_pages[]" value="<?php echo esc_attr($page->ID); ?>" <?php checked(in_array($page->ID, $selected_pages)); ?>>
                            <?php echo esc_html($page->post_title); ?>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php submit_button('Gem ændringer'); ?>
        </form>
    </div>
    <?php
}
