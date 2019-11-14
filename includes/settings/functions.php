<?php
declare(strict_types=1);
/**
 * Coil settings.
 */

namespace Coil\Settings;

/**
 * Add Coil settings to the admin navigation menu.
 *
 * @return void
 */
function register_admin_menu() : void {
	add_menu_page(
		esc_attr__( 'Settings for Coil', 'coil-monetize-content' ),
		_x( 'Coil', 'admin menu name', 'coil-monetize-content' ),
		apply_filters( 'coil_settings_capability', 'manage_options' ),
		'coil',
		__NAMESPACE__ . '\render_coil_settings_screen'
	);

	add_submenu_page(
		'coil',
		_x( 'Content Settings', 'admin submenu page title', 'coil-monetize-content' ),
		_x( 'Content Settings', 'admin submenu title', 'coil-monetize-content' ),
		apply_filters( 'coil_settings_capability', 'manage_options' ),
		'coil_content_settings',
		__NAMESPACE__ . '\render_coil_submenu_settings_screen'
	);
}

/**
 * Render the Coil setting screen.
 *
 * @return void
 */
function render_coil_settings_screen() : void {
	include_once( dirname( __FILE__ ) . '/temp-html-settings.php' );
}

/**
 * Render the Coil submenu setting screen.
 *
 * @return void
 */
function render_coil_submenu_settings_screen() : void {
	include_once( dirname( __FILE__ ) . '/plugin-settings.php' );
}

/**
 * Maybe save the Coil admin settings.
 *
 * @return void
 */
function maybe_save_coil_admin_settings() : void {

	if (
		! current_user_can( apply_filters( 'coil_settings_capability', 'manage_options' ) ) ||
		empty( $_REQUEST['coil_for_wp_settings_nonce'] )
	) {
		return;
	}

	// Check the nonce.
	check_admin_referer( 'coil_for_wp_settings_action', 'coil_for_wp_settings_nonce' );

	$payment_pointer_id = ! empty( $_POST['coil_payout_pointer_id'] ) ? $_POST['coil_payout_pointer_id'] : '';
	$content_container  = ! empty( $_POST['coil_content_container'] ) ? $_POST['coil_content_container'] : '';

	$coil_options = [
		'coil_payout_pointer_id' => sanitize_text_field( $payment_pointer_id ),
		'coil_content_container' => sanitize_text_field( $content_container ),
	];

	foreach ( $coil_options as $key => $value ) {
		if ( ! empty( $value ) ) {
			update_option( $key, $value );
		} else {
			delete_option( $key );
		}
	}
}

// Register and define the settings used on this page.
function register_admin_content_settings() {
	register_setting(
		'coil_content_settings_post_types',
		'coil_content_settings',
		__NAMESPACE__ . '\coil_content_settings_validate_options'
	);

	add_settings_section(
		'coil_content_settings_section',
		_x( 'Content Settings', 'content settings tab section title', 'coil-monetize-content' ),
		__NAMESPACE__ . '\coil_content_settings_section_render_posts_options',
		'coil_content_settings'
	);

	add_settings_field(
		'test_stinrg',
		'enter text here',
		'setting_input',
		'coil_content_settingss',
		'coil_content_settings'
	);
}

function coil_content_settings_section_render_posts_options() {

	echo 'This function renders the output of the radio buttons';
	// get all the post types within this page.
	$post_types = get_post_types(
		[],
		'objects'
	);

	// Allow the exclude post types to be filtered.
	$post_types_exclude = [
		'attachment',
		'revision',
		'nav_menu_item',
		'custom_css',
		'customize_changeset',
		'oembed_cache',
		'user_request',
		'wp_block',
	];
	$exclude = apply_filters( 'coil_settings_content_type_exclude', $post_types_exclude );

	$post_type_options = [];
	foreach( $post_types as $post_type ) {

		if ( ! empty( $exclude ) && in_array( $post_type->name, $exclude, true ) ) {
			continue;
		}
		$post_type_options[] = $post_type;
	}

	$form_gating_settings = [
		'no_monetization' => 'No Monetization',
		'monetized_public' => 'Monetized and Public',
		'monetized_subscribers' => 'Monetized Subscribers Only',
	];

	if ( ! empty( $post_type_options ) ) {
		?>
		<table class="form-table">
			<tbody>
				<?php
				foreach( $post_type_options as $post_type ) {
					?>
					<tr>
						<th scope="row"><?php echo esc_html( $post_type->label ); ?></th>
						<?php
						foreach( $form_gating_settings as $setting_key => $setting_value ) {
							$input_id   = $post_type->name . '_' . $setting_key;
							$input_name = $post_type->name . '_content_options';
							?>
							<td>

								<input type="radio" name="<?php echo esc_attr($input_name) ;?>" id="<?php echo esc_attr($input_id) ;?>"></input>
								<label for="<?php echo esc_attr($input_id) ;?>"><?php echo $setting_value; ?></label>
							</td>
							<?php
						}

						?>
					</tr>
					<?php
				}
				?>
			<tbody>
		</table>
		<?php
	}


}


function coil_content_settings_validate_options( $input ) {
	$valid = [];
	return $valid;
}
