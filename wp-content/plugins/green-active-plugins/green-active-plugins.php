<?php
/*
Plugin Name: Green Active Plugins!
Description: Change your WP admin active plugin's color from light gray to green!
Author: r-a-y
Version: 1.1
Author URI: http://buddypress.org/community/members/r-a-y
*/

function ray_green_active_plugins() {
?>
	<style type="text/css">
	tr.active th, tr.active td {background-color:#E7F7D3 !important;}
	tr.inactive th, tr.inactive td, .plugins .inactive {background-color:#fff !important;}
	.plugin-update-tr .update-message {text-align:center;}
	</style>
<?php
}

add_action( 'admin_head-plugins.php', 'ray_green_active_plugins', 99 );

?>