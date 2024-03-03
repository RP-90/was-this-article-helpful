<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function wtah_append_voting_buttons($content) {
    // Check if we're inside the main loop in a single post page.
    if (is_single() && in_the_loop() && is_main_query()) {

        // Check if the frontend display is enabled and if voting is not disabled for this post
        $frontend_enabled = get_option('wtah_enable_frontend', '1') == '1';
        $disable_voting = get_post_meta(get_the_ID(), '_wtah_disable_voting', true) == '1';
        $strings = wtah_get_translated_strings();

        // Exit if frontend display is disabled or voting is disabled for this post
        if (!$frontend_enabled || $disable_voting) {
            return $content; 
        }

        global $wpdb;

        // Get the user IP and the post ID
        $user_ip = $_SERVER['REMOTE_ADDR'];

        // Get the post ID
        $post_id = get_the_ID(); 

        // Define the table name
        $table_name = $wpdb->prefix . 'wtah_votes';

        // Hash the IP without salt for consistent hashing
        $hashed_user_ip = hash('sha256', $user_ip);

        // Check if this user (hashed IP) has already voted on this post
        $vote = $wpdb->get_var($wpdb->prepare(
            "SELECT vote FROM $table_name WHERE post_id = %d AND hashed_user_ip = %s",
            $post_id, $hashed_user_ip
        ));

        // Get the design option
        $design_option = esc_attr(get_option('wtah_design_option'));

        // Define the HTML for voting buttons.
        $voting_html = '
        <div id="wtah-voting-buttons" class="' . $design_option . '" data-post-id="' . esc_attr($post_id) . '">
        <p class="intro">' . esc_html($strings['helpful_question']) . '</p><div class="button-wrapper">';

        // Check if user already voted
        if ($vote !== null) {

            // Get vote percentages
            $percentages = wtah_get_vote_percentages($post_id);
            $yes_percentage = $percentages['yes'];
            $no_percentage = $percentages['no'];

            // User has voted, disable the voted button
            $yes_disabled = $vote == '1' ? 'disabled' : '';
            $no_disabled = $vote == '0' ? 'disabled' : '';

            $voting_html .= '
                <button id="wtah-yes" class="wtah-vote-btn" data-original-text="'.esc_html($strings['yes_answer']).'" data-vote="yes" ' . $yes_disabled . '>' . $yes_percentage . '% ' . esc_html($strings['yes_answer']) . '</button>
                <button id="wtah-no" class="wtah-vote-btn" data-original-text="'.esc_html($strings['no_answer']).'" data-vote="no" ' . $no_disabled . '>' . $no_percentage . '% ' . esc_html($strings['no_answer']) . '</button>
            ';
        } else {
            // User hasn't voted, show active buttons
            $voting_html .= '
                <button id="wtah-yes" class="wtah-vote-btn" data-original-text="'.esc_html($strings['yes_answer']).'" data-vote="yes">' . esc_html($strings['yes_answer']) . '</button>
                <button id="wtah-no" class="wtah-vote-btn" data-original-text="'.esc_html($strings['no_answer']).'" data-vote="no">' . esc_html($strings['no_answer']) . '</button>
            ';}

        $voting_html .= '</div></div>';

        // Append the voting HTML to the content.
        $content .= $voting_html;
    }

    // Return the content with or without the voting buttons.
    return $content;
}

// Add the filter to the_content.
add_filter('the_content', 'wtah_append_voting_buttons');

function wtah_handle_vote() {
    check_ajax_referer('wtah-nonce', 'security'); // Check nonce for security
    global $wpdb;

    //Get the post ID and make sure it's integer and exists
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    if (!$post_id || !get_post($post_id)) {
        wp_send_json_error('Invalid post ID.');
        return; // Exit if the post ID is not valid
    }

    //Define the user vote
    $user_vote = ($_POST['vote'] === 'yes') ? 1 : 0;

    //Get the user IP
    $user_ip = $_SERVER['REMOTE_ADDR'];

    //Table name
    $table_name = $wpdb->prefix . 'wtah_votes';

    // Hash the IP without salt for consistent hashing
    $hashed_user_ip = hash('sha256', $user_ip);

    // Rate limit the votes
    $rate_limit_key = 'vote_limit_' . $hashed_user_ip;
    $rate_count = get_transient($rate_limit_key) ?: 0;
    if ($rate_count > 50) {
        wp_send_json_error('You have exceeded the maximum number of votes allowed per 5 minuts.');
        return; // Exit if the rate limit is exceeded
    }

    // Update the rate count and set the transient to expire in 5 minutes (300 seconds)
    set_transient($rate_limit_key, $rate_count + 1, 300);

    // Retrieve the existing vote from the database
    $existing_vote = $wpdb->get_var($wpdb->prepare(
        "SELECT vote FROM $table_name WHERE post_id = %d AND hashed_user_ip = %s",
        $post_id, $hashed_user_ip
    ));

    // Check and handle the vote
    if ($existing_vote !== null) {
        if (intval($existing_vote) !== $user_vote) {
            $wpdb->update(
                $table_name,
                ['vote' => $user_vote],
                ['post_id' => $post_id, 'hashed_user_ip' => $hashed_user_ip]
            );
        }
    } else {
        $wpdb->insert($table_name, [
            'post_id' => $post_id,
            'hashed_user_ip' => $hashed_user_ip,
            'vote' => $user_vote
        ]);
    }

    // Use the helper function to get vote percentages
    $percentages = wtah_get_vote_percentages($post_id);

    // Send back the response
    wp_send_json_success(['yes_percentage' => $percentages['yes'], 'no_percentage' => $percentages['no']]);

}

// Add the AJAX action
add_action('wp_ajax_nopriv_wtah_handle_vote', 'wtah_handle_vote');
add_action('wp_ajax_wtah_handle_vote', 'wtah_handle_vote');