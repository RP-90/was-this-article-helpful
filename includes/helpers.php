<?php

//Easy support to print human-readable version of a variable.
function pre($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

// Get the vote counts for a post
function wtah_get_vote_counts($post_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wtah_votes';

    // Get the positive votes
    $positive_votes = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE post_id = %d AND vote = '1'",
        $post_id
    ));

    // Get the negative votes
    $negative_votes = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE post_id = %d AND vote = '0'",
        $post_id
    ));

    return array($positive_votes, $negative_votes);
}

// Get the vote percentages for a post
function wtah_get_vote_percentages($post_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wtah_votes';

    $total_votes = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE post_id = %d", $post_id));
    
    if ($total_votes > 0) {
        $yes_votes = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE post_id = %d AND vote = 1", $post_id));
        $yes_percentage = round(($yes_votes / $total_votes) * 100);
        $no_percentage = 100 - $yes_percentage;
    } else {
        $yes_percentage = $no_percentage = 0;
    }

    return ['yes' => $yes_percentage, 'no' => $no_percentage];
}

//Multilingual support
function wtah_helpful_article_strings() {
    return array(
        'helpful_question' => __('Was this article helpful?', 'wtah'),
        'yes_answer' => __('Yes', 'wtah'),
        'no_answer' => __('No', 'wtah'),
    );
}
add_filter('wtah_helpful_article_strings', 'wtah_helpful_article_strings');

// Get the translated strings
function wtah_get_translated_strings() {
    return apply_filters('wtah_helpful_article_strings', wtah_helpful_article_strings());
}