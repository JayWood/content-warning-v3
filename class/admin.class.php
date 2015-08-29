<?php

class CWV3Admin {

	/**
	 * Hooks for the administrator panel
	 * @since 3.6.3
	 */
	function hooks() {
		// Post Meta Box for this.
		add_action( 'add_meta_boxes', array( $this, 'setup_metabox' ) );
		add_action( 'save_post', array( $this, 'cwv3_meta_save' ) );

		add_action( 'admin_head', array( $this, 'render_lazy_mans_css' ) );

		$post_types = $this->get_cwv3_post_types();
		if ( ! empty( $post_types ) && is_array( $post_types ) ) {
			foreach ( $post_types as $post_type ) {
				add_filter( "manage_{$post_type}_posts_columns", array( $this, 'post_cols' ) );
			}
		}

		// Post column info
		add_action( 'manage_posts_custom_column', array( $this, 'set_col_data' ) );
		add_action( 'manage_pages_custom_column', array( $this, 'set_col_data' ) );
	}

	/**
	 * Centers custom column content
	 * @since 3.6.3
	 * @return null
	 */
	public function render_lazy_mans_css() {
		echo '<style type="text/css">th#cwv2{width: 32px; text-align:center;} td.column-cwv2{text-align:center;}</style>';
	}

	/**
	 * Sets column data for the CWv3 column
	 *
	 * @since 3.6.3
	 * @param $col
	 */
	public function set_col_data( $col ) {
		global $post;

		$sw = get_option( 'cwv3_sitewide' );
		$is_enabled = ( 'yes' == get_post_meta( $post->ID, 'cwv3_auth', true ) ) ? true : false;;
		switch ( $col ) {
			case 'cwv2':
				if ( $is_enabled || ( isset( $sw[0] ) && 'enabled' == $sw[0] ) ) {
					echo '<span class="dashicons dashicons-lock"></span>';
				}
				break;
		}
	}

	/**
	 * Adds columns to the post list table
	 *
	 * @since 3.6.3
	 * @param $cols
	 *
	 * @return array
	 */
	public function post_cols( $cols ) {
		return array_slice( $cols, 0, 1, true ) +
		       array( 'cwv2' => 'CW' ) +
		       array_slice( $cols, 1, count( $cols ) - 1, true );
	}

	/**
	 * Add metabox to post types
	 *
	 * @since 3.6.3
	 * @return void
	 */
	public function setup_metabox() {
		$post_type = $this->get_cwv3_post_types();
		if ( is_array( $post_type ) ) {
			foreach ( $post_type as $screen ) {
				add_meta_box( 'cwv3_meta_section',
					__( 'CWV3 Security', 'cwv3' ),
					array( $this, 'render_metabox' ),
					$screen,
					'side',
					'high'
				);
			}
		}
	}

	/**
	 * Gets the post types that can be used with CWv2
	 * @since 3.6.4
	 * @return array
	 */
	public function get_cwv3_post_types() {
		$types = apply_filters( 'cwv3_post_types', array( 'post', 'page' ) );
		$types = empty( $types ) ? array() : $types;
		return ! is_array( $types ) ? array( $types ) : $types;
	}

	/**
	 * Saves meta data
	 *
	 * @since 3.6.3
	 * @param int $post_id
	 * @return null
	 */
	public function cwv3_meta_save( $post_id ) {
		$post_types = $this->get_cwv3_post_types();
		// check isset before access (edit by @jgraup)
		if ( isset( $_POST['post_type'] ) && in_array( $_POST['post_type'], $post_types ) ) {
			if ( ! isset( $_POST['cwv3_meta'] ) || ! wp_verify_nonce( $_POST['cwv3_meta'], plugin_basename( __FILE__ ) ) ) { return; }
			if ( ! current_user_can( 'edit_page', $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {	return;	}

			// check isset before access (edit by @jgraup)
			if ( isset( $_POST['cwv3_auth'] ) ) {
				$cwv3_checkbox = sanitize_text_field( $_POST['cwv3_auth'] );
				update_post_meta( $post_id, 'cwv3_auth', $cwv3_checkbox );
			}
		}

	}

	/**
	 * Render the meta box for CWv3
	 * @since 3.6.3
	 * @param $post
	 */
	public function render_metabox( $post ) {
		wp_nonce_field( plugin_basename( __FILE__ ), 'cwv3_meta' );
		$meta_value = get_post_meta( $post->ID, 'cwv3_auth', true );
		$site_wide  = get_option( 'cwv3_sitewide' );
		$disabled   = isset( $site_wide[0] ) && 'enabled' == $site_wide[0] ? true : false;
		?>

        <label for="cwv3_auth"><input type="checkbox" id="cwv3_auth" name="cwv3_auth" <?php checked( 'yes', $meta_value, true ); ?> value="yes" <?php disabled( $disabled ); ?>/><?php _e( 'Use authorization for this content', 'cwv3' ); ?></label>
        <?php if ( $disabled ) : ?>
			<p class="description"><?php _e( 'Cannot be changed while site wide option is enabled.', 'cwv3' ); ?></p>
        <?php endif;
	}
}

$cwv3_admin = new CWV3Admin();
$cwv3_admin->hooks();
