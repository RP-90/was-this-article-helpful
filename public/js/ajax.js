jQuery(document).ready(function($) {
    $('.wtah-vote-btn').on('click', function(e) {
        e.preventDefault();
        var $this = $(this); // Store the clicked button
        var vote = $this.data('vote'); // Get the vote type
        var postID = $this.closest('#wtah-voting-buttons').data('post-id'); // Get the post ID

        $.ajax({
            type: 'POST',
            url: wtah_ajax_object.ajax_url, // Localized via wp_localize_script
            data: {
                action: 'wtah_handle_vote',
                vote: vote,
                post_id: postID,
                security: wtah_ajax_object.security // Nonce for security
            },
            success: function(response) {
                if(response.success && response.data) {
                    var yesPercentage = Math.round(response.data.yes_percentage); // Use yes_percentage from the response
                    var noPercentage = Math.round(response.data.no_percentage); // Use no_percentage from the response

                    var yesOriginalText = $('#wtah-yes').data('original-text'); // Get the original text from the data attribute
                    var noOriginalText = $('#wtah-no').data('original-text'); // Get the original text from the data attribute

                    $('#wtah-yes').text(yesPercentage + '% ' + yesOriginalText).prop('disabled', vote === 'yes');
                    $('#wtah-no').text(noPercentage + '% ' + noOriginalText).prop('disabled', vote === 'no');
                }
            },
            error: function() {
                // Handle error.
            }
        });
    });
});