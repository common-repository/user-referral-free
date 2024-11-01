<?php
/*
	* Page Name: 		referral-security.php
	* Page URL: 		https://softclever.com
	* Author: 			Md Maruf Adnan Sami
	* Author URL: 		https://www.mdmarufadnansami.com
*/ 

// Security Menu //
function scurf_add_security() {
    add_submenu_page(
        'user-referral-free-settings',
        __('Security Settings', 'user-referral-free'),
        __('Security', 'user-referral-free'),
        'manage_options',
        'user-referral-free-security',
        'scurf_render_security'
    );
}
add_action('admin_menu', 'scurf_add_security');

// Referral Security //
function scurf_render_security() { 
    // Delete referral history from the database
    if (isset($_POST['reset_referral_history']) && check_admin_referer('reset_referral_history')) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'referral_history';
        $wpdb->query("TRUNCATE TABLE $table_name");
        echo '<div class="notice notice-success is-dismissible"><p>'. __('Referral history successfully deleted!', 'user-referral-free') .'</p></div>';
    }

    // Reset user points meta value to 0 for all users
    if (isset($_POST['reset_user_points']) && check_admin_referer('reset_user_points')) {
        $users = get_users();
        foreach ($users as $user) {
            delete_user_meta($user->ID, 'user_points');
            update_user_meta($user->ID, 'user_points', 0);
        }
        echo '<div class="notice notice-success is-dismissible"><p>'. __('User points successfully deleted!', 'user-referral-free') .'</p></div>';
    }
    
    // Get total user points //
    function scurf_get_total_user_points() {
        $user_points = 0;
        $users = get_users();
        foreach ($users as $user) {
            $user_points += (int) get_user_meta($user->ID, 'user_points', true);
        }
        return $user_points;
    }
    
    // Get total user history //
    function scurf_get_total_referral_history() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'referral_history';
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        return $total_items;
    }
    
    // Get data //
    $total_user_points = scurf_get_total_user_points();
    $total_referral_history = scurf_get_total_referral_history();

    // Limit the history //
    $history_limit = get_option('last_history_count');
?>
<div class="section-divider">
    <?php require_once plugin_dir_path(__FILE__)."../core/referral-premium.php"; ?>
    
    <div class="referral-system">
        <h2><?php _e('Security Settings', 'user-referral-free'); ?></h2>

        <h4><?php echo esc_html( __( 'Reset History', 'user-referral-free' ) ); ?> ( <small><?php echo esc_html( __( 'Total:', 'user-referral-free' ) ); ?> <?php echo esc_html( number_format( $total_referral_history ) ); ?></small> )</h4>

        <p><?php _e('Click the button below to reset all the referral history.', 'user-referral-free'); ?></p>
        <form method="post" action="">
            <input type="hidden" name="reset_referral_history" value="1">
            <?php wp_nonce_field( 'reset_referral_history' ); ?>
            <p><input type="submit" value="<?php echo esc_attr( __( 'Reset Referral History', 'user-referral-free' ) ); ?>" class="button button-primary" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to reset the referral history?', 'user-referral-free' ) ); ?>');"></p>
        </form>

        <hr>

        <h4><?php echo esc_html( __( 'Reset Points', 'user-referral-free' ) ); ?> ( <small><?php echo esc_html( __( 'Total:', 'user-referral-free' ) ); ?> <?php echo esc_html( number_format( $total_user_points ) ); ?></small> )</h4>

        <p><?php _e('Click the button below to reset all the user referral points.', 'user-referral-free'); ?></p>
        <form method="post" action="">
            <input type="hidden" name="reset_user_points" value="1">
            <?php wp_nonce_field( 'reset_user_points' ); ?>
            <p><input type="submit" value="<?php echo esc_attr( __( 'Reset User Points', 'user-referral-free' ) ); ?>" class="button button-primary" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to reset all user referral points?', 'user-referral-free' ) ); ?>');"></p>
        </form>

        <hr>
        
        <h4>
            <?php echo esc_html( __( 'Delete History', 'user-referral-free' ) ); ?> ( <small><?php echo esc_html( __( 'Total:', 'user-referral-free' ) ); ?> <?php echo esc_html( number_format( $total_referral_history ) ); ?></small> )
            <span>
                <?php _e('PRO', 'user-referral-free'); ?>
            </span>
        </h4>
        <p><?php _e('Delete the last '.$history_limit.' referral history.', 'user-referral-free'); ?></p>
        <form method="post" action="" <?php scurf_add_alt_class(); ?>>
            <input type="hidden" name="delete_last_history" value="1">
            <?php wp_nonce_field( 'delete_last_history' ); ?>
            <p><input type="submit" value="<?php _e('Delete', 'user-referral-free'); ?> <?php echo esc_attr( $history_limit ); ?> <?php _e('History', 'user-referral-free'); ?>" class="button button-primary" onclick="return confirm('<?php _e('Are you sure you want to delete the last', 'user-referral-free'); ?> <?php echo esc_js( $history_limit ); ?> <?php _e('history', 'user-referral-free'); ?>?');"></p>
        </form>

        <hr>

        <h4>
            <?php echo esc_html( __( 'Delete Data', 'user-referral-free' ) ); ?>
            <span>
                <?php _e('PRO', 'user-referral-free'); ?>
            </span>
        </h4>
        <p><?php echo esc_html( __( 'Delete plugin data (eg: history, points) when the plugin is deactivated.', 'user-referral-free' ) ); ?></p>
        <form method="post" action="" <?php scurf_add_alt_class(); ?>>
            <input type="radio" id="delete_data_on_deactivate_on" name="delete_data_on_deactivate" value="on" <?php checked(get_option('delete_data_on_deactivate'), 'on'); ?>>
            <label for="delete_data_on_deactivate_on"><?php _e('Erase (On)', 'user-referral-free'); ?></label>
            
            <span class="radio-spacing"></span>

            <input type="radio" id="delete_data_on_deactivate_off" name="delete_data_on_deactivate" value="off" <?php checked(get_option('delete_data_on_deactivate'), 'off'); ?>>
            <label for="delete_data_on_deactivate_off"><?php _e('Erase (Off)', 'user-referral-free'); ?></label><br>
            <?php wp_nonce_field( 'delete_data_on_deactivate' ); ?>

            <div class="top-spacing"></div>
            
            <div class="button-space">
                <input type="submit" value="<?php _e('Save Changes', 'user-referral-free'); ?>" class="button button-primary">
            </div>
        </form>
    </div>
</div>
<?php } ?>