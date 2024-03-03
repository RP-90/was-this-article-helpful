<?php
/**
 * Plugin Name: Was This Article Helpful?
 * Description: Adds a simple voting system for articles with "Yes" and "No" buttons. Results shown as an average percentage.
 * Version: 1.0.0
 * Author: Rasmus Plambech
 * Author URI: https://github.com/RP-90 
 * Text Domain: was-this-article-helpful
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin absolute path
define( 'WTAH_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Include necessary files
require_once WTAH_PLUGIN_PATH . 'includes/functions.php';

// Include the rest of the functionalities in separate files
require_once WTAH_PLUGIN_PATH . 'includes/helpers.php';
require_once WTAH_PLUGIN_PATH . 'admin/metabox.php';
require_once WTAH_PLUGIN_PATH . 'admin/admin-columns.php';
require_once WTAH_PLUGIN_PATH . 'admin/settings.php';

function wtah_enqueue_scripts() {
    wp_enqueue_script('wtah-js', plugins_url('public/js/script.js', __FILE__), array('jquery'), '1.0.0', true);
    wp_enqueue_script('wtah-ajax', plugins_url('public/js/ajax.js', __FILE__), array('jquery'), '1.0.0', true);
    
    wp_localize_script('wtah-ajax', 'wtah_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('wtah-nonce')
    ));
    
    wp_enqueue_style('wtah-style', plugins_url('public/css/style.css', __FILE__), array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'wtah_enqueue_scripts');

// Create a table on plugin activation
function wtah_activate_plugin() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wtah_votes';

    // Check if the table already exists (good practice to check first)
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            post_id mediumint(9) NOT NULL,
            hashed_user_ip VARCHAR(64) NOT NULL, -- SHA-256 produces a 64-character hash
            vote TINYINT(1) NOT NULL, -- 0 for 'no', 1 for 'yes'
            PRIMARY KEY (id)
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    else {
        // Table already exists
        error_log('WTaH Votes table already exists.');
    }
}

// Hook the activation function
register_activation_hook(__FILE__, 'wtah_activate_plugin');

// Disable plugin updates
function wtah_disable_plugin_updates( $value ) {
    if ( isset( $value ) && is_object( $value ) ) {
        unset( $value->response['was-this-article-helpful/was-this-article-helpful.php'] );
    }
    return $value;
}
add_filter( 'site_transient_update_plugins', 'wtah_disable_plugin_updates' );