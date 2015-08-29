<?php
// Get categories
$cat_list       = get_categories();
$final_cat_list = array();
foreach ( $cat_list as $cw_cat ) {
	$termID   = $cw_cat->term_id;
	$termName = $cw_cat->name;

	$final_cat_list[ $termID ] = $termName;
}
$cwv3_op_data = array(
	'plugin_title' => 'Content Warning v3',
	'prefix'       => 'cwv3_',
	'menu_title'   => 'Content Warning v3',
	'menu_type'    => 'option',
	'slug'         => 'cwv3_options',
	'opData'       => array(
		'sitewide'   => array(
			'name'   => __( 'Sitewide', 'cwv3' ),
			'type'   => 'check',
			'desc'   => __( 'Takes priority over category, page, and post, home, and misc. pages/posts.', 'cwv3' ),
			'fields' => array(
				'enabled' => 'Enable',
			),
			'def'    => 'enabled',
		),
		'homepage'   => array(
			'name'   => __( 'Home Page', 'cwv3' ),
			'type'   => 'check',
			'desc'   => __( 'Toggle the home page dialog, useful if you have not set a static page for your front-page in Settings -> Reading.', 'cwv3' ),
			'fields' => array(
				'enabled' => 'Enable',
			),
			'def'    => 'enabled',

		),
		'misc'       => array(
			'name'   => __( 'Misc. Pages', 'cwv3' ),
			'type'   => 'check',
			'desc'   => __( 'Enable this to protect search, archive, and other such pages.', 'cwv3' ),
			'fields' => array(
				'enabled' => 'Enable',
			),
			'def'    => 'enabled',

		),
		// jQuery.cookie doesn't allow hour/minutes, so we have to have a specific timeframe
		// in days only.
		'death'      => array(
			'name' => __( 'Cookie Life', 'cwv3' ),
			'desc' => __( 'Time in days for the cookie to expire', 'cwv3' ),
			'type' => 'number',
			'def'  => '1',
		),
		// Dialog Options
		'd_title'    => array(
			'name' => __( 'Dialog Title', 'cwv3' ),
			'desc' => '',
			'type' => 'text',
			'def'  => 'WARNING: Explicit Content',
		),
		'd_msg'      => array(
			'name'     => __( 'Dialog Message', 'cwv3' ),
			'type'     => 'editor',
			'desc'     => __( 'A message shown to your visitor.', 'cwv3' ),
			'def'      => 'The content you are about to view may be considered offensive and/or inappropriate.  Furthermore, this content may be considered adult content, if you are not of legal age or are easily offended, you are required to click the exit button.',
			'settings' => array(
				'teeny'         => true,
				'media_buttons' => false,
			),
		),
		'exit_txt'   => array(
			'name' => __( 'Exit Text', 'cwv3' ),
			'type' => 'text',
			'desc' => __( 'The text for the exit button.', 'cwv3' ),
			'def'  => 'Exit',
		),
		'exit_link'  => array(
			'name' => __( 'Exit Link', 'cwv3' ),
			'type' => 'text',
			'desc' => __( 'The full URL a user should be directed to upon clicking the exit button.', 'cwv3' ),
			'def'  => 'http://google.com',
		),
		'enter_txt'  => array(
			'name' => __( 'Enter Text', 'cwv3' ),
			'type' => 'text',
			'desc' => __( 'The text for the enter button.', 'cwv3' ),
			'def'  => 'Enter',
		),
		'enter_link' => array(
			'name' => __( 'Enter Link', 'cwv3' ),
			'type' => 'text',
			'desc' => __( 'The full URL a user should be directed to upon clicking the enter button.  Leave blank to just close the dialog.', 'cwv3' ),
			'def'  => '#',
		),
		// Denial Options
		'denial'     => array(
			'name'   => __( 'Toggle Denial Option', 'cwv3' ),
			'desc'   => '',
			'type'   => 'check',
			'fields' => array( 'enabled' => 'Enable denial handling.' ),
		),
		'method'     => array(
			'name'   => __( 'Denial Handling Method', 'cwv3' ),
			'desc'   => '',
			'type'   => 'radio',
			'fields' => array(
				'redirect' => 'Redirect the user.',
				'show'     => 'Show the denial dialog.',
			),
			'def'    => 'redirect',
		),
		'den_title'  => array(
			'name' => __( 'Dialog Title', 'cwv3' ),
			'desc' => '',
			'type' => 'text',
			'def'  => 'Access Denied',
		),
		'den_msg'    => array(
			'name'     => __( 'Denial Message', 'cwv3' ),
			'desc'     => '',
			'type'     => 'editor',
			'def'      => __( 'You have been denied access to this content.  If you feel this is in error, please contact a site administrator.', 'cwv3' ),
			'settings' => array(
				'media_buttons' => false,
				'teeny'         => true,
			),
		),
		// Advanced Options
		//// Styling Options
		'bg_image'   => array(
			'name' => __( 'Background Image', 'cwv3' ),
			'desc' => __( 'If not empty, the dialog will use this instead of the background opacity and color.', 'cwv3' ),
			'type' => 'media',
		),
		'bg_opacity' => array(
			'name' => __( 'Background Opacity', 'cwv3' ),
			'desc' => __( 'Input a float value from 0-1, the latter being completely opaque.', 'cwv3' ),
			'type' => 'number',
			'def'  => 0.85,
			'step' => 0.01,
		),
		'bg_color'   => array(
			'name'   => __( 'Background Color', 'cwv3' ),
			'desc'   => __( 'The Overlay color.', 'cwv3' ),
			'type'   => 'color',
			'fields' => array( 'color' => '#000000' ),
		),
		'css'        => array(
			'name' => __( 'Custom CSS', 'cwv3' ),
			'desc' => __( 'For a completely custom look, just drop your css here.', 'cwv3' ),
			'type' => 'textbox',
			'def'  => '',
		),
		'cat_list'   => array(
			'name'   => __( 'Category restrictions', 'cwv3' ),
			'desc'   => __( 'Select categories that you would like to restrict with the dialog.', 'cwv3' ),
			'type'   => 'check',
			'fields' => $final_cat_list,
		),
	),
);
