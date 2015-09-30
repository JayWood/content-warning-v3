<?php

/**
 * CWv3 API calls
 */

/**
 * Display custom CSS
 * @since 3.6.3
 */
function cwv3_the_css() {
	echo cwv3_get_css();
}

/**
 * Get custom css
 * Uses filters so you can do what you want here programtically.
 * @since 3.6.3
 * @return string HTML/CSS
 */
function cwv3_get_css() {
	$image      = get_option( 'cwv3_bg_image', '' );
	$color      = get_option( 'cwv3_bg_color', '' );
	$custom_css = get_option( 'cwv3_css', '' );
	$opacity    = get_option( 'cwv3_bg_opacity', '' );

	$bg_image_css = ! empty( $image ) ? 'background-image: url( '. esc_url( $image ) . ' ) no-repeat top center;' : '';
	$bg_color_css = ! empty( $color ) ? 'background-color: ' . $color['color'] . ';' : '';

	ob_start();
	?>
	<!-- CWV3 CSS -->
	<style type="text/css">
	.cwv3.dialog-overlay{
		<?php echo $bg_image_css . $bg_color_css; ?>
		<?php if ( ! empty( $opacity ) ) : ?>
		opacity: <?php echo floatval( $opacity ); ?>;
		-moz-opacity: <?php echo floatval( $opacity ); ?>;
		-webkit-opacity: <?php echo floatval( $opacity ); ?>;
		<?php endif; ?>

	}
	<?php echo $custom_css; ?>
	<?php do_action( 'cwv3_after_css' ); ?>
	</style>
	<!-- END CWV3-CSS -->
	<?php

	return apply_filters( 'cwv3_css', ob_get_clean() );
}

/**
 * Echos out the dialog
 *
 * @since 3.6.3
 */
function cwv3_js_dialog() {
	echo cwv3_get_js_dialog();
}

/**
 * Builds dialog HTML
 *
 * @since 3.6.3
 * @return mixed|void
 */
function cwv3_get_js_dialog() {

	$exit_text           = get_option( 'cwv3_exit_txt', __( 'Exit', 'cwv3' ) );
	$enter_text          = get_option( 'cwv3_enter_txt', __( 'Enter', 'cwv3' ) );

	$cwv3_denial_title   = get_option( 'cwv3_den_title', __( 'Access Denied', 'cwv3' ) );
	$cwv3_denial_message = get_option( 'cwv3_den_msg', __( 'You have been denied access to this content.  If you feel this is in error, please contact a site administrator.', 'cwv3' ) );

	$cwv3_title          = get_option( 'cwv3_d_title', __( 'Warning: Explicit Content', 'cwv3' ) );
	$cwv3_message        = get_option( 'cwv3_d_msg', __( 'The content you are about to view may be considered offensive and/or inappropriate.  Furthermore, this content may be considered adult content, if you are not of legal age or are easily offended, you are required to click the exit button.', 'cwv3' ) );

	$exit_url            = get_option( 'cwv3_exit_link', '#' );
	$enter_url           = get_option( 'cwv3_enter_link', '#' );

	// Was going to use apply_filters the_content but didn't want other
	// extra filters here, so we run them 1 by 1 instead
	$cwv3_message = do_shortcode( wpautop( wp_kses_post( $cwv3_message ) ) );
	$cwv3_denial_message = do_shortcode( wpautop( wp_kses_post( $cwv3_denial_message ) ) );

	ob_start();
	?>
	<!-- CWV3 JS Dialog -->
	<div class="cwv3 dialog-overlay" style="display:none;">&nbsp;</div>
	<div id="cwv3_dialog" class="cwv3_dialog js" style="display:none;">
		<div class="cwv3 auth">
			<div class="cwv3_title"><?php echo esc_attr( $cwv3_title ); ?></div>
			<div class="cwv3_content"><?php echo $cwv3_message ?></div>
			<div class="cwv3_btns">
				<div class="cwv3_enter">
					<a href="<?php echo esc_url( $enter_url ); ?>"><?php echo esc_attr( $enter_text ); ?></a>
				</div>
				<div class="cwv3_exit">
					<a href="<?php echo esc_url( $exit_url ); ?>"><?php echo esc_attr( $exit_text ); ?></a>
				</div>
			</div>
		</div>
		<div class="cwv3 denied">
			<div class="cwv3_title"><?php echo esc_attr( $cwv3_denial_title ); ?></div>
			<div class="cwv3_content"><?php echo $cwv3_denial_message; ?></div>
			<div class="cwv3_btns">
				<div class="cwv3_exit">
					<a href="<?php echo esc_url( $exit_url ); ?>"><?php echo esc_attr( $exit_text ); ?></a>
				</div>
			</div>
		</div>
	</div>
	<!-- END CWV3 JS Dialog -->
	<?php
	$dialog_output = ob_get_clean();
	$params = array(
		'title'          => $cwv3_title,
		'message'        => $cwv3_message,
		'enter_url'      => $enter_url,
		'exit_url'       => $exit_url,
		'enter_text'     => $enter_text,
		'exit_text'      => $exit_text,
		'denial_title'   => $cwv3_denial_title,
		'denial_message' => $cwv3_denial_message,
	);
	return apply_filters( 'cwv3_js_dialog_output', $dialog_output, $params );
}
