<?php

class CWV3 {

	/**
	 * Get Cookie Name
	 *
	 * If the cookie is to be shown, this function will return the ID, and the javascript
	 * will handle the rest of it. If this function returns false, the javascript will not show a popup.
	 * @TODO: Lookinto these weird statements 'x' == ! empty( $y )... wtf?
	 * @since 3.6.3
	 * @return string|int String if special page like homepage, or post_id otherwise.
	 */
	public function get_cookie_name() {
		global $post;

		$site_wide = get_option( 'cwv3_sitewide' );
		$homepage  = get_option( 'cwv3_homepage' );
		$misc      = get_option( 'cwv3_misc' );

		$should_gate = apply_filters( 'cwv3_should_gate', true, $post );
		if ( false === $should_gate ) {
			return false;
		}

		if ( 'enabled' == ! empty( $site_wide ) ) {
			return 'sitewide';
		}

		if ( 'enabled' == ! empty( $homepage ) && is_front_page() ) {
			return 'homepage';
		}

		if ( 'enabled' == ! empty( $misc ) && ( is_search() || is_archive() ) ) {
			return 'misc';
		}

		if ( is_attachment() && isset( $post->post_parent ) ) {
			// Special consideration needs to be taken to check if the post parent is in-fact
			// gated in any way.
			$cat_gated = $this->is_cat_gated( $post->post_parent );
			if ( $cat_gated ) {
				// Return the category cookie name like _cat_###
				return '_cat_' . $cat_gated;
			} else if ( $this->is_gated( $post->post_parent ) ) {
				return $post->post_parent;
			}
		}

		if ( is_singular() && isset( $post->ID ) ) {
			$cat_gated = $this->is_cat_gated( $post->ID );
			if ( $cat_gated ) {
				// Return the category cookie name like _cat_###
				return '_cat_' . $cat_gated;
			} else if ( $this->is_gated( $post->ID ) ) {
				return $post->ID;
			}
		}

		return false;
	}

	/**
	 * Is Gated
	 *
	 * Checks a post ID to see if it's supposed to be
	 * gated in any way, either by metabox, or category from the
	 * regular category taxonomy.
	 *
	 * @since 3.6.3
	 * @param  int $post_id Post ID
	 *
	 * @return bool          TRUE | FALSE
	 */
	public function is_gated( $post_id ) {

		$meta = get_post_meta( $post_id, 'cwv3_auth', true );

		if ( ! empty( $meta ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Is Cat Gated
	 *
	 * Determines if a post is within a gated category, if so, will
	 * return the category id for use in cookie names like so '_cat_###'
	 *
	 * @since 3.6.3
	 * @param  int $post_id Post ID
	 *
	 * @return boolean|string   False on failure, cookie string otherwise
	 */
	public function is_cat_gated( $post_id ) {
		$cat_settings = get_option( 'cwv3_cat_list', array() );
		if ( ! empty( $cat_settings ) ) {
			$post_categories = get_the_category( $post_id );

			return $this->in_cat( $cat_settings, $post_categories );
		}

		return false;
	}

	/**
	 * In Cat
	 *
	 * Checks to see if the current post is within the set
	 * categories in the options panel, if so, returns the ID of the
	 * category that it resides in.
	 *
	 * @since 3.6.3
	 * @param array $cat_settings Array of categories from settings page
	 * @param array $post_categories Array of categories from get_the_category()
	 *
	 * @return boolean|int                            False on failure, category ID on success
	 */
	public function in_cat( $cat_settings, $post_categories ) {
		if ( ! is_array( $cat_settings ) ) {
			$cat_settings = array(); // Empty
		}

		foreach ( $post_categories as $post_category ) {
			if ( in_array( $post_category->term_id, $cat_settings ) ) {
				return $post_category->term_id;
			} else {
				continue;
			}
		}

		return false;
	}

	/**
	 * Hooks
	 *
	 * All WordPress hooks that are needed to make this plugin functional.
	 *
	 * @since 3.6.3
	 * @return null
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'register_frontend_data' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_dependancies' ) );

		add_action( 'wp_footer', array( $this, 'render_dialog' ) );
		add_action( 'wp_head', array( $this, 'override_css' ) );
	}

	/**
	 * Load Dependancies
	 *
	 * @since 3.6.3
	 * Pretty self-explanatory, loads all the data that needs to be loaded beforehand.
	 * @return null
	 */
	public function load_dependancies() {

		if ( current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_enqueue_style( 'cwv3_css' );
		wp_enqueue_script( 'cwv3_js' );

		$cookie_death   = get_option( 'cwv3_death', 1 );
		$localized_data = array(
			'opacity'        => get_option( 'cwv3_bg_opacity', 0.85 ),
			'cookie_path'    => SITECOOKIEPATH,
			'cookie_name'    => $this->get_cookie_name(),
			'cookie_time'    => intval( $cookie_death ) > 365 ? 365 : intval( $cookie_death ),
			// Max at one year if it's over 365 days.
			'denial_enabled' => get_option( 'cwv3_denial', 'enabled' ),
			'denial_method'  => get_option( 'cwv3_method', 'redirect' ),
			'redirect_url'   => esc_js( get_option( 'cwv3_exit_link', '#' ) ),
		);

		wp_localize_script( 'cwv3_js', 'cwv3_params', $localized_data );
	}

	/**
	 * Override CSS
	 * Placeholder method that uses the new API in inc/api.php
	 *
	 * @since 3.6.3
	 * @see cwv3_the_css()
	 */
	public function override_css() {
		cwv3_the_css();
	}

	/**
	 * Register Frontend Data
	 *
	 * @since 3.6.3
	 * @return null
	 */
	public function register_frontend_data() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Jquery Cookie
		wp_register_script( 'jquery_cookie', plugins_url( "js/jquery_cookie{$min}.js", dirname( __FILE__ ) ), array( 'jquery' ), '1.4.1', true );

		// Main data
		wp_register_script( 'cwv3_js', plugins_url( "js/cwv3{$min}.js", dirname( __FILE__ ) ), array( 'jquery_cookie' ), '3.6.0', true );
		wp_register_style( 'cwv3_css', plugins_url( "css/cwv3{$min}.css", dirname( __FILE__ ) ), '', '1.0' );
	}

	/**
	 * Render Dialog
	 *
	 * Redirect method to use the API.php that was created
	 *
	 * @since 3.6.3
	 * @see cwv3_js_dialog()
	 */
	public function render_dialog() {
		cwv3_js_dialog();
	}
}

$cwv3_frontend = new CWV3();
$cwv3_frontend->hooks();
