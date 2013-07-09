<?
	
class CWV3{
	
	function CWV3(){
		$this->__construct();
	}
	
	public function __construct(){
		// Styling and such
		add_action('init', array( &$this, 'register_frontend_data') );
		add_action('wp_enqueue_scripts', array(&$this, 'load_dependancies') );
		
		add_action('wp_footer', array(&$this, 'renderDialog'));
		
		// Post Meta Box for this.
		add_action('add_meta_boxes', array(&$this, 'cw_meta'));
		add_action('save_post', array(&$this, 'cwv3_meta_save'));
		
		// AJAX Handle
		add_action('wp_ajax_cwv3_ajax', array(&$this, 'handle_ajax'));
		add_action('wp_ajax_nopriv_cwv3_ajax', array(&$this, 'handle_ajax'));
	}
	
	public function cw_meta(){
		$scr = array('post', 'page');
		foreach($scr as $screen){		
			add_meta_box('cwv3_meta_section',
				__('CWV3 Security'),
				array(&$this, 'render_metabox'),
				$screen,
				'side',
				'high'
			);
		}
	}
	
	public function cwv3_meta_save($post_id){
		if('page' == $_POST['post_type'])
			if(!current_user_can('edit_page', $post_id) )
				return;
		else
			if(!current_user_can('edit_post', $post_id) )
				return;
				
		if(!isset($_POST['cwv3_meta'] ) || ! wp_verify_nonce($_POST['cwv3_meta'], plugin_basename(__FILE__) ) )
			return;
		
		$mydata = sanitize_text_field($_POST['cwv3_auth']);
		update_post_meta($post_id, 'cwv3_auth', $mydata);
			
	}
	
	public function handle_ajax(){
		$post_id = intval($_POST['id']);
		
		check_ajax_referer('cwv3_ajax_'.$post_id, 'nonce');
		
		$sw = get_option('cwv3_sitewide') == 'enabled' ? true : false;
		$cData = json_decode($_COOKIE['cwv3_auth']);
		$time = get_option('cwv3_death');
		$time = time()+($time['multiplier']*$time['time']);
		if($_POST['method'] == 'exit'){
			if(get_option('cwv3_denial') == 'enabled'){
				
			}
		}
		
		die;
	}
	
	public function load_dependancies(){
		global $post;
		
		wp_enqueue_style('cwv3_css');
		wp_enqueue_script('cwv3_js');
		
		$elink = get_option('cwv3_enter_link');
		$exlink = get_option('cwv3_exit_link');
		$p_ID = (is_home()) ? -1 : (is_attachment() ? $post->post_parent : (is_archive() || is_search()) ? -2 : $post->ID);
		
		wp_localize_script('cwv3_js', 'cwv3_params', array(
			'action' => 'cwv3_ajax',
			'nonce'	=>	wp_create_nonce('cwv3_ajax_'.$p_ID),
			'admin_url'	=>	admin_url( 'admin-ajax.php' ),
			'id'	=>	$p_ID,
			'sd'	=>	($this->check_data() !== true) ? true : false,
			'enter'	=>	!empty($elink) ? $elink : '#',
			'exit'	=>	!empty($exlink) ? $exlink : 'http://google.com'
		));
	}
	
	public function register_frontend_data(){
		// Colorbox w/ MIT License
		wp_register_style('colorbox', plugins_url('js/colorbox.1.4.14/colorbox.css', dirname(__FILE__)), '', '1.4.14', 'ALL');
		wp_register_script('colorbox_js', plugins_url('js/colorbox.1.4.14/jquery.colorbox-min.js', dirname(__FILE__)), array('jquery'), '1.4.14', true);
		
		// Main data
		wp_register_script('cwv3_js', plugins_url('js/cwv3.js', dirname(__FILE__)), array('colorbox_js'), '1.0', true);
		wp_register_style('cwv3_css', plugins_url('css/cwv3.css', dirname(__FILE__)), array('colorbox'), '1.0');	
	}
	
	public function set_cookie($id, $action){
		$cData = json_decode($_COOKIE['cwv3_auth']);
		$cData[$id] = $action;
		
		$time = get_option('cwv3_death');
		setcookie('cwv3_auth', json_encode($cData), ($time['multiplier'] * $time['time'])+time(),'/', COOKIE_DOMAIN, false);
	}
	
	public function check_data(){
		global $post;
		
		if(is_feed()){
			//Don't want to hender the feed, just in case.
			return true;
		}
		
		$cData = json_decode($_COOKIE['cwv3_auth']);
		$sw = get_option('cwv3_sitewide');
		$hm = get_option('cwv3_homepage');
		$mi = get_option('cwv3_misc');
		
		if($sw[0] == 'enabled'){
			return (!empty($cData['sitewide']) ? $cData['sitewide'] : false);
		}

		if(is_home() && $hm[0] == 'enabled'){
			return (!empty($cData['-1']) ? $cData['-1'] : false);
		}
		
		if((is_archive() || is_search()) && $mi[0] == 'enabled'){
			// Protect misc pages aswell
			return (!empty($cData['-2']) ? $cData['-2'] : false);
		}
		
		if(is_page()){
			$c = $cData['pages'][$post->ID];
			return(!empty($c) ? $c : false);
		}
		
		$id = (is_attachment() ? $post->post_parent : $post->ID);
		// First see if categories are setup in the admin side.
		$catData = get_option("cwv3_cat_list");
		$curCat = get_the_category($id);
		if(in_array($curCat, $catData)){
			//	If the current category is selected in the admin page, that means the administrator wishes to protect it.
			//	respect the admin's wishes and do it.
			return(!empty($cData['categories'][$post->id]) ? $cData['categories'][$id] : false );
		}
		// Since that's not the case, we need to check post_meta data and see if this post is protected.
		if(get_post_meta($post->ID, 'cwv3_auth', true) == 'yes'){
			return(!empty($cData['posts'][$post->ID]) ? $cData['posts'][$id] : false );
		}
		
		return true;
	}
	
	public function renderDialog(){
		
		$dtype = $this->check_data();
		$etxt = get_option('cwv3_enter_txt');
		$extxt = get_option('cwv3_exit_txt');
	?>
    	<!-- CWV3 Dialog -->
        <div style="display: none">
            <div id="cwv3_auth">
                <div id="cwv3_title"><? if($dtype === 'denial'): ?><? echo get_option('cwv3_den_title'); ?><? else: ?><? echo get_option('cwv3_d_title'); ?><? endif; ?></div>
                <div id="cwv3_content"><? if($dtype === 'denial'): ?><? echo get_option('cwv3_den_msg'); ?><? else: ?><? echo get_option('cwv3_d_msg'); ?><? endif; ?></div>
                <div id="cwv3_btns"><? if($dtype !== 'denial'): ?><div id="cwv3_enter"><a href="javascript:;" id="cw_enter_link"><? echo (!empty($etxt) ? $etxt : 'Enter'); ?></a></div><? endif; ?><div id="cwv3_exit"><a href="javascript:;" id="cw_exit_link"><? echo (!empty($extxt) ? $extxt : 'Exit'); ?></a></div></div>
            </div>
        </div>
        <!-- END CWV3 Dialog -->
	<?
	}
	
	public function render_metabox($post){
		wp_nonce_field(plugin_basename(__FILE__), 'cwv3_meta');
		
		$curval = get_post_meta($post->ID, 'cwv3_auth', true);?>
        <? //wp_die(print_r($curval), true); ?>
        <label for="cwv3_auth">Use authorization for this content:</label>
        <input type="checkbox" id="cwv3_auth" name="cwv3_auth" <? checked('yes', $curval, true); ?> value="yes"/>
        <?
	}
	
	
	
}

new CWV3;
	
?>