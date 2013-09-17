<?
/*
Plugin Name: M7 Customize
Plugin URI: http://mach7enterprises.com
Description: Customizing items over wordpress. This starts as a blank plugin with just the basics. Plugin is specific to each site it runs on.
Version: 0.1
Author: Tyson Brooks
Author URI: http://mach7enterprises.com
*/
//$current_user = wp_get_current_user(); $username = $current_user->user_login; $useremail = $current_user->user_email; $firstname = $current_user->user_firstname; $lastname = $current_user->user_lastname; $displayname = $current_user->display_name; $userid = $current_user->ID; $role=get_user_role($userid);


// Get Current User Role
function get_user_role($uid) {
		global $wpdb;
		$role = $wpdb->get_var("SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'wp_capabilities' AND user_id = {$uid}");
		  if(!$role) return 'non-user';
			$rarr = unserialize($role);
			$roles = is_array($rarr) ? array_keys($rarr) : array('non-user');
			return $roles[0];
}


// Replace that annoying Howdy!
function replace_howdy( $wp_admin_bar ) {
 $my_account=$wp_admin_bar->get_node('my-account');
 $newtitle = str_replace( 'Howdy,', 'Hello, ', $my_account->title );
 $wp_admin_bar->add_node( array(
 'id' => 'my-account',
 'title' => $newtitle,
 ) );
 }
 add_filter( 'admin_bar_menu', 'replace_howdy',25 );

 
// Remove stuff from the admin bar.
function m71_remove_admin_stuff() {
	$current_user = wp_get_current_user(); $userid = $current_user->ID; $role=get_user_role($userid);

	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('wp-logo');
	//$wp_admin_bar->remove_menu('site-name');
	$wp_admin_bar->remove_menu('view-site');
	$wp_admin_bar->remove_menu('updates');
	$wp_admin_bar->remove_menu('comments');
	$wp_admin_bar->remove_menu('wp-admin-bar-dashboard');
	if ($role == 'subscriber' || $role == 'chatroom_moderator') {
		$wp_admin_bar->remove_node( 'new-post' );
	}
if ($role!='administrator') {
		add_action('after_setup_theme', 'remove_admin_bar');
			function remove_admin_bar() {
				show_admin_bar(false);
			}
	}
}
add_action( 'wp_before_admin_bar_render', 'm71_remove_admin_stuff' );

// Show Author only their posts & media in media manager.
function posts_for_current_author($query) {
	$current_user = wp_get_current_user(); $userid = $current_user->ID; $role=get_user_role($userid);
	if ($role!='administrator') {
	global $user_level;
	if($query->is_admin && $user_level < 5) {
		global $user_ID;
		$query->set('author',  $user_ID);
		unset($user_ID);
	}
	unset($user_level);
	return $query;
}
}
add_filter('pre_get_posts', 'posts_for_current_author');


// Change the title of Posts to Advertisments
function change_post_menu_label() {
global $menu;
global $submenu;
$menu[5][0] = 'Advertisments';
$submenu['edit.php'][5][0] = 'Advertisments';
$submenu['edit.php'][10][0] = 'New Advertisment';
$submenu['edit.php'][16][0] = 'Advertisments Tags';
echo '';
}
function change_post_object_label() {
global $wp_post_types;
$labels = &$wp_post_types['post']->labels;
$labels->name = 'Advertisments';
$labels->singular_name = 'Advertisments';
$labels->add_new = 'New Advertisment';
$labels->add_new_item = 'New Advertisment';
$labels->edit_item = 'Edit Advertisment';
$labels->new_item = 'New Advertisment';
$labels->view_item = 'View Advertisment';
$labels->search_items = 'Search Advertisments';
$labels->not_found = 'Not found';
$labels->not_found_in_trash = 'Not found in trash';
 }
add_action( 'init', 'change_post_object_label' );
add_action( 'admin_menu', 'change_post_menu_label' );


// Change lable of Posts in admin bar to Advertisements
function change_post_admin_bar_label() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#wp-admin-bar-new-post > a').text('Advertisment');
        });
    </script>
    <?php
}
add_action( 'wp_after_admin_bar_render', 'change_post_admin_bar_label' );

// Remove pages from menu - by role
function m7_remove_menu_pages() {
	$current_user = wp_get_current_user(); $userid = $current_user->ID; $role=get_user_role($userid);
	if ($role == 'subscriber' || $role == 'chatroom_moderator') {
	remove_menu_page('edit.php');
	}
}
add_action( 'admin_menu', 'm7_remove_menu_pages' ); 
  
  
// Add Custom Dashboard Widget to Dashboard
function my_custom_dashboard_widgets() { 

global $wp_meta_boxes; 

}
add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets'); 

function custom_dashboard_help() { 
	$current_user = wp_get_current_user(); $userid = $current_user->ID; $role=get_user_role($userid);

echo '<p>Welcome to Embroidery Advertisers!<br/>If you need any help please feel free to email me, contact@embroideryadvertisers.com<br/>';
echo 'Dont forget to join us in our live chat room! You can find it under Members Area on the front page.';

echo '</p>'; 
}

// Admin footer modification - Remove powered by Wordpress
function m7_remove_footer_admin() {
    echo '<span id="footer-thankyou">Developed by <a href="http://mach7enterprises.com" target="_blank">Mach7 Enterprises</a></span>';
	
}
add_filter('admin_footer_text', 'm7_remove_footer_admin');


// Remove boxes from posts menu

function filterPosts() { 
$current_user = wp_get_current_user(); $userid = $current_user->ID; $role=get_user_role($userid);

if ($role!='administrator') {
	?>
	<style type="text/css">
	/**** Hides Menu Items ****/
	#menu-dashboard {
	display:none;
	}
	/**** Hides boxes in Post Menu ******/
	#wpac_controls_meta, #formatdiv, #categorydiv, #graphene_custom_meta, #expirationdatediv, #postimagediv, #eMember_sectionid, #page-links-to, #tagsdiv-post_tag  {
	display:none;
	}
	
	/**** Hides buttons in Posts box *******/
	#content_insertdate, #content_inserttime, #content_anchor, #content_wpEstoreButton, #content_wp_help {
	display:none;
	}
	</style>
<? }
}
add_action('admin_footer_text','filterPosts');


