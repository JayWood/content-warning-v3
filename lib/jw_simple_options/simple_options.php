<?
/**********************************************************************************
					JW Simple Options
			Author:			Jerry Wood Jr.
			Email:			jay@plugish.com
			WWW:			http://plugish.com

  This sofware is provided free of charge.  I use this in my own projects and
  it's ideal for small options pages when you don't feel like writing the 
  HTML to accompany it.  This work is Licensed under the Creative Commons
  Attribution Share-Alike 3.0 License.  All I ask, is you don't sell the 
  software as is.  You're more than welcome to include it in a package
  as long as you give the appropriate credit.
**********************************************************************************/
  
class JW_SIMPLE_OPTIONS{
	
	/**
	 *	@access private
	 *	@var string Class version number.
	 */
	private $ver = '1.1';
	
	/**
	 *	@access private
	 *	@var array() A map of the options data.
	 */
	private $options;
	
	/**
	 *	Location of the plugin/theme menu.
	 *
	 *	Accepts: page, link, comment, management, option, theme, plugin, user, dashboard, post, or media.
	 *	@access private
	 *	@var string Default: 'new'
	 */
	private $menu_type = 'new';
	
	/**
	 *	Used in options saving/gathering and menu data.
	 *
	 *	@access private
	 *	@var string Default: 'jw_'
	 */
	private $prefix = 'jw_';
	
	/**
	 *	Reader friendly menu name.
	 *
	 *	@access private
	 *	@var string Default: 'JW Options'
	 */
	private $menu_title = 'JW Options';
	
	/**
	 *	Capability needed by users to see this menu.
	 *
	 *	@access private
	 *	@var string Default: manage_options
	 *	@see add_menu_page()
	 */
	private $cap = 'manage_options';
	
	/**
	 *	URL friendly name of the menu, ie. 'options_page'
	 *
	 *	Will be prefixed by prefix variable.
	 *	@access private
	 *	@var string Default: 'options_page'
	 */
	private $slug = 'options_page';
	
	/**
	 *	Icon of the top-level menu.  Absolute URL.
	 *
	 *	@access private
	 *	@var string Default: NULL
	 */
	private $icon = NULL;
	
	/**
	 *	Menu position of the top-level menu.  Used only if menu_type is 'new'.
	 *
	 *	@access private
	 *	@var integer Default: NULL
	 */
	private $pos = NULL;
	
	/**
	 *	Used in menu pages and throughout the plugin
	 *
	 *	@access private
	 *	@var string Defaults to "JW Options Panel"
	 */
	private $plugin_title = 'JW Options Panel';
		
	/**
	 *	Used in menu generation and hooks.
	 *
	 *	@access private
	 *	@var string 
	 */
	private $hook;
	
	function JW_SIMPLE_OPTIONS($ops){
		$this->__construct($ops);
	}
	
	function __construct(array $ops){
		// Setup variables
		$this->plugin_title = empty($ops['plugin_title']) ? $this->plugin_title : $ops['plugin_title'];
		$this->menu_title = empty($ops['menu_title']) ? $this->menu_title : $ops['menu_title'];
		$this->cap = empty($ops['capability']) ? $this->cap : $ops['capability'];
		$this->slug = empty($ops['slug']) ? $this->prefix.$this->slug : $ops['slug'];
		$this->options = empty($ops['opData']) ? $this->options : $ops['opData'];
		$this->icon	= empty($ops['icon_url']) ? $this->icon : $ops['icon_url'];
		$this->pos = empty($ops['menu_pos']) ? $this->pos : $ops['menu_pos'];
		$this->prefix = empty($ops['prefix']) ? $this->prefix : $ops['prefix'];
		
		add_action('admin_init', array(&$this, 'register_admin_deps') );
		add_action('admin_menu', array(&$this, 'load_admin_menu') );
		add_action('admin_enqueue_scripts', array(&$this, 'load_admin_deps') );
		
				
	}
	
	/**
	 *	Builds an array of check boxes.
	 *
	 *	@param string $key Option identifier minus prefix.
	 *	@param array $data Associative array of data to display.
	 */
	public function buildCheckFields($key, $data, $def = false){
		$opData = get_option($this->prefix.$key, $def);
		?>
        	<fieldset>
			<? foreach($data as $k => $v): ?>
                <label for="<? echo $this->prefix.$key; ?>_<? echo $k; ?>" class="jw_check_fields">
                	<input id="<? echo $this->prefix.$key; ?>_<? echo $k; ?>" type="checkbox" name="<? echo $this->prefix.$key; ?>[]" <? $this->jop_checked($opData, $k, true); ?> value="<? echo $k; ?>"/> <? echo $v; ?>
                </label>
            <? endforeach; ?>
            </fieldset>
        <?		
		return $output;
		
	}
	
	/**
	 *	Builds an array of data, comparable to a matrix.
	 *	
	 *	Also provides neat javascript functionality such as adding/removing rows.
	 *	@param string $key Option identifier minus prefix.
	 *	@param array $fields A regular array of data identifiers, ie. array('field1', 'field2').
	 */
	public function buildDataArrayFields($key, $fields, $showhead = false){
		$opData = get_option($this->prefix.$key);
		?>
        	<a href="javascript:;" class="addrow" data_id="<? echo $key; ?>">[+] Add Row</a>
        	<table class="dataArrayFields" id="<? echo $key; ?>">
			<? $rowBase = 1; ?>
            <? if($showhead): ?>
                <thead>
                    <tr>
                    <? foreach($fields as $k => $v): ?>
                        <td><? echo $v; ?></td>
                    <? endforeach; ?>
                    </tr>
                </thead>
            <? endif; ?>
            <? if(!empty($opData) && is_array($opData)) :?>
            	<? foreach ($opData as $row): ?>
                	<tr id="data_row_<? echo $rowBase; ?>" class="data_row <? echo $key; ?>">
					<? foreach ($fields as $colName => $colLabel): ?>
                        <td class="data_col <? echo $colName; ?>"><input type="text" name="<? echo $this->prefix.$key ?>[<? echo $rowBase; ?>][<? echo $colName; ?>]" value="<? echo $row[$colName]; ?>"/></td>
                    <? endforeach; ?>
                        <td><a href="javascript:;" id="<? echo $rowBase; ?>" class="removerow" curBlock="<? echo $key; ?>">[X]</a></td>
                    </tr>                    
                    <? $rowBase++; ?>
                <? endforeach; ?>
            <? else: ?>
            	<tr id="data_row_<? echo $rowBase; ?>" class="data_row <? echo $key; ?>">
            	<? foreach ($fields as $colName => $colLabel): ?>
	                <td class="data_col <? echo $colName; ?>"><input type="text" name="<? echo $this->prefix.$key ?>[<? echo $rowBase; ?>][<? echo $colName; ?>]" /></td>
                <? endforeach; ?>
                	<td><a href="javascript:;" id="<? echo $rowBase; ?>" class="removerow <? echo $key; ?>" curBlock="<? echo $key; ?>">[X]</a></td>
                </tr>
            <? endif; ?>
            </table>
        <?
	}
	
	/**
	 *	WordPress 3.5 media upload functionality.
	 *
	 *	@param string Option identifier minus prefix.
	 */
	public function buildMediaOption($key){
		
		$opData = get_option($this->prefix.$key);
		
		$output = '<div class="uploader">';
		$output .= '<input type="text" name="'.$this->prefix.$key.'" id="'.$this->prefix.$key.'" class="regular-text" value="'.$opData.'" />';
		$output .= '<input type="button" id="'.$this->prefix.$key.'_upload" value="Upload" class="button upload_image_button" data-id="'.$this->prefix.$key.'" />';
		$output .= '</div>';
		
		return $output;
	}
	
	/**
	 *	Builds an array of radio buttons.
	 *
	 *	@param string $key Option identifier minus prefix.
	 *	@param array $data Associative array of data to display.
	 *	@param boolean $def If not false, provide a default value if no option exists.
	 */
	public function buildRadioFields($key, $data, $def = false){
		$opData = get_option($this->prefix.$key, $def);
		?>
        	<fieldset>
			<? foreach($data as $k => $v): ?>
                <label for="<? echo $this->prefix.$key; ?>_<? echo $k; ?>" class="jw_radio_fields">
                	<input id="<? echo $this->prefix.$key; ?>_<? echo $k; ?>" type="radio" name="<? echo $this->prefix.$key; ?>" <? checked($opData, $k, true); ?> value="<? echo $k; ?>"/> <? echo $v; ?>
                </label>
            <? endforeach; ?>
            </fieldset>
        <?
		
	}
	
	/**
	 *	Builds dropdown menu.
	 *
	 *	@param string $key Option identifier minus prefix.
	 *	@param array $data Associative array of data to display.
	 *	@param boolean $def If not false, provide a default value if no option exists.
	 */
	public function buildSelectOptions($key, $data, $def = false){
		
		$opData = get_option($this->prefix.$key, $def);
		
		$output = '<select name="'.$this->prefix.$key.'" id="'.$this->prefix.$key.'">';
		foreach($data as $k => $v){
			$output .= '<option value="'.$k.'" '.selected($opData, $k, false).'>'.$v.'</option>';
		}
		$output .= '</select>';
		$output .= '<!-- '.print_r($opData, true).'-->';
		
		return $output;
		
	}
	/**
	 *	Builds a timeframe selection that consists of one text input, and one dropdown.
	 *
	 *	@param string $key Option identifier minus prefix.
	 *	@param mixed $def If not false, provide a default value if no option exists.
	 */
	public function buildTimeframe($key, $def = false){
		// Should be two fields, one input text, one dropdown.
		$opData = get_option($this->prefix.$key, $def);
		
		if(empty($opData['multiplier'])) $opData['mulitplier'] = $def['multiplier'];
		if(empty($opData['time'])) $opData['time'] = $def['time'];
		
		?>
        <input type="text" name="<? echo $this->prefix.$key; ?>[multiplier]" value="<? echo $opData['multiplier']; ?>" class="jw_multiplier"/><select name="<? echo $this->prefix.$key; ?>[time]">
        	<option value="60" <? selected($opData['time'], 60, true); ?>>Minutes</option>
            <option value="<? echo 60*60; ?>" <? selected($opData['time'], 60*60, true); ?>>Hours</option>
            <option value="<? echo 60*60*24; ?>" <? selected($opData['time'], 60*60*24, true); ?>>Days</option>
            <option value="<? echo 60*60*24*30; ?>" <? selected($opData['time'], 60*60*24*30, true); ?>>Months</option>
            <option value="<? echo 60*60*24*365; ?>" <? selected($opData['time'], 60*60*24*365, true); ?>>Years</option>           
        </select>
        <?
	}
	
	/**
	 *	Custom Checked
	 *
	 *	Allows using arrays in checked variables
	 *	@return boolean 
	 */
	function jop_checked($haystack, $cur, $show = FALSE){
		if(is_array($haystack) && in_array($cur, $haystack)){
				$cur = $haystack = 1;
		}
		return checked($haystack, $cur, $show);
	}
	
	/**
	 *	Loads the admin menu with user-defined flags.
	 */
	function load_admin_menu(){
		switch($this->menu_type){
				case 'page':
					$hook = add_pages_page($this->plugin_title, $this->menu_title, $this->cap, $this->slug, array(&$this, 'render_options_page'));
					break;
				case 'link':
					$hook = add_links_page($this->plugin_title, $this->menu_title, $this->cap, $this->slug, array(&$this, 'render_options_page'));
					break;
				case 'comment':
					$hook = add_comments_page($this->plugin_title, $this->menu_title, $this->cap, $this->slug, array(&$this, 'render_options_page'));
					break;
				case 'management':
					$hook = add_management_page($this->plugin_title, $this->menu_title, $this->cap, $this->slug, array(&$this, 'render_options_page'));
					break;
				case 'option':
					$hook = add_options_page($this->plugin_title, $this->menu_title, $this->cap, $this->slug, array(&$this, 'render_options_page'));
					break;
				case 'theme':
					$hook = add_theme_page($this->plugin_title, $this->menu_title, $this->cap, $this->slug, array(&$this, 'render_options_page'));
					break;
				case 'plugin':
					$hook = add_plugins_page($this->plugin_title, $this->menu_title, $this->cap, $this->slug, array(&$this, 'render_options_page'));
					break;
				case 'user':
					$hook = add_users_page($this->plugin_title, $this->menu_title, $this->cap, $this->slug, array(&$this, 'render_options_page'));
					break;
				case 'dashboard':
					$hook = add_dashboard_page($this->plugin_title, $this->menu_title, $this->cap, $this->slug, array(&$this, 'render_options_page'));
					break;
				case 'post':
					$hook = add_posts_page($this->plugin_title, $this->menu_title, $this->cap, $this->slug, array(&$this, 'render_options_page'));
					break;
				case 'media':
					$hook = add_media_page($this->plugin_title, $this->menu_title, $this->cap, $this->slug, array(&$this, 'render_options_page'));
					break;
				default:
					$hook = add_menu_page($this->plugin_title, $this->menu_title, $this->cap, $this->slug, array(&$this, 'render_options_page'), $this->icon, $this->menu_pos);				
					break;
			}		
		$this->hook = $hook;
	}
	
	/**
	 *	Load up admin dependancies for functionality
	 */
	public function load_admin_deps($hook = false){
		if($hook == $this->hook && $hook != false){
			
			if(function_exists('wp_enqueue_media')) wp_enqueue_media();
			
			wp_enqueue_style('spectrum');
			wp_enqueue_script('spectrum');
			
			wp_enqueue_style($this->prefix.'admin_css');
			wp_enqueue_script($this->prefix.'admin_js');
		}
	}
	
	/**
	 *	Registering Admin information.
	 */
	public function register_admin_deps(){		
		foreach($this->options as $k => $v)	register_setting($this->prefix.'options', $this->prefix.$k);
		
		
		if(preg_match('/\/themes\//i', $this->file_data)){
			$type = 'theme';
		}elseif(preg_match('/\/plugins\//i', $this->file_data)){
			$type = 'plugin';
		}
		
		if('theme' == $type){
			$stylesheetDir = get_bloginfo('stylesheet_directory');
			$urls = array(
				$stylesheetDir.'/'.basename( dirname(  dirname(__FILE__) ) ).'/jw-simple-options/css',
				$stylesheetDir.'/'.basename( dirname(  dirname(__FILE__) ) ).'/jw-simple-options/js',
			);
		}else{
			$urls = array(
				'css'	=> plugins_url('css', __FILE__),
				'js'	=> plugins_url('js', __FILE__)
			);
		}
		
		wp_register_style('spectrum', $urls['css'].'/spectrum.css', '', '1.0.9');
		wp_register_script('spectrum', $urls['js'].'/spectrum.js', array('jquery'), '1.0.9' );
		
		wp_register_style( $this->prefix.'admin_css', $urls['css'].'/jw_simple_options.css', '', '1.0');
		wp_register_script( $this->prefix.'admin_js', $urls['js'].'/jquery.jw_simple_options.js' , '', '1.0');
		
	}
	
	/**
	 *	Display user-end options page
	 */
	public function render_options_page(){
		
		?>
        	<div class="wrap">
            	<div id="icon-options-general" class="icon32"><br /></div>
                <h2><? echo $this->plugin_title; ?></h2>
                <p class="description">Options page powered by: <a href="https://github.com/JayWood/jw_simple_options" title="A simple, easy to configure, flexible, and open-source framework to make options pages on the fly.">JW Simple Options</a></p>
                <form method="post" action="options.php">
                <? settings_fields($this->prefix.'options'); ?>
                <table class="form-table">
                	<tbody>
                    	<?
						foreach ($this->options as $k => $v){
							?>
							<tr valign="top">
								<th scope="row"><label for="<? echo $this->prefix.$k; ?>"><? echo $v['name']; ?></label></th>
                                <td><? echo $this->render_option_field($k, $v); ?>
									<? if( isset( $v['desc'] ) ): ?>
                                        <p class="description"><? echo $v['desc']; ?></p>
                                    <? endif; ?>
                                </td>
							</tr>
							<?
						}
						?>
                    </tbody>
                </table>
                <p class="submit">
                	<input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes">
                </p>
                </form>
            </div>
        <?
	}
	
	/**
	 *	Display Spectrum color selection
	 *	
	 *	@param string $key Option identifier minus prefix.
	 *	@param array $data Associative array of field data with value in hex6 format.  ie. array('colorID' => '#FFFFFF')
	 */
	public function render_color_select($key, $data){
		
		$opData = get_option($this->prefix.$key, $data);
		
		$output = '<!-- Color Selects -->';
		foreach($opData as $k => $v){
			$output .= '<input type="text" id="'.$key.'_'.$k.'" name="'.$this->prefix.$key.'['.$k.']" value="'.$v.'" class="color_select">';
		}
		
		return $output;
		
	}
	
	/**
	 *	Switch between options data types and display them.
	 *
	 *	Offload rendering where necessary.
	 */
	public function render_option_field($key, $data){
		switch($data['type']){
			case 'text':
				$output = '<input type="text" name="'.$this->prefix.$key.'" id="'.$this->prefix.$key.'" value="'.get_option($this->prefix.$key, $data['def']).'" class="regular-text" />';
				break;
			case 'password':
				$output = '<input type="password" name="'.$this->prefix.$key.'" id="'.$this->prefix.$key.'" value="'.get_option($this->prefix.$key).'" class="regular-text" />';
				break;
			case 'number':
				$output = '<input type="number" name="'.$this->prefix.$key.'" id="'.$this->prefix.$key.'" value="'.get_option($this->prefix.$key, $data['def']).'" />';
				break;
			case 'data_array':
				$output = $this->buildDataArrayFields($key, $data['fields'], $data['showhead']);
				break;
			case 'select':
				$output = $this->buildSelectOptions($key, $data['fields'], $data['def']);
				break;
			case 'color':
				$output = $this->render_color_select($key, $data['fields']);
				break;
			case 'media':
				if(function_exists('wp_enqueue_media')) $output = $this->buildMediaOption($key);
				break;
			case 'check':
				$output = $this->buildCheckFields($key, $data['fields'], $data['def']);
				break;
			case 'radio':
				$output = $this->buildRadioFields($key, $data['fields'], $data['def']);
				break;
			case 'textbox':
				$output = '<textarea name="'.$this->prefix.$key.'" id="'.$this->prefix.$key.'" rows="10" cols="50" class="large-text code" >'.get_option($this->prefix.$key, $data['def']).'</textarea>';
				break;
			case 'timeframe':
				$output = $this->buildTimeframe($key, $data['def']);
				break;
			case 'editor':
				$opData = get_option($this->prefix.$key, $data['def']);
				$output = wp_editor($opData, $this->prefix.$key, $data['settings']);
			default:
				$output = '<!-- Option ID: '.$key.'.'.$data['type'].' is not a valid option type. -->';
				break;
		}
		return $output;
	}
	
	/**
	 *	Uninstalls any options.
	 *
	 *	Needs to be called from functions.php
	 *	@see register_uninstall_hook()
	 */
	public function uninstall(){
		if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) return false;
		// Remove options
		foreach ($this->options as $k => $v) delete_option($this->prefix.$k);
	}
	
}

?>