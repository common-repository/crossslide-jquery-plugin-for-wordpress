<?php
/*
Plugin Name: Cross Slide Cross Fade
Plugin URI: http://thisismyurl.com/plugins/crossslide-jquery-plugin-for-wordpress/
Description: Adds the Cross Slide for WordPress effect to websites.
Author: Christopher Ross
Author URI: http://thisismyurl.com
Version: 2.0.5
*/

/**
 * Cross Slide Cross Fade core file
 *
 * This file contains all the logic required for the plugin
 *
 * @link		http://wordpress.org/extend/plugins/cross-slide/
 *
 * @package 	Cross Slide Cross Fade
 * @copyright	Copyright (c) 2008, Chrsitopher Ross
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License, v2 (or newer)
 *
 * @since 		Cross Slide Cross Fade 1.0
 */





// plugin definitions
load_plugin_textdomain( 'csj',false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
register_activation_hook( __FILE__, 'thisismyurl_wp_crossslide_activate' );

function thisismyurl_wp_crossslide_head() {
	wp_enqueue_script("jquery");
	wp_register_script( 'thisismyurl-crossslide-js', WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '' ,plugin_basename( __FILE__ ) ) . 'jquery.cross-slide.js' );
	wp_enqueue_script( 'thisismyurl-crossslide-js' );
}
add_action( 'wp_print_styles', 'thisismyurl_wp_crossslide_head' );

/**
 * Legacy Call to thisismyurl_wp_crossslide()
 */
function cr_wp_crossslide() {
	thisismyurl_wp_crossslide();
}

function thisismyurl_wp_crossslide() {




	$options = get_option( 'thisismyurl_csj' );

	if ( empty( $options ) )
		thisismyurl_wp_crossslide_activate();

	$slideshow = $options->slideshow;
	if ( count( $slideshow ) > 0 ) {
		foreach ( $slideshow as $header ) {
			$pics[] = $header;

		}
	}

	if ( $pics ) {

		if ( $options->csj_random == 1 )
			shuffle( $pics );

		foreach ( $pics as $pic ) {
			$crossslide_piclist .= "{ src: '".$pic."' },";
			if( $count == 0 ) {$first = $pic; $count++;}
			$randx=rand(80,100);
			$randy=rand(0,100);
			$randz=rand(0,9);
			$thisismyurl_kenburns .= "{
							src:  '".$pic."',
							alt:  '',
							from: '100% ".$randy."% 2x',
							to:   '".$randx."% ".$randy."% 2.".$randz."x',
							time: ".$options->csj_sleep."
						  },";


		}
	}

	$crossslide_piclist =  substr( $crossslide_piclist, 0, strlen( $crossslide_piclist )-1 );
	$thisismyurl_kenburns =  substr( $thisismyurl_kenburns, 0, strlen( $thisismyurl_kenburns )-1 );

	echo "<div style='background=( url:$first ); width: ". $options->csj_width ."px;height:". $options->csj_height ."px;' id='thisismyurl_slideshow'>";

	if ( $options->csj_kenburns != 1 ) { ?>
		<script type="text/javascript">
		//<!--
			var $j = jQuery.noConflict();

			$j( function(){
				$j( '#thisismyurl_slideshow' ).crossSlide( {
				  sleep: <?php echo $options->csj_sleep; ?>,
				  fade: <?php echo $options->csj_fade; ?>
				}, [

			<?php echo $crossslide_piclist;?>

			] );
			} );
		// -->
		</script>

	<?php } else { ?>
		<script type="text/javascript">

		var $j = jQuery.noConflict();

		$j( '#thisismyurl_slideshow' ).crossSlide( {
		  fade: <?php echo $options->csj_fade; ?>
		}, [
		  <?php echo $thisismyurl_kenburns;?>
		], function( idx, img, idxOut, imgOut ) {
		  if ( idxOut == undefined )
		  {
			// starting single image phase, put up caption
			$j( 'div.caption' ).text( img.alt ).animate( { opacity: .7 } )
		  }
		  else
		  {
			// starting cross-fade phase, take out caption
			$j( 'div.caption' ).fadeOut()
		  }
		} );
		// -->
		</script>
	<?php
	}
		echo "</div>";
}


function thisismyurl_wp_crossslide_activate() {

	$options = get_option( 'thisismyurl_csj' );

	if ( empty( $options ) ) {
		$options->csj_width = 800;
		$options->csj_height = 200;
		$options->csj_sleep = 5;
		$options->csj_fade = 1;
		$options->slideshow = $header;
		update_option( 'thisismyurl_csj',$options );
	}
}

// add menu to WP admin
function thisismyurl_csj_menu() {
	add_options_page( __( 'Cross Slide' ), __( 'Cross Slide' ), 10,'thisismyurl_csj.php', 'thisismyurl_csj_options' );
}
add_action( 'admin_menu', 'thisismyurl_csj_menu' );


// add plugin functions
function thisismyurl_csj_plugin_actions( $links, $file ){
	static $this_plugin;

	if( !$this_plugin )
		$this_plugin = plugin_basename( __FILE__ );

	if( $file == $this_plugin ){
		$new_links = array( '<a href="options-general.php?page=thisismyurl_csj.php">' . _( 'Settings' ) . '</a>',
						   '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=paypal@thisismyurl.com&lc=US&button%20_subtype=services">' . _( 'Donate' ) . '</a>'
						);
		$links = array_merge( $new_links, $links );


	}
	return $links;
}
add_filter( 'plugin_action_links', 'thisismyurl_csj_plugin_actions', 10, 2 );


function thisismyurl_csj_add_scripts_head(){
	if ( $_GET['page'] == 'thisismyurl_csj.php' ) {
	?>

	<script type="text/javascript">
	//<![CDATA[
	addLoadEvent = function( func ){if( typeof jQuery!="undefined" )jQuery( document ).ready( func );else if( typeof wpOnload!='function' ){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
	var userSettings = {
			'url': '/',
			'uid': '2',
			'time':'1296327223'
		},
		ajaxurl = '/wp-admin/admin-ajax.php',
		pagenow = 'settings_page_thisismyurl_csj',
		typenow = '',
		adminpage = 'settings_page_thisismyurl_csj',
		thousandsSeparator = ',',
		decimalPoint = '.',
		isRtl = 0;
	//]]>
	</script>
	<link rel='stylesheet' href='/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load=dashboard,plugin-install,global,wp-admin&amp;ver=030f653716b08ff25b8bfcccabe4bdbd' type='text/css' media='all' />
	<script type='text/javascript'>
	/* <![CDATA[ */
	var quicktagsL10n = {
		quickLinks: "( Quick Links )",
		wordLookup: "Enter a word to look up:",
		dictionaryLookup: "Dictionary lookup",
		lookup: "lookup",
		closeAllOpenTags: "Close all open tags",
		closeTags: "close tags",
		enterURL: "Enter the URL",
		enterImageURL: "Enter the URL of the image",
		enterImageDescription: "Enter a description of the image"
	};
	try{convertEntities( quicktagsL10n );}catch( e ){};
	/* ]]> */
	</script>
	<script type='text/javascript' src='/wp-admin/load-scripts.php?c=1&amp;load=jquery,utils,quicktags&amp;ver=b50ff5b9792a9e89a2e131ad3119a463'></script>

<?php
    }
}

// add scripts to header
add_filter( 'admin_head', 'thisismyurl_csj_add_scripts_head', 10, 2 );




function thisismyurl_csj_options( $options = NULL ) {

	$options = get_option( 'thisismyurl_csj' );

	if ( empty( $options ) )
		thisismyurl_wp_crossslide_activate();


	// save page options
	if ( $_GET['page'] == 'thisismyurl_csj.php' && isset( $_POST['csj_width'] ) ) {

		$options->csj_width = 				$_POST['csj_width'];
		$options->csj_height = 				$_POST['csj_height'];
		$options->csj_fade = 				$_POST['csj_fade'];
		$options->csj_sleep = 				$_POST['csj_sleep'];
		$options->csj_kenburns = 			$_POST['csj_kenburns'];
		$options->csj_random = 				$_POST['csj_random'];

		$slideshow = $options->slideshow;

		if ( $_POST['upload_image'] )
			$slideshow[] = $_POST['upload_image'];

		$options->slideshow = $slideshow;

		update_option( 'thisismyurl_csj',$options );
		$options = get_option( 'thisismyurl_csj' );
	}


	// delete slideshow images
	if ( $_GET['page'] == 'thisismyurl_csj.php' && isset( $_GET['slideshow_delete'] ) ) {

		if ( $options->slideshow ) {

			foreach ( $options->slideshow as $slideshow_url ) {

				if ( urldecode( $_GET['slideshow_delete'] ) != $slideshow_url )
					$new_slideshow[] = $slideshow_url;

			}

			$options->slideshow  = $new_slideshow;
			update_option( 'thisismyurl_csj',$options );
			$options = get_option( 'thisismyurl_csj' );
		}
	}
?>

   <div class="wrap">

	<a href="http://thisismyurl.com/" id="cross-icon" style="background: url( '<?php echo WP_PLUGIN_URL .'/'.str_replace( basename( __FILE__ ),"",plugin_basename( __FILE__ ) );?>/icon.png' ) no-repeat;" class="icon32"><br /></a>
	<h2><a href="http://thisismyurl.com/"><?php _e( 'Cross Slide for WordPress by Christopher Ross','csj' ) ?></a></h2>

	<p><?php _e( 'The CrossSlide jQuery plugin for WordPress is designed to quickly add the JS and CSS requirements to operate the jQuery slideshow. Japanese, German and French language support.','csj' ) ?></p>

	<form action='options-general.php?page=thisismyurl_csj.php' method='POST'>

	<h3><?php _e( 'Cross Slide','csj' ) ?></h3>

		<p><label><?php _e( 'Width','csj' ) ?>: <input name="csj_width" type="text" id="csj_width" value="<?php echo $options->csj_width; ?>" /></label></p>
		<p><label><?php _e( 'Height','csj' ) ?>: <input name="csj_height" type="text" id="csj_height" value="<?php echo $options->csj_height; ?>" /></label></p>
		<p><label><?php _e( 'Sleep','csj' ) ?>: <input name="csj_sleep" type="text" id="csj_sleep" value="<?php echo $options->csj_sleep; ?>" /></label></p>
		<p><label><?php _e( 'Fade','csj' ) ?>: <input name="csj_fade" type="text" id="csj_fade" value="<?php echo $options->csj_fade; ?>" /></label></p>


		<p><label><input name="csj_kenburns" type="checkbox" value="1" <?php if ( $options->csj_kenburns == '1' ) { echo "checked";}?> />&nbsp;<?php _e( 'Ken Burns','csj' ) ?></label></p>


		<p><label><input name="csj_random" type="checkbox" value="1" <?php if ( $options->csj_random == '1' ) { echo "checked";}?> />&nbsp;<?php _e( 'Random','csj' ) ?></label></p>

	<h3><?php _e( 'Images','csj' ) ?></h3>

		<p><?php _e( 'Please add your image files below.','csj' ); ?></p>

		<?php
		// loop headers here
		$slideshow = $options->slideshow;
		if ( count( $slideshow ) > 0 ) {
			foreach ( $slideshow as $slideshow_url ) {
				echo '<div style="margin: 10px; padding: 10px; width: ' . $options->csj_width . 'px; height: ' . $options->csj_height . 'px; background: url(' . $slideshow_url . ') no-repeat;"><a style="padding: 5px; border: solid 1px #123456; background: #fff; " href="options-general.php?page=thisismyurl_csj.php&slideshow_delete=' . urlencode( $slideshow_url ) . '">x</a></div>';
			}
		}
		?>

	<input id="upload_image" type="text" size="80" name="upload_image" value="" />
	<input id="upload_image_button" type="button" value="Upload Image" />

	<p>
	<input style='margin-bottom: 20px;' type="submit" class="button-primary" value="<?php _e( 'Save Changes','csj' ) ?>" />
	</p>
	</form>


<h3><?php _e( 'Help support this plugin', 'csj' );?></h3>
<p><?php _e( 'Please show your support for this plugin', 'csj' );?></p>

<ul style='padding: 5px 5px 15px 5px; list-style: square; margin-left: 20px;'>
	<li><a href="http://wordpress.org/extend/plugins/crossslide-jquery-plugin-for-wordpress/"><?php _e('Rate on WordPress.org','csj');?></a></li>
	<li><a href="http://twitter.com/thisismyurl"><?php _e('Following me on Twitter','csj');?></a></li>
	<li><a href="http://www.facebook.com/pages/thisismyurlcom/114745151907899"><?php _e('Becoming a Fan on Facebook','csj');?></a></li>
</ul>
<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=paypal@thisismyurl.com&lc=US&button%20_subtype=services"><img src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif"></a>


<?php
}


function thisismyurl_csj_scripts() {
	wp_enqueue_script( 'media-upload' );
	wp_enqueue_script( 'thickbox' );

	if ( $_GET['page'] == 'thisismyurl_csj.php' )  {
		wp_register_script( 'media-uploader-script', WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '' ,plugin_basename( __FILE__ ) ) . 'media-uploader-script.js', array( 'jquery','media-upload','thickbox' ) ) ;
		wp_enqueue_script( 'media-uploader-script' );
	}

}

function thisismyurl_csj_styles() {
	wp_enqueue_style( 'thickbox' );
}

add_action('admin_print_scripts', 'thisismyurl_csj_scripts');
add_action('admin_print_styles', 'thisismyurl_csj_styles');
