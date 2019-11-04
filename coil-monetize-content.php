<?php
declare(strict_types=1);
/**
 * Plugin Name: Coil Monetize Content
 * Plugin URI: https://wordpress.org/plugins/coil-monetize-content/
 * Description: Coil offers an effortless way to share content online and get paid for it.
 * Author: Coil
 * Author URI: https://coil.com
 * Version: 1.0.0
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: coil-monetize-content
 * Domain Path: /languages/
 */

namespace Coil;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * @var string Plugin version number.
 */
const PLUGIN_VERSION = '1.0.0';  // TODO: COIL_VERSION

// COIL_URL_PATH    = untrailingslashit( plugin_dir_url( __FILE__ ) ) );
// COIL_PLUGIN_FILE = __FILE__
/*
 * COIL_ASSET_SUFFIX = $suffix;
 */

/*require_once __DIR__ . '/includes/class-coil-block-assets.php';
require_once __DIR__ . '/includes/class-coil-compatibility.php';
require_once __DIR__ . '/includes/class-coil-meta-box.php';
require_once __DIR__ . '/includes/class-coil-post-meta.php';
require_once __DIR__ . '/includes/class-coil-gate-content.php';
require_once __DIR__ . '/includes/admin/class-coil-admin.php';*/
require_once __DIR__ . '/includes/functions.php';

add_action( 'plugins_loaded', __NAMESPACE__ . '\init_plugin' );
