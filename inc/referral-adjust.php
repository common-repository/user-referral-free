<?php
/*
	* Page Name: 		referral-adjust.php
	* Page URL: 		https://softclever.com
	* Author: 			Md Maruf Adnan Sami
	* Author URL: 		https://www.mdmarufadnansami.com
*/ 

// Adjust Points //
function scurf_add_adjust() {
    add_submenu_page(
        'user-referral-free-settings',
        __('Adjust Points', 'user-referral-free'),
        __('Adjust', 'user-referral-free'),
        'manage_options',
        'user-referral-free-adjust',
        'scurf_render_adjust'
    );
}
add_action('admin_menu', 'scurf_add_adjust');

// Adjust Points Page //
function scurf_render_adjust() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
?>

<div class="section-divider">
    <?php require_once plugin_dir_path(__FILE__)."../core/referral-premium.php"; ?>

    <div class="referral-system">
		<h2>
            <?php _e('Adjust Points by User ID', 'user-referral-free'); ?>
            <span>
                <?php _e('PRO', 'user-referral-free'); ?>
            </span>
        </h2>
		<form method="post">
			<table class="referral-table">
				<tr <?php scurf_add_alt_class(); ?>>
					<th><label for="user_id"><?php _e('User ID', 'user-referral-free'); ?></label></th>
					<td><input type="number" id="user_id" name="user_id" min="1" placeholder="Enter any user id"></td>
				</tr>
				<tr <?php scurf_add_alt_class(); ?>>
					<th><label for="points"><?php _e('Points', 'user-referral-free'); ?></label></th>
					<td><input type="number" id="points" name="points" placeholder="Enter points amount"></td>
				</tr>
				<tr <?php scurf_add_alt_class(); ?>>
					<th><label for="operation"><?php _e('Operation', 'user-referral-free'); ?></label></th>
					<td>
                        <input type="radio" id="add_points_single" name="operation" value="add" checked>
                        <label for="add_points_single"><?php _e('Add Points', 'user-referral-free'); ?></label>

                        <div class="input-spacing"></div>

                        <input type="radio" id="remove_points_single" name="operation" value="remove" class="padding-top: 15px;">
                        <label for="remove_points_single"><?php _e('Remove Points', 'user-referral-free'); ?></label>
					</td>
				</tr>
                <tr <?php scurf_add_alt_class(); ?>>
                    <th><label for="reason"><?php _e('Reason', 'user-referral-free'); ?></label></th>
                    <td><input type="text" id="reason" name="reason" placeholder="Type reason (optional)"></td>
                </tr>
			</table>
			<div class="button-space">
				<input type="submit" name="submit_single" value="<?php _e('Submit', 'user-referral-free'); ?>" class="button button-primary">
			</div>
		</form>
	</div>
</div>

<div class="section-divider">
    <div class="referral-system">
		<h2>
            <?php _e('Adjust Points by User Role', 'user-referral-free'); ?>
            <span>
                <?php _e('PRO', 'user-referral-free'); ?>
            </span>
        </h2>
		<form method="post">
			<table class="referral-table">
                <tr <?php scurf_add_alt_class(); ?>>
                    <th><label for="user_roles"><?php _e('User Roles', 'user-referral-free'); ?></label></th>
                    <td>
                        <?php
                            // Roles
                            $roles = get_editable_roles();
                            foreach ( $roles as $role_key => $role ) {
                                echo '<label class="role_name"><input type="checkbox" name="user_roles[]" value="' . esc_attr( $role_key ) . '"> ' . esc_html( $role['name'] ) . '</label>';
                            }
                        ?>
                    </td>
                </tr>
				<tr <?php scurf_add_alt_class(); ?>>
					<th><label for="points"><?php _e('Points', 'user-referral-free'); ?></label></th>
					<td><input type="number" id="points" name="points" min="0" placeholder="Enter points amount"></td>
				</tr>
				<tr <?php scurf_add_alt_class(); ?>>
					<th><label for="operation"><?php _e('Operation', 'user-referral-free'); ?></label></th>
					<td>
                        <input type="radio" id="add_points_bulk" name="operation" value="add" checked>
                        <label for="add_points_bulk"><?php _e('Add Points', 'user-referral-free'); ?></label>

                        <div class="input-spacing"></div>

                        <input type="radio" id="remove_points_bulk" name="operation" value="remove" class="padding-top: 15px;">
                        <label for="remove_points_bulk"><?php _e('Remove Points', 'user-referral-free'); ?></label>
					</td>
				</tr>
                <tr <?php scurf_add_alt_class(); ?>>
                    <th><label for="reason"><?php _e('Reason', 'user-referral-free'); ?></label></th>
                    <td><input type="text" id="reason" name="reason" placeholder="Type reason (optional)"></td>
                </tr>
			</table>
			<div class="button-space">
				<input type="submit" name="submit_bulk" value="<?php _e('Submit', 'user-referral-free'); ?>" class="button button-primary">
			</div>
		</form>
	</div>
</div>
<?php } ?>