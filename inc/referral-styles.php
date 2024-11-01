<?php
/*
	* Page Name: 		referral-styles.php
	* Page URL: 		https://softclever.com
	* Author: 			Md Maruf Adnan Sami
	* Author URL: 		https://www.mdmarufadnansami.com
*/

// Load Frontend Stylesheet //
function scurf_enqueue_frontend_styles() {
    wp_enqueue_style( 'scurf-style', plugins_url( '../assets/css/style-frontend.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'scurf_enqueue_frontend_styles' );

// Load Backend Stylesheet //
function scurf_enqueue_backend_styles() {
    wp_enqueue_style( 'scurf-style', plugins_url( '../assets/css/style-backend.css', __FILE__ ) );
}
add_action('admin_enqueue_scripts', 'scurf_enqueue_backend_styles');
?>