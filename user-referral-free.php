<?php
/*
	* Plugin Name: 		User Referral ( Free ) - Points, Rewards, Loyalty, Leader Board & Referrals Plugin
	* Plugin URI: 		https://softclever.com/
	* Description: 		A powerful referral system plugin for WordPress that allows users to earn points and rewards for referring new visitors and signups to your website.
	
	* Author: 			Md Maruf Adnan Sami
	* Author URI: 		https://www.mdmarufadnansami.com/
	* Version: 			8.0
	
	* Text Domain: 		user-referral-free
*/

// Link all the settings //
require_once plugin_dir_path(__FILE__)."user-timezone.php";
require_once plugin_dir_path(__FILE__)."user-options.php";
require_once plugin_dir_path(__FILE__)."user-hooks.php";

// Get settings link 
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'scurf_add_settings_link' );

// Register activation hook
register_activation_hook(__FILE__, 'scurf_plugin_activate_action');

// Plugin Data delete hook //
register_deactivation_hook(__FILE__, 'scurf_delete_data_on_deactivated');
?>