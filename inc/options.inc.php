<?php
// Get categories
$cat_list       = get_categories();
$final_cat_list = array();
foreach ( $cat_list as $cw_cat ) {
	$term_id   = $cw_cat->term_id;
	$term_name = $cw_cat->name;

	$final_cat_list[ $term_id ] = $term_name;
}
$cwv3_op_data = array(
	'plugin_title' => 'Content Warning v3',
	'prefix'       => 'cwv3_',
	'menu_title'   => 'Content Warning v3',
	'menu_type'    => 'option',
	'slug'         => 'cwv3_options',
	'opData'       => array(
		'sitewide'   => array(
			'name'   => __( 'Sitewide', 'content-warning-v2' ),
			'type'   => 'check',
			'desc'   => __( 'Takes priority over category, page, and post, home, and misc. pages/posts.', 'content-warning-v2' ),
			'fields' => array(
				'enabled' => 'Enable',
			),
			'def'    => 'enabled',
		),
		'homepage'   => array(
			'name'   => __( 'Home Page', 'content-warning-v2' ),
			'type'   => 'check',
			'desc'   => __( 'Toggle the home page dialog, useful if you have not set a static page for your front-page in Settings -> Reading.', 'content-warning-v2' ),
			'fields' => array(
				'enabled' => 'Enable',
			),
			'def'    => 'enabled',

		),
		'misc'       => array(
			'name'   => __( 'Misc. Pages', 'content-warning-v2' ),
			'type'   => 'check',
			'desc'   => __( 'Enable this to protect search, archive, and other such pages.', 'content-warning-v2' ),
			'fields' => array(
				'enabled' => 'Enable',
			),
			'def'    => 'enabled',

		),
		// jQuery.cookie doesn't allow hour/minutes, so we have to have a specific timeframe
		// in days only.
		'death'      => array(
			'name' => __( 'Cookie Life', 'content-warning-v2' ),
			'desc' => __( 'Time in days for the cookie to expire', 'content-warning-v2' ),
			'type' => 'number',
			'def'  => '1',
		),
		// Dialog Options
		'd_title'    => array(
			'name' => __( 'Dialog Title', 'content-warning-v2' ),
			'desc' => '',
			'type' => 'text',
			'def'  => 'WARNING: Explicit Content',
		),
		'd_msg'      => array(
			'name'     => __( 'Dialog Message', 'content-warning-v2' ),
			'type'     => 'editor',
			'desc'     => __( 'A message shown to your visitor.', 'content-warning-v2' ),
			'def'      => 'The content you are about to view may be considered offensive and/or inappropriate.  Furthermore, this content may be considered adult content, if you are not of legal age or are easily offended, you are required to click the exit button.',
			'settings' => array(
				'teeny'         => true,
				'media_buttons' => false,
			),
		),
		'exit_txt'   => array(
			'name' => __( 'Exit Text', 'content-warning-v2' ),
			'type' => 'text',
			'desc' => __( 'The text for the exit button.', 'content-warning-v2' ),
			'def'  => 'Exit',
		),
		'exit_link'  => array(
			'name' => __( 'Exit Link', 'content-warning-v2' ),
			'type' => 'text',
			'desc' => __( 'The full URL a user should be directed to upon clicking the exit button.', 'content-warning-v2' ),
			'def'  => 'http://google.com',
		),
		'enter_txt'  => array(
			'name' => __( 'Enter Text', 'content-warning-v2' ),
			'type' => 'text',
			'desc' => __( 'The text for the enter button.', 'content-warning-v2' ),
			'def'  => 'Enter',
		),
		'enter_link' => array(
			'name' => __( 'Enter Link', 'content-warning-v2' ),
			'type' => 'text',
			'desc' => __( 'The full URL a user should be directed to upon clicking the enter button.  Leave blank to just close the dialog.', 'content-warning-v2' ),
			'def'  => '#',
		),
		// Denial Options
		'denial'     => array(
			'name'   => __( 'Toggle Denial Option', 'content-warning-v2' ),
			'desc'   => '',
			'type'   => 'check',
			'fields' => array( 'enabled' => 'Enable denial handling.' ),
		),
		'method'     => array(
			'name'   => __( 'Denial Handling Method', 'content-warning-v2' ),
			'desc'   => '',
			'type'   => 'radio',
			'fields' => array(
				'redirect' => 'Redirect the user.',
				'show'     => 'Show the denial dialog.',
			),
			'def'    => 'redirect',
		),
		'den_title'  => array(
			'name' => __( 'Dialog Title', 'content-warning-v2' ),
			'desc' => '',
			'type' => 'text',
			'def'  => 'Access Denied',
		),
		'den_msg'    => array(
			'name'     => __( 'Denial Message', 'content-warning-v2' ),
			'desc'     => '',
			'type'     => 'editor',
			'def'      => __( 'You have been denied access to this content.  If you feel this is in error, please contact a site administrator.', 'content-warning-v2' ),
			'settings' => array(
				'media_buttons' => false,
				'teeny'         => true,
			),
		),
		// Advanced Options
		//// Styling Options
		'bg_image'   => array(
			'name' => __( 'Background Image', 'content-warning-v2' ),
			'desc' => __( 'If not empty, the dialog will use this instead of the background opacity and color.', 'content-warning-v2' ),
			'type' => 'media',
		),
		'bg_opacity' => array(
			'name' => __( 'Background Opacity', 'content-warning-v2' ),
			'desc' => __( 'Input a float value from 0-1, the latter being completely opaque.', 'content-warning-v2' ),
			'type' => 'number',
			'def'  => 0.85,
			'step' => 0.01,
		),
		'bg_color'   => array(
			'name'   => __( 'Background Color', 'content-warning-v2' ),
			'desc'   => __( 'The Overlay color.', 'content-warning-v2' ),
			'type'   => 'color',
			'fields' => array( 'color' => '#000000' ),
		),
		'css'        => array(
			'name' => __( 'Custom CSS', 'content-warning-v2' ),
			'desc' => __( 'For a completely custom look, just drop your css here.', 'content-warning-v2' ),
			'type' => 'textbox',
			'def'  => '',
		),
		'cat_list'   => array(
			'name'   => __( 'Category restrictions', 'content-warning-v2' ),
			'desc'   => __( 'Select categories that you would like to restrict with the dialog.', 'content-warning-v2' ),
			'type'   => 'check',
			'fields' => $final_cat_list,
		),
	),
);
