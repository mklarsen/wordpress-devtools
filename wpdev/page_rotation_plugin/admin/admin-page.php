<?php
if (!defined('ABSPATH')) exit;

// Registrer admin-menu
function pr_add_admin_menu() {
    add_menu_page(
        'Page Rotator',
        'Page Rotator',
        'manage_options',
        'page-rotator',
        'pr_admin_page',
        'dashicons-admin-page',
        20
    );
}
add_action('admin_menu', 'pr_add_admin_menu');

// Administrationsside
function pr_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $rotation_sets = get_option('pr_rotation_sets', []);

    // Håndter sletning af et sæt
    if (isset($_GET['delete_set'])) {
        $set_to_delete = sanitize_text_field($_GET['delete_set']);
        if (isset($rotation_sets[$set_to_delete])) {
            unset($rotation_sets[$set_to_delete]);
            update_option('pr_rotation_sets', $rotation_sets);
            echo '<div class="updated"><p>Rotationssættet blev slettet.</p></div>';
        }
    }

    // Håndter formularindsendelse
    $current_set = isset($_GET['set']) ? sanitize_text_field($_GET['set']) : 'default';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['create_new_set'])) {
            $new_set_name = sanitize_text_field($_POST['new_set_name']);
            if (!isset($rotation_sets[$new_set_name])) {
                $rotation_sets[$new_set_name] = [
                    'pages' => [],
                    'times' => [],
                    'durations' => [],
                    'orders' => [],
                    'title' => $new_set_name,
                ];
                update_option('pr_rotation_sets', $rotation_sets);
                $current_set = $new_set_name;
            } else {
                echo '<div class="error"><p>Et rotationssæt med dette navn findes allerede.</p></div>';
            }
        } else {
            $current_set = sanitize_text_field($_POST['current_set']);
            $rotation_sets[$current_set] = [
                'pages' => $_POST['pr_pages'] ?? [],
                'times' => $_POST['pr_page_times'] ?? [],
                'durations' => $_POST['pr_page_durations'] ?? [],
                'orders' => $_POST['pr_page_orders'] ?? [],
                'title' => sanitize_text_field($_POST['pr_set_title']),
            ];

            update_option('pr_rotation_sets', $rotation_sets);
        }
    }

    $selected_pages = $rotation_sets[$current_set]['pages'] ?? [];
    $page_times = $rotation_sets[$current_set]['times'] ?? [];
    $set_title = $rotation_sets[$current_set]['title'] ?? $current_set;
    $page_durations = $rotation_sets[$current_set]['durations'] ?? [];
    $page_orders = $rotation_sets[$current_set]['orders'] ?? [];
    $all_pages = get_posts(['post_type' => 'page', 'numberposts' => -1]);
    ?>

    <div class="wrap">
        <h1>Page Rotator Indstillinger</h1>

        <!-- Dropdown for at vælge eller oprette et sæt -->
        <form method="get" style="margin-bottom: 20px;">
            <input type="hidden" name="page" value="page-rotator">
            <label for="set">Vælg rotationssæt:</label>
            <select name="set" id="set" onchange="this.form.submit()">
                <?php foreach ($rotation_sets as $set_name => $set_data): ?>
                    <option value="<?php echo esc_attr($set_name); ?>" <?php selected($set_name, $current_set); ?>>
                        <?php echo esc_html($set_data['title'] ?? $set_name); ?>
                    </option>
                <?php endforeach; ?>
                <option value="new">+ Opret nyt sæt</option>
            </select>
        </form>

        <?php if ($current_set === 'new'): ?>
            <form method="post">
                <h2>Opret nyt rotationssæt</h2>
                <label for="new_set_name">Navn på sæt:</label>
                <input type="text" id="new_set_name" name="new_set_name" required>
                <button type="submit" name="create_new_set">Opret</button>
            </form>
        <?php else: ?>
            <form method="post">
                <h2>Rediger sæt: <?php echo esc_html($set_title); ?></h2>
                <input type="hidden" name="current_set" value="<?php echo esc_attr($current_set); ?>">
                <label for="pr_set_title">Sættets titel:</label>
                <input type="text" id="pr_set_title" name="pr_set_title" value="<?php echo esc_attr($set_title); ?>" style="display: inline-block; width: 50%; margin-bottom: 20px;">

                <ul>
                    <?php foreach ($all_pages as $page): ?>
                        <li>
                            <hr>
                                <label>
                                    <input type="checkbox" name="pr_pages[]" value="<?php echo esc_attr($page->ID); ?>" <?php checked(in_array($page->ID, $selected_pages)); ?>>
                                    <b><?php echo esc_html($page->post_title); ?></b>
                                </label>
                                <div style="margin-left: 20px;">
                                    Starttid: <input type="time" name="pr_page_times[<?php echo esc_attr($page->ID); ?>][start]" value="<?php echo esc_attr($page_times[$page->ID]['start'] ?? '00:00'); ?>">
                                    Sluttid: <input type="time" name="pr_page_times[<?php echo esc_attr($page->ID); ?>][end]" value="<?php echo esc_attr($page_times[$page->ID]['end'] ?? '23:59'); ?>">
                                    Tid (seconds): <input type="number" name="pr_page_durations[<?php echo esc_attr($page->ID); ?>]" value="<?php echo esc_attr($page_durations[$page->ID] ?? '5'); ?>" min="1" style="width: 60px;">
                                    Order: <input type="number" name="pr_page_orders[<?php echo esc_attr($page->ID); ?>]" value="<?php echo esc_attr($page_orders[$page->ID] ?? '0'); ?>" style="width: 60px;">

                                </div>
                            <hr>
                            <br>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php submit_button('Gem ændringer'); ?>
            </form>

            <!-- Slet sæt -->
            <form method="get" style="margin-top: 20px;">
                <input type="hidden" name="page" value="page-rotator">
                <input type="hidden" name="delete_set" value="<?php echo esc_attr($current_set); ?>">
                <button type="submit" class="button button-secondary" onclick="return confirm('Er du sikker på, at du vil slette dette rotationssæt?')">Slet dette sæt</button>
            </form>
        <?php endif; ?>
    </div>
    <?php
}
