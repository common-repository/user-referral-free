<?php
/*
	* Page Name: 		referral-data.php
	* Page URL: 		https://softclever.com
	* Author: 			Md Maruf Adnan Sami
	* Author URL: 		https://www.mdmarufadnansami.com
*/ 

// Delete referral history table when the plugin is deactivated //
function scurf_delete_data_on_deactivated() {
    $delete_data_option = get_option('delete_data_on_deactivate');

    if ($delete_data_option === 'on') {
        // Delete history data //
        global $wpdb;
        $table_name = $wpdb->prefix . 'referral_history';
        $sql = "DROP TABLE IF EXISTS $table_name;";
        $wpdb->query($sql);

        // Update user points meta to 0 //
        $users = get_users();
        foreach ($users as $user) {
            update_user_meta($user->ID, 'user_points', 0);
        }

        // Empty settings field //
        delete_option('visitor_referral_points');
        delete_option('signup_referral_points');
        delete_option('signup_link');

        delete_option('points_for_new_register');
        delete_option('points_for_daily_login');

        delete_option('points_for_post_published');
        delete_option('points_limit_for_daily_post');
        delete_option('points_for_comment_approved');

        delete_option('custom_post_types');
        delete_option('custom_post_types_point');

        delete_option('points_type_for_woocommerce_order');
        delete_option('fixed_points_for_woocommerce_order_completed');
        delete_option('percentage_points_for_woocommerce_order_completed');
        delete_option('minimum_woocommerce_order_amount_required');

        delete_option('min_points_transfer_amount');
        delete_option('max_points_transfer_amount');

        delete_option('points_for_commission');

        delete_option('translate_refer_visitor');
        delete_option('translate_refer_signup');
        delete_option('translate_new_register');
        delete_option('translate_daily_login');
        delete_option('translate_publish_post');
        delete_option('translate_approved_comment');
        delete_option('translate_custom_post');
        delete_option('translate_woocommerce_order');
        delete_option('translate_commission');

        delete_option('translate_points_give');
        delete_option('translate_points_deduct');
        delete_option('translate_points_added');
        delete_option('translate_points_removed');
        delete_option('translate_points_transferred');
        delete_option('translate_points_received');

        delete_option('all_history_count');
        delete_option('top_users_count');
        delete_option('last_history_count');
        delete_option('last_history_type');

        delete_option('delete_data_on_deactivate');
        
        delete_option('scurf_timezone');
    }
}
register_deactivation_hook( __FILE__, 'scurf_delete_data_on_deactivated' );
?>