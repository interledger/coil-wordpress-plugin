<?php
/**
 * Admin View: Settings
 *
 * @author   Sébastien Dumont
 * @category Admin
 * @package  Coil/Admin/Views
 * @license  GPL-2.0+
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap coil settings">

	<div class="container">

		<div class="content">
			<div class="logo">
				<img src="<?php echo COIL_URL_PATH . '/assets/images/coil-favicon-256.png'; ?>" alt="<?php echo esc_attr__( 'Coil for WordPress', 'coil-for-wp' ); ?>" />
			</div>

			<h1 class="screen-reader-text"><?php echo 'Coil'; ?></h1>

			<p><strong><?php printf( __( 'Thanks for choosing %s.', 'coil-for-wp' ), 'Coil' ); ?></strong></p>

			<p><?php esc_html_e( 'Please submit your payout pointer below in order for your content to be tracked for monetization.', 'coil-for-wp' ); ?></p>

			<form method="POST" action="" id="mainform">
				<label for="coil_payout_pointer_id"><?php _e( 'Payout Pointer', 'coil-for-wp' ); ?></label>
				<input class="wide-input" type="text" name="coil_payout_pointer_id" id="coil_payout_pointer_id" value="<?php echo get_option( 'coil_payout_pointer_id' ); ?>" placeholder="$pay.stronghold.co/1a1b4654bdgj06ab547228c43af27ac0f2411" />

				<p class="submit" style="text-align: center;">
					<?php submit_button( esc_attr__( 'Save', 'coil-for-wp' ), 'button-primary button-hero', esc_attr__( 'Save', 'coil-for-wp' ), false, array( 'id' => 'save' ) ); ?>
					<?php wp_nonce_field( 'coil_for_wp_settings_action', 'coil_for_wp_settings_nonce' ); ?>
				</p>
			</form>

		</div>

	</div>

</div>
