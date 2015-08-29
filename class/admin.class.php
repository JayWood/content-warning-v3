<?php

class CWV3Admin {

	function hooks() {
		// Post Meta Box for this.
		add_action( 'add_meta_boxes', array( $this, 'setup_metabox' ) );
		add_action( 'save_post', array( $this, 'cwv3_meta_save' ) );

		add_action( 'admin_head', array( $this, 'render_lazy_mans_css' ) );

		// Post column filters
		add_filter( 'manage_page_posts_columns', array( $this, 'post_cols' ) );
		add_filter( 'manage_post_posts_columns', array( $this, 'post_cols' ) );

		// Post column info
		add_action( 'manage_posts_custom_column', array( $this, 'set_col_data' ) );
		add_action( 'manage_pages_custom_column', array( $this, 'set_col_data' ) );
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
					echo '<span class="dashicons dashicons-lock"></span>';
				}
				break;
		}
	}

	public function post_cols( $cols ) {

		return array_slice( $cols, 0, 1, true ) +
		       array( 'cwv2' => 'CW' ) +
		       array_slice( $cols, 1, count( $cols ) - 1, true );

	}

	/**
	 * Add metabox to post types
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
	 * @return array
	 */
	public function get_cwv3_post_types() {
		$types = apply_filters( 'cwv3_post_types', array( 'post', 'page' ) );
		$types = empty( $types ) ? array() : $types;
		return ! is_array( $types ) ? array( $types ) : $types;
	}

	public function cwv3_meta_save( $post_id ) {
		$post_types = $this->get_cwv3_post_types();
		// check isset before access (edit by @jgraup)
		if ( isset( $_POST['post_type'] ) && in_array( $_POST['post_type'], $post_types ) ) {
			if ( ! current_user_can( 'edit_page', $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			} else {
				if ( ! isset( $_POST['cwv3_meta'] ) || ! wp_verify_nonce( $_POST['cwv3_meta'], plugin_basename( __FILE__ ) ) ) { return; }
			}
		}

		// check isset before access (edit by @jgraup)
		if ( isset( $_POST['cwv3_auth'] ) ) {
			$cwv3_checkbox = sanitize_text_field( $_POST['cwv3_auth'] );
			update_post_meta( $post_id, 'cwv3_auth', $cwv3_checkbox );
		}
	}

	public function render_metabox( $post ) {
		wp_nonce_field( plugin_basename( __FILE__ ), 'cwv3_meta' );
		$curval   = get_post_meta( $post->ID, 'cwv3_auth', true );
		$sw       = get_option( 'cwv3_sitewide' );
		$disabled = 'enabled' == $sw[0] ? true : false;
		?>

        <label for="cwv3_auth"><?php _e( 'Use authorization for this content', 'cwv3' ); ?>:</label>
        <input type="checkbox" id="cwv3_auth" name="cwv3_auth" <?php checked( 'yes', $curval, true ); ?> value="yes" <?php disabled( $disabled ); ?>/><br />
        <?php if ( 'enabled' == $sw[0] ) : ?>
	                <p class="description"><?php _e( 'Cannot be changed while site wide option is enabled.', 'cwv3' ); ?></p>
        <?php endif; ?>
        
        <?php
	}
}

$cwv3_admin = new CWV3Admin();
$cwv3_admin->hooks();
