<?php

class CWV3 {

	public function CWV3() {
		// Styling and such
		add_action( 'init', array( &$this, 'register_frontend_data' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_dependancies' ) );

		add_action( 'wp_footer', array( $this, 'render_dialog' ) );

		// Post Meta Box for this.
		add_action( 'add_meta_boxes', array( $this, 'cw_meta' ) );
		add_action( 'save_post', array( $this, 'cwv3_meta_save' ) );

		// AJAX Handle
		add_action( 'wp_ajax_cwv3_ajax', array( $this, 'handle_ajax' ) );
		add_action( 'wp_ajax_nopriv_cwv3_ajax', array( $this, 'handle_ajax' ) );

		// Post column filters
		add_filter( 'manage_page_posts_columns', array( $this, 'post_cols' ) );
		add_filter( 'manage_post_posts_columns', array( $this, 'post_cols' ) );

		// Post column info
		add_action( 'manage_posts_custom_column', array( $this, 'set_col_data' ) );
		add_action( 'manage_pages_custom_column', array( $this, 'set_col_data' ) );

		add_action( 'admin_head', array( &$this, 'render_lazy_mans_css' ) );

		add_action( 'wp_head', array( &$this, 'override_css' ) );
	}

	public function override_css() {

		$img = get_option( 'cwv3_bg_image', '' );
		$color = get_option( 'cwv3_bg_color' );
		?><style type="text/css"><?php
		$custom_css = get_option( 'cwv3_css', '' );

		if ( ! empty( $custom_css ) ){
			echo $custom_css;
		}

		if ( ! empty( $img ) ) {
			?>#cboxOverlay{background:url(<?php echo $img; ?>) no-repeat top center; background-color:<?php echo $color['color']; ?>;}<?php
		}else {
			?> #cboxOverlay{background-image:url(<?php echo $img; ?>) no-repeat top center; background-color:<?php echo $color['color']; ?>;} <?php
		}
		?></style><?php
	}

	public function render_lazy_mans_css() {
		echo '<style type="text/css">th#cwv2{width: 32px; text-align:center;} td.column-cwv2{text-align:center;}</style>';
	}

	public function set_col_data( $col ) {
		global $post;

		$sw = get_option( 'cwv3_sitewide' );
		switch ( $col ) {
		case 'cwv2':
			if ( 'yes' == get_post_meta( $post->ID, 'cwv3_auth', true ) || 'enabled' == $sw[0] ) {
				echo '<span style="color:#0F0; font-weight:bold;" class="cw_protected">Yes</span>';
			}else {
				echo '<span style="color:#F00; font-weight:bold;" class="cw_vulnerable">No</span>';
			}
			break;
		}
	}

	public function post_cols( $cols ) {

		return array_slice( $cols, 0, 1, true ) + array( 'cwv2' => 'CW' ) + array_slice( $cols, 1, count( $array ) - 1, true );

	}

	public function cw_meta() {
		$scr = array( 'post', 'page' );
		foreach ( $scr as $screen ) {
			add_meta_box( 'cwv3_meta_section',
				__( 'CWV3 Security' ),
				array( &$this, 'render_metabox' ),
				$screen,
				'side',
				'high'
			);
		}
	}

	public function cwv3_meta_save( $post_id ) {

		// check isset before access (edit by @jgraup)
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ){
			if ( ! current_user_can( 'edit_page', $post_id ) ){
				return;
			} else {
				if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

				if ( ! isset( $_POST['cwv3_meta'] ) || ! wp_verify_nonce( $_POST['cwv3_meta'], plugin_basename( __FILE__ ) ) ) { return; }
			}
		}

		// check isset before access (edit by @jgraup)
		if ( isset( $_POST['cwv3_auth'] ) ){
			$mydata = sanitize_text_field( $_POST['cwv3_auth'] );
			update_post_meta( $post_id, 'cwv3_auth', $mydata );
		}
	}

	public function handle_ajax() {
		$post_id = intval( $_POST['id'] );

		check_ajax_referer( 'cwv3_ajax_'.$post_id, 'nonce' );

		if ( 'exit' == $_POST['method'] ) {
			$d = get_option( 'cwv3_denial' );
			if ( 'enabled' == $d[0] ) {
				$resp = $this->set_cookie( $post_id, 3 );
			}
			$resp = 'denied';
		}else {
			$resp = $this->set_cookie( $post_id, 1 );
		}
		echo $resp;
		die;
	}

	public function load_dependancies() {
		global $post;

		if ( current_user_can( 'manage_options' ) ) { return; }

		wp_enqueue_style( 'cwv3_css' );
		wp_enqueue_script( 'cwv3_js' );

		$elink = get_option( 'cwv3_enter_link' );
		$exlink = get_option( 'cwv3_exit_link' );
		$p_ID = ( is_front_page() ) ? -1 : ( is_attachment() ? $post->post_parent : ( is_archive() || is_search() ) ? -2 : $post->ID );
		$d = get_option( 'cwv3_denial' );
		wp_localize_script( 'cwv3_js', 'cwv3_params', array(
				'action'    => 'cwv3_ajax',
				'nonce'     => wp_create_nonce( 'cwv3_ajax_'.$p_ID ),
				'admin_url' => admin_url( 'admin-ajax.php' ),
				'id'        => $p_ID,
				'sd'        => ( $this->check_data() == false || ( $this->check_data() == 3 && ! empty( $d ) ) ) ? true : false,
				'enter'     => ! empty( $elink ) ? $elink : '#',
				'exit'      => ! empty( $exlink ) ? $exlink : 'http://google.com',
				'opacity'   => get_option( 'cwv3_bg_opacity', 0.85 )
			) );
	}

	public function register_frontend_data() {
		// Colorbox w/ MIT License
		wp_register_style( 'colorbox', plugins_url( 'js/colorbox.1.5.10/colorbox.css', dirname( __FILE__ ) ), '', '1.4.14', 'ALL' );

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'colorbox_js', plugins_url( "js/colorbox.1.5.10/jquery.colorbox{$min}.js", dirname( __FILE__ ) ), array( 'jquery' ), '1.4.14', true );

		// Main data
		wp_register_script( 'cwv3_js', plugins_url( "js/cwv3{$min}.js", dirname( __FILE__ ) ), array( 'colorbox_js' ), uniqid(), true );
		wp_register_style( 'cwv3_css', plugins_url( "css/cwv3{$min}.css", dirname( __FILE__ ) ), array( 'colorbox' ), '1.0' );
	}

	public function set_cookie( $id, $action ) {

		$time = get_option( 'cwv3_death' );

		$sw = get_option( 'cwv3_sitewide' );
		$hm = get_option( 'cwv3_homepage' );
		$mi = get_option( 'cwv3_misc' );

		$cData = array(
			// check isset before access (edit by @jgraup)
			'pages'      => ! isset( $_COOKIE['cwv3_pages'] ) ? '' : json_decode( stripslashes( $_COOKIE['cwv3_pages'] ) ),
			'posts'      => ! isset( $_COOKIE['cwv3_posts'] ) ? '' : json_decode( stripslashes( $_COOKIE['cwv3_posts'] ) ),
			'categories' => ! isset( $_COOKIE['cwv3_cats'] ) ? ''  : json_decode( stripslashes( $_COOKIE['cwv3_cats'] ) )
		);

		// ensure we're using  valid objects (edit by @jgraup)
		foreach ( $cData as $key => $value ) {
			if ( is_scalar( $value ) ) {
				$cData[ $key ] = new stdClass;
			}
		}
		if ( 'enabled' == ! empty( $sw ) ) {
			$cData['pages']->sitewide = $action;
			return setcookie( 'cwv3_pages', json_encode( $cData['pages'] ), ( $time['multiplier'] * $time['time'] )+time(), COOKIEPATH, COOKIE_DOMAIN, false );
		}

		if ( 'enabled' == ! empty( $hm ) && -1 == $id ) {
			$cData['pages']->home = $action;
			return setcookie( 'cwv3_pages', json_encode( $cData['pages'] ), ( $time['multiplier'] * $time['time'] )+time(), COOKIEPATH, COOKIE_DOMAIN, false );
		}

		if ( 'enabled' == ! empty( $mi ) && -2 == $id ) {
			$cData['pages']->other = $action;
			return setcookie( 'cwv3_pages', json_encode( $cData['pages'] ), ( $time['multiplier'] * $time['time'] )+time(), COOKIEPATH, COOKIE_DOMAIN, false );
		}

		$type = get_post_type( $id );
		if ( 'post' == $type ) {
			$catData = get_option( 'cwv3_cat_list' );
			$curCat = get_the_category( $id );
			if ( $this->in_cat( $catData, $curCat ) ) {
				$cData['categories']->$id = $action;
				return setcookie( 'cwv3_cats', json_encode( $cData['categories'] ), ( $time['multiplier'] * $time['time'] )+time(), COOKIEPATH, COOKIE_DOMAIN, false );
			} else if ( 'yes' == get_post_meta( $id, 'cwv3_auth', true ) ) {
					$cData['posts']->$id = $action;
					return setcookie( 'cwv3_posts', json_encode( $cData['posts'] ), ( $time['multiplier'] * $time['time'] )+time(), COOKIEPATH, COOKIE_DOMAIN, false );
				}
		}

		if ( 'yes' == get_post_meta( $id, 'cwv3_auth', true ) ) {
			$cData['pages']->$id = $action;
			return setcookie( 'cwv3_pages', json_encode( $cData['pages'] ), ( $time['multiplier'] * $time['time'] )+time(), COOKIEPATH, COOKIE_DOMAIN, false );
		}

		return 'Failed to set cookie.';
	}

	public function check_data() {
		global $post;

		if ( is_feed() ) {
			//Don't want to hender the feed, just in case.
			return true;
		}

		$cData = array(
			// check isset before access (edit by @jgraup)
			'pages'      => ! isset( $_COOKIE['cwv3_pages'] ) ? '' : json_decode( stripslashes( @$_COOKIE['cwv3_pages'] ), true ),
			'posts'      => ! isset( $_COOKIE['cwv3_posts'] ) ? '' : json_decode( stripslashes( @$_COOKIE['cwv3_posts'] ), true ),
			'categories' => ! isset( $_COOKIE['cwv3_cats'] ) ? '' : json_decode( stripslashes( @$_COOKIE['cwv3_cats'] ), true )
		);

		$sw = get_option( 'cwv3_sitewide' );
		$hm = get_option( 'cwv3_homepage' );
		$mi = get_option( 'cwv3_misc' );

		if ( ! empty( $sw ) ) {
			return ! empty( $cData['pages']['sitewide'] ) ? $cData['pages']['sitewide'] : false;
		}

		if ( is_front_page() && ! empty( $hm ) ) {
			return ! empty( $cData['pages']['home'] ) ? $cData['pages']['home'] : false;
		}

		if ( ( is_archive() || is_search() ) && ! empty( $mi ) ) {
			// Protect misc pages aswell
			return ! empty( $cData['pages']['other'] ) ? $cData['pages']['other'] : false;
		}

		if ( is_page() && 'yes' == get_post_meta( $post->ID, 'cwv3_auth', true ) ) {
			$c = $cData['pages'][ $post->ID ];
			return ! empty( $c ) ? $c : false;
		}

		$id = ( is_attachment() ? $post->post_parent : $post->ID );
		// First see if categories are setup in the admin side.
		$catData = get_option( 'cwv3_cat_list' );
		$curCat = get_the_category( $id );
		if ( 'post' == get_post_type( $id ) && $this->in_cat( $catData, $curCat ) ) {
			// If the current category is selected in the admin page, that means the administrator wishes to protect it.
			// respect the admin's wishes and do it.
			return ! empty( $cData['categories'][ $post->ID ] ) ? $cData['categories'][ $id ] : false;
		}
		// Since that's not the case, we need to check post_meta data and see if this post is protected.
		if ( 'yes' == get_post_meta( $post->ID, 'cwv3_auth', true ) && ! is_front_page() ) {
			return ! empty( $cData['posts'][ $post->ID ] ) ? $cData['posts'][ $id ] : false;
		}

		return true;
	}

	public function in_cat( $catIDs, $catArray ) {
		if ( ! is_array( $catIDs ) ) {
			$catIDs = array(); // Empty
		}

		foreach ( $catArray as $cat ) {
			if ( in_array( $cat->term_id, $catIDs ) ) {
				return true;
			} else {
				continue;
			}
		}
		return false;
	}

	public function render_dialog() {

		$d = get_option( 'cwv3_denial' );
		if ( 3 == $this->check_data() && 'enabled' == $d[0] ) {
			$dtype = true;
		}else {
			$dtype = false;
		}
		$etxt         = get_option( 'cwv3_enter_txt', 'Enter' );
		$extxt        = get_option( 'cwv3_exit_txt', 'Exit' );

		$cwv3_title   = ( true == $dtype ) ? get_option( 'cwv3_den_title' ) : get_option( 'cwv3_d_title' );
		$cwv3_content = ( true == $dtype ) ? get_option( 'cwv3_den_msg' ) : get_option( 'cwv3_d_msg' );

		$exit_url     = get_option( 'cwv3_exit_link', '#' );
		$enter_url    = get_option( 'cwv3_enter_link', '#' );
?>
    	<!-- CWV3 Dialog -->
        <div style="display: none">
            <div id="cwv3_auth">
                <div id="cwv3_title"><?php echo esc_attr( $title ); ?></div>
                <div id="cwv3_content"><?php echo wp_kses_post( $cwv3_content ); ?></div>
                <div id="cwv3_btns">
                	<?php if ( true !== $dtype ): ?>
                		<div id="cwv3_enter"><a href="<?php echo esc_url( $enter_url ); ?>" id="cw_enter_link"><?php echo esc_attr( $etxt ); ?></a></div>
                	<?php endif; ?>
                		<div id="cwv3_exit"><a href="<?php echo esc_url( $exit_url ); ?>" id="cw_exit_link"><?php echo esc_attr( $extxt ); ?></a></div>
                	</div>
            </div>
        </div>
        <!-- END CWV3 Dialog -->
	<?php
	}

	public function render_metabox( $post ) {
		wp_nonce_field( plugin_basename( __FILE__ ), 'cwv3_meta' );
		$curval = get_post_meta( $post->ID, 'cwv3_auth', true );
		$sw = get_option( 'cwv3_sitewide' );
		$disabled = $sw[0] == 'enabled' ? true : false;
?>
        <label for="cwv3_auth">Use authorization for this content:</label>
        <input type="checkbox" id="cwv3_auth" name="cwv3_auth" <?php checked( 'yes', $curval, true ); ?> value="yes" <?php disabled( $disabled ); ?>/><br />
        <?php if ( 'enabled' == $sw[0] ) : ?>
	                <p class="description">Cannot be changed while site wide option is enabled.</p>
        <?php endif; ?>
        <?php
	}
}
new CWV3;

?>