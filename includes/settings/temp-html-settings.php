<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap coil settings">

	<div class="container">

		<div class="content">
			<div class="logo">
				<img src="<?php echo esc_url( plugin_dir_url( dirname( __DIR__ ) ) . 'assets/images/coil-favicon-256.png' ); ?>" alt="" />
			</div>

			<h1 class="screen-reader-text"><?php echo esc_html( _x( 'Coil', 'header', 'coil-monetize-content' ) ); ?></h1>

			<p><strong><?php esc_html__( 'Thanks for choosing Coil.', 'coil-monetize-content' ); ?></strong></p>

			<p><?php esc_html_e( 'Please submit your payment pointer below in order for your content to be tracked for monetization.', 'coil-monetize-content' ); ?></p>

			<form method="POST" action="" id="mainform">
				<label for="coil_payment_pointer_id"><?php esc_html_e( 'Payment Pointer', 'coil-monetize-content' ); ?></label>
				<input class="wide-input" type="text" name="coil_payment_pointer_id" id="coil_payment_pointer_id" value="<?php echo esc_attr( get_option( 'coil_payment_pointer_id' ) ); ?>" placeholder="$pay.stronghold.co/0000000000000000000000000000000000000" />

				<label for="coil_content_container"><?php esc_html_e( 'Content Container', 'coil-monetize-content' ); ?></label>
				<input class="wide-input" type="text" name="coil_content_container" id="coil_content_container" value="<?php echo esc_attr( get_option( 'coil_content_container' ) ); ?>" placeholder=".content-area .entry-content" />

				<p class="submit" style="text-align: center;">
					<?php
					submit_button(
						esc_html( _x( 'Save', 'verb - save changes', 'coil-monetize-content' ) ),
						'button-primary button-hero',
						'save',
						false
					);

					wp_nonce_field( 'coil_for_wp_settings_action', 'coil_for_wp_settings_nonce' );
					?>
				</p>
			</form>

		</div>

	</div>

</div>
