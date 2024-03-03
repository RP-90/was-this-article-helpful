<?php

function wtah_add_meta_boxes() {
    add_meta_box(
        'wtah_voting_results',          // ID of the metabox
        'Voting Results',               // Title of the metabox
        'wtah_display_voting_results',  // Callback function
        'post',                         // Screen on which to show the metabox
        'side',                         // Context where the box will show ('normal', 'side', 'advanced')
        'default'                       // Priority within the context
    );
}
add_action('add_meta_boxes', 'wtah_add_meta_boxes');

function wtah_display_voting_results($post) {
    // Get the vote counts for the post
    list($positive_votes, $negative_votes) = wtah_get_vote_counts($post->ID);

    // Calculate the total votes
    $total_votes = $positive_votes + $negative_votes;

    // Calculate percentages
    $positive_percentage = $total_votes > 0 ? ($positive_votes / $total_votes) * 100 : 0;
    $negative_percentage = $total_votes > 0 ? ($negative_votes / $total_votes) * 100 : 0;

    // Determine the color for each percentage
    $positive_color = 'green';
    $negative_color = 'red';
    echo 'Total votes: ' . esc_html($total_votes) . '<br>';
    echo '<span style="color: ' . $positive_color . ';">Positive votes: ' . esc_html($positive_votes) . ' (' . esc_html(number_format($positive_percentage, 2)) . '%)</span><br>';
    echo '<span style="color: ' . $negative_color . ';">Positive votes: ' . esc_html($negative_votes) . ' (' . esc_html(number_format($negative_percentage, 2)) . '%)</span>';

    // Add a checkbox to disable voting for this post
    $disable_voting = get_post_meta($post->ID, '_wtah_disable_voting', true);
    ?>
        <div style="margin-top: 20px;">
            <input type="checkbox" id="wtah_disable_voting" name="wtah_disable_voting" value="1" <?php checked($disable_voting, '1'); ?>>
            <label for="wtah_disable_voting">Disable Voting for this Post</label>
        </div>
    <?php
}


function wtah_save_postdata($post_id) {
    // Check if our nonce is set (add this if you use a nonce for your metabox).

    // Check if the current user has permission to edit the post.
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save the checkbox value
    $disable_voting = isset($_POST['wtah_disable_voting']) ? '1' : '';
    update_post_meta($post_id, '_wtah_disable_voting', $disable_voting);
}
add_action('save_post', 'wtah_save_postdata');