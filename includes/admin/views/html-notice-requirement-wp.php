<?php
/**
 * Admin View: WordPress Requirement Notice.
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
<div class="notice notice-error">
	<p><?php echo sprintf( __( 'Sorry, %1$s%3$s%2$s requires WordPress %4$s or higher. Please upgrade your WordPress setup.', 'coil-for-wp' ), '<strong>', '</strong>', 'Coil', COIL_WP_VERSION_REQUIRE ); ?></p>
</div>
