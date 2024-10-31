<?php
/*
Plugin Name: Nafeza Coming Soon
Description: Simple Plugin For Create Coming Soon Or Maintenance mode page
Version: 1.0.1
Author: Hussam Barbour
Text Domain: nafeza-coming-soon
License: GPL-2.0-or-later
*/
if (!defined('ABSPATH')) exit;

// Load plugin textdomain for translations
add_action('plugins_loaded', 'nafeza_coming_soon_load_textdomain');
function nafeza_coming_soon_load_textdomain() {
    load_plugin_textdomain('nafeza-coming-soon', false, basename(dirname(__FILE__)) . '/languages/');
}

add_action('template_redirect', 'nafeza_coming_soon_display');
function nafeza_coming_soon_display() {
    if (get_option('nafeza_coming_soon_active') && !current_user_can('edit_themes')) {
        $page_id = get_option('nafeza_coming_soon_page_id');
        if ($page_id) {
            $page_url = get_permalink($page_id);
            $actual_link = esc_url_raw( ( empty( $_SERVER['HTTPS'] ) ? 'http' : 'https' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" );
            
            if ($page_url && $actual_link != $page_url) {
                wp_redirect($page_url);
                exit;
            }
        } else {
            $message = get_option('nafeza_coming_soon_message', esc_html__('Our website is currently undergoing scheduled maintenance. Please check back soon!', 'nafeza-coming-soon'));
            wp_die($message, esc_html__('Coming Soon', 'nafeza-coming-soon'));
        }
    }
}

add_action('admin_notices', 'nafeza_coming_soon_admin_notice');
function nafeza_coming_soon_admin_notice() {
    if (get_option('nafeza_coming_soon_active')) {
        echo '<div class="notice notice-warning is-dismissible"><p><strong>' . esc_html__('Coming Soon Mode is Active!', 'nafeza-coming-soon') . '</strong> ' . esc_html__("Don't forget to deactivate when you are done.", "nafeza-coming-soon") . '</p></div>';
    }
}

add_action('admin_menu', 'nafeza_coming_soon_settings_page');
function nafeza_coming_soon_settings_page() {
    add_options_page(__('Coming Soon Settings', 'nafeza-coming-soon'), esc_html__('Coming Soon', 'nafeza-coming-soon'), 'manage_options', 'nafeza-coming-soon', 'nafeza_coming_soon_settings_content');
}

function nafeza_coming_soon_settings_content() {
?>
    <div class="wrap">
        <h1><?php echo esc_html__('Coming Soon Settings', 'nafeza-coming-soon'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('nafeza_coming_soon_group');
            do_settings_sections('nafeza-coming-soon');
            ?>
            <p><strong><?php echo esc_html__('Note:', 'nafeza-coming-soon'); ?></strong> <?php echo esc_html__('If the "Coming Soon Page" is selected, the "Coming Soon Message" will not be displayed.', 'nafeza-coming-soon'); ?></p>
            <?php
            submit_button();
            ?>
        </form>
    </div>
<?php
}

add_action('admin_init', 'nafeza_coming_soon_register_settings');
function nafeza_coming_soon_register_settings() {
    register_setting('nafeza_coming_soon_group', 'nafeza_coming_soon_active');
    register_setting('nafeza_coming_soon_group', 'nafeza_coming_soon_message');
    register_setting('nafeza_coming_soon_group', 'nafeza_coming_soon_page_id');

    add_settings_section('nafeza_coming_soon_main', esc_html__('Main Settings', 'nafeza-coming-soon'), null, 'nafeza-coming-soon');

    add_settings_field('nafeza_coming_soon_active_field', esc_html__('Activate Coming Soon Mode', 'nafeza-coming-soon'), 'nafeza_coming_soon_active_callback', 'nafeza-coming-soon', 'nafeza_coming_soon_main');
    add_settings_field('nafeza_coming_soon_message_field', esc_html__('Coming Soon Message', 'nafeza-coming-soon'), 'nafeza_coming_soon_message_callback', 'nafeza-coming-soon', 'nafeza_coming_soon_main');
    add_settings_field('nafeza_coming_soon_page_id_field', esc_html__('Coming Soon Page', 'nafeza-coming-soon'), 'nafeza_coming_soon_page_callback', 'nafeza-coming-soon', 'nafeza_coming_soon_main');
}

function nafeza_coming_soon_active_callback() {
    $isActive = get_option('nafeza_coming_soon_active');
    echo '<input type="checkbox" name="nafeza_coming_soon_active" value="1" ' . checked(1, $isActive, false) . ' />';
}

function nafeza_coming_soon_message_callback() {
    $message = get_option('nafeza_coming_soon_message', esc_html__('Our website is currently undergoing scheduled maintenance. Please check back soon!', 'nafeza-coming-soon'));
    echo '<textarea name="nafeza_coming_soon_message" rows="5" cols="50">' . esc_textarea($message) . '</textarea>';
}

function nafeza_coming_soon_page_callback() {
    $selected_page_id = get_option('nafeza_coming_soon_page_id');
    wp_dropdown_pages(array(
        'name' => 'nafeza_coming_soon_page_id',
        'selected' => $selected_page_id,
        'show_option_none' => esc_html__('Select a page', 'nafeza-coming-soon'),
    ));
}
?>
