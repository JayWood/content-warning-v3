<?php

class CWV3 {

	function CWV3() {
		$this->__construct();
	}

	public function __construct() {
		// Styling and such
		add_action( 'init', array( &$this, 'register_frontend_data' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_dependancies' ) );

		add_action( 'wp_footer', array( $this, 'renderDialog' ) );

		// Post Meta Box for this.
		add_action( 'add_meta_boxes', array( $this, 'cw_meta' ) );
		add_action( 'save_post', array( $this, 'cwv3_meta_save' ) );

		// AJAX Handle
		add_action( 'wp_ajax_cwv3_ajax', array( $this, 'handle_ajax' ) );
		add_action( 'wp_ajax_nopriv_cwv3_ajax', array( $this, 'handle_ajax' ) );

		// Post column filters
		add_filter( 'manage_page_posts_columns', array( $this, 'post_cols' ) );
		add_filter( 'manage_post_posts_columns', array( $this, 'post_cols' ) );


		//add_action('quick_edit_custom_box', array(&$this, 'display_qe'), 10, 2);

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
		if ( !empty( $img ) ) {
?>
				#cboxOverlay{background:url(<?php echo $img; ?>) no-repeat top center; background-color:<?php echo $color['color']; ?>;}
			<?php
		}else {
?>
				#cboxOverlay{background-image:url(<?php echo $img; ?>) no-repeat top center; background-color:<?php echo $color['color']; ?>;}
			<?php
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
			if ( get_post_meta( $post->ID, 'cwv3_auth', true ) == 'yes' || $sw[0] == 'enabled' ) {
				echo '<span style="color:#0F0; font-weight:bold;" class="cw_protected">Yes</span>';
			}else {
				echo '<span style="color:#F00; font-weight:bold;" class="cw_vulnerable">No</span>';
			}
			break;
		}
	}

	public function post_cols( $cols ) {

		return array_slice( $cols, 0, 1, true )+array( 'cwv2'=> 'CW' )+array_slice( $cols, 1, count( $array )-1, true );

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
		if ( 'page' == $_POST['post_type'] )
			if ( !current_user_can( 'edit_page', $post_id ) )
				return;
			else
				if ( !current_user_can( 'edit_post', $post_id ) )
					return;

				if ( !isset( $_POST['cwv3_meta'] ) || ! wp_verify_nonce( $_POST['cwv3_meta'], plugin_basename( __FILE__ ) ) )
					return;

				$mydata = sanitize_text_field( $_POST['cwv3_auth'] );
			update_post_meta( $post_id, 'cwv3_auth', $mydata );

	}

	public function handle_ajax() {
		$post_id = intval( $_POST['id'] );

		check_ajax_referer( 'cwv3_ajax_'.$post_id, 'nonce' );

		if ( $_POST['method'] == 'exit' ) {
			$d = get_option( 'cwv3_denial' );
			if ( $d[0] == 'enabled' ) {
				$resp = $this->set_cookie( $post_id, 3 );
			}
			$resp = "denied";
		}else {
			$resp = $this->set_cookie( $post_id, 1 );
		}
		echo $resp;
		die;
	}

	public function load_dependancies() {
		global $post;

		if ( current_user_can( 'manage_options' ) ) return;

		wp_enqueue_style( 'cwv3_css' );
		wp_enqueue_script( 'cwv3_js' );

		$elink = get_option( 'cwv3_enter_link' );
		$exlink = get_option( 'cwv3_exit_link' );
		$p_ID = ( is_home() ) ? -1 : ( is_attachment() ? $post->post_parent : ( is_archive() || is_search() ) ? -2 : $post->ID );
		$d = get_option( 'cwv3_denial' );
		wp_localize_script( 'cwv3_js', 'cwv3_params', array(
				'action'    => 'cwv3_ajax',
				'nonce'     => wp_create_nonce( 'cwv3_ajax_'.$p_ID ),
				'admin_url' => admin_url( 'admin-ajax.php' ),
				'id'        => $p_ID,
				'sd'        => ( $this->check_data() == false || ( $this->check_data() == 3 && $d[0] == 'enabled' ) ) ? true : false,
				'enter'     => !empty( $elink ) ? $elink : '#',
				'exit'      => !empty( $exlink ) ? $exlink : 'http://google.com',
				'opacity'   => get_option( 'cwv3_bg_opacity', 0.85 )
			) );
	}

	public function register_frontend_data() {
		// Colorbox w/ MIT License
		wp_register_style( 'colorbox', plugins_url( 'js/colorbox.1.4.14/colorbox.css', dirname( __FILE__ ) ), '', '1.4.14', 'ALL' );
		wp_register_script( 'colorbox_js', plugins_url( 'js/colorbox.1.4.14/jquery.colorbox-min.js', dirname( __FILE__ ) ), array( 'jquery' ), '1.4.14', true );

		// Main data
		wp_register_script( 'cwv3_js', plugins_url( 'js/cwv3.js', dirname( __FILE__ ) ), array( 'colorbox_js' ), uniqid(), true );
		wp_register_style( 'cwv3_css', plugins_url( 'css/cwv3.css', dirname( __FILE__ ) ), array( 'colorbox' ), '1.0' );
	}

	public function set_cookie( $id, $action ) {

		$time = get_option( 'cwv3_death' );

		$sw = get_option( 'cwv3_sitewide' );
		$hm = get_option( 'cwv3_homepage' );
		$mi = get_option( 'cwv3_misc' );
		if ( get_magic_quotes_gpc() == true ) {
			$cData = array(
				'pages'      => json_decode( stripslashes( $_COOKIE['cwv3_pages'] ) ),
				'posts'      => json_decode( stripslashes( $_COOKIE['cwv3_posts'] ) ),
				'categories' => json_decode( stripslashes( $_COOKIE['cwv3_cats'] ) )
			);
		}else {
			$cData = array(
				'pages'      => json_decode( $_COOKIE['cwv3_pages'] ),
				'posts'      => json_decode( $_COOKIE['cwv3_posts'] ),
				'categories' => json_decode( $_COOKIE['cwv3_cats'] )
			);
		}
		if ( $sw[0] == 'enabled' ) {
			$cData['pages']->sitewide = $action;
			return setcookie( 'cwv3_pages', json_encode( $cData['pages'] ), ( $time['multiplier'] * $time['time'] )+time(), COOKIEPATH, COOKIE_DOMAIN, false );
		}

		if ( $hm[0] == 'enabled' && $id == -1 ) {
			$cData['pages']->home = $action;
			return setcookie( 'cwv3_pages', json_encode( $cData['pages'] ), ( $time['multiplier'] * $time['time'] )+time(), COOKIEPATH, COOKIE_DOMAIN, false );
		}

		if ( $mi[0] == 'enabled' && $id == -2 ) {
			$cData['pages']->other = $action;
			//return print_r($cData, true);
			return setcookie( 'cwv3_pages', json_encode( $cData['pages'] ), ( $time['multiplier'] * $time['time'] )+time(), COOKIEPATH, COOKIE_DOMAIN, false );
		}

		$type = get_post_type( $id );
		if ( $type == 'post' ) {
			$catData = get_option( "cwv3_cat_list" );
			$curCat = get_the_category( $id );
			if ( $this->inCat( $catData, $curCat ) ) {
				$cData['categories']->$id = $action;
				return setcookie( 'cwv3_cats', json_encode( $cData['categories'] ), ( $time['multiplier'] * $time['time'] )+time(), COOKIEPATH, COOKIE_DOMAIN, false );
			}else if ( get_post_meta( $id, 'cwv3_auth', true ) == 'yes' ) {
					$cData['posts']->$id = $action;
					return setcookie( 'cwv3_posts', json_encode( $cData['posts'] ), ( $time['multiplier'] * $time['time'] )+time(), COOKIEPATH, COOKIE_DOMAIN, false );
				}
		}

		if ( get_post_meta( $id, 'cwv3_auth', true ) == 'yes' ) {
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
			'pages'      => json_decode( stripslashes( $_COOKIE['cwv3_pages'] ), true ),
			'posts'      => json_decode( stripslashes( $_COOKIE['cwv3_posts'] ), true ),
			'categories' => json_decode( stripslashes( $_COOKIE['cwv3_cats'] ), true )
		);

		//return print_r($cData, true);

		$sw = get_option( 'cwv3_sitewide' );
		$hm = get_option( 'cwv3_homepage' );
		$mi = get_option( 'cwv3_misc' );

		if ( $sw[0] == 'enabled' ) {
			return !empty( $cData['pages']['sitewide'] ) ? $cData['pages']['sitewide'] : false;
		}

		if ( is_home() && $hm[0] == 'enabled' ) {
			return !empty( $cData['pages']['home'] ) ? $cData['pages']['home'] : false;
		}

		if ( ( is_archive() || is_search() ) && $mi[0] == 'enabled' ) {
			// Protect misc pages aswell
			return !empty( $cData['pages']['other'] ) ? $cData['pages']['other'] : false;
		}

		if ( is_page() && get_post_meta( $post->ID, 'cwv3_auth', true ) == 'yes' ) {
			$c = $cData['pages'][$post->ID];
			return !empty( $c ) ? $c : false;
		}

		$id = ( is_attachment() ? $post->post_parent : $post->ID );
		// First see if categories are setup in the admin side.
		$catData = get_option( "cwv3_cat_list" );
		$curCat = get_the_category( $id );
		if ( get_post_type( $id ) == 'post' && $this->inCat( $catData, $curCat ) ) {
			// If the current category is selected in the admin page, that means the administrator wishes to protect it.
			// respect the admin's wishes and do it.
			return !empty( $cData['categories'][$post->ID] ) ? $cData['categories'][$id] : false;
		}
		// Since that's not the case, we need to check post_meta data and see if this post is protected.
		if ( get_post_meta( $post->ID, 'cwv3_auth', true ) == 'yes' && !is_home() ) {
			return !empty( $cData['posts'][$post->ID] ) ? $cData['posts'][$id] : false;
		}

		return 'failed all checks';
	}

	public function inCat( $catIDs, $catArray ) {
		if ( !is_array( $catIDs ) ) {
			$catIDs = array(); // Empty
		}

		foreach ( $catArray as $cat ) {
			if ( in_array( $cat->term_id, $catIDs ) ) {return true;}else {continue;}
		}
		return false;
	}

	public function renderDialog() {

		$d = get_option( 'cwv3_denial' );
		if ( $this->check_data() == 3 && $d[0] == 'enabled' ) {
			$dtype = true;
		}else {
			$dtype = false;
		}
		$etxt = get_option( 'cwv3_enter_txt' );
		$extxt = get_option( 'cwv3_exit_txt' );
?>
    	<!-- CWV3 Dialog -->
        <div style="display: none">
            <div id="cwv3_auth">
                <div id="cwv3_title"><?php if ( $dtype == true ): ?><?php echo get_option( 'cwv3_den_title' ); ?><?php else: ?><?php echo get_option( 'cwv3_d_title' ); ?><?php endif; ?></div>
                <div id="cwv3_content"><?php if ( $dtype === true ): ?><?php echo do_shortcode( get_option( 'cwv3_den_msg' ) ); ?><?php else: ?><?php echo do_shortcode( get_option( 'cwv3_d_msg' ) ); ?><?php endif; ?></div>
                <div id="cwv3_btns"><?php if ( $dtype !== true ): ?><div id="cwv3_enter"><a href="javascript:;" id="cw_enter_link"><?php echo !empty( $etxt ) ? $etxt : 'Enter'; ?></a></div><?php endif; ?><div id="cwv3_exit"><a href="javascript:;" id="cw_exit_link"><?php echo !empty( $extxt ) ? $extxt : 'Exit'; ?></a></div></div>
            </div>
        </div>
        <!-- END CWV3 Dialog -->
	<?php
	}

	public function render_metabox( $post ) {
		wp_nonce_field( plugin_basename( __FILE__ ), 'cwv3_meta' );
		$curval = get_post_meta( $post->ID, 'cwv3_auth', true );
		$sw = get_option( 'cwv3_sitewide' );
		$disabled = $sw[0] == 'enabled' ? 'disabled="disabled"' : '';


?>
        <?php //wp_die(print_r($curval), true); ?>
        <label for="cwv3_auth">Use authorization for this content:</label>
        <input type="checkbox" id="cwv3_auth" name="cwv3_auth" <?php checked( 'yes', $curval, true ); ?> value="yes" <?php echo $disabled;?>/><br />
        <?php if ( $sw[0] == 'enabled' ) : ?>
	                <p class="description">Cannot be changed while site wide option is enabled.</p>
        <?php endif; ?>
        <?php
	}


	// TODO
	public function display_qe( $column_name, $post_type ) {
		global $post;
?>
		<fieldset class="inline-edit-col-right inline-edit-book">
		  <div class="inline-edit-col column-<?php echo $column_name ?>">
			<label class="inline-edit-group">
			<?php
		switch ( $column_name ) {
		case 'cwv2':
			wp_nonce_field( plugin_basename( __FILE__ ), 'cwv3_meta' );
			$curval = get_post_meta( $post->ID, 'cwv3_auth', true );
			$sw = get_option( 'cwv3_sitewide' );
			$disabled = $sw[0] == 'enabled' ? 'disabled="disabled"' : ''; ?>

				<label for="cwv3_auth">
					<input type="checkbox" id="cwv3_auth" name="cwv3_auth" <?php checked( 'yes', $curval, true ); ?> value="yes" <?php echo $disabled; ?>/>
					<span class="checkbox-title">Use CWv2 for this content <?php echo $post->ID; ?></span>
					<?php if ( $sw[0] == 'enabled' ) : ?>
						<span class="description">(Cannot be changed while site wide option is enabled.)</span>
					<?php endif; ?>
				</label>

				<?php
			break;
		}
?>
			</label>
		  </div>
		</fieldset>
		<?php

	}
}
new CWV3;

?>
