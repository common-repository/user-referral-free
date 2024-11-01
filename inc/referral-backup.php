<?php
/*
	* Page Name: 		referral-backup.php
	* Page URL: 		https://softclever.com
	* Author: 			Md Maruf Adnan Sami
	* Author URL: 		https://www.mdmarufadnansami.com
*/ 

// Export / Import Data //
function scurf_add_backup() {
    add_submenu_page(
        'user-referral-free-settings',
        __('Export / Import Data', 'user-referral-free'),
        __('Backup', 'user-referral-free'),
        'manage_options',
        'user-referral-free-backup',
        'scurf_render_backup'
    );
}
add_action('admin_menu', 'scurf_add_backup');

// Backup Page //
function scurf_render_backup() {
    if (isset($_GET['status'])) {
        $status = sanitize_text_field($_GET['status']);

        if ($status === 'exported') {
            echo '<div class="updated notice is-dismissible"><p>'. __('Data successfully exported!', 'user-referral-free') .'</p></div>';

        } elseif ($status === 'no_data') {
            echo '<div class="error notice is-dismissible"><p>'. __('No data found!', 'user-referral-free') .'</p></div>';

        } elseif ($status === 'imported') {
            echo '<div class="updated notice is-dismissible"><p>'. __('Data successfully imported!', 'user-referral-free') .'</p></div>';

        } elseif ($status === 'invalid_format') {
            echo '<div class="error notice is-dismissible"><p>'. __('Please upload a CSV file!', 'user-referral-free') .'</p></div>';
        }
    }
    ?>
    <div class="section-divider">
        <?php require_once plugin_dir_path(__FILE__)."../core/referral-premium.php"; ?>

        <div class="referral-system">
            <h2><?php _e('Export / Import Data', 'user-referral-free'); ?></h2>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data" class="upload-section">
                <h5><?php _e('Export all the user data such as history, points meta, etc.', 'user-referral-free'); ?></h5> <hr>
                <input type="hidden" name="action" value="export_referral_data">
                <p>
                    <input type="submit" value="<?php _e('Export Data', 'user-referral-free'); ?>" class="button button-primary">
                </p>
                <?php wp_nonce_field('export_referral_data'); ?>
            </form>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data" class="upload-section-last">
                <h5><?php _e('Import all the user data from your existing downloaded CSV file.', 'user-referral-free'); ?></h5> <hr>
                <input type="hidden" name="action" value="import_referral_data">
                <p>
                    <input type="file" name="import_file">
                </p>
                <p>
                    <input type="submit" value="<?php _e('Import Data', 'user-referral-free'); ?>" class="button button-primary">
                </p>
                <?php wp_nonce_field('import_referral_data'); ?>
            </form>
        </div>
    </div>
    <?php
}

// Export Data //
function scurf_export_referral_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'referral_history';
    $data = $wpdb->get_results("SELECT * FROM $table_name");

    if (!empty($data)) {
        $filename = 'referral_data_' . date('YmdHis') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $filename);

        $file = fopen('php://output', 'w');
        //$header = array("id", "user_id", "login_id", "user_name", "type", "points", "ip_address", "created_at", "user_points");
        $header = array();
        fputcsv($file, $header);

        foreach ($data as $row) {
            $user_points = get_user_meta($row->user_id, 'user_points', true);
            fputcsv($file, array($row->id, $row->user_id, $row->login_id, $row->user_name, $row->type, $row->points, $row->ip_address, $row->created_at, $user_points));
        }

        fclose($file);
        exit;
    } else {
        wp_redirect(add_query_arg('status', 'no_data', admin_url('admin.php?page=user-referral-free-backup')));
        exit;
    }
}
add_action('admin_post_export_referral_data', 'scurf_export_referral_data');

// Import Data //
function scurf_import_referral_data() {
    if (!empty($_FILES['import_file']['tmp_name'])) {
        $file = $_FILES['import_file']['tmp_name'];
        $file_type = wp_check_filetype($_FILES['import_file']['name']);

        // Check if the file is a CSV
        if ($file_type['ext'] === 'csv' && $file_type['type'] === 'text/csv') {
            $handle = fopen($file, 'r');
            if ($handle) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'referral_history';

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $wpdb->insert(
                        $table_name,
                        array(
                            'id' => $data[0],
                            'user_id' => $data[1],
                            'login_id' => $data[2],
                            'user_name' => $data[3],
                            'type' => $data[4],
                            'points' => $data[5],
                            'ip_address' => $data[6],
                            'created_at' => $data[7]
                        )
                    );

                    // Update user_points meta field
                    $user_id = $data[1];
                    $user_points = $data[5];
                    update_user_meta($user_id, 'user_points', $user_points);
                }

                fclose($handle);
                wp_redirect(add_query_arg('status', 'imported', admin_url('admin.php?page=user-referral-free-backup')));
                exit;
            } else {
                wp_redirect(add_query_arg('status', 'invalid_format', admin_url('admin.php?page=user-referral-free-backup')));
                exit;
            }
        } else {
            wp_redirect(add_query_arg('status', 'invalid_format', admin_url('admin.php?page=user-referral-free-backup')));
            exit;
        }
    } else {
        wp_redirect(add_query_arg('status', 'invalid_format', admin_url('admin.php?page=user-referral-free-backup')));
        exit;
    }
}
add_action('admin_post_import_referral_data', 'scurf_import_referral_data');
?>