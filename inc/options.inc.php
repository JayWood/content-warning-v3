<?
// Get categories
$cat_list = get_categories();
$final_cat_list = array();
foreach($cat_list as $cw_cat){
	$termID = $cw_cat->term_id;
	$termName = $cw_cat->name;
	
	$final_cat_list[$termID] = $termName;
}
$cwv3_op_data = array(
	'plugin_title'	=>	'Content Warning v3',
	'prefix'	=>	'cwv3_',
	'menu_title'	=>	'CWv3 Options',
	'slug'	=>	'cwv3_options',
	'opData'	=>	array(
		'sitewide'	=>	array(
			'name'	=>	'Sitewide',
			'type'	=>	'check',
			'desc'	=>	'Takes priority over category, page, and post, home, and misc. pages/posts.',
			'fields'	=>	array(
				'enabled'	=>	'Enable'
			),
			'def'	=>	'enabled'
		),
		'homepage'	=>	array(
			'name'	=>	'Home Page',
			'type'	=>	'check',
			'desc'	=>	'Toggle the home page dialog, useful if you have not set a static page for your front-page in Settings -> Reading.',
			'fields'	=>	array(
				'enabled'	=>	'Enable'
			),
			'def'	=>	'enabled'
			
		),
		'misc'	=>	array(
			'name'	=>	'Misc. Pages',
			'type'	=>	'check',
			'desc'	=>	'Enable this to protect search, archive, and other such pages.',
			'fields'	=>	array(
				'enabled'	=>	'Enable'
			),
			'def'	=>	'enabled'
			
		),
		'death'		=>	array(
			'name'	=>	'Cookie Life',
			'desc'	=>	'How long before the cookie expires.',
			'type'	=>	'timeframe',
			'def'	=>	array('multiplier'=>1, 'time'=>60*60*24)
		),
		// Dialog Options
		'd_title'	=>	array(
			'name'	=>	'Dialog Title',
			'desc'	=>	'',
			'type'	=>	'text',
			'def'	=>	'WARNING: Explicit Content'
		),
		'd_msg'		=>	array(
			'name'	=>	'Dialog Message',
			'type'	=>	'editor',
			'desc'	=>	'A message shown to your visitor.',
			'def'	=>	'The content you are about to view may be considered offensive and/or inappropriate.  Furthermore, this content may be considered adult content, if you are not of legal age or are easily offended, you are required to click the exit button.',
			'settings'	=>	array(
				'teeny'	=>	true,
				'media_buttons'	=>	false
			)
		),
		'exit_txt'	=>	array(
			'name'	=>	'Exit Text',
			'type'	=>	'text',
			'desc'	=>	'The text for the exit button.',
			'def'	=>	'Exit'
		),
		'exit_link'	=>	array(
			'name'	=>	'Exit Link',
			'type'	=>	'text',
			'desc'	=>	'The full URL a user should be directed to upon clicking the exit button.',
			'def'	=>	'http://google.com'
		),
		'enter_txt'	=>	array(
			'name'	=>	'Enter Text',
			'type'	=>	'text',
			'desc'	=>	'The text for the enter button.',
			'def'	=>	'Enter'
		),
		'enter_link'	=>	array(
			'name'	=>	'Enter Link',
			'type'	=>	'text',
			'desc'	=>	'The full URL a user should be directed to upon clicking the enter button.  Leave blank to just close the dialog.',
			'def'	=>	'#'
		),
		// Denial Options
		'denial'	=>	array(
			'name'	=>	'Toggle Denial Option',
			'desc'	=>	'',
			'type'	=>	'check',
			'fields'	=>	array('enabled'	=>	'Enable denial handling.')
		),
		'method'	=>	array(
			'name'	=>	'Denial Handling Method',
			'desc'	=>	'',
			'type'	=>	'radio',
			'fields'	=>	array(
				'redirect'	=>	'Redirect the user.',
				'show'	=>	'Show the denial dialog.'
			),
			'def'	=>	'redirect'
		),
		'den_title'	=>	array(
			'name'	=>	'Dialog Title',
			'desc'	=>	'',
			'type'	=>	'text',
			'def'	=>	'Access Denied'
		),
		'den_msg'	=>	array(
			'name'	=>	'Denial Message',
			'desc'	=>	'',
			'type'	=>	'editor',
			'def'	=>	'You have been denied access to this content.  If you feel this is in error, please contact a site administrator.',
			'settings'	=>	array(
				'media_buttons'	=>	false,
				'teeny'	=>	true
			)
		),
		// Advanced Options
		//// Styling Options
		'bg_image'	=>	array(
			'name'	=>	'Background Image',
			'desc'	=>	'If not empty, the dialog will use this instead of the background opacity and color.',
			'type'	=>	'media'
		),
		'bg_opacity'	=>	array(
			'name'	=>	'Background Opacity',
			'desc'	=>	'Input a float value from 0-1, the latter being completely opaque.',
			'type'	=>	'number',
			'def'	=>	0.85,
			'step'	=>	0.01
		),
		'bg_color'	=>	array(
			'name'	=>	'Background Color',
			'desc'	=>	'The Overlay color.',
			'type'	=>	'color',
			'fields'	=>	array('color'=>'#000000')
		),
		'css'	=>	array(
			'name'	=>	'Custom CSS',
			'desc'	=>	'For a completely custom look, just drop your css here.',
			'type'	=>	'textbox',
			'def'	=>	''
		),
		'cat_list'	=>	array(
			'name'	=>	'Category restrictions',
			'desc'	=>	'Select categories that you would like to restrict with the dialog.',
			'type'	=>	'check',
			'fields'	=>	$final_cat_list
		)
	)
);
?>