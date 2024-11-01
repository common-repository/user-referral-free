<?php
/*
	* Page Name: 		referral-generate.php
	* Page URL: 		https://softclever.com
	* Author: 			Md Maruf Adnan Sami
	* Author URL: 		https://www.mdmarufadnansami.com
*/ 

// Generate Referral //
function scurf_get_referral_link($user_id) {
    $base_url = get_option('signup_link', site_url('/register/'));
    $referral_key = get_user_meta($user_id, 'referral_key', true);
    return add_query_arg('ref', $user_id, $base_url . '?ref=' . $referral_key);
}    

// Referral Link //
function scurf_referral_link_shortcode($atts) {
    $user_id = get_current_user_id();
    if (!$user_id) {
        return '';
    }
    $referral_link = scurf_get_referral_link($user_id);
    //return '<a href="' . esc_url($referral_link) . '">' . esc_html($referral_link) . '</a>';
    //return '' . esc_html($referral_link) . '';
    return esc_html($referral_link);
}
add_shortcode('referral_link', 'scurf_referral_link_shortcode');

function scurf_register_user_set_referral_id_meta($user_id) {
    // Check if the referral ID cookie is set
    if (isset($_COOKIE['ref'])) {
        // Get the referral ID from the cookie
        //$referral_id = $_COOKIE['ref'];
        $referral_id = sanitize_text_field( wp_unslash( $_COOKIE['ref'] ) );

        // Start output buffering
        ob_start();
        
        // Update the user meta with the referral ID
        update_user_meta($user_id, 'referral_id', $referral_id);
        
        // Delete the referral ID cookie
        setcookie('ref', '', time() - 3600, '/');

        // Flush the output buffer and send the headers
        ob_end_flush();
    }
}
add_action('user_register', 'scurf_register_user_set_referral_id_meta');

// Dashboard //
require_once plugin_dir_path(__FILE__)."referral-dashboard.php";
    
// Get options //
require_once plugin_dir_path(__FILE__)."referral-visitor.php";
require_once plugin_dir_path(__FILE__)."referral-signup.php";
require_once plugin_dir_path(__FILE__)."referral-give.php";
require_once plugin_dir_path(__FILE__)."referral-post.php";
require_once plugin_dir_path(__FILE__)."referral-custom.php";
require_once plugin_dir_path(__FILE__)."referral-comments.php";
require_once plugin_dir_path(__FILE__)."referral-registered.php";
require_once plugin_dir_path(__FILE__)."referral-login.php";
require_once plugin_dir_path(__FILE__)."referral-table.php";
require_once plugin_dir_path(__FILE__)."referral-visitor-table.php";
require_once plugin_dir_path(__FILE__)."referral-signup-table.php";
require_once plugin_dir_path(__FILE__)."referral-daily-login-table.php";
require_once plugin_dir_path(__FILE__)."referral-published-post-table.php";
require_once plugin_dir_path(__FILE__)."referral-approved-comment-table.php";
require_once plugin_dir_path(__FILE__)."referral-custom-post-table.php";
require_once plugin_dir_path(__FILE__)."referral-deduct-points.php";
?>