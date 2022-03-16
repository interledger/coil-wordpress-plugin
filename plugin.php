<?php
/**
 * Plugin Name: Coil Web Monetization
 * Plugin URI: https://wordpress.org/plugins/coil-web-monetization/
 * Description: Coil offers an effortless way to share WordPress content online, and get paid for it.
 * Requires PHP: 7.2
 * Author: Coil
 * Author URI: https://coil.com
 * Version: 1.9.0
 * License: Apache-2.0
 * License URI: http://www.apache.org/licenses/LICENSE-2.0.txt
 * Text Domain: coil-web-monetization
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( version_compare( PHP_VERSION, '7.2', '<' ) ) {

	add_action( 'admin_notices', 'coil_show_php_warning' );
	add_action( 'admin_init', 'coil_deactive_self' );

	return;
}

require_once __DIR__ . '/includes/admin/functions.php';
require_once __DIR__ . '/includes/settings/functions.php';
require_once __DIR__ . '/includes/settings/rendering.php';
require_once __DIR__ . '/includes/gating/functions.php';
require_once __DIR__ . '/includes/transfers/functions.php';
require_once __DIR__ . '/includes/user/functions.php';
require_once __DIR__ . '/includes/functions.php';

add_action( 'plugins_loaded', 'Coil\init_plugin' );
