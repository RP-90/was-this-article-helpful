<?php
function wtah_plugin_menu() {
    add_management_page(
        'WTAH Plugin Settings', // Page title
        'WTAH Voting', // Menu title
        'manage_options', // Capability
        'wtah-plugin-settings', // Menu slug
        'wtah_plugin_settings_page' // Function
    );
}
add_action('admin_menu', 'wtah_plugin_menu');

function wtah_plugin_settings_init() {

     // Register new setting for enabling/disabling frontend display
    register_setting('wtah-plugin-settings', 'wtah_enable_frontend', ['default' => '1']);

    //Register new setting for design option
    register_setting('wtah-plugin-settings', 'wtah_design_option', ['default' => 'design1']);

    add_settings_section(
        'wtah_design_section', 
        'Design Settings', 
        'wtah_design_section_callback', 
        'wtah-plugin-settings'
    );

    // Add enable frontend display field
    add_settings_field(
        'wtah_enable_frontend_field', 
        'Show on Frontend?', 
        'wtah_enable_frontend_field_callback', 
        'wtah-plugin-settings', 
        'wtah_design_section'
    );

    // Add choose design field
    add_settings_field(
        'wtah_design_field', 
        'Select Design', 
        'wtah_design_field_callback', 
        'wtah-plugin-settings', 
        'wtah_design_section'
    );
}
add_action('admin_init', 'wtah_plugin_settings_init');

// Callback functions for the settings fields
function wtah_design_section_callback() {
    echo esc_html('Choose the design for the voting buttons.');
}

// Enable frontend display field callback
function wtah_enable_frontend_field_callback() {
    $option = get_option('wtah_enable_frontend');
    ?>
        <input type="checkbox" name="wtah_enable_frontend" value="1" <?php checked(1, $option, true); ?>/>
    <?php
}

// Design option field callback
function wtah_design_field_callback() {
    $design_option = get_option('wtah_design_option');
    ?>
        <select name="wtah_design_option">
            <label>Select where the voting should be displayed</label>
            <option value="design1" <?php selected($design_option, 'design1'); ?>>After post output</option>
            <option value="design2" <?php selected($design_option, 'design2'); ?>>As a right side slideup</option>
        </select>
    <?php
}

// Settings page
function wtah_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h1>WTAH Plugin Settings</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields('wtah-plugin-settings');
                do_settings_sections('wtah-plugin-settings');
                submit_button();
            ?>
        </form>
    </div>
    <?php
}