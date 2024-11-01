<?php
/*
	* Page Name: 		referral-signup-table.php
	* Page URL: 		https://softclever.com
	* Author: 			Md Maruf Adnan Sami
	* Author URL: 		https://www.mdmarufadnansami.com
*/ 

// Get referral singup list only //
function scurf_signup_list_only_shortcode($atts) {
    $tl_refer_signup = get_option('translate_refer_signup');

    // Get the user ID from the shortcode attribute or current user ID.
    //$user_id = isset($atts['user_id']) && $atts['user_id'] != '' ? $atts['user_id'] : get_current_user_id();
    $user_id = isset($atts['user_id']) && $atts['user_id'] != '' ? absint($atts['user_id']) : get_current_user_id();
    
    // Get the referral singup records for the user.
    global $wpdb;
    $table_name = $wpdb->prefix . 'referral_history';
    /* $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d AND type = '$tl_refer_signup' AND points >= 1 ORDER BY created_at DESC", $user_id)); */

    $results = $wpdb->get_results(
        $wpdb->prepare(
          "SELECT * FROM $table_name WHERE user_id = %d AND type = %s AND points >= 1 ORDER BY created_at DESC",
          $user_id,
          $tl_refer_signup
        )
    );      
    
    // Check if there are no referral singup records for the user.
    if (empty($results)) {
        return "<p class='scurf_no_signup_history_found'>". __('No referral signup history found!', 'user-referral-free') ."</p>";
    }
    
    // Set the number of records to display per page.
    $per_page = get_option('all_history_count');
    
    // Get the current page number.
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;

    // Calculate the offset based on the current page number and number of records to display per page.
    $offset = ($paged - 1) * $per_page;

    // Get the total number of records.
    $total_records = count($results);

    // Calculate the total number of pages.
    $total_pages = ceil($total_records / $per_page);

    // Limit the results to the current page.
    $results = array_slice($results, $offset, $per_page);

    // Build the HTML output for the referral singup table.
    $output = '<table class="referral-history"><thead><tr><th>'. __('Type', 'user-referral-free') .'</th><th>'. __('Points', 'user-referral-free') .'</th><th>'. __('Date', 'user-referral-free') .'</th></tr></thead><tbody>';
    foreach ($results as $row) {
        $output .= '<tr>';
        $output .= '<td>' . $row->type . '</td>';
        $output .= '<td>' . number_format($row->points) . '</td>';
        //$output .= '<td>' . date('Y-m-d \a\t H:i:s A', strtotime($row->created_at)) . '</td>';
        $output .= '<td>' . esc_html(str_replace(array('am', 'pm'), array('AM', 'PM'), date_i18n(get_option('date_format') . ' \a\t ' . get_option('time_format'), strtotime($row->created_at)))) . '</td>';
        $output .= '</tr>';
    }
    $output .= '</tbody></table>';
    
    // Build the HTML output for the pagination links.
    $page_links = paginate_links(array(
        'base' => add_query_arg('paged', '%#%'),
        'format' => '',
        'prev_text' => __('&laquo; Prev'),
        'next_text' => __('Next &raquo;'),
        'total' => $total_pages,
        'current' => $paged
    ));

    if ($page_links) {
        $output .= '<div class="referral-pagination">' . wp_kses_post($page_links) . '</div>';
    }
    
    return $output;
}
add_shortcode('referral_signup_list_only', 'scurf_signup_list_only_shortcode');
?>