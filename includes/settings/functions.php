<?php
declare(strict_types=1);
/**
 * Coil settings.
 */

namespace Coil\Settings;

use Coil\Gating;

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
	?>
	<div class="wrap coil plugin-settings">
		<form action="options.php" method="post">
			<?php settings_fields( 'coil_content_settings_posts_group' ); ?>
			<?php do_settings_sections( 'coil_content_settings' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
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
		'coil_content_settings_posts_group',
		'coil_content_settings_posts_group',
		__NAMESPACE__ . '\coil_content_settings_post_types_validation'
	);

	add_settings_section(
		'coil_content_settings_section',
		_x( 'Content Settings', 'content settings tab section title', 'coil-monetize-content' ),
		__NAMESPACE__ . '\coil_content_settings_section_render_posts_options',
		'coil_content_settings'
	);

}

/**
 * Renders the output of the radio buttons based on the post
 * types available in WordPress.
 *
 * @return void
 */
function coil_content_settings_section_render_posts_options() {

	$post_types = get_post_types(
		[],
		'objects'
	);

	// Set up options to exclude certain post types.
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

	// Store the post type options using the above exclusion options.
	$post_type_options = [];
	foreach ( $post_types as $post_type ) {

		if ( ! empty( $exclude ) && in_array( $post_type->name, $exclude, true ) ) {
			continue;
		}
		$post_type_options[] = $post_type;
	}

	// If there are post types available, output them:
	if ( ! empty( $post_type_options ) ) {

		$form_gating_settings = Gating\get_monetization_settings();
		$content_settings_posts_options = get_option( 'coil_content_settings_posts_group' );
		?>
		<table class="widefat">
			<thead>
				<th><?php _e( 'Post Type', 'coil-monetize-content' ); ?></th>
				<?php foreach ( $form_gating_settings as $setting_key => $setting_value ) : ?>
					<th class="posts_table_header">
						<?php echo esc_html( $setting_value ); ?>
					</th>
				<?php endforeach; ?>
			</thead>
			<tbody>
				<?php foreach ( $post_type_options as $post_type ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $post_type->label ); ?></th>
						<?php foreach ( $form_gating_settings as $setting_key => $setting_value ) :
							$input_id   = $post_type->name . '_' . $setting_key;
							$input_name = 'coil_content_settings_posts_group[' . $post_type->name . ']';

							/**
							 * Specify the default checked state on the input from
							 * any settings stored in the database. If the individual
							 * input status is not set, default to the first radio
							 * option (No Monetization)
							 */
							$checked_input = false;
							if ( $setting_key === 'no' ) {
								$checked_input = 'checked="true"';
							} else if ( isset( $content_settings_posts_options[$post_type->name] ) ) {
								$checked_input = checked( $setting_key, $content_settings_posts_options[$post_type->name], false );
							}
							?>
							<td>
								<?php
								printf( '<input type="radio" name="%s" id="%s" value="%s"%s></input>',
									esc_attr( $input_name ),
									esc_attr( $input_id ) ,
									esc_attr( $setting_key ),
									$checked_input
								);
								?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
}


function coil_content_settings_post_types_validation( $post_content_settings ) {
	return array_map(
		function( $radio_value ) {
			$valid_choices = array_keys( Gating\get_monetization_settings() );
			return ( in_array( $radio_value, $valid_choices ) ? $radio_value : 'no' );
		},
		(array)$post_content_settings
	);
}
