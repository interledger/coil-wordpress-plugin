<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap coil plugin-settings">

	<p>As a site admin, I want to be able to set a global default of WM gating for all page/post/CPTs, so that I can enable gating and WM on all specific types of content at once.</p>
	<form action="options.php" method="post">

	<?php settings_fields( 'coil_content_settings_post_types' ); ?>
	<?php do_settings_sections( 'coil_content_settings' ); ?>


	</form>

	<?php


	?>

</div>
