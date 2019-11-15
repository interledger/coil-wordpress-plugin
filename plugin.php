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
 */

namespace Coil;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * @var string Plugin version number.
 */
const PLUGIN_VERSION = '1.0.0';

require_once __DIR__ . '/includes/admin/functions.php';
require_once __DIR__ . '/includes/settings/functions.php';
require_once __DIR__ . '/includes/gating/functions.php';
require_once __DIR__ . '/includes/user/functions.php';
require_once __DIR__ . '/includes/functions.php';

add_action( 'plugins_loaded', __NAMESPACE__ . '\init_plugin' );
