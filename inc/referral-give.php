<?php
/*
	* Page Name: 		referral-give.php
	* Page URL: 		https://softclever.com
	* Author: 			Md Maruf Adnan Sami
	* Author URL: 		https://www.mdmarufadnansami.com
*/ 

// Page Give Points //
function scurf_give_points_shortcode($atts) {
    $tl_points_give = get_option('translate_points_give');
    $tl_commission = get_option('translate_commission');

    // Get the user ID from the shortcode attribute or current user ID.
    if (isset($atts['user']) && $atts['user'] != 'current') {
        //$user_id = intval($atts['user']);
        $user_id = absint( $atts['user'] );
    } else {
        $user_id = get_current_user_id();
    }

    // Check if the user ID in the shortcode matches the current user ID.
    if (isset($atts['user']) && $atts['user'] != 'current' && $user_id != get_current_user_id()) {
        return 'You are not authorized for this action!';
    }

    // Get the amount of give points from the shortcode attribute.
    $amount = isset($atts['amount']) && $atts['amount'] != '' ? (int)$atts['amount'] : 0;

    // Get the type of give points from the shortcode attribute.
    $type = isset($atts['type']) && $atts['type'] != '' ? $atts['type'] : $tl_points_give;

    // Get the limit of give points from the shortcode attribute.
    $limit = isset($atts['limit']) && $atts['limit'] != '' ? (int)$atts['limit'] : 0;

    // Check if the limit has been reached for the given type and date.
    $date = date('Y-m-d');

    $meta_key_times = 'give_points_times_' . $type . '_' . $date;
    $meta_key_date = 'give_points_date_' . $type;

    $points_given_today = (int)get_user_meta($user_id, $meta_key_times, true);
    $last_given_date = get_user_meta($user_id, $meta_key_date, true);

    if ($last_given_date !== $date) {
        // If it's a new day, reset the counter and update the last given date.
        update_user_meta($user_id, $meta_key_times, 0);
        update_user_meta($user_id, $meta_key_date, $date);
        $points_given_today = 0;
    }

    if ($limit === 0 || $points_given_today < $limit) {
        // Add the give points to the user's points.
        $current_points = (int)get_user_meta($user_id, 'user_points', true);
        $new_points = $current_points + $amount;
        update_user_meta($user_id, 'user_points', $new_points);

        // Update the points given today count for the given type and date.
        update_user_meta($user_id, $meta_key_times, $points_given_today + 1);

        // Get user name //
        $c_id = $user_id;
        $c_data = get_userdata($c_id);
        if ($c_data) {
            $clogin_id = $c_data->user_login;
            $f_name = $c_data->first_name;
            $l_name = $c_data->last_name;
            $user_name = "$f_name $l_name";
        } else {
            $user_name = "Unknown";
        }

        // Add the give points to the referral history table.
        global $wpdb;
        $table_name = $wpdb->prefix . 'referral_history';
        $wpdb->insert($table_name, array(
            'user_id' => $user_id,
            'user_name' => $user_name,
            'type' => $type,
            'points' => $amount,
            'ip_address' => sanitize_text_field($_SERVER['REMOTE_ADDR']),
            'login_id' => $clogin_id,
        ));

        if (!empty($referer_id) && $percentage !== "00") {}

        // Update the points given today count for the given type and date.
        if ($type !== $tl_points_give) {
            update_user_meta($user_id, 'points_given_today_' . $type . '_' . $date, $points_given_today + 1);
        }

        return ''. __('You have received', 'user-referral-free') .' <b>' . $amount . '</b> '. __('points', 'user-referral-free') .'!';
    } else {
        return ''. __('You have reached the daily limit for', 'user-referral-free') .' ( ' . $type . ' ).';
    }
}
add_shortcode('give_points', 'scurf_give_points_shortcode');

// Function to add/update custom fields for a user.
function scurf_add_give_points_custom_fields($user_id) {
    // Initialize the date in Y-m-d format.
    $date = date('Y-m-d');

    // Set default values for the custom fields.
    $default_times = 0;

    // Get the current values of the custom fields.
    $existing_times = get_user_meta($user_id, 'give_points_times', true);
    $existing_date = get_user_meta($user_id, 'give_points_date', true);

    // Update the custom fields if they don't exist or if it's a new day.
    if (empty($existing_times) || $existing_date !== $date) {
        update_user_meta($user_id, 'give_points_times', $default_times);
        update_user_meta($user_id, 'give_points_date', $date);
    }
}

// Hook the function to add/update custom fields when a new user is registered.
add_action('user_register', 'scurf_add_give_points_custom_fields');

// Hook the function to add/update custom fields when a user profile is updated.
add_action('profile_update', 'scurf_add_give_points_custom_fields');

// Function to get the current count of points given for a specific type and date.
function scurf_get_give_points_count($user_id, $type) {
    // Get the current date in Y-m-d format.
    $date = date('Y-m-d');

    // Create the meta keys based on the type and date.
    $meta_key_times = 'give_points_times_' . $type . '_' . $date;

    // Get the count of points given for the specific type and date.
    $points_given_today = get_user_meta($user_id, $meta_key_times, true);

    return (int)$points_given_today;
}

// Function to increment the count of points given for a specific type and date.
function scurf_increment_give_points_count($user_id, $type) {
    // Get the current date in Y-m-d format.
    $date = date('Y-m-d');

    // Create the meta keys based on the type and date.
    $meta_key_times = 'give_points_times_' . $type . '_' . $date;

    // Get the current count of points given for the specific type and date.
    $points_given_today = (int)get_user_meta($user_id, $meta_key_times, true);

    // Increment the count and update the custom field.
    $new_points_given_today = $points_given_today + 1;
    update_user_meta($user_id, $meta_key_times, $new_points_given_today);

    // Update the `give_points_date` field to the current date.
    update_user_meta($user_id, 'give_points_date', $date);
}
?>