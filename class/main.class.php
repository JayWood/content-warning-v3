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

		$cookie_death = get_option( 'cwv3_death', 1 );

		wp_localize_script( 'cwv3_js', 'cwv3_params', array(
			'opacity'        => get_option( 'cwv3_bg_opacity', 0.85 ),
			'cookie_path'    => SITECOOKIEPATH,
			'cookie_name'    => $this->get_cookie_name(),
			'cookie_time'    => intval( $cookie_death ) > 365 ? 365 : intval( $cookie_death ), // Max at one year if it's over 365 days.
			'denial_enabled' => get_option( 'cwv3_denial', 'enabled' ),
			'denial_method'  => get_option( 'cwv3_method', 'redirect' ),
			'redirect_url'   => esc_js( get_option( 'cwv3_exit_link', '#' ) ),

		) );
	}

	public function register_frontend_data() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Jquery Cookie
		wp_register_script( 'jquery_cookie', plugins_url( "js/jquery_cookie{$min}.js", dirname( __FILE__ ) ), array( 'jquery' ), '1.4.1', true );

		// Main data
		wp_register_script( 'cwv3_js', plugins_url( "js/cwv3{$min}.js", dirname( __FILE__ ) ), array( 'jquery_cookie' ), '3.6.0', true );
		wp_register_style( 'cwv3_css', plugins_url( "css/cwv3{$min}.css", dirname( __FILE__ ) ), '', '1.0' );
	}

	/**
	 * Get Cookie Name
	 *
	 * If the cookie is to be shown, this function will return the ID, and the javascript
	 * will handle the rest of it.
	 *
	 * If this function returns false, the javascript will not show a popup.
	 * 
	 * @return string|int String if special page like homepage, or post_id otherwise.
	 */
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
			if ( $this->is_gated( $post->post_parent ) ){
				return $post->post_parent;
			}
		}

		if ( is_singular() && isset( $post->ID ) ){
			if ( $this->is_gated( $post->ID ) ){
				return $post->ID;
			}
		}

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
	public function is_gated( $post_id ){

		$meta = get_post_meta( $post_id, 'cwv3_auth', true );

		if ( ! empty( $meta ) ) {
			return true;
		} else {
			// The post itself was not gated, check categories.
			$category_array = get_option( 'cwv3_cat_list', array() );
			if ( ! empty( $category_array ) ){
				// We have categories to check, so let us do so.
				$current_categories = get_the_category( $post_id );
				if ( $this->in_cat( $category_array, $current_categories ) ){
					return true; // Just return true if it's in the category.
				}
			}
		}

		return false;
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