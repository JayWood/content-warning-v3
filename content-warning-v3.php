<?php
/*
Plugin Name:	Content Warning v2
Plugin URI:		http://plugish.com/plugins/content-warning-v3
Description: 	A plugin based on my v2 code, while drastically deviating from the original.  Used mainly for NSFW websites, this plugin provides a dialog popup to warn viewers of it's possible content.
Author: 		Jerry Wood Jr.
Version:		3.5.1
Author URI:		http://plugish.com
*/
require_once (dirname(__FILE__).'/inc/options.inc.php');
require_once (dirname(__FILE__).'/lib/jw_simple_options/simple_options.php');
require_once (dirname(__FILE__).'/class/main.class.php');
$cwv3_options = new JW_SIMPLE_OPTIONS($cwv3_op_data);
register_uninstall_hook(__FILE__, $cwv3_options->uninstall() );