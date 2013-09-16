<?php
function update_account_status($username){
    global $wpdb;
    if($username){
        $member_table = WP_EMEMBER_MEMBERS_TABLE_NAME;
        $ret_member_db = $wpdb->get_row("SELECT * FROM $member_table WHERE user_name='" . $wpdb->escape($username) . "'", OBJECT);
        if($ret_member_db){
            $wp_user = get_user_by('login', $username );        
            if($wp_user){
                $new_capabilities = array();
                $account_states = array('expired','','inactive','pending','unsubscribed');
                $modified = false;
                if($wp_user->wp_capabilities)
                foreach($wp_user->wp_capabilities as $role=>$state){
                    if($role =='administrator'){
                        $new_capabilities[$role] = $state;                        
                    }
                    if(in_array($ret_member_db->account_state,$account_states)){
                        if(is_bool(strpos($role, '_'))){
                           $new_capabilities[$role . '_' . $ret_member_db->account_state] = $state;
                           $modified = true;
                        }
                    }else if($ret_member_db->account_state === 'active'){
                        $parts =explode('_',$role) ;
                        $new_capabilities[$parts[0]] = $state;
                        $modified = true;
                    }                    
                }
                if($modified)update_user_meta($wp_user->ID,'wp_capabilities', $new_capabilities);                
            }
        }               
    }
}
function wp_login_callback($username,$password){
    global $wpdb;
    global $emember_auth;
    global $emember_config;
    $emember_auth = Emember_Auth::getInstance();
    $emember_config = Emember_Config::getInstance();    
    $auto_emember_login = $emember_config->getValue('eMember_signin_emem_user');
    update_account_status($username);
    if($auto_emember_login){
    	$credentials = array('user'=>$username, 'pass'=>$password, 'rememberme'=>true);     
	    $emember_auth->login($credentials/*$username,$password,true*/);
	    if($emember_auth->isLoggedIn()){
	        $after_login_page = $emember_config->getValue('after_login_page');
	        $membership_level = $emember_auth->getUserInfo('membership_level');
	        $membership_level_resultset = $emember_auth->userInfo->primary_membership_level;
	        $_SESSION['membership_level_name'] = $membership_level_resultset->alias;    	
	    }
    }
}

function show_wp_eMember_login_widget($args){
    global $emember_config;
    $emember_config = Emember_Config::getInstance();    
    extract($args);
    $widget_title = $emember_config->getValue('wp_eMember_widget_title');
    if (empty($widget_title)) $widget_title = EMEMBER_MEMBER_LOGIN;
    echo $before_widget;
    echo $before_title . $widget_title . $after_title;
    echo eMember_login_widget();    
    echo $after_widget;
}

function get_renewal_link(){
    global $emember_config;
    $emember_config = Emember_Config::getInstance();    
    $account_upgrade_url = $emember_config->getValue('eMember_account_upgrade_url');
    if(empty($account_upgrade_url)){ 
    	$msg = 'Account renewal page is not defined. Please Contact <a href="mailto:'.
    	       $emember_config->getValue('admin_email').'">Admin</a>.';
    	return wp_emember_format_message($msg);
    }
      
	$msg = EMEMBER_SUBSCRIPTION_EXPIRED_MESSAGE .' '.EMEMBER_PLEASE.
		   ' <a href=" '.$account_upgrade_url .'" target=_blank>'.EMEMBER_RENEW_OR_UPGRADE.
	       '</a> ' . EMEMBER_YOUR_ACCOUNT;
	return wp_emember_format_message($msg);
}

function get_login_link(){
    global $emember_config;
    $emember_config = Emember_Config::getInstance();    
    $login_url = get_permalink();
    $join_url = $emember_config->getValue('eMember_payments_page');
    $eMember_enable_fancy_login = $emember_config->getValue('eMember_enable_fancy_login');
    if(empty($join_url)) {
    	$msg = '<b>Membership Payment/Join Page</b> is not defined in the settings page.Please Contact <a href="mailto:'.
    	       $emember_config->getValue('admin_email').'">Admin</a>.';
    	return wp_emember_format_message($msg);
    }
    
    $format_classes = "eMember_protected_message";
	if($emember_config->getValue('eMember_format_post_page_protected_msg')){//apply default formatting to the post/page protected message
		$format_classes .= ' eMember_protected_message_default';
	}
	    
    if($eMember_enable_fancy_login){
    	$url_text = '<div class="'.$format_classes.'">';
    	$url_text .= EMEMBER_PLEASE .' <a id="'.microtime(true).'" class="emember_fancy_login_link activeLink" href="javascript:void(0);">'. 
                EMEMBER_LOGIN .'</a> ' .EMEMBER_TO_VIEW_CONTENT;
    	$url_text .= '<span class="eMember_not_a_member_msg">('.EMEMBER_NON_MEMBER.' <a href="'.$join_url.'">'.EMEMBER_JOIN.'</a>)</span>';
    	$url_text .= '</div>';    	
        return $url_text;           	
	    //return wp_emember_format_message($url_text . $output);	    
    }
      
    $disable_inline_login = $emember_config->getValue('eMember_disable_inline_login');
    if($disable_inline_login){
    	$login_url = $emember_config->getValue('login_page_url');
    }
    else{
	    if(strpos($login_url, '?')){    
			$login_url = $login_url.'&event=login';
	    }
	    else{
	    	$login_url = $login_url.'?event=login';
	    }
    }

    $no_fancy_login = '';
    $no_fancy_login .= '<div class="'.$format_classes.'">';
    $no_fancy_login .= EMEMBER_PLEASE .' <a href="'.$login_url.'">'. EMEMBER_LOGIN .'</a> ' .EMEMBER_TO_VIEW_CONTENT;
    $no_fancy_login .= '<span class="eMember_not_a_member_msg">('.EMEMBER_NON_MEMBER.' <a href="'.$join_url.'">'.EMEMBER_JOIN.'</a>)</span>';
    $no_fancy_login .= '</div>';
                    
    return $no_fancy_login;
}

/*** Return a login link HTML code with just the "Login" anchor text based on settings an parameter value ***/
function eMember_get_login_link_only_based_on_settings_condition($direct_login_page_url='',$redirect_to='')
{
	global $emember_config;
    $emember_config = Emember_Config::getInstance();    
    $join_url = $emember_config->getValue('eMember_payments_page');  
	$login_link = "";
	/*Start checking parameter and settings values and server the correct HTML code for the login link*/	
	if(!empty($direct_login_page_url))//Just need a direct link to the login page
	{		
		$link_url = $emember_config->getValue('login_page_url');
		if(empty($link_url)){return '<div class="emember_error">You did not specify a login page URL in the settings menu. Please specify a value in the login page URL field of eMember settings!</div>';}
		$login_link .= '<a href="'.$link_url.'">'.EMEMBER_LOGIN.'</a>'; 
		return $login_link;
	}
	//Check if fancy login is enabled
	$eMember_enable_fancy_login = $emember_config->getValue('eMember_enable_fancy_login');	
    if($eMember_enable_fancy_login)//Fancy login enabled... create link suitable for fancy login
    {    	
	    $login_link .= '<a id="'.microtime(true).'" class="emember_fancy_login_link" href="javascript:void(0);">'. EMEMBER_LOGIN .'</a>';
	    if(is_search()){ return $login_link; } 
	    
	    ob_start();
	    include_once('fancy_login.php');   
	    $output = ob_get_contents();
	    ob_end_clean();  
	    return $login_link . $output;    
    }
    //Not using fancy display.. serve normal login link
    $content_url = get_permalink();
    $seperator = '?';
	if(strpos($content_url, '?')){
		$seperator = '&';
	}    
	$link_url = $content_url.$seperator."event=login";
	$login_link .= '<a href="'.$link_url.'">'.EMEMBER_LOGIN.'</a>';   
	return $login_link;   	
}

function filter_eMember_login_form($content){  
    $pattern = '#\[wp_eMember_login_form:end]#';
    preg_match_all ($pattern, $content, $matches); 

    foreach ($matches[0] as $match){
        $replacement = print_eMember_login_form();
        $content = str_replace ($match, $replacement, $content);
    }	    

    return $content;
}
function print_eMember_login_form(){
    return eMember_login_widget();
}
function wp_emem_logout(){
    global $emember_config;
    $emember_config = Emember_Config::getInstance();    
    $auth = Emember_Auth::getInstance();

	/*********for role integration#start*****************/
    global $user_login;
    get_currentuserinfo();
    $emember_login = $auth->getUserInfo('user_name');

    $auth->logout();   
    if($emember_login==$user_login){
        wp_logout();
        wp_set_current_user(0);
    }
    $eMember_auto_affiliate_account_login = $emember_config->getValue('eMember_auto_affiliate_account_login');
    if($eMember_auto_affiliate_account_login && function_exists('wp_aff_platform_install')){
    	//logout the affiliate account
    	unset($_SESSION['user_id']);	
    	setcookie("user_id", "", time()-60*60*24*7, "/",COOKIE_DOMAIN);
    }
          
    $logout_page = $emember_config->getValue('after_logout_page');
    $nvp_string = "member_logout=1";
    if($logout_page){    	
    	$redirect_page = wp_emember_add_name_value_pair_to_url($logout_page,$nvp_string);
        wp_emember_redirect_to_url($redirect_page);
    }
    else{
        $wpurl = get_bloginfo("wpurl");
        $redirect_page = wp_emember_add_name_value_pair_to_url($wpurl,$nvp_string);
        wp_emember_redirect_to_url($redirect_page);
    }
}

function logout_handler()
{   
    global $auth,$emember_config;
    $emember_config = Emember_Config::getInstance();
    $auth = Emember_Auth::getInstance();   
    $auth->logout();
    $eMember_auto_affiliate_account_login = $emember_config->getValue('eMember_auto_affiliate_account_login');
    if($eMember_auto_affiliate_account_login && function_exists('wp_aff_platform_install')){
    	//logout the affiliate account
    	unset($_SESSION['user_id']);	
    	setcookie("user_id", "", time()-60*60*24*7, "/",COOKIE_DOMAIN);
    }
}

function eMember_login_widget(){
    $emember_config = Emember_Config::getInstance();    
    $auth = Emember_Auth::getInstance();
    $username = $auth->getUserInfo('user_name'); 
    $output = '';
    if($auth->isLoggedIn()){
        $expires = $auth->getUserInfo('account_state');
        if($auth->subscription_duration['type'] =='noexpire')
            $sub_expires = EMEMBER_NEVER;
        else if ($auth->subscription_duration['type'] =='fixeddate')
            $sub_expires = emember_date_locale(strtotime($auth->subscription_duration['duration']));
        else{             
            $sub_start = strtotime($auth->getUserInfo('subscription_starts'));
            $sub_expires = emember_date_locale(strtotime("+" . $auth->subscription_duration['duration'] . " days ", $sub_start));
        }
        $states = array('active'=>EMEMBER_ACTIVE,
                         'inactive'=>EMEMBER_INACTIVE,
                         'expired'=>EMEMBER_EXPIRED,
                         'pending'=>EMEMBER_PENDING,
                         'unsubscribed'=>EMEMBER_UNSUBSCRIBED);
        $eMember_secure_rss = $emember_config->getValue('eMember_secure_rss');
        $eMember_show_welcome_page_link = $emember_config->getValue('eMember_show_link_to_after_login_page');
        $feed_url = get_bloginfo('rss2_url');
        //$feed_url = get_bloginfo('url') . '?feed=ememberfeed&key=' . md5($auth->getUserInfo('member_id'));
        global $wp_rewrite;
        //$nonce = wp_create_nonce('emember-secure-feed-nonce');        
        if($wp_rewrite->using_permalinks()){
        	$feed_url .= '?emember_feed_key=' . md5($auth->getUserInfo('member_id'));
        }
        else{
        	$feed_url .= '&emember_feed_key=' . md5($auth->getUserInfo('member_id'));
        }
        $logout = get_logout_url();
        $output .= '<div class="eMember_logged_widget">';
        $output .= '<div class="eMember_logged_user_info_section">';
        $output .= EMEMBER_LOGGED_IN_AS;
        $output .= '<label class="eMember_highlight">'.$username.'</label>';
        $output .= '<br />' . EMEMBER_LOGGED_IN_LEVEL;
        $output .= '<label class="eMember_highlight">'.$auth->user_membership_level_name.'</label>';
        $output .= '<br />' . EMEMBER_ACCOUNT_STATUS. " ";               
        $output .= '<label class="eMember_highlight">'.$states[$auth->getUserInfo('account_state')].'</label>';
        if($expires != 'expired'){        
            $output .= '<br />' . EMEMBER_ACCOUNT_EXPIRES_ON . " ";        
            $output .= '<label class="eMember_highlight">'.$sub_expires.'</label>';
        }  
        else{
            $renew_url = $emember_config->getValue('eMember_account_upgrade_url');
            $output .= '<a href="'.$renew_url.'">'.EMEMBER_RENEW_OR_UPGRADE.'</a>';
        }
        $output .= '</div>';//End of eMember_logged_user_info_section
        $output .= '<ul>';
        $output .= '<li><a href="'.$logout.'">'.EMEMBER_LOGOUT.'</a></li>';
        if($eMember_secure_rss)
            $output .= '<li><a href="'. $feed_url . '">'.EMEMBER_MY_FEED.'</a></li>';        
        $edit_profile_page = $emember_config->getValue('eMember_profile_edit_page');
        $support_page = $emember_config->getValue('eMember_support_page');
        if(!empty($edit_profile_page))
            $output .= '<li><a href="'.$edit_profile_page.'">'.EMEMBER_EDIT_PROFILE.'</a></li>';
        if(!empty($support_page))
            $output .= '<li><a href="'.$support_page.'">'.EMEMBER_SUPPORT_PAGE.'</a></li>';
		if($eMember_show_welcome_page_link){
			$welcome_page_url = emember_get_after_login_page_url_of_current_user();
			$output .= '<li><a href="'. $welcome_page_url . '">'.EMEMBER_WELCOME_PAGE.'</a></li>';
		}
        $bookmark_feature = $emember_config->getValue('eMember_enable_bookmark');        
		if($bookmark_feature){
			$bookmark_page_url = $emember_config->getValue('eMember_bookmark_listing_page');
            if(!empty($bookmark_page_url))
    			$output .= '<li><a href="'. $bookmark_page_url . '">'.EMEMBER_BOOKMARK_PAGE.'</a></li>';
		}
        $output .= '</ul>';
        $custom_login_msg = $emember_config->getValue('eMember_login_widget_message_for_logged_members');
        if(!empty($custom_login_msg)){
        	$custom_login_msg = do_shortcode($custom_login_msg);
        	$output .= html_entity_decode($custom_login_msg, ENT_COMPAT);
        }
        $output .= '</div>';//End of eMember_logged_widget
    }
    else  
        $output = show_login_form();

    return $output;
}

function eMember_compact_login_widget($show_profile_link=''){
    global $auth;
    global $emember_config;
    $emember_config = Emember_Config::getInstance();
    $join_url = $emember_config->getValue('eMember_payments_page');  
    $auth = Emember_Auth::getInstance();    
    $output = "";
    $output .= "<div class='eMember_compact_login'>";
    if($auth->isLoggedIn()){
    	$output .= EMEMBER_HELLO;
    	$name = $auth->getUserInfo('first_name')." ".$auth->getUserInfo('last_name');;
    	$output .= $name;
    	
    	if(!empty($show_profile_link)){
			$output .= ' | ';
			$edit_profile_page = $emember_config->getValue('eMember_profile_edit_page');
			$output .= '<a href="'.$edit_profile_page.'">'.EMEMBER_EDIT_PROFILE.'</a>';    		
    	}
    	
    	$logout = get_logout_url();
    	$output .= ' | ';
    	$output .= '<a href="'.$logout.'">'.EMEMBER_LOGOUT.'</a>';
    }
    else {
        if(is_search()) return get_login_link();
    	$output .= EMEMBER_HELLO;
    	$eMember_enable_fancy_login = $emember_config->getValue('eMember_enable_fancy_login');
        if($eMember_enable_fancy_login){
        	$output .= '<a id="'.microtime(true).'" class="emember_fancy_login_link" href="javascript:void(0);">'.EMEMBER_LOGIN.'</a>';
		    ob_start();
		    include_once('fancy_login.php');   
		    $output_fancy_jquery = ob_get_contents();
		    ob_end_clean();      
		    $output .= $output_fancy_jquery;
	    } 	
    	else{
    		$login_url = $emember_config->getValue('login_page_url');
    		$output .= '<a href="'.$login_url.'">'.EMEMBER_LOGIN .'</a>';
    	}
    	$output .= EMEMBER_NOT_A_MEMBER_TEXT;
    	$join_url = $emember_config->getValue('eMember_payments_page');
    	$output .= '<a href="'.$join_url.'">'.EMEMBER_JOIN .'</a>';    	
    }       
    $output .= "</div>"; 
    return $output;
}

function eMember_compact_login_widget_custom(){
    $emember_config = Emember_Config::getInstance();
    $auth = Emember_Auth::getInstance();    
    $output = "";
    $output .= "<div class='eMember_compact_login_custom'>";
    if($auth->isLoggedIn()){//User is logged in (show the details for logged in user)
    	
    	//Show the member's name (remove the following 3 lines if you don't want to show the name)
    	$output .= $auth->getUserInfo('first_name')." ".$auth->getUserInfo('last_name');;
    		
    	//Show a link to the profile edit page (remove the following 3 lines if you don't want to show a edit profile link)
    	$output .= ' | ';
		$edit_profile_page = $emember_config->getValue('eMember_profile_edit_page');
		$output .= '<a href="'.$edit_profile_page.'">'.EMEMBER_EDIT_PROFILE.'</a>';    		

    	//Show a logout link (remove the following 3 lines if you don't want to show a logout link)
    	$logout = get_logout_url();
    	$output .= ' | ';
    	$output .= '<a href="'.$logout.'">'.EMEMBER_LOGOUT.'</a>';
    }
    else {//User is not logged in (show the login prompt)
    	
    	//Show a login link
		$login_url = $emember_config->getValue('login_page_url');
		$output .= '<a href="'.$login_url.'">'.EMEMBER_LOGIN .'</a>';
    		    	
		//Show link to the Join us page
    	$output .= EMEMBER_NOT_A_MEMBER_TEXT;
    	$join_url = $emember_config->getValue('eMember_payments_page');
    	$output .= '<a href="'.$join_url.'">'.EMEMBER_JOIN .'</a>';  	
    }       
    $output .= "</div>";//End of "eMember_compact_login_custom" div
    return $output;
}

function get_logout_url(){
    $url = get_bloginfo('url');
    if(strpos($url,'?')) $logout = $url."&event=logout";
    else $logout = $url."/?event=logout";
    return $logout;
}
function wp_emember_is_member_logged_in($level_id='')
{
	/*returns true if the member is logged in. if a level_id is specified then return true only if a member from that level is logged in*/
    $emember_auth = Emember_Auth::getInstance();
	if($emember_auth->isLoggedIn()){
		if(empty($level_id)){
	    	return true;//member is logged in
		}
		$membership_level = $emember_auth->getUserInfo('membership_level');
		if($level_id == $membership_level){
			return true;
		}
		if(emember_is_logged_into_secondary_level($level_id)){
			return true;
		}
	}
	return false;//member is not logged in
}

function emember_is_logged_into_secondary_level($level_id){
	$emember_auth = Emember_Auth::getInstance();
	if(!($emember_auth->isLoggedIn())){return false;}
	$secondary_levels = $emember_auth->userInfo->secondary_membership_levels;
	if(isset($secondary_levels)) {
		foreach($secondary_levels as $level) {
			if ($level->id == $level_id) {
				return true;
			}
		}
	}
	return false;
}

function show_login_form(){  
   $emember_auth = Emember_Auth::getInstance();
   $emember_config = Emember_Config::getInstance();

   $msg = $emember_auth->getSavedMessage('eMember_login_status_msg');
   $state_code = $emember_auth->getSavedMessage('eMember_login_status_code');
   $join_url = $emember_config->getValue('eMember_payments_page'); 
   $eMember_multiple_logins = $emember_config->getValue('eMember_multiple_logins');
   ob_start();
   ?>
    <form action="" method="post" class="loginForm wp_emember_loginForm" name="wp_emember_loginForm" id="wp_emember_loginForm" >
    <?php wp_nonce_field('emember-login-nonce'); ?>
	<table width="95%" border="0" cellpadding="3" cellspacing="5" class="forms">
	    <tr>
	    	<td colspan="2"><label for="login_user_name" class="eMember_label"><?php echo EMEMBER_USER_NAME; ?></label></td>
	    </tr>
	    <tr>
	        <td colspan="2"><input class="eMember_text_input" type="text" id="login_user_name" name="login_user_name" size="15" value="<?php echo isset($_POST['login_user_name'])?strip_tags($_POST['login_user_name']):"";?>" /></td>
	    </tr>
	    <tr>
	    	<td colspan="2"><label for="login_pwd" class="eMember_label"><?php echo EMEMBER_PASSWORD; ?></label></td>
		</tr>
	    <tr>
	        <td colspan="2"><input class="eMember_text_input" type="password" id="login_pwd" name="login_pwd" size="15" value="<?php echo isset($_POST['login_pwd'])?strip_tags($_POST['login_pwd']):"";?>" /></td>
	    </tr>
		<tr>
			<td colspan="2">
			<?php echo apply_filters('emember_captcha_login', ""); ?>
			</td>
		</tr>		
        <?php if (empty($eMember_multiple_logins)):?>
	    <tr>
	        <td colspan="2"><label><input type="checkbox" tabindex="90" value="forever" id="rememberme" name="rememberme" /> <?php echo EMEMBER_REMEMBER_ME; ?></label></td>
	    </tr>
        <?php endif;?>
	    <tr>
	        <td colspan="2">
	        <input type="hidden" value="1" name="testcookie" />
	        <input name="doLogin" type="submit" id="doLogin" class="eMember_button" value="<?php echo EMEMBER_LOGIN;?>" />
	        </td>	       
	    </tr>
	    <tr> 
	        <td colspan="2"> 
	        <?php
	        $password_reset_url = $emember_config->getValue('eMember_password_reset_page');
	        if($password_reset_url): 
	        ?>
	        <a id="forgot_pass" href="<?php echo $password_reset_url;?>"><?php echo EMEMBER_FORGOT_PASS;?></a>
	        <?php else :?>
	        <a id="forgot_pass" rel="#emember_forgot_pass_prompt" class="forgot_pass_link" href="javascript:void(0);"><?php echo EMEMBER_FORGOT_PASS;?></a>
	        <?php endif;?>	        
	         </td>
	    </tr>
	    <tr> 
	        <td colspan="2"><a id="register" class="register_link" href="<?php echo $join_url; ?>"><?php echo EMEMBER_JOIN_US;?></a></td>
	    </tr>
	    <tr>
	    	<td colspan="2"><span class="<?php echo ($state_code == 6)? 'emember_ok':'emember_error';?>"> <?php echo $msg;?> </span></td>
	    </tr>
	</table>
	</form>   
   <?php 
    $output = ob_get_contents();
    ob_end_clean();
	return $output;
}
function wp_emember_num_days_since_expired($subcript_period,$subscript_unit,$start_date){
    $expires = emember_calculate_expiry_date($subcript_period,$subscript_unit,$start_date); 
    if($expires = 'noexpire') return 10000000;  
    $now = time();
    $expires = strtotime($expires);
    if($now>$expires)
        return floor(($now-$expires)/ (60*60*24));
}
function wp_emember_num_days_before_expires($subcript_period,$subscript_unit,$start_date){
    $expires = emember_calculate_expiry_date($subcript_period,$subscript_unit,$start_date);
    if($expires = 'noexpire') return 10000000;
    $now = time();
    $expires = strtotime($expires);
    if($now<$expires)
        return floor(($expires-$now)/ (60*60*24));
}
function wp_emember_is_subscription_expired($member, $level){
   $subcript_period = (int)$level->subscription_period;
   $subscript_unit  = $level->subscription_unit;
   $expiry_1st = strtotime($member->expiry_1st);
   $expiry_2nd = strtotime($member->expiry_2nd);

   if($expiry_1st  && ($expiry_1st>0)){
	   $is_expired = time() > $expiry_1st;
       return $is_expired;
   }

   if($expiry_2nd && ($expiry_2nd>0)){
   	   $is_expired = time() > $expiry_2nd;
       return $is_expired; 
   }

   if(($subcript_period == 0)  && !empty($subscript_unit)){
       $expires = strtotime($subscript_unit);
       $now = time();
       if($now>$expires) return true;
       return false;
   }
   switch($subscript_unit){
      case 'Days':
         break;
      case 'Weeks':
         $subcript_period = $subcript_period*7;
         break;
      case 'Months':
         $subcript_period = $subcript_period*30;
         break;
      case 'Years':
         $subcript_period = $subcript_period*365;
         break;
   }
    if($subcript_period===0)
       return false;
    /**alternative***/
    $d = ($subcript_period==1)? ' day':' days';
    $sDate = date('Y-m-d', strtotime(" - " . abs($subcript_period) .$d ));
    if((strtotime($sDate)-strtotime($member->subscription_starts))>=0){
        return true;
    }
    return false;
}

function emember_update_htpasswd($user, $pass){
    $upload_dir = wp_upload_dir();
    $dir = $upload_dir['basedir'] . '/emember/downloads/';        
    $htpasswd = file_get_contents($dir.'.htpasswd');
    $newhtpasswd = array();
    foreach (explode("\n",trim($htpasswd)) as $i){
        if (empty($i)) continue;
        $t = explode(':', $i);
        if ($t[0] == $user) continue;
        $newhtpasswd[] = $i;
    }
    $newhtpasswd[] = $user.':'.crypt($pass);    
    $newhtpasswd = implode("\n",$newhtpasswd);
    $newhtpasswd = ltrim($newhtpasswd);
    file_put_contents($dir.'.htpasswd', $newhtpasswd);
}

function emember_get_exipiry_date(){
    $auth = Emember_Auth::getInstance();  
    if(!$auth->isLoggedIn())return "User is not logged in!";
    $expires = $auth->getUserInfo('account_state');
    if($auth->subscription_duration['type'] =='noexpire')
        $sub_expires = EMEMBER_NEVER;
    else if ($auth->subscription_duration['type'] =='fixeddate')
        $sub_expires = emember_date_locale(strtotime($auth->subscription_duration['duration']));
    else{             
        $sub_start = strtotime($auth->getUserInfo('subscription_starts'));
        $sub_expires = emember_date_locale(strtotime("+" . $auth->subscription_duration['duration'] . " days ", $sub_start));
    }        
    return $sub_expires;
}

function emember_get_exipiry_date_additional_levels(){
	global $wpdb;
    $auth = Emember_Auth::getInstance();  
    if(!$auth->isLoggedIn())return "User is not logged in!";
    $ids = $auth->getUserInfo('more_membership_levels');
	$addtionals = array();
	if(empty($ids)) return $addtionals;
	$query = "SELECT alias,subscription_period, subscription_unit FROM " . WP_EMEMBER_MEMBERSHIP_LEVEL_TABLE . " WHERE id IN (" . $ids . ")";	
	$result = $wpdb->get_results($query);
	foreach ($result as $row){
		$my_subcript_period = $row->subscription_period; 
		$my_subscript_unit  = $row->subscription_unit; 
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
		if($type =='noexpire')
			$sub_expires = EMEMBER_NEVER;
		else if ($type =='fixeddate')
			$sub_expires = emember_date_locale(strtotime($my_subcript_period));
		else{             
			$sub_start = strtotime($auth->getUserInfo('subscription_starts'));
			$sub_expires = emember_date_locale(strtotime("+" . $my_subcript_period . " days ", $sub_start));
		}
		$additionals[$row->alias] = $sub_expires;	
	}
    return $additionals;
}
function emember_calculate_expiry_date($subcript_period,$subscript_unit,$start_date){
    if(($subcript_period == 0)  && !empty($subscript_unit)){ //will expire after a fixed date.
        return $subscript_unit;
    }
    switch($subscript_unit){
        case 'Days':
        break;
        case 'Weeks':
        $subcript_period = $subcript_period*7;
        break;
        case 'Months':
        $subcript_period = $subcript_period*30;
        break;
        case 'Years':
        $subcript_period = $subcript_period*365;
        break;
    }
    if($subcript_period==0){// its set to no expiry until cancelled
        return 'noexpire';
    }
	//Using duration value - lets calculate the expiry
    $d = ($subcript_period==1)? ' day':' days';
    return date('Y-m-d', strtotime(" + " . abs($subcript_period) .$d, strtotime($start_date) ));
}

function emember_get_expiry_by_member_id($member_id){
    global $wpdb;
    $query = "SELECT subscription_starts, membership_level, subscription_period, subscription_unit FROM " .
            WP_EMEMBER_MEMBERS_TABLE_NAME . " LEFT JOIN " . WP_EMEMBER_MEMBERSHIP_LEVEL_TABLE . 
            " ON membership_level = id WHERE member_id = " . $wpdb->escape($member_id);   
    $result = $wpdb->get_row($query); 
    if(empty($result))return '1969-01-01';
    $period = $result->subscription_period; 
    $unit   = $result->subscription_unit;
    $start  = $result->subscription_starts;
    return emember_calculate_expiry_date($period,$unit,$start);
}
