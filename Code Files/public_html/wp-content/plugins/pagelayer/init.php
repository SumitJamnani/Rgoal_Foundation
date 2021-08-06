<?php

// We need the ABSPATH
if (!defined('ABSPATH')) exit;

define('PAGELAYER_BASE', plugin_basename(PAGELAYER_FILE));
define('PAGELAYER_PRO_BASE', 'pagelayer-pro/pagelayer-pro.php');
define('PAGELAYER_VERSION', '1.5.3');
define('PAGELAYER_DIR', dirname(PAGELAYER_FILE));
define('PAGELAYER_SLUG', 'pagelayer');
define('PAGELAYER_URL', plugins_url('', PAGELAYER_FILE));
define('PAGELAYER_CSS', PAGELAYER_URL.'/css');
define('PAGELAYER_JS', PAGELAYER_URL.'/js');
define('PAGELAYER_PRO_URL', 'https://pagelayer.com/pricing?from=plugin');
define('PAGELAYER_WWW_URL', 'https://pagelayer.com/');
define('PAGELAYER_DOCS', 'https://pagelayer.com/docs/');
define('PAGELAYER_API', 'https://api.pagelayer.com/');
define('PAGELAYER_SC_PREFIX', 'pl');
define('PAGELAYER_YOUTUBE_BG', 'https://www.youtube.com/watch?v=Csa6rvCWmLU');
define('PAGELAYER_CMS_NAME', defined('SITEPAD') ? 'SitePad' : 'WordPress');
define('PAGELAYER_BLOCK_PREFIX', defined('SITEPAD') ? 'sp' : 'wp');
define('PAGELAYER_CMS_DIR_PREFIX', defined('SITEPAD') ? 'site' : 'wp');
define('PAGELAYER_DEV', file_exists(dirname(__FILE__).'/dev.php') ? 1 : 0);
define('PAGELAYER_FONT_POST_TYPE', 'pagelayer-fonts');

include_once(PAGELAYER_DIR.'/main/functions.php');
include_once(PAGELAYER_DIR.'/main/class.php');

function pagelayer_died(){
	 print_r(error_get_last());
}
//register_shutdown_function('pagelayer_died');

// Ok so we are now ready to go
register_activation_hook(PAGELAYER_FILE, 'pagelayer_activation');

// Is called when the ADMIN enables the plugin
function pagelayer_activation(){

	global $wpdb;

	$sql = array();

	/*$sql[] = "DROP TABLE IF EXISTS `".$wpdb->prefix."pagelayer_logs`";

	foreach($sql as $sk => $sv){
		$wpdb->query($sv);
	}*/

	add_option('pagelayer_version', PAGELAYER_VERSION);
	add_option('pagelayer_options', array());

}

// Checks if we are to update ?
function pagelayer_update_check(){

global $wpdb;

	$sql = array();
	$current_version = get_option('pagelayer_version');
	$version = (int) str_replace('.', '', $current_version);

	// No update required
	if($current_version == PAGELAYER_VERSION){
		return true;
	}

	// Is it first run ?
	if(empty($current_version)){

		// Reinstall
		pagelayer_activation();

		// Trick the following if conditions to not run
		$version = (int) str_replace('.', '', PAGELAYER_VERSION);

	}

	// Save the new Version
	update_option('pagelayer_version', PAGELAYER_VERSION);

}

// Add the action to load the plugin 
add_action('plugins_loaded', 'pagelayer_load_plugin');

// The function that will be called when the plugin is loaded
function pagelayer_load_plugin(){

	global $pagelayer;

	// Check if the installed version is outdated
	pagelayer_update_check();

	// Set the array
	$pagelayer = new PageLayer();
	
	if(empty($pagelayer->BRAND_TEXT)){
		$pagelayer->BRAND_TEXT = 'Pagelayer';
	}
	
	if(empty($pagelayer->LOGO)){
		$pagelayer->LOGO = PAGELAYER_URL.'/images/pagelayer-logo-40.png';
	}
	
	// Load license
	pagelayer_load_license();

	// Is there any ACTION set ?
	$pagelayer->action = pagelayer_optreq('pagelayer-action');

	// Load settings
	$pagelayer->settings['post_types'] = empty(get_option('pl_support_ept')) ? ['post', 'page'] : get_option('pl_support_ept');
	$pagelayer->settings['enable_giver'] = get_option('pagelayer_enable_giver');
	$pagelayer->settings['max_width'] = (int) (empty(get_option('pagelayer_content_width')) ? 1170 : get_option('pagelayer_content_width'));
	$pagelayer->settings['tablet_breakpoint'] = (int) (empty(get_option('pagelayer_tablet_breakpoint')) ? 768 : get_option('pagelayer_tablet_breakpoint'));
	$pagelayer->settings['mobile_breakpoint'] = (int) (empty(get_option('pagelayer_mobile_breakpoint')) ? 360 : get_option('pagelayer_mobile_breakpoint'));
	$pagelayer->settings['sidebar'] = get_option('pagelayer_sidebar');
	$pagelayer->settings['body_font'] = get_option('pagelayer_body_font');
	$pagelayer->settings['color'] = get_option('pagelayer_color');
	
	// Any custom types
	$pagelayer->settings['post_types'] = apply_filters('pagelayer_supported_post_type', $pagelayer->settings['post_types']);
		
	// Load CSS settings	
	foreach($pagelayer->css_settings as $k => $params){
		foreach($pagelayer->screens as $sk => $sv){
			$suffix = (!empty($sv) ? '_'.$sv : '');
			$setting = empty($params['key']) ? 'pagelayer_'.$k.'_css' : $params['key'];
			$tmp = get_option($setting.$suffix);
			if(!empty($tmp)){
				$pagelayer->css[$k.$suffix] = $tmp;
			}
		}
	}
	
	// Backward compat for colors
	if(!empty($pagelayer->settings['color']['background']) && empty($pagelayer->css['body']['background-color'])){
		$pagelayer->css['body']['background-color'] = $pagelayer->settings['color']['background'];
	}
	
	if(!empty($pagelayer->settings['color']['text']) && empty($pagelayer->css['body']['color'])){
		$pagelayer->css['body']['color'] = $pagelayer->settings['color']['text'];
	}
	
	// Link Color
	if(!empty($pagelayer->settings['color']['link']) && empty($pagelayer->css['a']['color'])){
		$pagelayer->css['a']['color'] = $pagelayer->settings['color']['link'];
	}
	
	// Link Hover Color
	if(!empty($pagelayer->settings['color']['link-hover']) && empty($pagelayer->css['a-hover']['color'])){
		$pagelayer->css['a-hover']['color'] = $pagelayer->settings['color']['link-hover'];
	}
	
	// Headings Color
	if(!empty($pagelayer->settings['color']['heading'])){
		$htmp = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];		
		foreach($htmp as $k => $v){
			if(empty($pagelayer->css[$v]['color'])){
				$pagelayer->css[$v]['color'] = $pagelayer->settings['color']['heading'];
			}			
		}
	}
	
	// Backward compat for body font
	if(!empty($pagelayer->settings['body_font'])){
		$pagelayer->settings['body']['font-family'] = $pagelayer->settings['body_font'];
	}
	
	// Load the language
	load_plugin_textdomain('pagelayer', false, PAGELAYER_SLUG.'/languages/');
	
	// Load our array for builder
	pagelayer_builder_array();
	
	// Its premium
	if(defined('PAGELAYER_PREMIUM')){
	
		// Check for updates
		include_once(PAGELAYER_DIR.'/main/plugin-update-checker.php');
		$pagelayer_updater = Pagelayer_PucFactory::buildUpdateChecker(PAGELAYER_API.'updates.php?version='.PAGELAYER_VERSION, PAGELAYER_FILE);
		
		// Add the license key to query arguments
		$pagelayer_updater->addQueryArgFilter('pagelayer_updater_filter_args');
		
		// Show the text to install the license key
		add_filter('puc_manual_final_check_link-pagelayer-pro', 'pagelayer_updater_check_link', 10, 1);
		
		// Load the template builder
		include_once(PAGELAYER_DIR.'/main/template-builder.php');
		
		$pagelayer->allowed_mime_type = array(
			'ttf' => 'font/ttf',
			'woff' => 'font/woff|application/font-woff|application/x-font-woff',
			'woff2' => 'font/woff2|font/x-woff2'
		);
		
		// Load the pagelayer custom fonts
		include_once(PAGELAYER_DIR.'/main/custom_fonts.php');		
	
	}else{
	
		// Show the promo
		pagelayer_maybe_promo([
			'after' => 1,// In days
			'interval' => 30,// In days
			'pro_url' => PAGELAYER_PRO_URL,
			'rating' => 'https://wordpress.org/plugins/pagelayer/#reviews',
			'twitter' => 'https://twitter.com/pagelayer?status='.rawurlencode('I love #Pagelayer Site Builder by @pagelayer team for my #WordPress site - '.home_url()),
			'facebook' => 'https://www.facebook.com/pagelayer',
			'website' => PAGELAYER_WWW_URL,
			'image' => PAGELAYER_URL.'/images/pagelayer-logo-256.png'
		]);
	
	}
	
	// Are we to disable the getting started promo
	if(isset($_GET['pagelayer-getting-started']) && (int)$_GET['pagelayer-getting-started'] == 0){
		update_option('pagelayer_getting_started', time());
		die('DONE');
	}
	
	// Show the getting started video option
	$seen = get_option('pagelayer_getting_started');
	if(empty($seen) && !empty($_GET['page']) && $_GET['page'] != 'pagelayer_getting_started'){
		add_action('admin_notices', 'pagelayer_getting_started_notice');
	}

}

// Add our license key if ANY
function pagelayer_updater_filter_args($queryArgs) {
	
	global $pagelayer;
	
	if ( !empty($pagelayer->license['license']) ) {
		$queryArgs['license'] = $pagelayer->license['license'];
	}
	
	return $queryArgs;
}

// Handle the Check for update link and ask to install license key
function pagelayer_updater_check_link($final_link){
	
	global $pagelayer;
	
	if(empty($pagelayer->license['license'])){
		return '<a href="'.admin_url('admin.php?page=pagelayer_license').'">Install Pagelayer Pro License Key</a>';
	}
	
	return $final_link;
}

// This adds the left menu in WordPress Admin page
add_action('admin_menu', 'pagelayer_admin_menu', 5);

// Shows the admin menu of Pagelayer
function pagelayer_admin_menu() {

	global $wp_version, $pagelayer;

	$capability = 'activate_plugins';// TODO : Capability for accessing this page

	// Add the menu page
	add_menu_page(__('Pagelayer Editor'), __('Pagelayer'), $capability, 'pagelayer', 'pagelayer_page_handler', PAGELAYER_URL.'/images/pagelayer-logo-19.png');

	// Settings Page
	add_submenu_page('pagelayer', __('Pagelayer Editor'), __('Settings'), $capability, 'pagelayer', 'pagelayer_page_handler');
	
	// Meta Settings Page
	add_submenu_page('admin.php', __('Meta Settings'), __('Meta Settings'), 'edit_posts', 'pagelayer_meta_setting', 'pagelayer_meta_handler');
	
	// UI Settings
	add_submenu_page('pagelayer', __('Website Settings'), __('Website Settings'), $capability, 'pagelayer_website_settings', 'pagelayer_website_page');

	// Add new template
	add_submenu_page('pagelayer', __('Theme Templates'), __('Theme Templates'), $capability, 'edit.php?post_type=pagelayer-template');

	// Add new template Link
	//add_submenu_page('pagelayer', __('Add New Template'), __('Add New Template'), $capability, 'edit.php?post_type=pagelayer-template#new');

	// Add new template
	add_submenu_page('pagelayer', __('Add New Template'), __('Add New Template'), $capability, 'pagelayer_template_wizard', 'pagelayer_builder_template_wizard');

	// Export Feature
	if(defined('PAGELAYER_PREMIUM')){
		
		// Add new template
		add_submenu_page('pagelayer', __('Custom Fonts'), __('Custom Fonts'), $capability, 'edit.php?post_type='.PAGELAYER_FONT_POST_TYPE);

		// Export Theme
		add_submenu_page('pagelayer', __('Export Content into a Theme'), __('Export Theme'), $capability, 'pagelayer_template_export', 'pagelayer_builder_export');

		// Import Theme
		add_submenu_page('pagelayer', __('Import content from a Theme'), __('Import Theme'), $capability, 'pagelayer_import', 'pagelayer_import_page');
	
	}
	
	// Getting Started
	add_submenu_page('pagelayer', __('Getting Started'), __('Getting Started'), $capability, 'pagelayer_getting_started', 'pagelayer_getting_started');

	// Its Free
	if(!defined('PAGELAYER_PREMIUM')){

		// Go Pro link
		add_submenu_page('pagelayer', __('Pagelayer Go Pro'), __('Go Pro'), $capability, PAGELAYER_PRO_URL);

	}

	// License Page
	add_submenu_page('pagelayer', __('Pagelayer Editor'), __('License'), $capability, 'pagelayer_license', 'pagelayer_license_page');

	// Replace Media
	add_submenu_page('admin.php', __('Replace media', 'pagelayer'),	__('Replace media', 'pagelayer'), 'upload_files', 'pagelayer_replace_media', 'pagelayer_replace_media');
	
}

// This function will handle the Settings Pages in PageLayer
function pagelayer_website_page(){

	global $wp_version, $pagelayer;

	include_once(PAGELAYER_DIR.'/main/website.php');
	
	pagelayer_website_settings();

}

// Getting Started
function pagelayer_getting_started(){

	global $wp_version, $pagelayer;
	
	update_option('pagelayer_getting_started', time());

	include_once(PAGELAYER_DIR.'/main/getting_started.php');
	
}

// This function will handle the post_metas Pages in PageLayer
function pagelayer_meta_handler(){

	global $wp_version, $pagelayer;

	include_once(PAGELAYER_DIR.'/main/post_metas.php');
	
	pagelayer_meta_page();

}

// Pagelayer post meta page view handler
add_action('admin_enqueue_scripts', 'pagelayer_post_meta_page');
function pagelayer_post_meta_page() {
	
	// Set Current screen
	$screen = get_current_screen();
	$meta_id = 'admin_page_pagelayer_meta_setting';
	
	if( !is_admin() || trim($screen->id) != $meta_id ) {
		return;
	}
	
	if(!isset($_REQUEST['post'])){
		return;		
	}
	
	// Remove all the notice hooks
	remove_all_actions('admin_notices');
	remove_all_actions('all_admin_notices');
	
	$_REQUEST['post'] = (int) $_REQUEST['post'];
	$post = get_post( $_REQUEST['post'] );
	
	// Enqueue Scripts
	wp_enqueue_script( 'post' );
	
	// Is support media
	$thumbnail_support = current_theme_supports( 'post-thumbnails', $post->post_type ) && post_type_supports( $post->post_type, 'thumbnail' );
	if ( ! $thumbnail_support && 'attachment' === $post->post_type && $post->post_mime_type ) {
		if ( wp_attachment_is( 'audio', $post ) ) {
			$thumbnail_support = post_type_supports( 'attachment:audio', 'thumbnail' ) || current_theme_supports( 'post-thumbnails', 'attachment:audio' );
		} elseif ( wp_attachment_is( 'video', $post ) ) {
			$thumbnail_support = post_type_supports( 'attachment:video', 'thumbnail' ) || current_theme_supports( 'post-thumbnails', 'attachment:video' );
		}
	}

	if ( $thumbnail_support ) {
		add_thickbox();
		wp_enqueue_media( array( 'post' => $post->ID ) );
	}
	
	$meta_box_url = admin_url( 'post.php' );		
	$meta_box_url = add_query_arg(
		array(
			'post'	=> $post->ID,
			'action'	=> 'editpost',
		),
		$meta_box_url
	);
	
	echo '<style>
.'.$meta_id.' #adminmenumain, .'.$meta_id.' #wpfooter, .'.$meta_id.' #wpadminbar{
display:none;
}
.'.$meta_id.' #wpcontent{
margin:auto;
}
</style>
	
<script type="text/javascript">

function pagelayer_post_edit(jEle, e){
	
	e.preventDefault();
	var formData = new FormData( jQuery(jEle)[0] );

	jQuery.ajax({
		url: "'.$meta_box_url.'",
		type: "POST",
		data: formData,
		processData: false,
		contentType: false,
		cache:false,
		success:function(result){
			//window.location.reload();						
			alert("Post meta has been updated successfully !");
		},
		error:function(result){				
			alert("There is an error while updating post meta !");
		}
	});
}
		
</script>';
	
}

// On post Save handler
add_action('save_post', 'pagelayer_save_post', 10, 3);
function pagelayer_save_post( $post_id, $post, $update ) {
	
	if( !isset($_REQUEST['is_pagelayer_editor']) ){
		return;
	}
	
	// Save Header and footer code
	$header_code = !empty($_REQUEST['pagelayer_header_code']) ? $_REQUEST['pagelayer_header_code'] : '' ;
	$footer_code = !empty($_REQUEST['pagelayer_footer_code']) ? $_REQUEST['pagelayer_footer_code'] : '' ;
	
	// Set Custom header footer code
	if(!empty($header_code)){
		update_post_meta($post_id, 'pagelayer_header_code', $header_code);
	}else{
		delete_post_meta($post_id, 'pagelayer_header_code');
	}
	
	if(!empty($footer_code)){
		update_post_meta($post_id, 'pagelayer_footer_code', $footer_code);
	}else{
		delete_post_meta($post_id, 'pagelayer_footer_code');
	}
	
}

// This function will handle the Settings Pages in PageLayer
function pagelayer_page_handler(){

	global $wp_version, $pagelayer;

	include_once(PAGELAYER_DIR.'/main/settings.php');
	
	pagelayer_settings_page();

}

// This function will handle the Settings Pages in PageLayer
function pagelayer_license_page(){

	global $wp_version, $pagelayer;

	include_once(PAGELAYER_DIR.'/main/license.php');
	
	pagelayer_license();

}

// Import Pagelayer Templates
function pagelayer_import_page(){

	global $wp_version, $pagelayer;

	include_once(PAGELAYER_DIR.'/main/import.php');
	
	pagelayer_import();

}

// Load the Live Body
add_action('template_redirect', 'pagelayer_load_live_body', 4);

function pagelayer_load_live_body(){

	global $post;

	// If its not live editing then stop
	if(!pagelayer_is_live()){
		return;
	}

	// If its the iFRAME then return
	if(pagelayer_is_live_iframe()){
		return;
	}

	// Are you allowed to edit ?
	if(!pagelayer_user_can_edit($post->ID)){
		return;
	}

	// Load the editor live body
	include_once(PAGELAYER_DIR.'/main/live-body.php');

	pagelayer_live_body();

}

// Add the JS and CSS for Posts and Pages when being viewed ONLY if there is our content called
add_action('template_redirect', 'pagelayer_enqueue_frontend', 5);

function pagelayer_enqueue_frontend($force = false){

	global $post, $pagelayer;

	if(!empty($pagelayer->cache['enqueue_frontend'])){
		return;
	}

	if(empty($post->ID) && empty($force)){
		return;
	}
	
	$is_pagelayer = false;
	$is_audio = false;
	
	// This IF is for Archives mainly as $post->ID is only the first post in the archive 
	// and we need to make sure that other posts are pagelayer or not
	if(!empty($GLOBALS['wp_query']->posts) && is_array($GLOBALS['wp_query']->posts)){
		foreach($GLOBALS['wp_query']->posts as $v){
			if(get_post_meta($v->ID , 'pagelayer-data')){
				$is_pagelayer = true;
			}
			
			if(preg_match('/(\[pl_audio|pagelayer\/pl_audio)/is', $v->post_content)){
				$is_audio = true;
			}
		}
	}

	// Enqueue the FRONTEND CSS
	if((!empty($post->ID) && get_post_meta($post->ID , 'pagelayer-data')) || $is_pagelayer || $force){

		// We dont need the auto <p> and <br> as they interfere with us
		remove_filter('the_content', 'wpautop');
		
		// No need to add curly codes to the content
		remove_filter('the_content', 'wptexturize');

		pagelayer_load_shortcodes();
		$pagelayer->cache['enqueue_frontend'] = true;
		
		// Load the global styles
		add_action('wp_head', 'pagelayer_global_js', 2);
		
		$premium_js = '';
		$premium_css = '';
		if(defined('PAGELAYER_PREMIUM')){
			$premium_js = ',chart.min.js,premium-frontend.js,shuffle.min.js';
			$premium_css = ',premium-frontend.css';
			
			// Load this For audio widget
			if($is_audio || pagelayer_is_live_iframe()){
				wp_enqueue_script('wp-mediaelement');
				wp_enqueue_style( 'wp-mediaelement' );
				$pagelayer->sc_audio_enqueued = 1;
			}
		}
		
		if(pagelayer_enable_giver()){
		
			$write = '';
			
			// Dev mode - Dynamic JS and CSS
			if(defined('PAGELAYER_DEV') && !empty(PAGELAYER_DEV)){
				$write = '&write=1';
			}
			
			// Enqueue our Editor's Frontend JS
			wp_register_script('pagelayer-frontend', PAGELAYER_JS.'/givejs.php?give=pagelayer-frontend.js,nivo-lightbox.min.js,wow.min.js,jquery-numerator.js,simpleParallax.min.js,owl.carousel.min.js&premium='.$premium_js.$write, array('jquery'), PAGELAYER_VERSION);
		
			// Get list of enabled icons
			$icons_css = '';
			$icons = pagelayer_enabled_icons();
			foreach($icons as $icon){
				$icons_css .= ','.$icon.'.min.css';
			}

			wp_register_style('pagelayer-frontend', PAGELAYER_CSS.'/givecss.php?give=pagelayer-frontend.css,nivo-lightbox.css,animate.min.css,owl.carousel.min.css,owl.theme.default.min.css'.$icons_css.'&premium='.$premium_css.$write, array(), PAGELAYER_VERSION);
		
		// Static Files
		}else{
			
			wp_register_script('pagelayer-frontend', PAGELAYER_JS.'/combined'.(!empty($premium_js) ? '.premium' : '').'.js', array('jquery'), PAGELAYER_VERSION);

			wp_register_style('pagelayer-frontend', PAGELAYER_CSS.'/combined'.(!empty($premium_css) ? '.premium' : '').'.css', array(), PAGELAYER_VERSION);
		}
		
		wp_enqueue_script('pagelayer-frontend');
		wp_enqueue_style('pagelayer-frontend');
		
		// Load the global styles
		add_action('wp_head', 'pagelayer_global_styles', 5);
		add_filter('body_class', 'pagelayer_body_class', 10, 2);
		
		// Load custom widgets
		do_action('pagelayer_custom_frontend_enqueue');

	}

}

// Load the google fonts
add_action('wp_footer', 'pagelayer_enqueue_fonts', 5);
function pagelayer_enqueue_fonts($suffix = '-header'){
	
	global $pagelayer;

	if(empty($pagelayer->cache['enqueue_frontend'])){
		return;
	}
	
	$url = [];
	$cst = [];

	foreach($pagelayer->css as $k => $set){
	
		// Fetch body font if given
		if(!empty($set['font-family'])){
			
			$val = $set['font-family'];			
			$font_weight = empty($set['font-weight']) ? 400 : $set['font-weight'];
			$font_style = empty($set['font-style']) ? 'normal' : $set['font-style'];
			$font_style = in_array($font_style, ['italic', 'oblique']) ? 'i' : '';
			
			$pagelayer->runtime_fonts[$val][$font_weight.$font_style] = $font_weight.$font_style;
			
		}
	
	}
		
	foreach($pagelayer->runtime_fonts as $font => $weights){
		if(strpos($font, '_plf')){
			if(!in_array($font, $pagelayer->fonts_sent)){
				$pagelayer->fonts_sent[] = $font;
				$cst[] = preg_replace('/_plf$/is', '', $font);
			}
		}else{
			$v = $font.':'.implode(',', $weights);
			if(!in_array($v, $pagelayer->fonts_sent)){
				$url[] = $v;
				$pagelayer->fonts_sent[] = $v;
			}
		}
	}
	
	// If no fonts are to be set, then we dont set
	if(!empty($url)){
		$fonts_url = 'https://fonts.googleapis.com/css?family='.rawurlencode(implode('|', $url));
		
		wp_register_style('pagelayer-google-font'.$suffix, $fonts_url, array(), PAGELAYER_VERSION);
		wp_enqueue_style('pagelayer-google-font'.$suffix);
		
		echo '<link rel="preload" href="'.$fonts_url.'" as="fetch" crossorigin="anonymous">';
	}

	if(empty($cst)){
		return;
	}
	
	$args = [
		'post_type' => PAGELAYER_FONT_POST_TYPE,
		'status' => 'publish',
		'post_name__in' => $cst
	];
	
	//var_dump($args);
	
	$query = get_posts($args);
	//var_dump($query);
	
	if(empty($query)){
		return;
	}
	
	foreach($query as $font){
		$meta_box_value = get_post_meta($font->ID, 'pagelayer_font_link', true);
		if(empty($meta_box_value)){
			continue;
		}
			
		echo '<style id="'.$font->post_name.'_plf" >@font-face { font-family: "'.$font->post_name.'_plf"'.'; src: url("'.$meta_box_value.'"); font-weight: 100 200 300 400 500 600 700 800 900;}</style>';
	}
}

// Load any header we have
function pagelayer_global_js(){
	global $pagelayer;
	
	$pagelayer_recaptch_lang = get_option('pagelayer_google_captcha_lang');

	echo '<script>
var pagelayer_ajaxurl = "'.admin_url( 'admin-ajax.php' ).'?";
var pagelayer_global_nonce = "'.wp_create_nonce('pagelayer_global').'";
var pagelayer_server_time = '.time().';
var pagelayer_is_live = "'.pagelayer_is_live().'";
var pagelayer_facebook_id = "'.get_option('pagelayer-fbapp-id').'";
var pagelayer_settings = '.json_encode($pagelayer->settings).';
var pagelayer_recaptch_lang = "'.(!empty($pagelayer_recaptch_lang) ? $pagelayer_recaptch_lang : '').'";
</script>';

}

// We need to handle global styles
function pagelayer_global_styles(){
	
	global $pagelayer;
	
	$styles = '<style id="pagelayer-global-styles" type="text/css">'.PHP_EOL;
	
	// Style for only child row holder
	$styles .= '.pagelayer-row-stretch-auto > .pagelayer-row-holder, .pagelayer-row-stretch-full > .pagelayer-row-holder.pagelayer-width-auto{ max-width: '.$pagelayer->settings['max_width'].'px; margin-left: auto; margin-right: auto;}'.PHP_EOL;
	
	if(!pagelayer_is_live()){
		
		// Set responsive value
		$styles .= '@media (min-width: '.($pagelayer->settings['tablet_breakpoint'] + 1).'px){
			.pagelayer-hide-desktop{
				display:none !important;
			}
		}

		@media (max-width: '.$pagelayer->settings['tablet_breakpoint'].'px) and (min-width: '.($pagelayer->settings['mobile_breakpoint'] + 1).'px){
			.pagelayer-hide-tablet{
				display:none !important;
			}
		}

		@media (max-width: '.$pagelayer->settings['mobile_breakpoint'].'px){
			.pagelayer-hide-mobile{
				display:none !important;
			}
		}'.PHP_EOL;

	}
	
	$screen_style['tablet'] = '';
	$screen_style['mobile'] = '';
	
	// PX suffix
	$pxs = ['font-size', 'letter-spacing', 'word-spacing'];
	$arrays = ['padding', 'margin'];
	
	// Loop CSS settings
	foreach($pagelayer->css as $k => $v){
		
		$r = [];
		
		foreach($pagelayer->css[$k] as $kk => $vv){
			
			if(empty($vv)){
				continue;
			}
			
			if(in_array($kk, $arrays)){
				$skel = [0, 0, 0, 0];
				$vv = array_replace($skel, $vv);
				$vv = implode('px ', $vv).'px';
			}
			
			$r[] = $kk.':'.$vv.(in_array($kk, $pxs) ? 'px' : '');
			
		}
		
		if(empty($r)){
			continue;
		}
		
		$matches = [];
		preg_match('/_(mobile|tablet)$/is', $k, $matches);
		$key = str_replace(['_mobile', '_tablet'], '', $k);
		$screen = @$matches[1];
		
		//echo $key.' - '.$k;pagelayer_print($matches);
		
		$params = $pagelayer->css_settings[$key];
		
		$sel = empty($params['sel']) ? ($key == 'body' ? '' : $key) : $params['sel'];
		
		$style = 'body.pagelayer-body '.$sel.'{'.implode(';', $r)."}\n";
		
		// Mobile or tablet ?
		if(!empty($screen)){
			$screen_style[$screen] .= $style;
		}else{
			$styles .= $style;
		}
	}

// Tablet Styles
$styles .= '@media (max-width: '.$pagelayer->settings['tablet_breakpoint'].'px){
	[class^="pagelayer-offset-"],
	[class*=" pagelayer-offset-"] {
		margin-left: 0;
	}

	.pagelayer-row .pagelayer-col {
		margin-left: 0;
		width: 100%;
	}
	.pagelayer-row.pagelayer-gutters .pagelayer-col {
		margin-bottom: 16px;
	}
	.pagelayer-first-sm {
		order: -1;
	}
	.pagelayer-last-sm {
		order: 1;
	}
	
'.$screen_style['tablet'].'
}'.PHP_EOL;

// Any mobile style ?
if(!empty($screen_style['mobile'])){	
	$styles .= '@media (max-width: '.$pagelayer->settings['mobile_breakpoint'].'px){
'.$screen_style['mobile'].'}'.PHP_EOL;
}
	
	$styles .= PHP_EOL.'</style>';
	
	// Lets just build a temporary list of fonts so that we can add prefetch !
	pagelayer_enqueue_fonts();
	
	if(!empty($pagelayer->runtime_fonts)){
		echo '<link rel="dns-prefetch" href="https://fonts.gstatic.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous">';
	}
	
	echo $styles;
}

function pagelayer_body_class($classes, $class){
	$classes[] = 'pagelayer-body';
	return $classes;
}

// Load the live editor if needed
add_action('wp_enqueue_scripts', 'pagelayer_load_live', 9999);
function pagelayer_load_live(){

	global $post, $pagelayer;

	$pagelayer->load_live_errors = array();

	// If its not live editing then stop
	if(!pagelayer_is_live_iframe($pagelayer->load_live_errors)){

		// Is it the live mode then lets throw an error ?
		if(pagelayer_optreq('pagelayer-iframe')){
			add_action('wp_head', 'pagelayer_load_live_errors', 999);
		}
		
		return;
	}

	// Are you allowed to edit ?
	if(!pagelayer_user_can_edit($post->ID)){
		return;
	}

	// Load the editor class
	include_once(PAGELAYER_DIR.'/main/live.php');

	// Call the constructor
	$pl_editor = new PageLayer_LiveEditor();

}

// Show the live errors if any
function pagelayer_load_live_errors(){
	
	global $post, $pagelayer;
	
	// Any errors ?
	if(empty($pagelayer->load_live_errors)){
		return;
	}
	
	echo '<script>
alert("'.str_replace('"', '\\"', implode("\n", $pagelayer->load_live_errors)).'");
</script>';
	
}

// If we are doing ajax and its a pagelayer ajax
if(wp_doing_ajax()){	
	include_once(PAGELAYER_DIR.'/main/ajax.php');
}

// Show the backend editor options
add_action('edit_form_after_title', 'pagelayer_after_title', 10);
function pagelayer_after_title(){

	global $post;
	
	// Get the current screen
	$current_screen = get_current_screen();
	
	// For gutenberg
	if(method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor()){

		// Add the code in the footer
		add_action('admin_footer', 'pagelayer_gutenberg_after_title');
		
		return;
	}
	
	// Is pagelayer supposed to edit this ?
	if(!pagelayer_user_can_edit($post)){
		return;
	}
	
	$link = pagelayer_shortlink($post->ID).'&pagelayer-live=1';

	echo '
<div id="pagelayer-editor-button-row" style="margin-top:15px; display:inline-block;">
	<a id="pagelayer-editor-button" href="'.$link.'" class="button button-primary button-large" style="height:auto; padding:6px; font-size:18px; display:flex; align-items:center;">
		<img src="'.PAGELAYER_URL.'/images/pagelayer-logo-40.png" width="24" style="margin-right:4px" /> <span>'.__('Edit with Pagelayer').'</span>
	</a>
</div>';

}

function pagelayer_gutenberg_after_title(){

	global $post;
	
	// Is pagelayer supposed to edit this ?
	if(!pagelayer_user_can_edit($post)){
		return;
	}
	
	$link = pagelayer_shortlink($post->ID).'&pagelayer-live=1';

	echo '
<div id="pagelayer-editor-button-row" style="margin-left:15px; display:none">
	<a id="pagelayer-editor-button" href="'.$link.'" class="button button-primary button-large" style="height:auto; padding:6px; font-size:18px; display:flex; align-items:center;">
		<img src="'.PAGELAYER_URL.'/images/pagelayer-logo-40.png" align="top" width="24" style="margin-right:4px"/> <span>'.__('Edit with Pagelayer').'</span>
	</a>
</div>

<script type="text/javascript">
jQuery(document).ready(function(){
	
	var pagelayer_timer;
	var pagelayer_button = function(){
		var button = jQuery("#pagelayer-editor-button-row");
		var g = jQuery(".edit-post-header-toolbar");
		if(g.length < 1){
			return;
		}
		button.detach();
		//console.log(button);
		g.parent().append(button);
		button.show();
		clearInterval(pagelayer_timer);
	}
	pagelayer_timer = setInterval(pagelayer_button, 100);
});
</script>';
	
}

// Handle Old Slug URL redirect for live link
add_filter( 'old_slug_redirect_url', 'pagelayer_old_slug_redirect', 10, 1);
function pagelayer_old_slug_redirect($link){
	
	if(pagelayer_optreq('pagelayer-live')){
		$link = add_query_arg('pagelayer-live', '1', $link);
	}
	
	return $link;
}

// Clone Post
add_action('admin_action_pagelayer_clone_post', 'pagelayer_clone_post');
function pagelayer_clone_post(){

	// Nonce verification
	check_admin_referer('pagelayer-options');
	
	if(!current_user_can('edit_posts')){
		wp_die('You don\'t have access to clone this post.');
	}

	// Get the original post id
	$post_id = (int) $_REQUEST['post'];
	$post = get_post( $post_id );
	
	// If post data exists, create the post clone
	if(empty($post)){
		wp_die('No post found');
	}
	
	$current_user = wp_get_current_user();
	$new_post_author = $current_user->ID;
	
	unset($post->ID);
	$post->post_author = $new_post_author;
	$post->post_name = '';
	$post->post_status = 'draft';
	$post->post_title = $post->post_title.' Clone';
	$post->post_date = '';
	$post->post_date_gmt = '';

	$new_post_id = wp_insert_post( $post );
	
	if(empty($new_post_id)){
		wp_die('Post creation failed, could not find original post: ' . $post_id);
	}

	// Get all current post terms and set them to the new post draft
	$taxonomies = get_object_taxonomies($post->post_type);
	foreach ($taxonomies as $taxonomy) {
		$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
		wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
	}

	// Clone all post meta info	
	$post_meta_infos = get_post_meta($post_id);
	if (count($post_meta_infos) > 0) {
		foreach ($post_meta_infos as $meta_key => $meta_value){
			add_post_meta($new_post_id, $meta_key, wp_slash( maybe_unserialize($meta_value[0]) ));
		}
	}

	// Finally, redirect to the edit post screen for the new draft
	wp_redirect( get_edit_post_link($new_post_id, '') );
	exit;
	
}

// Add the clone link to action list for post_row_actions
add_filter('post_row_actions', 'pagelayer_clone_post_link', 10, 2);
add_filter('page_row_actions', 'pagelayer_clone_post_link', 10, 2);
function pagelayer_clone_post_link( $actions, $post ) {
  if (current_user_can('edit_posts') && $post->post_status !== 'trash' && !defined('SITEPAD') && get_option('pagelayer_disable_clone') != 1) {
	  $actions['clone'] = '<a href="'.wp_nonce_url('admin.php?action=pagelayer_clone_post&post='.$post->ID, 'pagelayer-options') . '" rel="permalink">'.__( 'Clone', 'pagelayer') .'</a>';
  }
  return $actions;
}

add_filter( 'post_row_actions', 'pagelayer_quick_link', 10, 2 );
add_filter( 'page_row_actions', 'pagelayer_quick_link', 10, 2 );
function pagelayer_quick_link($actions, $post){
	global $pagelayer;
	
	// Some woocommerce pages are not having ID
	if(empty($post->ID)){
		return $actions;
	}
	
	// Is pagelayer supposed to edit this ?
	if(!pagelayer_user_can_edit($post)){
		return $actions;
	}

	$link = pagelayer_shortlink($post->ID).'&pagelayer-live=1';	

	$actions['pagelayer'] = '<a href="'.esc_url( $link ).'">'.__( 'Edit using Pagelayer', 'pagelayer') .'</a>';

	return $actions;
}

// Add settings link on plugin page
add_filter('plugin_action_links_pagelayer/pagelayer.php', 'pagelayer_plugin_action_links');
function pagelayer_plugin_action_links($links){
	
	if(!defined('PAGELAYER_PREMIUM')){
		 $links[] = '<a href="'.PAGELAYER_PRO_URL.'" style="color:#3db634;" target="_blank">'._x('Go Pro', 'Upgrade to Pagelayer Pro for many more features', 'pagelayer').'</a>';
	}

	$settings_link = '<a href="admin.php?page=pagelayer">Settings</a>';	
	array_unshift($links, $settings_link); 
	
	return $links;
}

// Add custom header
add_action('wp_head', 'pagelayer_add_custom_head');
function pagelayer_add_custom_head(){
	global $post;
	
	$global_code = wp_unslash( get_option('pagelayer_header_code') );

	if(!empty($post)){
		$header_code = get_post_meta($post->ID , 'pagelayer_header_code', true);
	}
	
	if(!empty($global_code)){
		echo $global_code."\n";
	}
	
	if(!empty($header_code)){
		echo $header_code."\n";
	}
		
}

// Add custom footer
add_action('wp_footer', 'pagelayer_add_custom_footer');
function pagelayer_add_custom_footer(){
	global $post;
		
	$global_code = wp_unslash( get_option('pagelayer_footer_code') );
	
	if(!empty($post)){
		$footer_code = get_post_meta($post->ID , 'pagelayer_footer_code', true);
	}
	
	if(!empty($global_code)){
		echo $global_code."\n";
	}
	
	if(!empty($footer_code)){
		echo $footer_code."\n";
	}
	
}

// Handle Logout Redirect here
add_action('wp_logout', 'pagelayer_after_logout');
function pagelayer_after_logout($user_id){
	
	// Get the URL
	$url = get_user_option('pagelayer_logout_url', $user_id);
	
	// Now blank it
	update_user_option($user_id, 'pagelayer_logout_url', '');
	
	// We will redirect if we have the given item set.
	if(!empty($url)){
		wp_redirect( $url );
		exit();
	}
	
}

// Replace Media
$media_replace = get_option( 'pagelayer_disable_media_replace');
if(empty($media_replace)){
	
// Add URL to Replace Meda 
add_filter('media_row_actions', 'pagelayer_add_media_action', 10, 2);
function pagelayer_add_media_action($actions, $post){
	
	$url = admin_url('upload.php');
	$url = add_query_arg(array(
		'page' => 'pagelayer_replace_media',
		'id' => $post->ID,
	), $url);
	
  	$actions['pagelayer_replace_media'] = '<a href="'.$url.'" rel="permalink">'.esc_html__('Replace media', 'pagelayer').'</a>';
	
  	return $actions;
	
}

}

// Replace Media Function
function pagelayer_replace_media(){
	
	include_once(PAGELAYER_DIR.'/main/replace-media.php');
	
	pagelayer_replace_page();

}

// Hide admin bar 
add_action( 'init', 'pagelayer_hide_admin_bar');
function pagelayer_hide_admin_bar(){
	
	// Is it the live mode ?
	if(!pagelayer_optreq('pagelayer-live', false) || !pagelayer_optreq('pagelayer-iframe', false)){
		return false;
	}

	show_admin_bar(false);
}

// Pagelayer Template Loading Mechanism
include_once(PAGELAYER_DIR.'/main/template.php');
