<?php
declare(strict_types=1);
/**
 * Plugin Name: Coil Web Monetization
 * Plugin URI: https://wordpress.org/plugins/coil-web-monetization/
 * Description: Coil offers an effortless way to share WordPress content online, and get paid for it.
 * Author: Coil
 * Author URI: https://coil.com
 * Version: 1.4.0
 * License: Apache-2.0
 * License URI: http://www.apache.org/licenses/LICENSE-2.0.txt
 * Text Domain: coil-web-monetization
 */

namespace Coil;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * @var string Plugin version number.
 */
const PLUGIN_VERSION = '1.4.0';

/**
 * @var string Plugin root file.
 */
const COIL__FILE__ = __FILE__;

require_once __DIR__ . '/includes/admin/functions.php';
require_once __DIR__ . '/includes/settings/functions.php';
require_once __DIR__ . '/includes/gating/functions.php';
require_once __DIR__ . '/includes/user/functions.php';
require_once __DIR__ . '/includes/functions.php';

add_action( 'plugins_loaded', __NAMESPACE__ . '\init_plugin' );
