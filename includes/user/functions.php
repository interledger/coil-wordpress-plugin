<?php
declare(strict_types=1);
/**
 * Coil user profile settings.
 */

namespace Coil\User;

/**
 * Adds a text field on the user profile page to save a per-user payment pointer.
 *
 * @param WP_User $user	The WordPress user being displayed.
 * @return void
 */
function add_user_profile_payment_pointer_option( $user ) : void {

	if (
		! current_user_can( apply_filters( 'coil_settings_capability', 'manage_options' ) ) ||
		empty( $user )
	) {
		return;
	}

	$userid = $user->ID;
	$user_payment_pointer = get_user_meta( $userid, 'coil_user_payment_pointer_id', true );
	?>
	<tr>
		<th scope="row"><label for="coil_user_payment_pointer_id"><?php esc_html_e( 'User payment pointer', 'coil-monetize-content' ); ?></label></th>
		<td>
			<input type="text" name="coil_user_payment_pointer_id" id="coil_user_payment_pointer_id" value="<?php echo esc_attr( $user_payment_pointer ); ?>" class="regular-text">
			<p class="description"><?php esc_html_e( 'Set a payment pointer for this user.', 'coil-monetize-content' ); ?></p>
		</td>
	</tr>
	<?php
	wp_nonce_field( 'coil_user_payment_pointer_action', 'coil_user_payment_pointer_nonce' );
}

/**
 * Saves the user payment pointer setting as user_meta if conditons
 * are met.
 *
 * @param WP_User $user	The WordPress user being displayed.
 * @return void
 */
function maybe_save_user_profile_payment_pointer_option( $user_id ) : void {

	if (
		! current_user_can( apply_filters( 'coil_settings_capability', 'manage_options' ) )
	) {
		return;
	}

	check_admin_referer( 'coil_user_payment_pointer_action', 'coil_user_payment_pointer_nonce' );

	$user_payment_pointer_id = ! empty( $_POST['coil_user_payment_pointer_id'] ) ? sanitize_text_field( $_POST['coil_user_payment_pointer_id'] ) : '';
	update_user_meta( $user_id, 'coil_user_payment_pointer_id', $user_payment_pointer_id );

}

/**
 * Output a different payment pointer in the meta tag, if set on the user.
 *
 * @see Coil\print_meta_tag()
 * @param string $payment_pointer The currently defined payment pointer.
 * @return string The $payment_pointer from the global settings page or a user.
 */
function maybe_output_user_payment_pointer_meta_tag( $payment_pointer ) : string {

	$author_id = get_post_field( 'post_author', $post_id );
	$user_payment_pointer = sanitize_text_field( get_user_meta( $author_id, 'coil_user_payment_pointer_id', true ) );

	return $payment_pointer = ( ! empty( $user_payment_pointer ) ) ? $user_payment_pointer : $payment_pointer;
}
