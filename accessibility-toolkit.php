<?php
/**
 * Plugin Name:         Accessibility Toolkit
 * Plugin URI:          https://pluginrx.com/plugin/accessibility-toolkit/
 * Description:         Admin-side accessibility enhancements including alt text editing and tools to assist with WCAG compliance.
 * Version:             1.0.1
 * Requires at least:   5.9
 * Tested up to:        6.8
 * Requires PHP:        7.4
 * Author:              PluginRx
 * Author URI:          https://pluginrx.com/
 * Discord URI:         https://discord.gg/3HnzNEJVnR
 * Text Domain:         accessibility-toolkit
 * License:             GPLv2 or later
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Created on:          June 18, 2025
 */


/**
 * Define Namespace
 */
namespace Apos37\AccessibilityToolkit;


/**
 * Exit if accessed directly.
 */
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Defines
 */
$plugin_data = get_file_data( __FILE__, [
    'name'         => 'Plugin Name',
    'version'      => 'Version',
    'plugin_uri'   => 'Plugin URI',
    'requires_php' => 'Requires PHP',
    'textdomain'   => 'Text Domain',
    'author'       => 'Author',
    'author_uri'   => 'Author URI',
    'discord_uri'  => 'Discord URI'
] );

// Versions
define( 'A11YTOOLKIT_VERSION', $plugin_data[ 'version' ] );
define( 'A11YTOOLKIT_SCRIPT_VERSION', A11YTOOLKIT_VERSION );                                                // TODO: REPLACE WITH time() DURING TESTING
define( 'A11YTOOLKIT_MIN_PHP_VERSION', $plugin_data[ 'requires_php' ] );

// Names
define( 'A11YTOOLKIT_NAME', $plugin_data[ 'name' ] );
define( 'A11YTOOLKIT_TEXTDOMAIN', $plugin_data[ 'textdomain' ] );
define( 'A11YTOOLKIT__TEXTDOMAIN', str_replace( '-', '_', A11YTOOLKIT_TEXTDOMAIN ) );
define( 'A11YTOOLKIT_AUTHOR', $plugin_data[ 'author' ] );
define( 'A11YTOOLKIT_AUTHOR_URI', $plugin_data[ 'author_uri' ] );
define( 'A11YTOOLKIT_PLUGIN_URI', $plugin_data[ 'plugin_uri' ] );
define( 'A11YTOOLKIT_GUIDE_URL', A11YTOOLKIT_AUTHOR_URI . 'guide/plugin/' . A11YTOOLKIT_TEXTDOMAIN . '/' );
define( 'A11YTOOLKIT_DOCS_URL', A11YTOOLKIT_AUTHOR_URI . 'docs/plugin/' . A11YTOOLKIT_TEXTDOMAIN . '/' );
define( 'A11YTOOLKIT_SUPPORT_URL', A11YTOOLKIT_AUTHOR_URI . 'support/plugin/' . A11YTOOLKIT_TEXTDOMAIN . '/' );
define( 'A11YTOOLKIT_DISCORD_URL', $plugin_data[ 'discord_uri' ] );

// Paths
define( 'A11YTOOLKIT_BASENAME', plugin_basename( __FILE__ ) );                                              //: text-domain/text-domain.php
define( 'A11YTOOLKIT_ABSPATH', plugin_dir_path( __FILE__ ) );                                               //: /home/.../public_html/wp-content/plugins/text-domain/
define( 'A11YTOOLKIT_DIR', plugins_url( '/' . A11YTOOLKIT_TEXTDOMAIN . '/' ) );                             //: https://domain.com/wp-content/plugins/text-domain/
define( 'A11YTOOLKIT_INCLUDES_ABSPATH', A11YTOOLKIT_ABSPATH . 'inc/' );                                     //: /home/.../public_html/wp-content/plugins/text-domain/inc/
define( 'A11YTOOLKIT_INCLUDES_DIR', A11YTOOLKIT_DIR . 'inc/' );                                             //: https://domain.com/wp-content/plugins/text-domain/inc/
define( 'A11YTOOLKIT_JS_PATH', A11YTOOLKIT_INCLUDES_DIR . 'js/' );                                          //: https://domain.com/wp-content/plugins/text-domain/inc/js/
define( 'A11YTOOLKIT_CSS_PATH', A11YTOOLKIT_INCLUDES_DIR . 'css/' );                                        //: https://domain.com/wp-content/plugins/text-domain/inc/css/
define( 'A11YTOOLKIT_IMG_PATH', A11YTOOLKIT_INCLUDES_DIR . 'img/' );                                        //: https://domain.com/wp-content/plugins/text-domain/inc/img/
define( 'A11YTOOLKIT_SETTINGS_PATH', admin_url( 'tools.php?page=' . A11YTOOLKIT__TEXTDOMAIN ) );            //: https://domain.com/wp-admin/tools.php?page=text-domain

// Screen IDs
define( 'A11YTOOLKIT_SETTINGS_SCREEN_ID', 'tools_page_' . A11YTOOLKIT__TEXTDOMAIN );


/**
 * Includes
 */
require_once A11YTOOLKIT_INCLUDES_ABSPATH . 'common.php';
require_once A11YTOOLKIT_INCLUDES_ABSPATH . 'helpers.php';
require_once A11YTOOLKIT_INCLUDES_ABSPATH . 'integrations.php';
require_once A11YTOOLKIT_INCLUDES_ABSPATH . 'settings.php';
require_once A11YTOOLKIT_INCLUDES_ABSPATH . 'media-library.php';
require_once A11YTOOLKIT_INCLUDES_ABSPATH . 'structural.php';
require_once A11YTOOLKIT_INCLUDES_ABSPATH . 'modes.php';

if ( get_option( 'a11ytoolkit_frontend_tools', true ) ) {
    require_once A11YTOOLKIT_INCLUDES_ABSPATH . 'admin-bar.php';
}