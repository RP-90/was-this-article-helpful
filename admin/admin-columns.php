<?php

// Add a new column to the post list table
function wtah_add_voting_columns($columns) {
    $columns['wtah_voting'] = 'Voting Results';
    return $columns;
}
add_filter('manage_posts_columns', 'wtah_add_voting_columns');

function wtah_voting_column_content($column, $post_id) {
    if ($column == 'wtah_voting') {
        // Get the vote counts for the post
        list($positive_votes, $negative_votes) = wtah_get_vote_counts($post_id);

        // Calculate the total votes
        $total_votes = $positive_votes + $negative_votes;

        // Calculate percentages
        $positive_percentage = $total_votes > 0 ? ($positive_votes / $total_votes) * 100 : 0;
        $negative_percentage = $total_votes > 0 ? ($negative_votes / $total_votes) * 100 : 0;

        // Determine the color for each percentage
        $positive_color = 'green';
        $negative_color = 'red';
        echo 'Total votes: ' . esc_html($total_votes) . '<br>';
        echo '<span style="color: ' . $positive_color . ';">Positive: ' . esc_html(number_format($positive_percentage, 2)) . '%</span><br>';
        echo '<span style="color: ' . $negative_color . ';">Negative: ' . esc_html(number_format($negative_percentage, 2)) . '%</span>';
    }
}
add_action('manage_posts_custom_column', 'wtah_voting_column_content', 10, 2);
