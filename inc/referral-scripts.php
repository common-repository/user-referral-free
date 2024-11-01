<?php
/*
	* Page Name: 		referral-scripts.php
	* Page URL: 		https://softclever.com
	* Author: 			Md Maruf Adnan Sami
	* Author URL: 		https://www.mdmarufadnansami.com
*/ 

// Add Javascript //
function scurf_enqueue_scripts() {
    wp_enqueue_script( 'scurf-script', plugins_url( '../assets/js/script.js', __FILE__ ), array( 'jquery' ), '5.0', true );
}
add_action( 'wp_enqueue_scripts', 'scurf_enqueue_scripts' );
?>