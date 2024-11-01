<?php
/*
	* Page Name: 		referral-custom.php
	* Page URL: 		https://softclever.com
	* Author: 			Md Maruf Adnan Sami
	* Author URL: 		https://www.mdmarufadnansami.com
*/ 

// Update approved custom post points //
function scurf_update_user_points_on_approval($post_id) {
	// Get the post object
	$post = get_post($post_id);

	// Check if the post type and its status is "publish" //
	$get_custom_posts = get_option('custom_post_types');
	$custom_post_array = explode(',', $get_custom_posts); // Convert string to array //

	if (!empty($custom_post_array) && is_array($custom_post_array) && in_array($post->post_type, $custom_post_array) && $post->post_status === 'publish') {
		$tl_custom_post = get_option('translate_custom_post');
		$tl_commission = get_option('translate_commission');

		// Check if points have already been awarded for this post //
		$points_awarded = get_post_meta($post_id, 'points_awarded', true);

		// If points have already been awarded, exit the function //
		if ($points_awarded) {
			return;
		}

		// Get the author ID //
		$author_id = $post->post_author;

		// Get the current user points //
		$user_points = get_user_meta($author_id, 'user_points', true);

		// Get the specific points to be awarded for task approval //
		$points_to_award = get_option('custom_post_types_point');

		if ($points_to_award !== "00") {
			// Award points to the author //
			award_points_to_user($author_id, $tl_custom_post, $points_to_award);

			// Set the post meta flag to indicate that points have been awarded //
			update_post_meta($post_id, 'points_awarded', true);
		}
	}
}
add_action('save_post', 'scurf_update_user_points_on_approval');

// Award points to a user and log the points in the referral history table //
function award_points_to_user($user_id, $type, $points) {
	// Get the user data //
	$user_data = get_userdata($user_id);
	if (!$user_data) {
		return;
	}

	// Get the user login and name //
	$login_id = $user_data->user_login;
	$first_name = $user_data->first_name;
	$last_name = $user_data->last_name;
	$user_name = $first_name && $last_name ? "$first_name $last_name" : 'Unknown';

	// Store the points and user information in the referral history table //
	global $wpdb;
	$table_name = $wpdb->prefix . 'referral_history';
	$wpdb->insert($table_name, array(
		'user_id' => $user_id,
		'user_name' => $user_name,
		'type' => $type,
		'points' => $points,
		'ip_address' => sanitize_text_field($_SERVER['REMOTE_ADDR']),
		'login_id' => $login_id,
	));

	// Update the user points by adding the awarded points //
	$user_points = get_user_meta($user_id, 'user_points', true);
	update_user_meta($user_id, 'user_points', $user_points + $points);
}

// Calculate referral commission based on the awarded points and commission percentage //
function scurf_calculate_referral_commission($points, $percentage) {}
?>
