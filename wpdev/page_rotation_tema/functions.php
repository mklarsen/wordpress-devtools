<?php
// Fjern "Powered by WordPress" fra footer
add_filter('the_generator', '__return_null');

// Fjern WordPress-version fra header
remove_action('wp_head', 'wp_generator');

// Indlæs Page Rotator-pluginets assets
function pr_theme_enqueue_assets() {
    if (is_front_page()) {
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
}
add_action('wp_enqueue_scripts', 'pr_theme_enqueue_assets');

// Tilføj temaindstillinger
function pr_theme_add_settings_page() {
    add_theme_page(
        'Page Rotator Settings',      // Sidens titel
        'Rotator Settings',           // Menu titel
        'manage_options',             // Kapacitet
        'pr-theme-settings',          // Menu slug
        'pr_theme_render_settings'    // Callback-funktion
    );
}
add_action('admin_menu', 'pr_theme_add_settings_page');

// Render indstillingssiden
function pr_theme_render_settings() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Håndter formularindsendelse
    if (isset($_POST['pr_theme_bg_color'])) {
        $bg_color = sanitize_hex_color($_POST['pr_theme_bg_color']);
        update_option('pr_theme_bg_color', $bg_color);
    }

    // Hent baggrundsfarve
    $bg_color = get_option('pr_theme_bg_color', '#000000');

    if (isset($_POST['pr_theme_selected_set'])) {
        $selected_set = sanitize_text_field($_POST['pr_theme_selected_set']);
        update_option('pr_theme_selected_set', $selected_set);
    }

    // Hent gemt værdi
    $selected_set = get_option('pr_theme_selected_set', 'default');
    $rotation_sets = get_option('pr_rotation_sets', []);

    ?>
    <div class="wrap">
        <h1>Page Rotator Tema -indstillinger</h1>
        <form method="post">
            <label for="pr_theme_selected_set">Vælg rotationssæt:</label>
            <select name="pr_theme_selected_set" id="pr_theme_selected_set">
                <?php foreach ($rotation_sets as $set_name => $set_data): ?>
                    <option value="<?php echo esc_attr($set_name); ?>" <?php selected($set_name, $selected_set); ?>>
                        <?php echo esc_html($set_data['title'] ?? $set_name); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="pr_theme_bg_color">Background Color:</label>
            <input type="color" id="pr_theme_bg_color" name="pr_theme_bg_color" value="<?php echo esc_attr($bg_color); ?>">

            <?php submit_button('Gem indstillinger'); ?>
        </form>
    </div>
    <?php
}

// Hent baggrundsfarve i temaet
function pr_theme_get_bg_color() {
    return get_option('pr_theme_bg_color', '#000000');
}

// Hent det valgte rotationssæt i temaet
function pr_theme_get_selected_set() {
    return get_option('pr_theme_selected_set', 'default');
}

// Debug function til at skrive til browserens konsol
function console($message, $type = 'info') {
    $types = ['info', 'warn', 'error'];
    if (!in_array($type, $types)) {
        $type = 'info';
    }
    echo "<script>console.{$type}('" . addslashes($message) . "');</script>";
}

// Sæt cookie, hvis checksum ikke er sat
function pr_theme_set_cookie() {
    $checksum = get_option('pr_rotation_checksum', '');
    setcookie("pr_rotation_checksum", $checksum, time() + (86400 * 30), "/"); // 86400 = 1 day
}
add_action('init', 'pr_theme_set_cookie', 'consolelog');
?>
