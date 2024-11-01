<?php
/*
	* Page Name: 		referral-table.php
	* Page URL: 		https://softclever.com
	* Author: 			Md Maruf Adnan Sami
	* Author URL: 		https://www.mdmarufadnansami.com
*/ 

// Show user points table //
function scurf_points_table_shortcode( $atts ) {
    $output = '<table class="referral-history"><thead><tr><th>'. __('Type', 'user-referral-free') .'</th><th>'. __('Segment', 'user-referral-free') .'</th><th>'. __('Points', 'user-referral-free') .'</th></tr></thead><tbody>';
    
    $data_1 = get_option('visitor_referral_points');
    $data_2 = get_option('signup_referral_points');
    $data_3 = intval(get_option('points_for_daily_login'));
    $data_4 = intval(get_option('points_for_comment_approved'));
    $data_5 = intval(get_option('points_for_commission'));

    if($data_1 == '0') {} else {
        $output .= '<tr>';
        $output .= '<td>'. __('Visitor Referral', 'user-referral-free') .'</td>';
        $output .= '<td>'. __('When someone clicks on your referral link.', 'user-referral-free') .'</td>';
        $output .= '<td>' . get_option('visitor_referral_points') . '</td>';
        $output .= '</tr>';
    }

    if($data_2 == '0') {} else {
        $output .= '<tr>';
        $output .= '<td>'. __('Signup Referral', 'user-referral-free') .'</td>';
        $output .= '<td>'. __('When someone signup using your referral link.', 'user-referral-free') .'</td>';
        $output .= '<td>' . get_option('signup_referral_points') . '</td>';
        $output .= '</tr>';
    }

    if($data_3 == '0') {} else {
        $output .= '<tr>';
        $output .= '<td>'. __('Daily Login', 'user-referral-free') .'</td>';
        $output .= '<td>'. __('When you have login per day.', 'user-referral-free') .'</td>';
        $output .= '<td>' . get_option('points_for_daily_login') . '</td>';
        $output .= '</tr>';
    }

    if($data_4 == '0') {} else {
        $output .= '<tr>';
        $output .= '<td>'. __('Post Comment', 'user-referral-free') .'</td>';
        $output .= '<td>'. __('When you put a comment on any post.', 'user-referral-free') .'</td>';
        $output .= '<td>' . get_option('points_for_comment_approved') . '</td>';
        $output .= '</tr>';
    }

    if($data_5 == '0') {} else {
        $output .= '<tr>';
        $output .= '<td>'. __('Commission', 'user-referral-free') .'</td>';
        $output .= '<td>'. __('When your referred user earn points you will received a percentage.', 'user-referral-free') .'</td>';
        $output .= '<td>' . get_option('points_for_commission') . '%</td>';
        $output .= '</tr>';
    }

    if ($data_1 == '0' && $data_2 == '0' && $data_3 == '0' && $data_4 == '0' && $data_5 == '0') {
        $output = "". __('There is no segment available!', 'user-referral-free') ."";
    }

    $output .= '</tbody></table>';

    return $output;
}
add_shortcode( 'referral_points_table', 'scurf_points_table_shortcode' );
?>