<?
function get_user_role($uid) {
		global $wpdb;
		$role = $wpdb->get_var("SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'wp_capabilities' AND user_id = {$uid}");
		  if(!$role) return 'non-user';
			$rarr = unserialize($role);
			$roles = is_array($rarr) ? array_keys($rarr) : array('non-user');
			return $roles[0];
}
	
add_action( 'admin_head-post-new.php', 'check_post_limit' );
function check_post_limit() {
    global $userdata;
    global $post_type;
    global $wpdb;    
    $role=get_user_role($userdata->ID);
	$day=date('Y-m-d', strtotime(' -7 day'));
    $week = date( $day.' 00:00:00', ( gmmktime() ));
	$today = date( 'Y-m-d 00:00:00', ( gmmktime() ));

	if ($role=='one_ad_wk') {
		$item_count = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_author = $userdata->ID AND post_modified > '$week'" );
		if( $item_count >= 1 ) { wp_die( "<style type='text/css'>#error-page{height:65px;padding-top:-95px;}</style><p>I'm sorry but our records show that you have reached your limit number of posts for this week.</p>" ); }
	} elseif ($role=='one_ad') {
		$item_count = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_author = $userdata->ID AND post_modified > '$today'" );
		if( $item_count >= 1 ) { wp_die( "<style type='text/css'>#error-page{height:65px;padding-top:-95px;}</style><p>I'm sorry but our records show that you have reached your limit number of posts for today.</p>" ); }
	} elseif ($role=='two_ads') {
		$item_count = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_author = $userdata->ID AND post_modified > '$today'" );
		if( $item_count >= 2 ) { wp_die( "<style type='text/css'>#error-page{height:65px;padding-top:-95px;}</style><p>I'm sorry but our records show that you have reached your limit number of posts for today.</p>" ); }
	} elseif ($role=='three_ads') {
		$item_count = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_author = $userdata->ID AND post_modified > '$today'" );
		if( $item_count >= 3 ) { wp_die( "<style type='text/css'>#error-page{height:45px;padding-top:-95px;}</style><p>I'm sorry but our records show that you have reached your limit number of posts for today.</p>" ); }
	}
	return;
}
?>
