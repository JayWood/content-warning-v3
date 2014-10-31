<?php

/**
 * CWv3 API calls
 */

/**
 * Display custom CSS
 */
function cwv3_the_css(){
	echo cwv3_get_css();
}

/**
 * Get custom css
 * Uses filters so you can do what you want here programtically.
 * @return string HTML/CSS
 */
function cwv3_get_css(){
	$image      = get_option( 'cwv3_bg_image', '' );
	$color      = get_option( 'cwv3_bg_color', '' );
	$custom_css = get_option( 'cwv3_css', '' );

	$bg_image_css = ! empty( $image ) ? 'background-image: url( '. esc_url( $img ) . ' ) no-repeat top center;' : '';
	$bg_color_css = ! empty( $color ) ? 'background-color: ' . $color['color'] . ';' : '';

	ob_start();
	?><style type="text/css"><?php

	if ( ! empty( $custom_css ) ){
		echo apply_filters( 'cwv3_custom_css', $custom_css );
	}
	?>#cboxOverlay{
		echo $bg_image_css . $bg_color_css;
	}<?php

	do_action( 'cwv3_after_css' );
	?></style><?php

	return apply_filters( 'cwv3_css', ob_get_clean() );
}

