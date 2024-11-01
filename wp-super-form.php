<?php
/*
Plugin Name: WP Super Form
Description: Simple contact form to collect the lead
Version: 0.1
Text Domain: wp-super-form
Domain Path: /languages
Author: CodeFilter
License: GPL2
*/
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define('WPSF_VERSION', '0.1');
define('WPSF_DIR_NAME_FILE', __FILE__ );
define('WPSF_DIR_NAME', dirname( __FILE__ ));
define('WPSF_DIR_URI', plugin_dir_url(__FILE__));
define('WPSF_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ));
define('WPSF_PLUGIN_URL', plugin_dir_url( __FILE__ ));

require_once WPSF_DIR_NAME .'/contact.php';
require_once WPSF_DIR_NAME .'/view/contact.php';
