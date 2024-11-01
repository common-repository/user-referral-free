<?php
/*
	* Page Name: 		referral-dashboard.php
	* Page URL: 		https://softclever.com
	* Author: 			Md Maruf Adnan Sami
	* Author URL: 		https://www.mdmarufadnansami.com
*/ 

// Add Custom Dashboard Widget //
function scurf_add_user_points_dashboard_widget() {
    if (current_user_can('administrator')) {
        wp_add_dashboard_widget(
            'scurf-user-points-widget',
            __('User Points', 'user-referral-free'),
            'scurf_render_user_points_dashboard_widget'
        );
    }
}
add_action('wp_dashboard_setup', 'scurf_add_user_points_dashboard_widget');

// Render User Points Dashboard Widget //
function scurf_render_user_points_dashboard_widget() {
    echo '<div class="scurf-user-points-widget">';
    scurf_display_user_points_count();
    scurf_display_user_points_graph();
    echo '</div>';
}

// Display User Points Count //
function scurf_display_user_points_count() {
    $total_points = 0;

    // Get all users //
    $users = get_users();

    // Calculate total points //
    foreach ($users as $user) {
        $user_points = get_user_meta($user->ID, 'user_points', true);
        $total_points += intval($user_points);
    }

    // Output total points count //
    //echo '<div class="dashboard-points"><strong>Total User Points:</strong> <span class="button-primary">' . number_format($total_points) . '</span></div>';

    echo '<div class="dashboard-points"><strong>'. __('Total User Points:', 'user-referral-free') .'</strong> <span class="button-primary">' . esc_html(number_format($total_points)) . '</span></div>';

}

// User Points //
function scurf_display_user_points_graph() {
    $args = array(
        'meta_key' => 'user_points',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'number' => get_option('top_users_count'), // Adjust the number of top users you want to display
        'fields' => array('ID', 'display_name') // Retrieve both user ID and display name
    );

    $users = get_users($args);
    $data = array();
    $data_labels = array();
    foreach ($users as $user) {
        $user_points = intval(get_user_meta($user->ID, 'user_points', true));
        if ($user_points > 0) {
            $data[] = $user_points;
            $data_labels[] = $user->ID;
            //$data_labels[] = $user->display_name;
            //$data_labels[] = $user->display_name . ' ( ' . $user->ID . ' )';
            //$data_labels[] = $user->display_name . ' ( ID: ' . $user->ID . ' )';
        }
    }

    echo '<canvas id="user-points-chart"></canvas>';

    // Chart.js library locally
    wp_enqueue_script('scurf-chart', plugin_dir_url(__FILE__) . '../assets/js/chart.js', array(), '5.0', true);
    wp_enqueue_script('scurf-chart-script', plugin_dir_url(__FILE__) . '../assets/js/chart-script.js', array(), '5.0', true);

    // Localize the data to pass it to the JavaScript file
    wp_localize_script('scurf-chart-script', 'scurfChartData', array(
        'labels' => $data_labels,
        'data' => $data,
    ));
}

// Top Users Widget //
function scurf_add_dashboard_top_users_widget() {}
add_action('wp_dashboard_setup', 'scurf_add_dashboard_top_users_widget');

// Render Dashboard Widget Content //
function scurf_render_top_users_widget() {}
?>