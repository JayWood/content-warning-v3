<?php

class CWV3 {

	public function hooks(){
		add_action( 'init', array( $this, 'register_frontend_data' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_dependancies' ) );

		add_action( 'wp_footer', array( $this, 'render_dialog' ) );
		add_action( 'wp_head', array( $this, 'override_css' ) );
	}

	public function override_css() {
		cwv3_the_css();
	}

	public function load_dependancies() {
		global $post;

		if ( current_user_can( 'manage_options' ) ) { return; }

		wp_enqueue_style( 'cwv3_css' );
		wp_enqueue_script( 'cwv3_js' );

		wp_localize_script( 'cwv3_js', 'cwv3_params', array(
			'opacity'       => get_option( 'cwv3_bg_opacity', 0.85 ),
			'cookie_path'   => SITECOOKIEPATH,
			'cookie_name'   => $this->get_cookie_name(),
			'cookie_time'   => $cookie_death,
			'denial'        => get_option( 'cwv3_denial', 'enabled' ),
			'denial_method' => get_option( 'cwv3_method_show', 'redirect' ),

		) );
	}

	public function register_frontend_data() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Colorbox w/ MIT License
		wp_register_style( 'colorbox', plugins_url( 'js/colorbox.1.5.10/colorbox.css', dirname( __FILE__ ) ), '', '1.5.10', 'ALL' );
		wp_register_script( 'colorbox_js', plugins_url( "js/colorbox.1.5.10/jquery.colorbox{$min}.js", dirname( __FILE__ ) ), array( 'jquery' ), '1.5.10', true );

		// Jquery Cookie
		wp_register_script( 'jquery_cookie', plugins_url( "js/jquery_cookie{$min}.js", dirname( __FILE__ ) ), array( 'jquery' ), '1.4.1', true );

		// Main data
		wp_register_script( 'cwv3_js', plugins_url( "js/cwv3{$min}.js", dirname( __FILE__ ) ), array( 'colorbox_js', 'jquery_cookie' ), '3.6.0', true );
		wp_register_style( 'cwv3_css', plugins_url( "css/cwv3{$min}.css", dirname( __FILE__ ) ), array( 'colorbox' ), '1.0' );
	}

	public function get_cookie_name(){
		global $post;

		$sitewide    = get_option( 'cwv3_sitewide' );
		$homepage    = get_option( 'cwv3_homepage' );
		$misc        = get_option( 'cwv3_misc' );

		if ( 'enabled' == ! empty( $sitewide ) ){
			return 'sitewide';
		}

		if ( 'enabled' == ! empty( $homepage ) && is_front_page() ){
			return 'homepage';
		}

		if ( 'enabled' == ! empty( $misc ) && ( is_search() || is_archive() ) ){
			return 'misc';
		}

		// Don't need people looking at attachments that belong to a
		// protected post.
		if ( is_attachment() && isset( $post->post_parent ) ) {
			// Special consideration needs to be taken to check if the post parent is in-fact
			// gated in any way, if so, return its ID here.
			$is_gated = $this->check_post( $post->post_parent );
			if ( true === $is_gated ){
				return $post->post_parent;
			}
		}

		/*$type = get_post_type( $id );
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
		}*/

		return false;
	}

	/**
	 * Check Post
	 * Checks a post ID to see if it's supposed to be
	 * gated in any way, either by metabox, or category from the
	 * regular category taxonomy.
	 * 	
	 * @param  int 	$post_id Post ID
	 * @return bool          TRUE | FALSE
	 */
	public function check_post( $post_id ){

		$meta = get_post_meta( $post_id, $key, true );

		return false;
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
		cwv3_js_dialog();
	}
}

$cwv3_frontend = new CWV3();
$cwv3_frontend->hooks();