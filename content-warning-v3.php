<?php
/*
Plugin Name:	Content Warning v2
Plugin URI:		http://plugish.com/plugins/content-warning-v3
Description: 	A WordPress Plugin to allow site owners to display an acceptance dialog.  Used mainly for NSFW websites, this plugin provides a dialog popup to warn viewers of it's possible content.
Author: 		Jerry Wood Jr.
Version:		3.6.6
Author URI:		http://plugish.com
Text Domain:    cwv3
*/
require_once dirname( __FILE__ ) . '/inc/api.php';

if ( is_admin() ) {
	require_once dirname( __FILE__ ) . '/inc/options.inc.php';
	if ( ! class_exists( 'JW_SIMPLE_OPTIONS' ) ) {
		require_once dirname( __FILE__ ) . '/lib/jw_simple_options/simple_options.php';
	}
	require_once dirname( __FILE__ ) . '/class/admin.class.php';

	$cwv3_options = new JW_SIMPLE_OPTIONS( $cwv3_op_data );
	register_uninstall_hook( __FILE__, array( $cwv3_options, 'uninstall' ) );
} else {
	require_once dirname( __FILE__ ) . '/class/main.class.php';
}

add_action( 'plugins_loaded', 'jw_cwv3_load_text_domain' );
function jw_cwv3_load_text_domain() {
	load_plugin_textdomain( 'cwv3', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}

