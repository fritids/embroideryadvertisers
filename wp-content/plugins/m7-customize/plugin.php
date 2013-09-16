<?
/*
Plugin Name: M7 Customize
Plugin URI: http://mach7enterprises.com
Description: Customizing items over wordpress.
Version: 0.1
Author: Tyson Brooks
Author URI: http://mach7enterprises.com
*/
//$current_user = wp_get_current_user(); $username = $current_user->user_login; $useremail = $current_user->user_email; $firstname = $current_user->user_firstname; $lastname = $current_user->user_lastname; $displayname = $current_user->display_name; $userid = $current_user->ID; $role=get_user_role($userid);

function get_user_role($uid) {
		global $wpdb;
		$role = $wpdb->get_var("SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'wp_capabilities' AND user_id = {$uid}");
		  if(!$role) return 'non-user';
			$rarr = unserialize($role);
			$roles = is_array($rarr) ? array_keys($rarr) : array('non-user');
			return $roles[0];
}

function replace_howdy( $wp_admin_bar ) {
 $my_account=$wp_admin_bar->get_node('my-account');
 $newtitle = str_replace( 'Howdy,', 'Hello, ', $my_account->title );
 $wp_admin_bar->add_node( array(
 'id' => 'my-account',
 'title' => $newtitle,
 ) );
 }
 add_filter( 'admin_bar_menu', 'replace_howdy',25 );

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
if ($role=='subscriber') {
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
show_admin_bar(false);
}

	?>
	<script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#wp-admin-bar-site-name > a').text('Back to <? echo get_bloginfo('sitename');?>');
        });
    </script>
	<?
	}
}
add_action( 'wp_before_admin_bar_render', 'm71_remove_admin_stuff' );


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

/*
add_action( 'init', 'create_post_type' );
function create_post_type() {
	register_post_type( 'craft_project',
		array(
			'labels' => array(
				'name' => __( 'Craft Projects' ),
				'singular_name' => __( 'Craft Project' ),
				'menu_position' => '5'
			),
		'public' => true,
		'has_archive' => true,
		)
	);
}
*/
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




/*
function m7_remove_admin_stuff() {
	$current_user = wp_get_current_user(); $userid = $current_user->ID; $role=get_user_role($userid);
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('wp-logo');
//	$wp_admin_bar->remove_menu('view-site');
//	$wp_admin_bar->remove_menu('updates');
//	$wp_admin_bar->remove_menu('comments');
	if ($role == 'subscriber' || $role == 'chatroom_moderator') {
	$wp_admin_bar->remove_node( 'new-post' );
	}
}
add_action( 'wp_before_admin_bar_render', 'm7_remove_admin_stuff' );
*/

//adminbar//
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


 function m7_remove_menu_pages() {
	$current_user = wp_get_current_user(); $userid = $current_user->ID; $role=get_user_role($userid);
	//remove_submenu_page('index.php', 'update-core.php');
	if ($role == 'subscriber' || $role == 'chatroom_moderator') {
	remove_menu_page('edit.php');
	//remove_menu_page('edit.php?post_type=craft_blog');
	}
}
add_action( 'admin_menu', 'm7_remove_menu_pages' ); 
  
add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets'); 
function my_custom_dashboard_widgets() { 

global $wp_meta_boxes; 
wp_add_dashboard_widget('custom_help_widget', 'Welcome to Embroidery Advertisers', 'custom_dashboard_help');
//wp_add_dashboard_widget('custom_chat_widget', 'Embroidery Advertisers Chat', 'custom_dashboard_chat'); 
} 
function custom_dashboard_help() { 
	$current_user = wp_get_current_user(); $userid = $current_user->ID; $role=get_user_role($userid);

echo '<p>Welcome to Embroidery Advertisers!<br/>If you need any help please feel free to email me, contact@embroideryadvertisers.com<br/>';
echo 'Dont forget to join us in our live chat room! You can find it under Members Area on the front page.';
//echo '<br/><br/>role: '.$role;
echo '</p>'; 
}
/*
function custom_dashboard_chat() { 
	$current_user = wp_get_current_user(); $username = $current_user->user_login;

echo '<iframe src="http://lightirc.com/start/?host=irc.mach7enterprises.com&autojoin=%23embroideryadvertisers%2C%23ea-mods&showServerWindow=false&styleURL=css%2Flightblue.css&nick='.$username.'&policyPort=9024" style="width:800px; height:400px;"></iframe>'; 
}*/

// Admin footer modification
function m7_remove_footer_admin() {
    echo '<span id="footer-thankyou">Developed by <a href="http://mach7enterprises.com" target="_blank">Mach7 Enterprises</a></span>';
	
}
add_filter('admin_footer_text', 'm7_remove_footer_admin');



add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );

function my_show_extra_profile_fields( $user ) { 
	$current_user = wp_get_current_user(); if (empty($_GET['user_id'])) {$userid = $current_user->ID; } else { $userid=$_GET['user_id'];} $role=get_user_role($userid);
	if ($role == 'administrator' || $role == 'chatroom_moderator') {
?>
	<h3>Extra profile information</h3>
	<table class="form-table">
		<tr>
			<th><label for="chatpass">Chat Password</label></th>
			<td>
				<input type="text" name="chatpass" id="chatpass" value="<?php echo esc_attr( get_the_author_meta( 'chatpass', $user->ID ) ); ?>" class="regular-text" readonly /><br />
				<span class="description">This is your chat password. (Automatically generated by your website password.)</span>
			</td>
		</tr>
	</table>
<?php }}
add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

function my_save_extra_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
	if (!empty($_POST['pass1'])) { $pass=$_POST['pass1'];} else { $pass='';}
	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
	update_usermeta( $user_id, 'chatpass', $pass );
}


function allowAuthorEditing(){
  add_post_type_support( 'craft_project', 'author' );
}
add_action('init','allowAuthorEditing');
