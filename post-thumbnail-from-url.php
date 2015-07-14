<?php
   /*
   Plugin Name: Post Thumbnail From Url
   Plugin URI: http://michelesettembre.com/en/work/wordpress-plugin-post-thumbnail-from-url/
   Description: Post Thumbnail From URL is a plugin to import images using a public URL straight in your media library from the featured Image metabox in post edit page
   Version: 1.0
   Author: Michele Settembre
   Author URI: http://www.michelesettembre.com
   License: GPL2
   */

// funzione da lanciare con plugin attivato
class post_thumbnail_from_url {
	public function __construct(){
		register_activation_hook( __FILE__, array($this, 'plugin_activated' ));
		register_deactivation_hook( __FILE__, array($this, 'plugin_deactivated' ));
	}

	public function plugin_activated(){
		// This will run when the plugin is activated, setup the database
		add_option( 'Activated_Plugin', 'Plugin-Slug' );
	}

	public function plugin_deactivated(){
		// This will run when the plugin is deactivated, use to delete the database
		delete_option( 'Activated_Plugin' );
	}
}

$options = get_option( 'bcd_post_thumbnail_from_url_settings' );
if($options['bcd_post_thumbnail_from_url_activate'] == 1) {

	function bcd_enqueue_scripts() {
		wp_register_style( 'bcd_ptfu_admin_css', plugin_dir_url( __FILE__ ) . '/bcd_ptfu.css', false, '1.0.0' );
		wp_enqueue_style( 'bcd_ptfu_admin_css' );
		wp_register_style( 'bcd_ptfu_fontawesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', false);
		wp_enqueue_style( 'bcd_ptfu_fontawesome' );
		wp_enqueue_script( 'bcd_ptfu_script', plugin_dir_url( __FILE__ ) . 'bcd_ptfu.js' );
	}
	add_action( 'admin_enqueue_scripts', 'bcd_enqueue_scripts' );

	/*function admin_head_post_editing_and_adding() {

	}
	add_action( 'admin_head-post.php', 'admin_head_post_editing_and_adding' );
	add_action( 'admin_head-post-new.php',  'admin_head_post_editing_and_adding' );*/
}

function bcd_post_thumbnail_from_url_ajax_register_url() {
	$res = array();
	$data = array_merge($_GET, $_POST);
	$res['data'] = $data;
	if(isset($data['url']) && !empty($data['url'])) {
		media_sideload_image($data['url'], '', '', '');
		$res['message'] = 1;
	} else {
		$res['message'] = 0;
	}

	header( "Content-Type: application/json" );
	echo json_encode($res);
	ob_flush();
	exit;
}

add_action('wp_ajax_bcd_post_thumbnail_from_url_ajax_register_url', 'bcd_post_thumbnail_from_url_ajax_register_url');
add_action('wp_ajax_nopriv_bcd_post_thumbnail_from_url_ajax_register_url', 'bcd_post_thumbnail_from_url_ajax_register_url');

function bcd_post_thumbnail_from_url_ajax_get_language() {
	$res = array();
	$res['lang_001'] = __('Get a pic from the web:', 'post-thumbnail-from-url');
	$res['lang_002'] = __('The image has been successfully got, open the media library and select it', 'post-thumbnail-from-url');
	$res['lang_003'] = __('An error occurred, please try again later', 'post-thumbnail-from-url');

	header( "Content-Type: application/json" );
	echo json_encode($res);
	ob_flush();
	exit;
}

add_action('wp_ajax_bcd_post_thumbnail_from_url_ajax_get_language', 'bcd_post_thumbnail_from_url_ajax_get_language');
add_action('wp_ajax_nopriv_bcd_post_thumbnail_from_url_ajax_get_language', 'bcd_post_thumbnail_from_url_ajax_get_language');


// specific page in settings menu
function bcd_post_thumbnail_from_url_menu() {
	add_options_page( __('Post thumbnail from URL options', 'post-thumbnail-from-url'), __('Post Thumbnail from URL', 'post-thumbnail-from-url'), 'manage_options', 'bcd-post-thumbnail-from-url-plugin', 'bcd_post_thumbnail_from_url_pageloader');
}
add_action('admin_menu', 'bcd_post_thumbnail_from_url_menu');

// Add settings link on plugin page
function bcd_plugin_settings_link($links) {
	$settings_link = '<a href="options-general.php?page=bcd-post-thumbnail-from-url-plugin">' . __('Settings', 'post-thumbnail-from-url') . '</a>';
	array_unshift($links, $settings_link);
	return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter('plugin_action_links_' . $plugin, 'bcd_plugin_settings_link' );

// localization
function ptfu_load_textdomain() {
	load_plugin_textdomain( 'post-thumbnail-from-url', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}
add_action('plugins_loaded', 'ptfu_load_textdomain');

// registra le opzioni
function bcd_post_thumbnail_from_url_register_settings() {
    //this will save the option in the wp_options table as 'wpse61431_settings'
    //the third parameter is a function that will validate your input values
    register_setting('bcd_post_thumbnail_from_url_settings', 'bcd_post_thumbnail_from_url_settings', 'bcd_post_thumbnail_from_url_settings_validate');
}
add_action('admin_init', 'bcd_post_thumbnail_from_url_register_settings');

// validation function
function bcd_post_thumbnail_from_url_settings_validate($args) {
    //$args will contain the values posted in your settings form, you can validate them as no spaces allowed, no special chars allowed or validate emails etc.
    // esempio di regola con notifica di errore
	/*
	if(!isset($args['bcd_post_thumbnail_from_url_email']) || !is_email($args['bcd_post_thumbnail_from_url_email']))
	{
        //add a settings error because the email is invalid and make the form field blank, so that the user can enter again
        $args['bcd_post_thumbnail_from_url_email'] = '';
    	add_settings_error('bcd_post_thumbnail_from_url_settings', 'bcd_post_thumbnail_from_url_invalid_email', 'Please enter a valid email!', $type = 'error');
    }
	*/
    //make sure you return the args
    return $args;
}

//Display the validation errors and update messages
/*
 * Admin notices
 */
function bcd_post_thumbnail_from_url_admin_notices() {
   settings_errors();
}
add_action('admin_notices', 'bcd_post_thumbnail_from_url_admin_notices');

// the page
function bcd_post_thumbnail_from_url_pageloader() {
	?>
    <div class="wrap">
    	<h2><?php _e('Post thumbnail from URL Options page', 'post-thumbnail-from-url'); ?></h2>
        <p><?php _e('Active/Deactive the "post thumbnail from url" functionality and options', 'post-thumbnail-from-url'); ?></p>
    	<form action="options.php" method="post">
		    <?php
			settings_fields( 'bcd_post_thumbnail_from_url_settings' );
			do_settings_sections( __FILE__ );

			//get the older values, wont work the first time
			$options = get_option( 'bcd_post_thumbnail_from_url_settings' );
		    if(!isset($options['bcd_post_thumbnail_from_url_activate']) || $options['bcd_post_thumbnail_from_url_activate'] == '') {
			    $options['bcd_post_thumbnail_from_url_activate'] = 0;
		    }
			?>
		    <table width="100%">
			    <tbody>
				    <tr>
					    <th scope="row" align="left"><?php _e('Show to Form to get image from the web in the Post thumbnail Metabox'); ?></th>
					    <td>
						    <fieldset>
							    <label>
								    <input name="bcd_post_thumbnail_from_url_settings[bcd_post_thumbnail_from_url_activate]" type="radio" value="1" <?php
								    if(isset($options['bcd_post_thumbnail_from_url_activate']) && $options['bcd_post_thumbnail_from_url_activate'] == 1) {
									    echo'checked';
								    }
								    ?> /> <?php _e('Activate', 'post-thumbnail-from-url'); ?>
								    <input name="bcd_post_thumbnail_from_url_settings[bcd_post_thumbnail_from_url_activate]" type="radio" value="0" <?php
								    if(isset($options['bcd_post_thumbnail_from_url_activate']) && $options['bcd_post_thumbnail_from_url_activate'] == 0)  {
									    echo'checked';
								    }
								    ?> /> <?php _e('Deactivate', 'post-thumbnail-from-url'); ?>
							    </label>
						    </fieldset>
					    </td>
				    </tr>
			    </tbody>
		    </table>
		    <table width="100%">
			    <tbody>
			        <tr>
				        <td>
	                        <input type="submit" class="button button-primary button-large pull-right" value="<?php _e('Save', 'post-thumbnail-from-url'); ?>" />
			            </td>
			        </tr>
			    </tbody>
		    </table>
		</form>
    </div>
    <?php
}
?>