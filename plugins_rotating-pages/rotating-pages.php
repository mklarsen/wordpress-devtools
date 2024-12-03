<?php
/*
Plugin Name: Rotating Pages
Description: Viser udvalgte sider med rotation på front-end og giver admin mulighed for at styre rotationstid.
Version: 1.1
Author: Martin Kraus Larsen
*/

// Register shortcode and load scripts
add_action('init', 'rp_register_shortcode');
function rp_register_shortcode() {
    add_shortcode('rotating_pages', 'rp_display_rotating_pages');
}

// Register admin menu and settings
add_action('admin_menu', 'rp_add_admin_menu');
add_action('admin_init', 'rp_register_settings');

function rp_add_admin_menu() {
    add_menu_page('Rotating Pages', 'Rotating Pages', 'manage_options', 'rotating-pages', 'rp_settings_page');
}

function rp_register_settings() {
    register_setting('rp_settings_group', 'rp_selected_pages');
    register_setting('rp_settings_group', 'rp_rotation_time');
}

// Frontend display function
function rp_display_rotating_pages() {
    $selected_pages = get_option('rp_selected_pages', []);
    $rotation_time = get_option('rp_rotation_time', 5) * 1000; // convert to milliseconds

    if (empty($selected_pages)) {
        return "<p>Ingen sider valgt til rotation.</p>";
    }

    ob_start();
    ?>
    <div id="rotating-pages-container">
        <?php foreach ($selected_pages as $page_id): ?>
            <div class="rotating-page" style="display:none;">
                <?php echo apply_filters('the_content', get_post_field('post_content', $page_id)); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let pages = document.querySelectorAll('#rotating-pages-container .rotating-page');
            let index = 0;
            function rotatePages() {
                pages.forEach(page => page.style.display = 'none');
                pages[index].style.display = 'block';
                index = (index + 1) % pages.length;
            }
            rotatePages();
            setInterval(rotatePages, <?php echo $rotation_time; ?>);
        });
    </script>
    <style>
        #rotating-pages-container {
            width: 100%;
            margin: 0 auto;
        }
    </style>
    <?php
    return ob_get_clean();
}

// Admin page for plugin settings
function rp_settings_page() {
    ?>
    <div class="wrap">
        <h1>Rotating Pages Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('rp_settings_group'); ?>
            <?php do_settings_sections('rp_settings_group'); ?>
            
            <h2>Vælg Sider til Rotation</h2>
            <select name="rp_selected_pages[]" multiple style="width: 100%; height: 200px;">
                <?php
                $pages = get_pages();
                $selected_pages = get_option('rp_selected_pages', []);
                foreach ($pages as $page) {
                    $selected = in_array($page->ID, $selected_pages) ? 'selected' : '';
                    echo "<option value='{$page->ID}' $selected>{$page->post_title}</option>";
                }
                ?>
            </select>

            <h2>Rotationstid (sekunder)</h2>
            <input type="number" name="rp_rotation_time" value="<?php echo esc_attr(get_option('rp_rotation_time', 5)); ?>" min="1" />

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
?>