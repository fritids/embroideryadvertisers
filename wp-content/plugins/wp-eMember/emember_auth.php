<?php
/**
 * @@name Auth
 * @description User Authentication API for eMember plugin.
 * @@access public
 * @@author nur hasan <nur858@gmail.com>
 */
class Emember_Auth{
   //authentication
   var $hasmore;
   var $loggedIn;
   var $userInfo;
   var $customUserInfo;
   var $errorMsg;
   var $sessionName;
   var $errorCode;
   
   //permission
    var $protection;
    var $user_membership_level;
    var $user_membership_level_name;
    var $protected_posts;
    var $protected_pages;
    var $protected_comments;
    var $protected_categories;
    var $loggedin_for_feed;
    var $config;
    var $is_post_visible;
    static  $_this;
    static function getInstance(){
    	if(empty(self::$_this)){
    		self::$_this = new Emember_Auth();
    		return self::$_this;
    	}        
    	return self::$_this;
    }
    private function  __construct(){
   	   //permission
        $this->is_post_visible = false;
        $this->protection = dbAccess::find(WP_EMEMBER_MEMBERSHIP_LEVEL_TABLE, " id='1' ");
        $this->user_membership_level = null;
        $this->my_pages = array();
        $this->my_posts = array();
        $this->my_comments = array();
        $this->my_categories = array();
        $protected = unserialize($this->protection->post_list);
        $this->protected_posts = is_bool($protected)? array(): (array)$protected;

        $protected = unserialize($this->protection->page_list);
        $this->protected_pages = is_bool($protected)? array(): (array)$protected;

        $protected = unserialize($this->protection->comment_list);
        $this->protected_comments = is_bool($protected)? array(): (array)$protected;
		
        $protected = unserialize($this->protection->attachment_list);
        $this->protected_attachments = is_bool($protected)? array(): (array)$protected;

        $protected = unserialize($this->protection->custom_post_list);
        $this->protected_custom_posts = is_bool($protected)? array(): (array)$protected;
		
        $protected = unserialize($this->protection->category_list);
        $this->protected_categories = is_bool($protected)? array(): (array)$protected;   
        $this->loggedin_for_feed = false;   	
   	   //authentication
        include_once('emember_config.php');
    	  $this->config = Emember_Config::getInstance();   	      	  
   	    $this->hasmore     = array();
        $this->loggedIn    = false;
        $this->password_reminder_block_added = 'no';
        $this->sessionName = 'wordpress.AUTH.eMember';
        $this->errorMsg    = '';
        $sess_id = session_id();
	    if(empty($sess_id))@session_start();
       dbAccess::delete(WP_EMEMBER_AUTH_SESSION_TABLE, 
       		' (UNIX_TIMESTAMP( \''.date('Y-m-d H:i:s').'\' ) - UNIX_TIMESTAMP(last_impression)) >1800 ');//remove invalid sessions.       
       $condition   = 'session_id = \'' . session_id() . '\'';       
       $sessionInfo = dbAccess::find(WP_EMEMBER_AUTH_SESSION_TABLE, $condition);
       $this->sessionInfo = $sessionInfo;
       $user_id   = null;
       if(!$sessionInfo){
       	  if(isset($_COOKIE['e_m_bazooka_' . COOKIEHASH])){
          $emember_config = Emember_Config::getInstance();
          if($emember_config->getValue('eMember_multiple_logins')){
               $condition = 'md5(user_id)=\'' . $_COOKIE['e_m_bazooka'] . '\' ORDER BY login_impression Limit 0,1';
               $session  = dbAccess::find(WP_EMEMBER_AUTH_SESSION_TABLE, $condition);
               if($session&& ($session->session_id!==session_id())){
                   $this->loggedIn = false;
                   $this->userInfo = null;
                   $this->errorMsg = EMEMBER_ALREADY_LOGGED_IN;
                   $_SESSION['eMember_login_status_msg'] = EMEMBER_ALREADY_LOGGED_IN;
                   $this->errorCode= 13;
                   setcookie('e_m_bazooka_' . COOKIEHASH, "", time()-3600, "/",COOKIE_DOMAIN);
                   return;                                   
               }
           }
           	 $e_m_bazooka_val = $_COOKIE['e_m_bazooka_' . COOKIEHASH];
           	 if(isset($_COOKIE['e_m_bazooka_un_' . COOKIEHASH])){//Check the additional username parameter too
           	 	$e_m_bazooka_un_val = $_COOKIE['e_m_bazooka_un_' . COOKIEHASH];
             	$condition = "md5(member_id)='.$e_m_bazooka_val.' AND md5(user_name)='.$e_m_bazooka_un_val.'";
           	 }
           	 else{
           	 	$condition = "md5(member_id)='.$e_m_bazooka_val.'";
           	 }
       	     $result = dbAccess::find(WP_EMEMBER_MEMBERS_TABLE_NAME, $condition);
      	     if(empty($result)){
       	         $not_logged_in = null;
      	     }
       	     else{
		         $_SESSION[$this->sessionName] = session_id();           
		         $sessionInfo = array('session_id'=>session_id(),
		                              'user_id'=>$result->member_id,
                                  	  'user_name'=>$result->user_name,
		                              'logged_in_from_ip'=>get_real_ip_addr(),
		                              'last_impression'=>current_time('mysql',1));
		         $this->sessionInfo = $sessionInfo;
		         dbAccess::insert(WP_EMEMBER_AUTH_SESSION_TABLE,$sessionInfo);
		         dbAccess::update(WP_EMEMBER_MEMBERS_TABLE_NAME,
		         		'member_id='.$result->member_id, 
		         		array('last_accessed_from_ip'=>get_real_ip_addr(),
		         				'last_accessed'=>current_time('mysql',1)));       	     	
       	         $user_id  = $result->member_id;
       	     }    	         	
       	  }
       	  else
       	  	  $user_id = null;       	  	  
       }
       else{
           $user_id = $sessionInfo->user_id;
       }
          
       if(empty($user_id)){
           $this->loggedIn = false;
           $this->userInfo = null;
           $this->errorMsg = EMEMBER_NOT_LOGGED_IN;
           //$_SESSION['eMember_login_status_msg'] = EMEMBER_NOT_LOGGED_IN;
           $this->errorCode= 1;
           return;       	
       }          
       $userInfo = dbAccess::find(WP_EMEMBER_MEMBERS_TABLE_NAME,'member_id='.$user_id);
       $this->userInfo = $userInfo;
                    
       if($userInfo){
       	   $this->setPermissions(); 
           if($userInfo->account_state==='inactive'){
               $this->loggedIn = false;
               $this->userInfo = null;
               $this->errorMsg = EMEMBER_ACCOUNT_INACTIVE;
               $_SESSION['eMember_login_status_msg'] = EMEMBER_ACCOUNT_INACTIVE;
               $this->errorCode= 3;
               return;
           }
           if($userInfo->account_state==='pending'){
               $this->loggedIn = false;
               $this->userInfo = null;
               $this->errorMsg = EMEMBER_ACCOUNT_PENDING;
               $_SESSION['eMember_login_status_msg'] = EMEMBER_ACCOUNT_PENDING;
               $this->errorCode= 3;
               return;
           }
           
           $allow_expired_account = $this->config->getValue('eMember_allow_expired_account');
           $account_upgrade_url = $this->config->getValue('eMember_account_upgrade_url');                  
           if($userInfo->account_state=='unsubscribed');// Nothing to do.
           if(wp_emember_is_subscription_expired($this->userInfo, $this->userInfo->primary_membership_level)){
               dbAccess::update(WP_EMEMBER_MEMBERS_TABLE_NAME,'member_id='.$userInfo->member_id, array('account_state'=>'expired'));               
               if(!$allow_expired_account){
                    $this->loggedIn = false;
                    $this->userInfo = null;
                    $this->errorCode= 8;
                    $this->errorMsg = EMEMBER_SUBSCRIPTION_EXPIRED_MESSAGE;
                    $_SESSION['eMember_login_status_msg'] = EMEMBER_SUBSCRIPTION_EXPIRED_MESSAGE;
                    return;                  
               }                              
           }
           $custom_field = $this->config->getValue('eMember_custom_field');           
           if($custom_field){
               $customUserInfo = dbAccess::find(WP_EMEMBER_MEMBERS_META_TABLE,'user_id='.$user_id. ' AND meta_key=\'custom_field\'');
               $this->customUserInfo = unserialize($customUserInfo->meta_value);
           }                     
           $this->loggedIn = true;
           $this->userInfo = $userInfo;           
           $this->errorMsg = EMEMBER_LOGGED_IN_AS . $this->userInfo->user_name;
           $_SESSION['eMember_login_status_msg'] = EMEMBER_LOGGED_IN_AS . $this->userInfo->user_name;
           $this->errorCode= 4;
           return;
       }
       $this->errorMsg = EMEMBER_NOT_LOGGED_IN;
       //$_SESSION['eMember_login_status_msg'] = EMEMBER_NOT_LOGGED_IN;
       $this->errorCode= 1;       
       $this->loggedIn = false;
       $this->userInfo = null;
   }
   function login($credentials/*$user, $pass,$remember*/){
       global $wpdb;      
       if(isset($credentials['user']))$credentials['user'] = strip_tags($credentials['user']);
       if(isset($credentials['pass']))$credentials['pass'] = strip_tags($credentials['pass']);
       if(isset($credentials['md5ed_id']))$credentials['md5ed_id'] = strip_tags($credentials['md5ed_id']);
       if($this->isLoggedIn()){ //preventing same thing happening another time.
            if(isset($credentials['user']) && ($credentials['user'] == $this->userInfo->user_name))return;
            if(isset($credentials['member_id'])&& ($credentials['member_id'] == $this->userInfo->member_id)) return;
            if(isset($credentials['md5ed_id'])&& ($credentials['md5ed_id'] == md5($this->userInfo->member_id))) return;
       }        
       if(isset($credentials['user'])&&isset($credentials['pass'])){
        eMember_log_debug("Emember: trying logging in :" . $credentials['user'],true);      
           if(empty($credentials['user']) || empty($credentials['pass'])){
               $this->loggedIn = false;
               $this->userInfo = null;
               $this->errorMsg = EMEMBER_USER_PASS_EMPTY;
               $_SESSION['eMember_login_status_msg'] = EMEMBER_USER_PASS_EMPTY;;
               $this->errorCode= 12;
               return;       	               
           }
           include_once(ABSPATH.WPINC.'/class-phpass.php');
           $wp_hasher = new PasswordHash(8, TRUE);
           $password  = $wp_hasher->HashPassword($credentials['pass']);       
           $user      = $wpdb->escape( trim($credentials['user']));
           $condition = " user_name= '$user' ";
           $session_cond =  " user_name= '$user' ";
       }
       else if (isset($credentials['md5ed_id'])){ 
           $condition = ' md5(member_id)=\'' . $credentials['md5ed_id'] . '\'';
           $session_cond = ' md5(user_id)=\'' . $credentials['md5ed_id'] . '\'';
           $this->loggedin_for_feed = true;
           eMember_log_debug("Emember: trying logging in member with md5ed id: " .  $credentials['md5ed_id'],true);      
       }
       else if (isset($credentials['member_id'])){
           $condition = ' member_id=\'' . $credentials['member_id'] . '\' ';
           $session_cond = ' user_id=\'' . $credentials['member_id'] . '\' ';
           eMember_log_debug("Emember: trying logging in member with member id: " .  $credentials['member_id'],true);      
       }
       $emember_config = Emember_Config::getInstance();
       if($emember_config->getValue('eMember_multiple_logins')){
           if(empty($session_cond)) 
                $session_cond = 'session_id != \''. session_id() . '\'';
           else
               $session_cond = $session_cond . ' AND session_id != \''. session_id() . '\'';
           dbAccess::delete(WP_EMEMBER_AUTH_SESSION_TABLE,$session_cond);
       }else{
           dbAccess::delete(WP_EMEMBER_AUTH_SESSION_TABLE,'session_id="' . session_id() . '"');
       }       
       $userInfo = array();
       if(isset($condition))$userInfo  = dbAccess::find(WP_EMEMBER_MEMBERS_TABLE_NAME, $condition);	          
       
       $this->userInfo = $userInfo;   
       if($userInfo){
       	   //$this->setPermissions();
               if(isset($credentials['pass'])){
                   if(!$wp_hasher->CheckPassword(trim($credentials['pass']),$userInfo->password)){
                       $this->loggedIn = false;
                       $this->userInfo = null;
                       $this->errorCode= 7;
                       $this->errorMsg = EMEMBER_WRONG_PASS;               
                       $_SESSION['eMember_login_status_msg'] = EMEMBER_WRONG_PASS;
                       return;
                   }
               }
           if($userInfo->account_state=='inactive'){
               $this->loggedIn = false;
               $this->userInfo = null;
               $this->errorCode= 3;
               $this->errorMsg = EMEMBER_ACCOUNT_INACTIVE;
               $_SESSION['eMember_login_status_msg'] = EMEMBER_ACCOUNT_INACTIVE;
               return;
           }
           if($userInfo->account_state=='pending'){
               $this->loggedIn = false;
               $this->userInfo = null;
               $this->errorCode= 3;
               $this->errorMsg = EMEMBER_ACCOUNT_PENDING;
               $_SESSION['eMember_login_status_msg'] = EMEMBER_ACCOUNT_PENDING;
               return;
           }

           $login_limit = $this->config->getValue('eMember_login_limit');
           $insert = false; 
           if(!empty($login_limit)){           	
               $query = "SELECT meta_value FROM " . WP_EMEMBER_MEMBERS_META_TABLE . 
                        " WHERE user_id = " . $userInfo->member_id . " AND meta_key = 'login_count'";
               $login_count = $wpdb->get_row($query);
               $login_count = unserialize(isset($login_count->meta_value)?$login_count->meta_value:"");
               if($login_count === false) $insert = true;
               if(isset($login_count[date('y-m-d')])) {
               	   $current_ip = get_real_ip_addr();
               	   if(!in_array($current_ip, $login_count[date('y-m-d')])){
	               	   if(count($login_count[date('y-m-d')])>=intval($login_limit)){
			               $this->loggedIn = false;
			               $this->userInfo = null;
			               $this->errorCode= 10;
			               $this->errorMsg = EMEMBER_LOGIN_LIMIT_ERROR;
                                       $_SESSION['eMember_login_status_msg'] = EMEMBER_LOGIN_LIMIT_ERROR;
			               return;	               	   	
	               	   }
	               	   array_push($login_count[date('y-m-d')], $current_ip);	               	   
	               	   $login_count[date('y-m-d')] = array_unique($login_count[date('y-m-d')]);
               	   }               	   
               }
               else{
               	   $login_count = array( date('y-m-d')=>array( get_real_ip_addr()));
               }   
               if($insert)
               	   $query =  "INSERT INTO " . WP_EMEMBER_MEMBERS_META_TABLE . "(user_id,meta_key,meta_value)".
               	             "VALUES(".$userInfo->member_id.", 'login_count', '".serialize($login_count)."')";
               else 
                   $query =  "UPDATE " . WP_EMEMBER_MEMBERS_META_TABLE . " SET meta_value = '" . serialize($login_count) . "'".
                             " WHERE user_id= " . $userInfo->member_id . " AND meta_key = 'login_count'";
               $wpdb->query($query);            
           }      
           
           $allow_expired_account = $this->config->getValue('eMember_allow_expired_account');
           $account_upgrade_url = $this->config->getValue('eMember_account_upgrade_url');
       
		   $this->setPermissions();
           if($userInfo->account_state=='unsubscribed');// Nothing to do.		   
           if(wp_emember_is_subscription_expired($this->userInfo, $this->userInfo->primary_membership_level)){
               dbAccess::update(WP_EMEMBER_MEMBERS_TABLE_NAME,'member_id='.$userInfo->member_id, array('account_state'=>'expired'));                              
               if(!$allow_expired_account){
                    $this->loggedIn = false;
                    $this->userInfo = null;
                    $this->errorCode= 8;
                    $this->errorMsg = EMEMBER_SUBSCRIPTION_EXPIRED_MESSAGE;
                    $_SESSION['eMember_login_status_msg'] = EMEMBER_SUBSCRIPTION_EXPIRED_MESSAGE;
                    return;                  
               }               
           }
           
	       if(isset($credentials['rememberme'])){	       	
		       	if ( version_compare(phpversion(), '5.2.0', 'ge') ) {
			         setcookie('e_m_bazooka_' . COOKIEHASH, md5($userInfo->member_id), time()+3600*24*7, "/", COOKIE_DOMAIN, is_ssl() ? true : false, true);
			         setcookie('e_m_bazooka_un_' . COOKIEHASH,md5($userInfo->user_name),time()+3600*6,"/",COOKIE_DOMAIN);
		       	}
		       	else{
				    $cookie_domain = COOKIE_DOMAIN;
				    if ( !empty($cookie_domain) )
						$cookie_domain .= '; HttpOnly';
			         setcookie('e_m_bazooka_' . COOKIEHASH, md5($userInfo->member_id), time()+3600*24*7, "/", $cookie_domain, is_ssl() ? true : false);
	           }
	       }
	       else{
	       		setcookie('e_m_bazooka_' . COOKIEHASH,md5($userInfo->member_id),time()+3600*6,"/",COOKIE_DOMAIN);
	       		setcookie('e_m_bazooka_un_' . COOKIEHASH,md5($userInfo->user_name),time()+3600*6,"/",COOKIE_DOMAIN);
	       }
           setcookie('eMember_in_use_' . COOKIEHASH,true,time()+3600*24*7,"/",COOKIE_DOMAIN);
		   if (function_exists('wp_cache_serve_cache_file')){//WP Super cache workaround
		       setcookie("comment_author_","eMember",time()+21600,"/",COOKIE_DOMAIN);
		   }
		              
           $_SESSION[$this->sessionName] = session_id();           
           $sessionInfo = array('session_id'=>session_id(),
                                'user_id'=>$userInfo->member_id,
                                'user_name'=>$userInfo->user_name,
                                'logged_in_from_ip'=>get_real_ip_addr(),
                                'login_impression'=>current_time('mysql',1),
                                'last_impression'=>current_time('mysql',1));
           $this->sessionInfo = $sessionInfo;
           dbAccess::insert(WP_EMEMBER_AUTH_SESSION_TABLE,$sessionInfo);
           dbAccess::update(WP_EMEMBER_MEMBERS_TABLE_NAME,'member_id='.$userInfo->member_id, 
           		array('last_accessed_from_ip'=>get_real_ip_addr(),'last_accessed'=>current_time('mysql',1)));
           $this->userInfo = $userInfo;
           $this->loggedIn = true; 
           //notify any other plugin listening for the member login event via WordPress action
		   do_action('eMember_login_complete');                 
       }
       else if(isset($credentials['md5ed_id'])){
       	   die(EMEMBER_NO_USER_KEY);
       }
       else{
           $this->errorMsg = EMEMBER_WRONG_USER_PASS;
           $_SESSION['eMember_login_status_msg'] = EMEMBER_WRONG_USER_PASS;
           $this->errorCode= 5;
           $this->loggedIn = false;
           $this->userInfo = null;
       }
   }
   function getSavedMessage($key){
       $msg = isset($_SESSION[$key])?$_SESSION[$key]: "";
       unset ($_SESSION[$key]);
       return $msg;
   }
   function logout(){
       if(!$this->isLoggedIn())return;
       setcookie("e_m_bazooka_" . COOKIEHASH, '', time()-60*60*24*7, "/",COOKIE_DOMAIN);
       setcookie('e_m_bazooka_un_' . COOKIEHASH, '', time()-60*60*24*7, "/", COOKIE_DOMAIN);
       setcookie('eMember_in_use_' . COOKIEHASH,'',time()-3600*24*7, "/",COOKIE_DOMAIN);
       dbAccess::delete(WP_EMEMBER_AUTH_SESSION_TABLE,'session_id="' . session_id() . '"');
       $this->loggedIn = false;
       $this->userInfo = null;
       $this->errorMsg = EMEMBER_LOGOUT_SUCCESS;
       $_SESSION['eMember_login_status_msg'] = EMEMBER_LOGOUT_SUCCESS;
       $this->errorCode= 6;
       $_SESSION['eMember_login_status_code'] = 6;       
//       nocache_headers();
       unset($_SESSION[$this->sessionName]);
       eMember_log_debug("Emember Logged out.",true);      
   }

   function silent_logout(){
       if(!$this->isLoggedIn())return;
       dbAccess::delete(WP_EMEMBER_AUTH_SESSION_TABLE,'session_id="' . session_id() . '"');
       $this->loggedIn = false;
       $this->userInfo = null;
       $this->errorMsg = "";
       $_SESSION['eMember_login_status_msg'] = "";
       $this->errorCode= 1;
       $_SESSION['eMember_login_status_code'] = 1;       
//       nocache_headers();
       unset($_SESSION[$this->sessionName]);
       eMember_log_debug("Emember Logged out from feed.",true);      
   }
   function getUserInfo($key, $default = ""){
       if(!$this->loggedIn){
           return false;
       }       
       if($key === "user_membership_level_name"){//Membership level name
    		return $this->user_membership_level_name;
       }
       if($key === "user_additional_membership_level_names"){
            $tbl = WP_EMEMBER_MEMBERSHIP_LEVEL_TABLE;
            global $wpdb;               
            if($this->userInfo->more_membership_levels){
                $names =  $wpdb->get_col("SELECT alias FROM $tbl WHERE id IN (" . $this->userInfo->more_membership_levels.")");     
                return implode(',', $names);
            }
            return "";            
       }    
       if($key === "profile_picture"){//member's profile pic embedded with class eMember_custom_profile_picture
    		return $this->getProfilePictureEmbeded();
       } 
       if($key === "profile_picture_src"){//member's profile picture raw image URL
    		return $this->getProfilePictureSrc();
       }    
       if($key === "member_expiry_date"){
       		return emember_get_exipiry_date();
       }                    
       if(isset($this->userInfo->$key)&&!empty($this->userInfo->$key)){
           return $this->userInfo->$key;
       }    
       $key = stripslashes($key);	         
	   $key = str_replace(array('\\','\'','(',')','[',']',' ','"', '%','<','>'), "_",$key);       
       if(isset($this->customUserInfo[$key])&&!empty($this->customUserInfo[$key]))
           return $this->customUserInfo[$key];           
       return $default;
   }
   function getProfilePictureSrc($member_id=""){
		if(!$this->loggedIn)return "";
   		if(empty($member_id)){
   			$member_id = $this->userInfo->member_id;
   		}
   		$emember_config = Emember_Config::getInstance();
		$use_gravatar = $emember_config->getValue('eMember_use_gravatar');
		$d = WP_EMEMBER_URL.'/images/default_image.gif';
		if($use_gravatar)
			return WP_EMEMBER_GRAVATAR_URL. "/" . md5(strtolower($this->userInfo->email)) . "?d=" . urlencode($d) . "&s=" . 96;		
		$image = $this->userInfo->profile_image;
	   	$upload_dir  = wp_upload_dir();
	    if(!empty($image))
	    	return $upload_dir['baseurl'] . '/emember/' . $image;  
	    return $d; 	
   }   
   function getProfilePictureEmbeded()
   {
   		$image_url = $this->getProfilePictureSrc();
   	 	$output .= '<img src="'.$image_url.'" alt="" class="eMember_custom_profile_picture" />';
   	 	return $output;
   }
   function isLoggedIn(){
       return $this->loggedIn;
   }
   function getMsg(){
       return $this->errorMsg;
   }
   function getCode(){
       return $this->errorCode;
   }
   
   function setPermissions($level = null){
   	   $level_info = array();	
   	   $current_level = isset($this->userInfo->membership_level)? $this->userInfo->membership_level : $level;
   	   //if(empty($current_level)) die("No Membership Level found for the user.");
       $my_level = dbAccess::find(WP_EMEMBER_MEMBERSHIP_LEVEL_TABLE, ' id=\'' .$current_level . '\' ');
       $this->userInfo->primary_membership_level = $my_level;
       $options  =  unserialize($my_level->options);          
       if(isset($options['promoted_level_id'])&&($options['promoted_level_id']!=-1)){       	          	  
       	   $current_subscription_starts = strtotime($this->userInfo->subscription_starts);	
       	   $more_levels = $this->userInfo->more_membership_levels;	
       	   $more_levels = is_array($more_levels)?array_filter($more_levels): $more_levels;				         	   								  									
       	   $sec_levels = explode(',', $more_levels);

       	   $current_time = time();
       	   while(1){
		        if($current_level === $options['promoted_level_id']) break;
 		        $promoted_after = trim($options['days_after']); 	
                if(empty($promoted_after)) break;

       	   		$d = ($promoted_after==1)? ' day':' days';
       	   		$expires = strtotime(" + " . abs($promoted_after) .$d,  $current_subscription_starts);  	   		       	   		       	  
       	   	    if($expires>$current_time) break;
       	   	    if(!isset($options['promoted_level_id'])||($options['promoted_level_id']==-1)) break;
       	   	    //$current_subscription_starts = $expires;
       	   		$sec_levels[] = $current_level;
       	   		$current_level = $options['promoted_level_id'];        	   		
		        $my_level = dbAccess::find(WP_EMEMBER_MEMBERSHIP_LEVEL_TABLE, ' id=\'' .$current_level . '\' ');
  		        $this->userInfo->primary_membership_level = $my_level;
 		        $options  =  unserialize($my_level->options); 		               	   		       	   	           	   	
       	   }
    	    if(($current_level!=-1)){        
    	    	$level_info ['membership_level'] =$current_level;
    	    	//$level_info ['current_subscription_starts'] = date('y-m-d', $current_subscription_starts);

    	    	if($this->config->getValue('eMember_enable_secondary_membership'))
    	    		$level_info['more_membership_levels'] = implode(',', array_unique($sec_levels));
    	    	$this->userInfo->membership_level = $current_level; 
    	    	
    	    	dbAccess::update(WP_EMEMBER_MEMBERS_TABLE_NAME,'member_id='.$this->userInfo->member_id, $level_info);
    	    	$this->userInfo->primary_membership_level = $my_level;
    	    }       	          	   
       }
       
       $this->my_options = unserialize($my_level->options);       
       $this->user_membership_level_name = $my_level->alias;
       $my_contents = unserialize($my_level->post_list);
       $this->my_posts = (is_bool($my_contents))? array() : (array)$my_contents;
       
       $my_contents = unserialize($my_level->page_list);
       $this->my_pages = (is_bool($my_contents))? array() : (array)$my_contents;
                
       $my_contents = unserialize($my_level->comment_list);
       $this->my_comments = (is_bool($my_contents))? array() : (array)$my_contents;

       $my_contents = unserialize($my_level->attachment_list);
       $this->my_attachments = (is_bool($my_contents))? array() : (array)$my_contents;
	   
       $my_contents = unserialize($my_level->custom_post_list);
       $this->my_custom_posts = (is_bool($my_contents))? array() : (array)$my_contents;	   
	   
       $my_contents = unserialize($my_level->category_list);
       $this->my_categories = (is_bool($my_contents))? array() : (array)$my_contents;   
       $my_subcript_period = (int)$my_level->subscription_period;
       $my_subscript_unit  = $my_level->subscription_unit;
	   if($this->config->getValue('eMember_enable_secondary_membership')){
	       if(!empty($this->userInfo->more_membership_levels)){
	       	   $my_secondary_levels = dbAccess::findAll(
	       	   		WP_EMEMBER_MEMBERSHIP_LEVEL_TABLE, 
	       	   		' id IN ( ' . $this->userInfo->more_membership_levels . ' ) ');
	       	   $this->userInfo->secondary_membership_levels = $my_secondary_levels ;
	       	   
	       	   foreach($my_secondary_levels as $my_secondary_level){
                  if(wp_emember_is_subscription_expired($this->userInfo, $my_secondary_level))continue;
	       		  $my_contents = unserialize($my_secondary_level->post_list);
	              $this->my_posts = (is_bool($my_contents))? $this->my_posts : 
	              array_unique(array_merge($this->my_posts,(array)$my_contents));
	       	   	
	       		  $my_contents = unserialize($my_secondary_level->page_list);
	              $this->my_pages = (is_bool($my_contents))? $this->my_pages : 
	              array_unique(array_merge($this->my_pages,(array)$my_contents));
	
	       		  $my_contents = unserialize($my_secondary_level->comment_list);
	              $this->my_comments = (is_bool($my_contents))? $this->my_comments : 
	              array_unique(array_merge($this->my_comments,(array)$my_contents));
				  
	       		  $my_contents = unserialize($my_secondary_level->attachment_list);
	              $this->my_attachments = (is_bool($my_contents))? $this->my_attachments : 
	              array_unique(array_merge($this->my_attachments,(array)$my_contents));

	       		  $my_contents = unserialize($my_secondary_level->custom_post_list);
	              $this->my_custom_posts = (is_bool($my_contents))? $this->my_custom_posts : 
	              array_unique(array_merge($this->my_custom_posts,(array)$my_contents));				  
	
	       		  $my_contents = unserialize($my_secondary_level->category_list);
	              $this->my_categories = (is_bool($my_contents))? $this->my_categories : 
	              array_unique(array_merge($this->my_categories,(array)$my_contents));              
	       	   }       	           	          	  
	       }
	       else 
	       	   $this->userInfo->secondary_membership_levels = NULL;
	   }
       if(($my_subcript_period == 0)&&empty($my_subscript_unit))
           $type = 'noexpire';
       else if(($my_subcript_period == 0)&& !empty($my_subscript_unit)){
           $type = 'fixeddate';
           $my_subcript_period = $my_subscript_unit;
       }
       else{
           $type = 'interval';
           switch($my_subscript_unit){
              case 'Days':
                 break;
              case 'Weeks':
                 $my_subcript_period = $my_subcript_period*7;
                 break;
              case 'Months':
                 $my_subcript_period = $my_subcript_period*30;
                 break;
              case 'Years':
                 $my_subcript_period = $my_subcript_period*365;
                 break;
           }           
       }        
       $this->subscription_duration = array(
           		'duration'=>$my_subcript_period,
           		'type'=>$type);
       $this->permissions = $my_level->permissions;
   } 
    function is_subscription_expired(){   
        return wp_emember_is_subscription_expired(
                	$this->userInfo, 
                	$this->userInfo->primary_membership_level);
        die('Something is wrong:expire');
    }
    function is_protected_post($id){
        return in_array($id, $this->protected_posts);
    }
    function is_protected_page($id){
        return in_array($id, $this->protected_pages);
    }
	
    function is_protected_attachment($id){
        return in_array($id, $this->protected_attachments);
    }
    function is_protected_custom_post($id){
        return in_array($id, $this->protected_custom_posts);
    }
	
    function is_protected_comment($id){
        return in_array($id, $this->protected_comments);
    }
    
    function is_protected_category($id){
        return in_category($this->protected_categories, $id);
    }
    function is_protected_parent_category($id){
        $cats = get_the_category($id);
        $parents = array();
        foreach ($cats as $key => $cat) {
            $parents = array_merge($parents,explode(',',get_category_parents($cat->cat_ID,false,',')));
        }
        $parents = array_unique($parents);
        foreach($parents as $parent){
            if(empty($parent)) continue;
            if(in_array(get_cat_ID($parent), $this->protected_categories)) return true;
        }
    }
    function is_permitted_attachment($id){
    	return (($this->permissions&16)===16) && in_array($id, $this->my_attachments );
    }
    function is_permitted_custom_post($id){
    	return (($this->permissions&32)===32) && in_array($id, $this->my_custom_posts );
    }	
    function is_permitted_category($id){
    	return (($this->permissions&1)===1) && in_category($this->my_categories, $id);
    }
    function is_permitted_post($id){
    	return (($this->permissions&4)===4) && in_array($id, $this->my_posts );
    }
    function is_permitted_page($id){       
    	return (($this->permissions&8)===8) && in_array( $id, $this->my_pages);      
    }
    function is_permitted_comment($id){
    	return (($this->permissions&2)===2) && in_array($id,$this->my_comments);
    }
    function is_page_accessible($id){
        return $this->is_post_accessible($id);
    }
    function is_post_accessible($id){
        if($this->is_protected_category($id)||$this->is_protected_parent_category($id)){
			if(!$this->isLoggedIn()) return false;
            if($this->is_subscription_expired()) return false;
            if($this->is_permitted_category($id)) return true;
            return false;
        }               
    	$posts = array_merge($this->protected_pages, $this->protected_posts);
		$posts = array_merge($this->protected_custom_posts, $posts);
		$post  = array_merge($this->protected_attachments, $posts);
    	if(!in_array($id, $posts)) return true;
		if(!$this->isLoggedIn()) return false;
    	if($this->is_subscription_expired()) return false;
    	if($this->is_permitted_post($id)) return true;
    	if($this->is_permitted_page($id)) return true; 
		if($this->is_permitted_custom_post($id)) return true;    		
		if($this->is_permitted_attachment($id)) return true;    		
    	return false;
    }
    function is_comment_accessible($id){
    	if(!in_array($id, $this->protected_comments)) return true;
    	if((($this->permissions&2)===2) && in_array($id,$this->my_comments)) return true;    	
    	return false;
    }
    function my_pages_posts(){
        if(!$this->isLoggedIn())return false;
        global $wpdb;
        $query = "SELECT meta_value from " . WP_EMEMBER_MEMBERS_META_TABLE .
                " WHERE meta_key ='emember_single_page_post' AND user_id = " . 
                $this->userInfo->member_id;
        $result = $wpdb->get_col($query);
        return empty($result)? array(): unserialize($result);
    }
    static function buy_page_post($user_id, $post_info = array()){
        if(!isset($post_info['post_id'])) return;        
        if(isset($post_info['valid_till'])){
            global $wpdb;
            $query = "SELECT meta_value from " . WP_EMEMBER_MEMBERS_META_TABLE .
                    " WHERE meta_key ='emember_single_page_post' AND user_id = " . $user_id;
            $result = $wpdb->get_col($query);
            $result = empty($result)? array(): unserialize($result);
            $result[$post_info['post_id']] = $post_info;
            $query = "INSERT INTO " . WP_EMEMBER_MEMBERS_META_TABLE . 
                    '( user_id, meta_key, meta_value ) VALUES(' . 
                    $user_id .',\'emember_single_page_post\',' . 
                    '\''.addslashes(serialize($result)).'\')';        
            $wpdb->query($query);
        }
    }
    function is_my_page_post($post_id){
        if(!$this->isLoggedIn())return false;
        global $wpdb;        
        $query = "SELECT meta_value from " . WP_EMEMBER_MEMBERS_META_TABLE .
                " WHERE meta_key ='emember_single_page_post' AND user_id = " . 
                $this->userInfo->member_id;
        $result = $wpdb->get_col($query);
        $result = empty($result)? array(): unserialize($result);        
        if(isset($result[$post_id])&& (strtotime($result['valid_till'])>time()))           
            return true;
        return false;
    }
}
?>
