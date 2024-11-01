<?php
/*
	* Page Name: 		referral-comments.php
	* Page URL: 		https://softclever.com
	* Author: 			Md Maruf Adnan Sami
	* Author URL: 		https://www.mdmarufadnansami.com
*/ 

// Award points for new comment that approved //
function scurf_scurf_award_points_for_comment($comment_id) {
    $comment = get_comment($comment_id);
    $post_id = $comment->comment_post_ID;
    $user_id = $comment->user_id;

    if ($comment->comment_approved === '1' && $user_id) {
        $points_awarded = intval(get_option('points_for_comment_approved'));

        if ($points_awarded !== "00") {
            $comments_limit = 1;
            $today = date('Y-m-d');
            $user_comment_count = get_user_meta($user_id, $today, true);
            $post_comment_count = get_post_meta($post_id, $today, true);

            if ($post_comment_count < $comments_limit) {
                $comment_ids = scurf_get_comment_ids_for_user_and_post($user_id, $post_id);

                if (!in_array($comment_id, $comment_ids)) {
                    scurf_award_points_for_comment($user_id, $points_awarded);
                    scurf_increment_comment_counts($user_id, $post_id, $today);
                    scurf_handle_commission($user_id, $points_awarded);
                    scurf_store_comment_information($user_id, $comment, $points_awarded);
                }
            }
        }
    }
}

// Get comment IDs for a user and post //
function scurf_get_comment_ids_for_user_and_post($user_id, $post_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'comments';
    $query = $wpdb->prepare(
        "SELECT comment_ID FROM $table_name WHERE user_id = %d AND comment_post_ID = %d",
        $user_id,
        $post_id
    );
    return $wpdb->get_col($query);
}

// Award points to the user //
function scurf_award_points_for_comment($user_id, $points_awarded) {
    $user_points = (int) get_user_meta($user_id, 'user_points', true);
    update_user_meta($user_id, 'user_points', $user_points + $points_awarded);
}

// Increment comment counts for the user and post //
function scurf_increment_comment_counts($user_id, $post_id, $today) {
    $user_comment_count = (int) get_user_meta($user_id, $today, true);
    $post_comment_count = (int) get_post_meta($post_id, $today, true);

    update_user_meta($user_id, $today, $user_comment_count + 1);
    update_post_meta($post_id, $today, $post_comment_count + 1);
}

// Handle commission for referral //
function scurf_handle_commission($user_id, $points_awarded) {}

// Store the comment information in the database //
function scurf_store_comment_information($user_id, $comment, $points_awarded) {
    $commenter_name = scurf_get_user_display_name($user_id);
    $ip_address = sanitize_text_field($_SERVER['REMOTE_ADDR']);
    $login_id = scurf_get_user_login_id($user_id);

    global $wpdb;
    $table_name = $wpdb->prefix . 'referral_history';
    $wpdb->insert($table_name, array(
        'user_id' => $user_id,
        'user_name' => $commenter_name,
        'type' => get_option('translate_approved_comment'),
        'points' => $points_awarded,
        'ip_address' => $ip_address,
        'login_id' => $login_id,
    ));
}

// Store the commission information in the database //
function scurf_store_commission_information($user_id, $commission) {}

// Get the user's display name //
function scurf_get_user_display_name($user_id) {
    $user_data = get_userdata($user_id);

    if ($user_data) {
        $first_name = $user_data->first_name;
        $last_name = $user_data->last_name;
        return trim("$first_name $last_name");
    } else {
        return "Unknown";
    }
}

// Get the user's login ID //
function scurf_get_user_login_id($user_id) {
    $user_data = get_userdata($user_id);
    return ($user_data) ? $user_data->user_login : '';
}

// Add action hooks //
add_action('comment_post', 'scurf_scurf_award_points_for_comment');
add_action('comment_unapproved_to_approved', 'scurf_scurf_award_points_for_comment');
?>