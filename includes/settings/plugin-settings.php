<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap coil plugin-settings">
	<form action="options.php" method="post">
		<?php settings_fields( 'coil_content_settings_posts_group' ); ?>
		<?php do_settings_sections( 'coil_content_settings' ); ?>
		<?php submit_button(); ?>
	</form>
</div>
