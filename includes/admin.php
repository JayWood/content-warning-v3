<?php

class CWV2_Admin {

	/**
	 * @var ContentWarning_v2
	 */
	public $plugin;

	/**
	 * @var String
	 */
	public $options_page;

	/**
	 * @var String
	 */
	public $option_prefix = 'cwv3_';

	/**
	 * CWV2_Admin constructor.
	 *
	 * @param ContentWarning_v2 $plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		$this->hooks();
	}


	/**
	 * Hooks for the administrator panel
	 *
	 * @author JayWood
	 *
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

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_script' ) );
	}

	/**
	 * Enqueues up the adminisrator scripts/styles
	 *
	 * @param string $hook
	 *
	 * @author JayWood
	 */
	public function enqueue_admin_script( $hook ) {
		if ( $this->options_page !== $hook ) {
			return;
		}

		wp_enqueue_script( 'cwv2-admin', $this->plugin->url( "js/admin{$this->plugin->min}.js" ), array( 'jquery' ), $this->plugin->version, true );
	}

	/**
	 * Adds the options menu.
	 *
	 * @author JayWood
	 * @since 3.7
	 */
	public function admin_menu() {
		$this->options_page = add_options_page( __( 'Content Warning Options', 'content-warning-v2' ), __( 'CWV2 Options', 'content-warning-v2' ), 'manage_options', 'cwv2-options', array( $this, 'render_settings_page' ) );

		// Setup sections etc...
		foreach ( $this->get_settings_config() as $section ) {
			add_settings_section( $section['id'], $section['name'], array( $this->plugin->settings, $section['group'] ), $this->options_page );
			if ( isset( $section['fields'] ) ) {
				foreach ( $section['fields'] as $option_data ) {
					add_settings_field(
						$this->option_prefix . $option_data['id'],
						$option_data['name'],
						array(
							$this->plugin->settings,
							$option_data['type']
						),
						$this->options_page,
						$section['id'],
						array(
							'id'      => $this->option_prefix . $option_data['id'],
							'name'    => $this->option_prefix . $option_data['id'],
							'desc'    => $option_data['desc'],
							'options' => isset( $option_data['options'] ) ? $option_data['options'] : false,
						)
					);
				}
			}
		}
	}

	/**
	 * Renders the settings page.
	 *
	 * @author JayWood
	 */
	public function render_settings_page() {
		?><div class="wrap">
		<h2><?php _e( 'Content Warning v2 Settings', 'minecraft-suite' ); ?></h2>
		<form method="post" action="options.php">
			<?php
			settings_fields( $this->option_prefix . 'options_group' );
			do_settings_sections( $this->options_page );
			submit_button();
			?>
		</form>
		</div><?php
	}

	/**
	 * Centers custom column content
	 *
	 * @author JayWood
	 *
	 * @since 3.6.3
	 * @return null
	 */
	public function render_lazy_mans_css() {
		echo '<style type="text/css">th#cwv2{width: 32px; text-align:center;} td.column-cwv2{text-align:center;}</style>';
	}

	/**
	 * Sets column data for the CWv3 column
	 *
	 * @author JayWood
	 *
	 * @since 3.6.3
	 *
	 * @param string $col
	 */
	public function set_col_data( $col ) {
		global $post;

		$sw         = get_option( 'cwv3_sitewide' );
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
	 * @author JayWood
	 *
	 * @since 3.6.3
	 *
	 * @param array $cols
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
	 * @author JayWood
	 *
	 * @since 3.6.3
	 * @return void
	 */
	public function setup_metabox() {
		$post_type = $this->get_cwv3_post_types();
		if ( is_array( $post_type ) ) {
			foreach ( $post_type as $screen ) {
				add_meta_box( 'cwv3_meta_section',
					__( 'CWV3 Security', 'content-warning-v2' ),
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
	 *
	 * @author JayWood
	 *
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
	 * @author JayWood
	 *
	 * @since 3.6.3
	 *
	 * @param int $post_id
	 *
	 * @return null
	 */
	public function cwv3_meta_save( $post_id ) {
		$post_types = $this->get_cwv3_post_types();
		// check isset before access (edit by @jgraup)
		if ( isset( $_POST['post_type'] ) && in_array( $_POST['post_type'], $post_types ) ) {
			if ( ! isset( $_POST['cwv3_meta'] ) || ! wp_verify_nonce( $_POST['cwv3_meta'], plugin_basename( __FILE__ ) ) ) {
				return;
			}
			if ( ! current_user_can( 'edit_page', $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// check isset before access (edit by @jgraup)
			if ( isset( $_POST['cwv3_auth'] ) ) {
				$cwv3_checkbox = sanitize_text_field( $_POST['cwv3_auth'] );
				update_post_meta( $post_id, 'cwv3_auth', $cwv3_checkbox );
			} else {
				delete_post_meta( $post_id, 'cwv3_auth' );
			}
		}

	}

	/**
	 * Render the meta box for CWv3
	 *
	 * @author JayWood
	 *
	 * @since 3.6.3
	 *
	 * @param WP_Post $post
	 */
	public function render_metabox( $post ) {
		wp_nonce_field( plugin_basename( __FILE__ ), 'cwv3_meta' );
		$meta_value = get_post_meta( $post->ID, 'cwv3_auth', true );
		$site_wide  = get_option( 'cwv3_sitewide' );
		$disabled   = isset( $site_wide[0] ) && 'enabled' == $site_wide[0] ? true : false;
		?>

		<label for="cwv3_auth"><input type="checkbox" id="cwv3_auth" name="cwv3_auth" <?php checked( 'yes', $meta_value, true ); ?> value="yes" <?php disabled( $disabled ); ?>/><?php _e( 'Use authorization for this content', 'content-warning-v2' ); ?>
		</label>
		<?php if ( $disabled ) : ?>
			<p class="description"><?php _e( 'Cannot be changed while site wide option is enabled.', 'content-warning-v2' ); ?></p>
		<?php endif;
	}

	/**
	 * Gets the settings config
	 *
	 * @author JayWood
	 * @return array
	 */
	public function get_settings_config() {
		return array(
			array(
				'id'     => 'general-settings',
				'name'   => __( 'General Settings', 'content-warning-v2' ),
				'group'  => 'def_group',
				'fields' => array(
					array(
						'id'      => 'sitewide',
						'name'    => __( 'Sitewide', 'content-warning-v2' ),
						'desc'    => __( 'Takes priority over category, page, and post, home, and misc. pages/posts.', 'content-warning-v2' ),
						'type'    => 'check',
						'options' =>
							array(
								'enabled' => 'Enable',
							),
					),
					array(
						'id'      => 'homepage',
						'name'    => __( 'Home Page', 'content-warning-v2' ),
						'desc'    => __( 'Toggle the home page dialog, useful if you have not set a static page for your front-page in Settings -> Reading.', 'content-warning-v2' ),
						'type'    => 'check',
						'options' =>
							array(
								'enabled' => 'Enable',
							),
					),
					array(
						'id'      => 'misc',
						'name'    => __( 'Misc. Pages', 'content-warning-v2' ),
						'desc'    => __( 'Enable this to protect search, archive, and other such pages.', 'content-warning-v2' ),
						'type'    => 'check',
						'options' =>
							array(
								'enabled' => 'Enable',
							),
					),
					array(
						'id'   => 'death',
						'name' => __( 'Cookie Life', 'content-warning-v2' ),
						'desc' => __( 'Time in days for the cookie to expire', 'content-warning-v2' ),
						'type' => 'number',
					),
				),
			),
			array(
				'id'     => 'entry-settings',
				'name'   => __( 'Dialog Settings', 'content-warning-v2' ),
				'group'  => 'def_group',
				'fields' => array(
					array(
						'id'   => 'd_title',
						'name' => __( 'Dialog Title', 'content-warning-v2' ),
						'desc' => __( '', 'content-warning-v2' ),
						'type' => 'text',
					),
					array(
						'id'   => 'd_msg',
						'name' => __( 'Dialog Message', 'content-warning-v2' ),
						'desc' => __( 'A message shown to your visitor.', 'content-warning-v2' ),
						'type' => 'editor',
					),
					array(
						'id'   => 'exit_txt',
						'name' => __( 'Exit Text', 'content-warning-v2' ),
						'desc' => __( 'The text for the exit button.', 'content-warning-v2' ),
						'type' => 'text',
					),
					array(
						'id'   => 'exit_link',
						'name' => __( 'Exit Link', 'content-warning-v2' ),
						'desc' => __( 'The full URL a user should be directed to upon clicking the exit button.', 'content-warning-v2' ),
						'type' => 'text',
					),
					array(
						'id'   => 'enter_txt',
						'name' => __( 'Enter Text', 'content-warning-v2' ),
						'desc' => __( 'The text for the enter button.', 'content-warning-v2' ),
						'type' => 'text',
					),
					array(
						'id'   => 'enter_link',
						'name' => __( 'Enter Link', 'content-warning-v2' ),
						'desc' => __( 'The full URL a user should be directed to upon clicking the enter button.  Leave blank to just close the dialog.', 'content-warning-v2' ),
						'type' => 'text',
					),
					array(
						'id'      => 'denial',
						'name'    => __( 'Toggle Denial Option', 'content-warning-v2' ),
						'desc'    => __( '', 'content-warning-v2' ),
						'type'    => 'check',
						'options' =>
							array(
								'enabled' => 'Enable denial handling.',
							),
					),
					array(
						'id'      => 'method',
						'name'    => __( 'Denial Handling Method', 'content-warning-v2' ),
						'desc'    => __( '', 'content-warning-v2' ),
						'type'    => 'radio',
						'options' =>
							array(
								'redirect' => __( 'Redirect the user.', 'content-warning-v2' ),
								'show'     => __( 'Show the denial dialog.', 'content-warning-v2' ),
							),
					),
					array(
						'id'   => 'den_title',
						'name' => __( 'Dialog Title', 'content-warning-v2' ),
						'desc' => __( '', 'content-warning-v2' ),
						'type' => 'text',
					),
					array(
						'id'   => 'den_msg',
						'name' => __( 'Denial Message', 'content-warning-v2' ),
						'desc' => __( '', 'content-warning-v2' ),
						'type' => 'editor',
					),
					array(
						'id'   => 'bg_image',
						'name' => __( 'Background Image', 'content-warning-v2' ),
						'desc' => __( 'If not empty, the dialog will use this instead of the background opacity and color.', 'content-warning-v2' ),
						'type' => 'media',
					),
					array(
						'id'   => 'bg_opacity',
						'name' => __( 'Background Opacity', 'content-warning-v2' ),
						'desc' => __( 'Input a float value from 0-1, the latter being completely opaque.', 'content-warning-v2' ),
						'type' => 'number',
					),
					array(
						'id'      => 'bg_color',
						'name'    => __( 'Background Color', 'content-warning-v2' ),
						'desc'    => __( 'The Overlay color.', 'content-warning-v2' ),
						'type'    => 'color',
						'options' =>
							array(
								'color' => '#000000',
							),
					),
					array(
						'id'   => 'css',
						'name' => __( 'Custom CSS', 'content-warning-v2' ),
						'desc' => __( 'For a completely custom look, just drop your css here.', 'content-warning-v2' ),
						'type' => 'textbox',
					),
					array(
						'id'      => 'cat_list',
						'name'    => __( 'Category restrictions', 'content-warning-v2' ),
						'desc'    => __( 'Select categories that you would like to restrict with the dialog.', 'content-warning-v2' ),
						'type'    => 'check',
						'options' =>
							array(),
					),
				),
			),
		);
	}
}
