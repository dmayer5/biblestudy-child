<?php

function total_child_enqueue_parent_theme_style()
{

    // Dynamically get version number of the parent stylesheet (lets browsers re-cache your stylesheet when you update your theme)
    $theme = wp_get_theme('Total');
    $version = $theme->get('Version');

    // Load the stylesheet
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css', array(), $version);

}

add_action('wp_enqueue_scripts', 'total_child_enqueue_parent_theme_style');


// Outputs Google Analytics
function cbc_google_analytics() {
	?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-169341409-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-169341409-2');
</script>
	<?php
}
add_action('wp_head', 'cbc_google_analytics',99);



//WYSIWYG for Inline Notes
function inline_notes_js(){
    wp_enqueue_script('tinymce', get_stylesheet_directory_uri() . '/js/tinymce/tinymce.min.js', array('jquery'), '', true);
}
add_action('wp_enqueue_scripts', 'inline_notes_js');

function tinymce_inline_notes(){ ?>
<script>
    // add other options here that you like just NO block elements
    // var toolbarItems = 'undo redo cut copy paste | bold italic underline | bullist numlist | backcolor forecolor | code';
    var toolbarItems = 'undo redo | bold italic underline | backcolor forecolor';

    tinymce.init({
        selector: '.ldin-notes-form > textarea',
        menubar: false,
        statusbar: false,
        //plugins: 'paste code lists',
        plugins: 'paste',
        paste_auto_cleanup_on_paste : true,
        paste_as_text: true,
        paste_remove_styles: true,
        paste_remove_styles_if_webkit: true,
        paste_strip_class_attributes: true,
        toolbar: toolbarItems,
        valid_elements: "*",
        force_br_newlines : true,
  		force_p_newlines : false,
  		forced_root_block : '', // Needed for 3.x
  		remove_linebreaks: false,
        extended_valid_elements : "span[style]",
        mobile: {
            menubar: false,
            toolbar: toolbarItems
        },
        setup: function (editor) {
            editor.on('init', function (e) {
                var content = " ";
                var existingNote = document.getElementById(editor.id.replace('ldin-notes-field','current-notes')).innerHTML;
                if (existingNote.trim().length > 0) {
                    content = existingNote;
                }
                editor.setContent(content);
            });

            editor.on("change keyup", function (e) {
                console.log('saving');
                tinyMCE.triggerSave(); // updates all instances
                // editor.save(); // updates this instance's textarea
                jQuery(editor.getElement()).trigger('change'); // for garlic to detect change
            });
        }
    });

</script>

<?php }
add_action( 'wp_footer', 'tinymce_inline_notes', 99 );

/* START Stop removing div tags from WordPress - Linklay
function ikreativ_tinymce_fix( $init )
{
	  
    // html elements being stripped
    $init['extended_valid_elements'] = 'div[*]';

    // pass back to wordpress
    return $init;
}
add_filter('tiny_mce_before_init', 'ikreativ_tiny_mce_fix');
// END Stop removing div tags from WordPress - Linklay*/


function my_custom_scripts(){	
   global  $post;	
    wp_enqueue_script('custom-js', get_stylesheet_directory_uri(), array('jquery'), '', true);	
        if ( 'sfwd-lessons' === get_post_type() ) {	
			if ( wp_script_is( 'selectify-js', 'enqueued' ) ) {	
				return;	
			} else {	
				wp_register_script('selectify-js', get_stylesheet_directory_uri() . '/js/selectify.js', array('jquery'), '', true);	
				wp_enqueue_script( 'selectify-js' );	
				wp_register_script('autoexpand-js', get_stylesheet_directory_uri() . '/js/auto-expand.js', array('jquery'), '', true);	
				wp_enqueue_script( 'autoexpand-js' );
				wp_localize_script('selectify-js', 'selectify_vars', array(	
					'postID' => $post->ID	
				)	
			);	
				wp_enqueue_style('selectify-style', get_stylesheet_directory_uri() . '/css/selectify.css', array(), 1.1);	
			}	
    }	
}	
add_action('wp_enqueue_scripts', 'my_custom_scripts');


//Adds the Open Graph in the Language Attributes
function add_opengraph_doctype( $output ) {
        return $output . ' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"';
    }
add_filter('language_attributes', 'add_opengraph_doctype');
 
//Lets add Open Graph Meta Info
 
function insert_fb_in_head() {
    global $post;
    if ( !is_singular()) //if it is not a post or a page
        return;
       // echo '<meta property="fb:app_id" content="Your Facebook App ID" />';
        echo '<meta property="og:title" content="Online Bible Study | Compass Bible Church "/>';
        echo '<meta property="og:type" content="article"/>';
        echo '<meta property="og:url" content="' . get_permalink() . '"/>';
		echo '<meta property="og:description" content="The various ministry bible studies are designed to get you personally connected on a weekly basis with other Christians in order to help you advance in your understanding and application of biblical themes that are critically important in the Christian life." />';
        echo '<meta property="og:site_name" content="Compass Bible Church Online Study"/>';
    if(!has_post_thumbnail( $post->ID )) { //the post does not have featured image, use a default image
        $default_image="https://compassbible.study/wp-content/uploads/2020/09/social-share.png"; //replace this with a default image on your server or an image in your media library
        echo '<meta property="og:image" content="' . $default_image . '"/>';
    }
    else{
        $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
        echo '<meta property="og:image" content="' . esc_attr( $thumbnail_src[0] ) . '"/>';
    }
    echo "
";
}
add_action( 'wp_head', 'insert_fb_in_head', 5 );


// Adds Version Number to Stylesheet, to help with Caching, will be removed at launch
add_action( 'wp_enqueue_scripts', function() {

	if ( ! defined( 'WPEX_THEME_STYLE_HANDLE' ) ) {
		return;
	}

	// First de-register the main child theme stylesheet
	wp_deregister_style( WPEX_THEME_STYLE_HANDLE );

	// Then add it again, using filemtime for the version number so everytime the child theme changes so will the version number
	wp_register_style( WPEX_THEME_STYLE_HANDLE, get_stylesheet_uri(), array(), filemtime( get_stylesheet_directory() . '/style.css' ) );

	// Finally enqueue it again
	wp_enqueue_style( WPEX_THEME_STYLE_HANDLE );
} );


// Adds Proxima Nova Font to Customizer
function wpex_add_custom_fonts()
{
    return array('proxima-nova');
}
// Proxima Nova Font Import
function proxima_nova_font()
{
    echo '<link rel="stylesheet" href="https://use.typekit.net/mbb7cps.css">';
}
	add_action('wp_head', 'proxima_nova_font');


// Universal Logo
function my_header_logo_img_url( $image ) {
    // Change image for your front page
        $image = '/wp-content/uploads/2020/06/cbc-logo.png';   
    // Return logo image
    return $image;
}
add_filter( 'wpex_header_logo_img_url', 'my_header_logo_img_url' );

function my_custom_logo_url( $url ) {
    $url = '/';
    return $url;
}
add_filter( 'wpex_logo_url', 'my_custom_logo_url' );

	

// Last Session Script
function cbc_last_session() {
if(is_user_logged_in()):?>
<script>
    jQuery.ajax({
        type: "POST",
        url: '/last_visited_tracking.php',
        data: {
            'user_id' : '<?php echo get_current_user_id() ?>',
            'user_visiting' : window.location.href,
        },
        dataType: 'html'
    })
    .done(function(data) {
        // log data to the console so we can see
        console.log(data); 
        // here we will handle errors and validation messages
    });
</script>
<?php endif;?>
<script>
    if(window.location.pathname == "/member-login/") {
        var url_string =  window.location.href;
        var url = new URL(url_string);
        var c = url.searchParams.get("refer");
        if(c != ""){
            setCookie('referreer', c, 1)
        }
        else{
            eraseCookie('referreer') ;
        }
    }
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
    function eraseCookie(name) {   
        document.cookie = name+'=; Max-Age=-99999999;';  
    }
</script>
<?php
}
add_action('wp_footer', 'cbc_last_session');


// show admin bar only for admins & editors
function remove_admin_bar() {
if (!current_user_can('edit_posts') && !is_admin()) {
  show_admin_bar(false);
}
}
add_action('after_setup_theme', 'remove_admin_bar');


// Redirect non-admin & editors users to My Account
function redirect_non_admin_users() {
	if ( ! current_user_can( 'edit_posts') && '/wp-admin/admin-ajax.php' != $_SERVER['PHP_SELF'] ) {
		 wp_redirect( site_url( '/studies/' ) ); 
		exit;
	}
}
add_action( 'admin_init', 'redirect_non_admin_users' );

// Removes Menu items for Editors
function remove_menus(){
// get current login user's role
$roles = wp_get_current_user()->roles;
 
// test role
if( !in_array('editor',$roles)){
return;
}
 
//remove menu from site backend.
remove_menu_page( 'edit.php' ); //Posts
remove_menu_page( 'edit-comments.php' ); //Comments
remove_menu_page( 'users.php' ); //Users
remove_menu_page( 'tools.php' ); //Tools
remove_menu_page( 'profile.php' ); //Profile
remove_menu_page( 'options-general.php' ); //Settings
remove_menu_page( 'admin.php?page=vc-welcome' ); //Visual Composer
remove_menu_page('/weekend/edit-tags.php?taxonomy=category&post_type=groups'); // Groups
remove_menu_page('edit.php?post_type=sfwd-question'); // Custom post type 2
}
add_action( 'admin_menu', 'remove_menus' , 100 );

// Redirects non-members to Member Login Page 
function my_template_redirect()
{
	global $current_user;

	$okay_pages = array( 5, 'member-login', 'register','lostpassword', 'resetpass');
	
	
	//if the user doesn't have a membership, send them home				
	if(!$current_user->ID 

		&& !is_page($okay_pages) 
		&& !strpos($_SERVER['REQUEST_URI'], "login"))
	{		
		wp_redirect("/member-login/?redirect_to=" . urlencode($_SERVER['REQUEST_URI']));
	}	

}
add_action('template_redirect', 'my_template_redirect');

// Login Redirect
if ( ! function_exists( 'my_pmpro_login_redirect_url_for_members' ) ) {
	function my_pmpro_login_redirect_url_for_members( $redirect_to, $request, $user ) {
		if ( pmpro_url( 'account' ) === $redirect_to ) {
			if(isset($_COOKIE['referreer']) && $_COOKIE['referreer'] != 'null'):
				$redirect_to = sanitize_url( $_COOKIE['referreer']);
			else:
				if( get_user_meta($user -> ID, 'user_last_visited', true)){
					$redirect_to = get_user_meta($user -> ID, 'user_last_visited', true);
				}
				else{
					$redirect_to = home_url( 'studies' );
				}
			endif;
		}
		return $redirect_to;
	}
}
if ( ! has_filter( 'pmpro_login_redirect_url', 'my_pmpro_login_redirect_url_for_members' ) ) {
	add_filter( 'pmpro_login_redirect_url', 'my_pmpro_login_redirect_url_for_members', 10, 3 );
}


// Redirect to home after logout
add_action('wp_logout','go_home');
function go_home(){
  wp_redirect( '/member-login/' );
  exit();
}


// Login/Register Page Redirect
$blog_id = get_current_blog_id();
  if ( $blog_id == 1 ) {
function login_check()
{
    if ( is_user_logged_in() && is_page(array(5, 6, 7 )) ){
        wp_redirect( site_url( '/studies/') );
        exit;
    }
}
add_action('wp', 'login_check'); 
}

/* Logged in User Homepage Page Redirect
$blog_id = get_current_blog_id();
  if (( $blog_id == 1 ) ) {
function home_logged_in_check(){
 if ( is_user_logged_in() && is_page(5) ){
	 $redirect_to = get_user_meta($user -> ID, 'user_last_visited');
wp_redirect($redirect_to );
 }

}
add_action('template_redirect', 'home_logged_in_check'); 
}
*/

// Gravity Forms PMP Connector
add_filter("pmpro_login_redirect", "__return_false");

function my_pmpro_default_registration_level($user_id) {
	//Give all members who register membership level 1
	pmpro_changeMembershipLevel(1, $user_id);
}
add_action('user_register', 'my_pmpro_default_registration_level');


// Scripture Popup
function cbc_crossref() {
?>

<script>
	var refTagger = {
		settings: {
			bibleReader: "bible.faithlife",
			bibleVersion: "ESV",
			noSearchClassNames: ["h1","h2","h3","h4","a","span.passage-entry",".passage-entry"],
			noSearchTagNames: [],
			roundCorners: true,
			socialSharing: [],
			customStyle : {
				heading: {
					backgroundColor : "#f2f2f2",
					color : "#4E7992",
					fontFamily : "Arial, 'Helvetica Neue', Helvetica, sans-serif",
					fontSize : "16px"
				},
				body   : {
					color : "#303030",
					fontFamily : "Arial, 'Helvetica Neue', Helvetica, sans-serif",
					fontSize : "14px"
				}
			}
		}
	};
	(function(d, t) {
		var g = d.createElement(t), s = d.getElementsByTagName(t)[0];
		g.src = "//api.reftagger.com/v2/RefTagger.js";
		s.parentNode.insertBefore(g, s);
	}(document, "script"));
</script>

<?php
}
add_action('wp_footer', 'cbc_crossref', 10, 1);


// Line Dot Shortcode
function cbc_line_dot()
{
    ob_start();
    echo '<div class="line-dot"></div>';

    return ob_get_clean();
}
add_shortcode('line-dot', 'cbc_line_dot');


// Gravity Forms Tab Fix
add_filter('gform_tabindex', 'gform_tabindexer', 10, 2);
function gform_tabindexer($tab_index, $form = false)
{
    $starting_index = 1000; // if you need a higher tabindex, update this number
    if ($form)
        add_filter('gform_tabindex_' . $form['id'], 'gform_tabindexer');
    return GFCommon::$tab_index >= $starting_index ? GFCommon::$tab_index : $starting_index;
}



		/**
		Below Are Visual Composer Modules for the Lesson Pages:
		
		*Title/Description- This is user for Bible Study lessons that are seperated by day. For Eample Day 1 would be the title and Read 1 Thessalonians 1:1 would be the description
		
		*Question- This is where all the questions are added. Each question will have a Add Note inside of it. There are no limits on how many questions can be added.
		
		*Digging Deeper- This is for all digging deeper content. Typically a link to a website or a note for the user.
		
		*Leader Notes- This will be used for Leaders only. In order to display on the front end the user must be a leader, this is done through the membership plugin, PaidMembershipsPro and the ID for the user is 2.
		
		
		 **/


// VC Module for Title/Description
if ( ! class_exists( 'CBC_Titles_Module' ) ) {

	class CBC_Titles_Module {

		/**
		 * Main constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			
			// Registers the shortcode in WordPress
			add_shortcode( 'CBC_Titles_Module', array( 'CBC_Titles_Module', 'output' ) );

			// Map shortcode to Visual Composer
			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'CBC_Titles_Module', array( 'CBC_Titles_Module', 'map' ) );
			}

		}

		/**
		 * Shortcode output
		 *
		 * @since 1.0.0
		 */
		public static function output( $atts, $content = null ) {

			// Extract shortcode attributes (aka your module settings)
			extract( vc_map_get_attributes( 'CBC_Titles_Module', $atts ) );

			// Define output
			
			
    $html = '
   <div class="lesson-day-section clr">
     
        <div class="lesson-day-title"><h2><span class="ticon ticon-book"></span> ' . $label . '</h2></div>
         
        <div class="lesson-day-description">' . $description . '</div>
     
    </div>';      
     
    return $html;
     


		}

		/**
		 * Map shortcode to VC
		 *
		 * This is an array of all your settings which become the shortcode attributes ($atts)
		 * for the output. See the link below for a description of all available parameters.
		 *
		 * @since 1.0.0
		 * @link  https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=38993922
		 */
		public static function map() {
			return array(
				'name'        => esc_html__( 'Lesson Day Title w/ Description', 'locale' ),
				'description' => esc_html__( 'Displays the Title/Description', 'locale' ),
				'base'        => 'CBC_Veress_Module',
				'params'      => array(

				
							array(
								'type' => 'textfield',
								'heading' => __( 'Title', 'total' ),
								'param_name' => 'label',
								'admin_label' => false,
							),
							array(
								'type' => 'textfield',
								'heading' => __( 'Description', 'total' ),
								'param_name' => 'description',
								'admin_label' => false,
							
						),
					
				),
			);
		}

	}

}
new CBC_Titles_Module;



// VC Module for Bible Verses
if ( ! class_exists( 'CBC_Verse_Module' ) ) {

	class CBC_Verse_Module {

		/**
		 * Main constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			
			// Registers the shortcode in WordPress
			add_shortcode( 'CBC_Verse_Module', array( 'CBC_Verse_Module', 'output' ) );

			// Map shortcode to Visual Composer
			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'CBC_Verse_Module', array( 'CBC_Verse_Module', 'map' ) );
			}

		}

		/**
		 * Shortcode output
		 *
		 * @since 1.0.0
		 */
		public static function output( $atts, $content = null ) {

			// Extract shortcode attributes (aka your module settings)
			extract( vc_map_get_attributes( 'CBC_Verse_Module', $atts ) );

			// Define output
			
			
    $html = '
   <div class="bible-verse-section clr">
     
        <div class="bible-verse-title"><h2><span class="ticon ticon-bookmark-o"></span> Bible Verse: ' . $label . '</h2></div>
         
        <div class="bible-verse-description">' . $description . '</div>
     
    </div>';      
     
    return $html;
     


		}

		/**
		 * Map shortcode to VC
		 *
		 * This is an array of all your settings which become the shortcode attributes ($atts)
		 * for the output. See the link below for a description of all available parameters.
		 *
		 * @since 1.0.0
		 * @link  https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=38993922
		 */
		public static function map() {
			return array(
				'name'        => esc_html__( 'Bible Verse', 'locale' ),
				'description' => esc_html__( 'Displays the bible verse of choice', 'locale' ),
				'base'        => 'CBC_Verse_Module',
				'params'      => array(

				
							array(
								'type' => 'textfield',
								'heading' => __( 'Bible Verse Title', 'total' ),
								'param_name' => 'label',
								'admin_label' => false,
							),
							array(
								'type' => 'textarea',
								'heading' => __( 'Bible Verse', 'total' ),
								'param_name' => 'description',
								'admin_label' => false,
							
						),
					
				),
			);
		}

	}

}
new CBC_Verse_Module;


	
	
// VC Module for Digging Deeper Module
if ( ! class_exists( 'CBC_Digging_Deeper_Module' ) ) {

	class CBC_Digging_Deeper_Module {

		/**
		 * Main constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			
			// Registers the shortcode in WordPress
			add_shortcode( 'CBC_Digging_Deeper_Module', array( 'CBC_Digging_Deeper_Module', 'output' ) );

			// Map shortcode to Visual Composer
			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'CBC_Digging_Deeper_Module', array( 'CBC_Digging_Deeper_Module', 'map' ) );
			}

		}

		/**
		 * Shortcode output
		 *
		 * @since 1.0.0
		 */
		public static function output( $atts, $content = null ) {

			// Extract shortcode attributes (aka your module settings)
			extract( vc_map_get_attributes( 'CBC_Digging_Deeper_Module', $atts ) );

			// Define output
			
			
    $html = '
  <div class="digging-deeper-section"><h2><span class="ticon ticon-search"></span> Digging Deeper</h2>
         
        <div class="digging-deeper-content">' . $content . '</div>
     
    </div>';      
     
    return $html;
     


		}

		/**
		 * Map shortcode to VC
		 *
		 * This is an array of all your settings which become the shortcode attributes ($atts)
		 * for the output. See the link below for a description of all available parameters.
		 *
		 * @since 1.0.0
		 * @link  https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=38993922
		 */
		public static function map() {
			return array(
				'name'        => esc_html__( 'Digging Deeper', 'locale' ),
				'description' => esc_html__( 'Displays the Digging Deeper Content', 'locale' ),
				'base'        => 'CBC_Digging_Deeper_Module',
				'params'      => array(

				
						
							array(
								'type' => 'textarea_html',
								'heading' => __( 'Description', 'total' ),
								'param_name' => 'content',
								'admin_label' => false,
							
						),
					
				),
			);
		}

	}

}
new CBC_Digging_Deeper_Module;



	// VC Module for Leader Notes Module

if ( ! class_exists( 'CBC_Leader_Module' ) ) {

	class CBC_Leader_Module {

		/**
		 * Main constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			
			// Registers the shortcode in WordPress
			add_shortcode( 'CBC_Leader_Module', array( 'CBC_Leader_Module', 'output' ) );

			// Map shortcode to Visual Composer
			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'CBC_Leader_Module', array( 'CBC_Leader_Module', 'map' ) );
			}

		}

		/**
		 * Shortcode output
		 *
		 * @since 1.0.0
		 */
		public static function output( $atts, $content = null ) {

			// Extract shortcode attributes (aka your module settings)
			extract( vc_map_get_attributes( 'CBC_Leader_Module', $atts ) );

			// Define output
					global $current_user;
			$leaders = (array) vc_param_group_parse_atts( $leaders );
			$level = pmpro_getMembershipLevelForUser($current_user->ID);

				if ($level->id ==2) {
	
			
    $html = '
<div class="leader-notes-section"><h2><span class="ticon ticon-users"></span> Leader Notes</h2>
         
        <div class="leader-notes-content">' . $content . '</div>
     
    </div>';      
     
    return $html;
     }
		


		}

		/**
		 * Map shortcode to VC
		 *
		 * This is an array of all your settings which become the shortcode attributes ($atts)
		 * for the output. See the link below for a description of all available parameters.
		 *
		 * @since 1.0.0
		 * @link  https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=38993922
		 */
		public static function map() {
			return array(
				'name'        => esc_html__( 'Leader Notes', 'locale' ),
				'description' => esc_html__( 'Displays the Leader Notes Content', 'locale' ),
				'base'        => 'CBC_Leader_Module',
				'params'      => array(

				
						
							array(
								'type' => 'textarea_html',
								'heading' => __( 'Description', 'total' ),
								'param_name' => 'content',
								'admin_label' => false,
							
						),
					),
				
			);
		}

	}

}
new CBC_Leader_Module;

