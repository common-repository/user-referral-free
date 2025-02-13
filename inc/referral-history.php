<?php
/*
	* Page Name: 		referral-history.php
	* Page URL: 		https://softclever.com
	* Author: 			Md Maruf Adnan Sami
	* Author URL: 		https://www.mdmarufadnansami.com
*/ 

// Referral History //
function scurf_add_history() {
    add_submenu_page(
        'user-referral-free-settings',
        __('All History', 'user-referral-free'),
        __('History', 'user-referral-free'),
        'manage_options',
        'user-referral-free-history',
        'scurf_render_history'
    );
}
add_action('admin_menu', 'scurf_add_history'); 

function scurf_render_history() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'referral_history';

    // Pagination variables
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    //$per_page = 1; // number of rows per page
    $per_page = get_option('all_history_count'); // number of rows per page
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_pages = ceil($total_items / $per_page);

    // Search variables
    //$search_term = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    //$search_term = isset($_POST['search']) ? sanitize_text_field(wp_unslash($_POST['search'])) : '';

    if (isset($_POST['search'])) {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
          // User doesn't have the necessary permission, handle the error or exit gracefully
          die('You do not have permission to perform this action.');
        } else {
            $search_term = isset($_POST['search']) ? sanitize_text_field(wp_unslash($_POST['search'])) : '';
        }
    }           

    if (isset($search_term)) {
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE user_name LIKE %s OR type LIKE %s OR ip_address LIKE %s ORDER BY created_at DESC", $search_term, "%{$search_term}%", "%{$search_term}%", "%{$search_term}%");
    } else {
        $query = "SELECT * FROM $table_name ORDER BY created_at DESC";
    }

    // Pagination query
    $offset = ($current_page - 1) * $per_page;
    $query .= " LIMIT $per_page OFFSET $offset";

    $referral_history = $wpdb->get_results($query, ARRAY_A);

    if (isset($search_term) && empty($referral_history)) {
        echo '<div class="error"><p>'. __('No history found!', 'user-referral-free') .'</p></div>';
        $referral_history = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
        $search_results = 'false';
    } else {
        $search_results = 'true';
    }

    // Check for multiple Visitors or Signups from the same IP //
    $ip_counts = array();
    $referer_names = array();
    foreach ($referral_history as $history) {
        $tl_refer_visitor = get_option('translate_refer_visitor');
        $tl_refer_signup = get_option('translate_refer_signup');

        if ($history['type'] == $tl_refer_visitor || $history['type'] == $tl_refer_signup) {
            $ip = $history['ip_address'];
            if (isset($ip_counts[$ip])) {
                $ip_counts[$ip]++;
                if (!in_array($history['user_name'], $referer_names[$ip])) {
                    $referer_names[$ip][] = $history['user_name'];
                }
            } else {
                $ip_counts[$ip] = 1;
                $referer_names[$ip] = array($history['user_name']);
            }
        }
    }

    // Display message for multiple Visitors or Accounts from the same IP
    $multiple_ip_message = "";
    foreach ($ip_counts as $ip => $count) {
        if ($count > 2) {
            $referer_names_string = implode(", ", array_unique($referer_names[$ip]));
            $multiple_ip_message .= "". $ip ." / ";
        }
    }

    if (!empty($multiple_ip_message)) {
        $multiple_ip_message = rtrim($multiple_ip_message, " / ");
        echo '<div class="notice notice-warning is-dismissible"><p>'. __('Multiple Visitors or Accounts found from IP:', 'user-referral-free') .' ' . esc_html($multiple_ip_message) . '.</p></div>';
    }
?>
<div class="section-divider">
    <?php require_once plugin_dir_path(__FILE__)."../core/referral-premium.php"; ?>

    <div class="referral-system">
        <h2><?php _e('All History', 'user-referral-free'); ?></h2>

        <form method="post">
            <p>
                <label for="search"><?php _e('Search History:', 'user-referral-free'); ?></label>
                <input type="text" name="search" value="<?php if (isset($search_term)) {echo $search_term;} ?>" placeholder="<?php _e('Search by user name, ip address and type...', 'user-referral-free'); ?>">
                <input type="submit" value="<?php _e('Search', 'user-referral-free'); ?>" class="button button-search">
            </p>
        </form>
        <table class="referral-history wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('SL', 'user-referral-free'); ?></th>
                    <!-- <th><?php //_e('ID', 'user-referral-free'); ?></th> -->
                    <th><?php _e('User', 'user-referral-free'); ?></th>
                    <th><?php _e('Type', 'user-referral-free'); ?></th>
                    <th><?php _e('Points', 'user-referral-free'); ?></th>
                    <th><?php _e('IP Address', 'user-referral-free'); ?></th>
                    <th><?php _e('Date', 'user-referral-free'); ?></th>
                    <th><?php _e('Actions', 'user-referral-free'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    if ($search_results == 'true') {
                    foreach ($referral_history as $referral): 
                    $formatted_points = number_format($referral['points']);
                ?>               
                    <tr>
                        <td>#<?php echo esc_html($referral['id']); ?></td>
                        <!-- <td><?php //echo esc_html($referral['user_id']); ?></td> -->
                        <td><a href="<?php echo admin_url(); ?>user-edit.php?user_id=<?php echo esc_html($referral['user_id']); ?>" target="_blank"><?php echo esc_html( $referral['user_name'] ); ?></a></td>
                        <td><?php echo esc_html( $referral['type'] ); ?></td>
                        <td><?php echo esc_html( $formatted_points ); ?></td>
                        <td><?php echo esc_html( $referral['ip_address'] ); ?></td>
                        <!-- <td><?php //echo date_i18n(get_option('date_format') . ' \a\t ' . get_option('time_format'), strtotime($referral['created_at'])); ?></td> -->
                        <td><?php $date_string = date_i18n(get_option('date_format') . ' \a\t ' . get_option('time_format'), strtotime($referral['created_at'])); $formatted_date = str_replace(array('am', 'pm'), array('AM', 'PM'), $date_string); echo esc_html($formatted_date); ?></td>
                        <td>
                            <button class="delete-history button-primary" data-history-id="<?php echo esc_html($referral['id']); ?>"><?php _e('Delete', 'user-referral-free'); ?></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php } else { ?>    
                    </tr> 
                        <td colspan="7"><?php _e('No data.', 'user-referral-free'); ?></td>
                    <tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php if ($total_pages > 1 && $search_results == 'true'): ?>
    <div class="pagination">
        <?php
            $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
            $results_per_page = $per_page;

            if ($current_page > $total_pages) {
                $current_page = $total_pages;
            }

            if ($current_page < 1) {
                $current_page = 1;
            }

            $args = array(
                'base'         => add_query_arg('paged', '%#%'),
                'format'       => '',
                'prev_text'    => __('&laquo; Prev'),
                'next_text'    => __('Next &raquo;'),
                'total'        => $total_pages,
                'current'      => $current_page,
                'show_all'     => false,
                'end_size'     => 1,
                'mid_size'     => 2,
                'add_args'     => false,
                'add_fragment' => ''
            );
            
            $page_links = paginate_links($args);

            if ($page_links) {
                //echo wp_kses_post($page_links);
                echo wp_kses_post($page_links);
            }
        ?>
    </div>
<?php endif; ?>
<script>
    jQuery(document).ready(function($) {
        // Delete history row
        $(".delete-history").on("click", function() {
            var historyId = $(this).data("history-id");
            if (confirm("<?php _e('Are you sure you want to delete this history?', 'user-referral-free'); ?>")) {
                $.ajax({
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    type: "POST",
                    data: {
                        action: "delete_history",
                        history_id: historyId
                    },
                    success: function(response) {
                        if (response.success) {
                            // Row deleted successfully, display a message or perform any other action
                            alert("<?php _e('History deleted successfully!', 'user-referral-free'); ?>");
                            // Refresh the page or update the table using JavaScript as per your requirement
                            
                            location.reload(); // Reload the page
                        } else {
                            // Error deleting the row, display an error message or perform any other action
                            alert("<?php _e('Error deleting history!', 'user-referral-free'); ?>");
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Error occurred, display an error message or perform any other action
                        alert("<?php _e('Error deleting history!', 'user-referral-free'); ?>");
                    }
                });
            }
        });
    });
</script>
<?php }
// Delete history row callback
function scurf_delete_history_callback() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'referral_history';

    //$history_id = $_POST['history_id'];
    $history_id = isset($_POST['history_id']) ? absint($_POST['history_id']) : 0;

    $result = $wpdb->delete($table_name, ['id' => $history_id]);

    if ($result) {
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_delete_history', 'scurf_delete_history_callback');
add_action('wp_ajax_nopriv_delete_history', 'scurf_delete_history_callback');

// Get histories from database //
function scurf_get_referral_history() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'referral_history';
    $query = "SELECT * FROM $table_name";
    $results = $wpdb->get_results($query, ARRAY_A);
    return $results;
}

// User referral history //
function scurf_history_shortcode($atts) {
    // Get the user ID from the shortcode attribute or current user ID.
    $user_id = isset($atts['user_id']) && $atts['user_id'] != '' ? $atts['user_id'] : get_current_user_id();
    
    // Get the referral history records for the user.
    global $wpdb;
    $table_name = $wpdb->prefix . 'referral_history';
    //$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d AND points >= 1", $user_id));
    /* $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d AND points >= 1 ORDER BY created_at DESC", $user_id)); */

    $results = $wpdb->get_results(
        $wpdb->prepare(
          "SELECT * FROM $table_name WHERE user_id = %d AND points >= 1 ORDER BY created_at DESC",
          $user_id
        )
    );      
    
    // Check if there are no referral history records for the user.
    if (empty($results)) {
        return '<div class="error"><p>'. __('No history found!', 'user-referral-free') .'</p></div>';
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

    // Build the HTML output for the referral history table.
    $output = '<table class="referral-history"><thead><tr><th>'. __('Type', 'scurf') .'</th><th>'. __('Points', 'scurf') .'</th><th>'. __('Date', 'scurf') .'</th></tr></thead><tbody>';
    foreach ($results as $row) {
        $output .= '<tr>';
        $output .= '<td>' . $row->type . '</td>';
        $output .= '<td>' . number_format($row->points) . '</td>';
        //$output .= '<td>' . date('Y-m-d \a\t H:i:s A', strtotime($row->created_at)) . '</td>';
        //$output .= '<td>' . date_i18n(get_option('date_format') . ' \a\t ' . get_option('time_format'), strtotime($row->created_at)) . '</td>';
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
add_shortcode('referral_history', 'scurf_history_shortcode');  
?>