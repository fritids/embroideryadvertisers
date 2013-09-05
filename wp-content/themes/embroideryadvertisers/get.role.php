<?
    require (ABSPATH . WPINC . '/pluggable.php');
    global $userdata;
    get_currentuserinfo();

	function get_user_role($uid) {
		global $wpdb;
		$role = $wpdb->get_var("SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'wp_capabilities' AND user_id = {$uid}");
		  if(!$role) return 'non-user';
			$rarr = unserialize($role);
			$roles = is_array($rarr) ? array_keys($rarr) : array('non-user');
			return $roles[0];
	}
$role=get_user_role($userdata->ID);
?>
