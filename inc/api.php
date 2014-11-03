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

function cwv3_js_dialog(){

}

function cwv3_get_js_dialog(){

	$exit_text = get_option( 'cwv3_exit_txt', __( 'Exit', 'cwv3' ) );
	$enter_text = get_option( 'cwv3_enter_txt', __( 'Enter', 'cwv3' ) );

	$cwv3_denial_title = get_option( 'cwv3_den_title', __( 'Access Denied', 'cwv3' ) );
	$cwv3_denial_message = get_option( 'cwv3_den_msg', __( 'You have been denied access to this content.  If you feel this is in error, please contact a site administrator.', 'cwv3' ) );

	$cwv3_title = get_option( 'cwv3_den_title', __( 'Access Denied', 'cwv3' ) );
	$cwv3_message = get_option( 'cwv3_den_msg', __( 'You have been denied access to this content.  If you feel this is in error, please contact a site administrator.', 'cwv3' ) );

	$exit_url     = get_option( 'cwv3_exit_link', '#' );
	$enter_url    = get_option( 'cwv3_enter_link', '#' );
	?>
	<div id="cwv3_dialog" class="cwv3_dialog js">
		<div class="cwv3 auth">
			<div class="cwv3_title"><?php echo esc_attr( $cwv3_den_title ); ?></div>
			<div class="cwv3_content"><?php echo wp_kses_post( $cwv3_den_msg ); ?></div>
			<div class="cwv3_btns">
				<div class="cwv3_enter">
					<a href="<?php echo esc_url( $enter_url ); ?>"><?php echo esc_attr( $enter_text ); ) ?></a>
				</div>
				<div class="cwv3_exit">
					<a href="<?php echo esc_url( $exit_url ); ?>"><?php echo esc_attr( $exit_url ); ) ?></a>
				</div>
			</div>
		</div>
		<div class="cwv3 denied">
			<div class="cwv3_title"><?php echo esc_attr( $cwv3_title ); ?></div>
			<div class="cwv3_content"><?php echo wp_kses_post( $cwv3_message ); ?></div>
			<div class="cwv3_btns">
				<div class="cwv3_exit">
					<a href="<?php echo esc_url( $exit_url ); ?>"><?php echo esc_attr( $exit_url ); ) ?></a>
				</div>
			</div>
		</div>
	</div>
	<?php

}