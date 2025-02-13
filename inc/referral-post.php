<?php
/*
	* Page Name: 		referral-post.php
	* Page URL: 		https://softclever.com
	* Author: 			Md Maruf Adnan Sami
	* Author URL: 		https://www.mdmarufadnansami.com
*/ 

// Award points for new post //
function scurf_award_points_for_post($post_id) {
    // Get the post author information
    $post = get_post($post_id);
    $user_id = $post->post_author;

    // If the post is published and the user is logged in, award points
    if ($post->post_status == 'publish' && $user_id && !wp_is_post_revision($post_id) && !get_post_meta($post_id, 'points_awarded', true)) {
        $tl_publish_post = get_option('translate_publish_post');
        $tl_commission = get_option('translate_commission');
        $points_awarded = intval(get_option('points_for_post_published')); // Change this to the desired point value
        $posts_limit = get_option('points_limit_for_daily_post'); // Change this to the desired daily limit
        $today = date('Y-m-d');
        $user_post_count = get_user_meta($user_id, $today, true);
        $total_post_count = get_user_meta($user_id, 'total_posts', true);

        // Check if the user has reached their daily limit
        if ($user_post_count < $posts_limit && $points_awarded !== "00") {
            // Award points and increment daily and total post counts for user
            update_user_meta($user_id, 'user_points', get_user_meta($user_id, 'user_points', true) + $points_awarded);
            update_user_meta($user_id, $today, $user_post_count + 1);
            update_user_meta($user_id, 'total_posts', $total_post_count + 1);
            update_post_meta($post_id, 'points_awarded', true);

            // Get author name
            $u_data = get_userdata($user_id);
            if ($u_data) {
                $login_id = $u_data->user_login;
                $f_name = $u_data->first_name;
                $l_name = $u_data->last_name;
                $author_name = "$f_name $l_name";
            } else {
                $author_name = "Unknown";
            }

            // Store the points and post information in the database
            global $wpdb;
            $table_name = $wpdb->prefix . 'referral_history';
            $wpdb->insert($table_name, array(
                'user_id' => $user_id,
                'user_name' => $author_name,
                'type' => $tl_publish_post,
                'points' => $points_awarded,
                'ip_address' => sanitize_text_field( $_SERVER['REMOTE_ADDR'] ),
                'login_id' => $login_id,
            ));

            if(!empty($referer_id) && $percentage !== "00") {}
        }
    }
}

// Add action hooks
add_action('publish_post', 'scurf_award_points_for_post', 10, 2);
?>