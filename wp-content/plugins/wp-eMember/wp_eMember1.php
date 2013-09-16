<?php
/********************************************
***      THIS IS NOT A FREE PLUGIN        ***
*** DO NOT COPY ANY CODE FROM THIS PLUGIN ***
*********************************************/
require_once ( ABSPATH . WPINC . '/pluggable.php' );
global $wp_version;
if(version_compare($wp_version, '3.1', '< '))
    require_once ( ABSPATH . WPINC . '/registration.php' );

define('WP_EMEMBER_FOLDER', dirname(plugin_basename(__FILE__)));
define('WP_EMEMBER_URL', plugins_url('',__FILE__));
define('WP_EMEMBER_PATH',plugin_dir_path( __FILE__ ));

if (isset($_SERVER["HTTPS"])&&$_SERVER["HTTPS"] == "on")
    {$gravatar_url = "https://secure.gravatar.com/avatar";}
else 
    {$gravatar_url = "http://www.gravatar.com/avatar";}
define('WP_EMEMBER_GRAVATAR_URL',$gravatar_url);
include_once('emember_config.php');
$emember_config = Emember_Config::getInstance();
$config = $emember_config;
$auth = null;
$emember_auth = null;
add_action('plugins_loaded', 'emember_bootstrap');

function emember_bootstrap(){
    global $eMember_language_file;
    include_once('emember_config.php');
    $emember_config = Emember_Config::getInstance();
    $lang = $emember_config->getValue('eMember_language');
    if (!empty($lang))$eMember_language_file = WP_EMEMBER_PATH ."lang/".$lang.".php";
    else $eMember_language_file = WP_EMEMBER_PATH."lang/eng.php";
    $eMember_language_file = apply_filters('emember_get_language_path', $eMember_language_file, $lang);
    include_once($eMember_language_file); 
    global $emember_auth,$auth;
    $emember_auth = Emember_Auth::getInstance();
    $auth = $emember_auth;
    add_action('shutdown', 'wp_emember_shutdown_chores');
}

include_once('eMember_debug_handler.php');
include_once('eMember_db_access.php');
include_once('eMember_misc_functions.php');
include_once('emember_auth.php');
include_once('emember_ajax.php');
include_once('emember_access_checkers.php');
include_once('emember_custom_feed.php');
include_once('eMember_auto_responder_handler.php');
include_once('wp_emember_fb_reg_handler.php');
include_once('eMember_auth_utils.php');
include_once('eMember_bookmark_utils.php');
include_once('eMember_registration_utils.php');
include_once('eMember_profile_utils.php');
include_once('eMember_3rd_party_plugin_integration_code.php');
include_once('lib/class.emember.uploader.php');
include_once('eMember_cronjob_functions.php');

function init_emember(){
    emember_dynamic_js_load();
    emember_process_reg_form();
    emember_process_free_rego_with_confirm_form();
    if(isset($_REQUEST['doLogin'])){
	    $valid_captcha = apply_filters('emember_captcha_varify_login', true);
		if(!$valid_captcha){
			wp_die('Security check: failed captcha. Please try again!' , '', array( 'back_link' => true ) );
		}
        $nonce=$_REQUEST['_wpnonce'];
        if (! wp_verify_nonce($nonce, 'emember-login-nonce') ) {
            eMember_log_debug("Login nonce check failed ",true);       
            die("Security check failed on login");
        }
        if(isset($_REQUEST['emember_u_name']) && isset($_REQUEST['emember_pwd'])){
            $_POST['login_user_name'] = $_REQUEST['emember_u_name'];
            $_POST['login_pwd']= $_REQUEST['emember_pwd'];
        }
        emember_login();
    }else{
    	emember_logout_init();
    }
    emember_update_profile_init();
    emember_general_init_code();
}
function emember_login($redirect = true){
    global $wpdb;
    global $emember_config;
    global $emember_auth;
    $emember_auth = Emember_Auth::getInstance();
    $emember_config = Emember_Config::getInstance();    
    $credentials = array();
    $credentials['user'] = strip_tags($_POST['login_user_name']);
    $credentials['pass'] = strip_tags($_POST['login_pwd']);
    $clientip = $_SERVER['REMOTE_ADDR']; 
    if ($emember_config->getValue('eMember_multiple_logins') == '1') unset($_POST['rememberme']);
    $credentials['rememberme'] = (isset($_POST['rememberme'])?$_POST['rememberme'] : false);
    eMember_log_debug("Authenticating login request for username: ".$credentials['user'].". Request came from IP Address: ".$clientip,true);
    $emember_auth->login($credentials);
    if($emember_auth->isLoggedIn()){    
    	eMember_log_debug("Authentication completed for username: ".$credentials['user'].". IP Address: ".$clientip,true);	
        $user_id = username_exists( $credentials['user'] );        
        $after_login_page = $emember_config->getValue('after_login_page');
        $membership_level = $emember_auth->getUserInfo('membership_level');
        $membership_level_resultset = $emember_auth->userInfo->primary_membership_level;
        $_SESSION['membership_level_name'] = $membership_level_resultset->alias;
        
        //Log into the affiliate account if the option is set
        $eMember_auto_affiliate_account_login = $emember_config->getValue('eMember_auto_affiliate_account_login');
        if($eMember_auto_affiliate_account_login && function_exists('wp_aff_platform_install')){
        	eMember_log_debug("Logging into Affiliate Platform account",true);	
            $_SESSION['user_id']= $_POST['login_user_name'];
    	    if(isset($_POST['rememberme'])){
    	    	setcookie("user_id", $_POST['login_user_name'], time()+60*60*24*7, "/",COOKIE_DOMAIN);    	
    	    }
    	    else{
    	    	setcookie("user_id", $_POST['login_user_name'], time()+60*60*6, "/",COOKIE_DOMAIN);
    	    }
        }
        $folder_protection = $emember_config->getValue('emember_download_folder_protection');
        if($folder_protection){
            emember_update_htpasswd($credentials['user'],$credentials['pass']);
        }
        
        $sign_in_wp = $emember_config->getValue('eMember_signin_wp_user');
        if($sign_in_wp){   
        	eMember_log_debug("Logging into WordPress account. User ID: ".$user_id,true);	
            if($user_id){
                $preserve_role = $emember_auth->getUserInfo('flags');
                if(($preserve_role & 1) != 1){ 
                	$user_info = get_userdata($user_id);
                	$user_cap = is_array($user_info->wp_capabilities)?array_keys($user_info->wp_capabilities):array();
                    $account_stat = $emember_auth->getUserInfo('account_state');
                    
                	if(($account_stat === 'active') && !in_array('administrator',$user_cap))
                		update_wp_user_Role($user_id, $membership_level_resultset->role);
                }
                update_account_status($credentials['user']);
                wp_signon(array(
                                'user_login'=>$credentials['user'],
                                'user_password'=>$credentials['pass'],
                                'remember'=>isset($_POST['rememberme'])?$_POST['rememberme']:''
                                ),
                                is_ssl() ? true : false);
            }
        }
        if($redirect){
            $enable_after_login_redirect = $emember_config->getValue('eMember_enable_redirection');                      
            if($enable_after_login_redirect){
            	eMember_log_debug("Redirecting member to the after login redirection page.",true);
            	//TODO - Refactor the following code by using the "emember_get_after_login_page_url_of_current_user()" function  
                $separate_home_page = $emember_auth->getUserInfo('home_page');
	            if(!empty($separate_home_page)){
	                wp_emember_redirect_to_url($separate_home_page);exit;
	            }            	
                if(!empty($membership_level_resultset->loginredirect_page)){
                    wp_emember_redirect_to_url($membership_level_resultset->loginredirect_page);exit;
                }
                else if(!empty($after_login_page)){
                    wp_emember_redirect_to_url($after_login_page);exit;
                }
            }  
        }
    }    
}
function emember_logout_init(){    
	$emember_auth = Emember_Auth::getInstance();
	$emember_config = Emember_Config::getInstance();

    $sign_in_wp = $emember_config->getValue('eMember_signin_wp_user');
	if($sign_in_wp && !is_user_logged_in() && $emember_auth->isLoggedIn()){//If Not logged into WP while emember is logged in
	    if(username_exists($emember_auth->getUserInfo('user_name'))){
		    eMember_log_debug("User Exists in WP but not logged in. ",true);
            $emember_auth->silent_logout();
            eMember_log_debug("Logging out of emember because wp cookie for this user expired ",true);
	    }else{
	    	eMember_log_debug("You have auto login to WP enabled but WP User doesn't exist for this user! WP User login won't execute.",true);
	    }
    }
	$autologout = $emember_config->getValue ('wp_eMember_auto_logout');
    if(isset($_REQUEST['member_logout']) && $_REQUEST['member_logout']=='1'){
        //Make sure the user is fully logged out
        //eMember_log_debug("Logging out the member via the 'member_logout' GET request!",true);
        $emember_auth->logout();
        wp_emember_redirect_to_non_logout_url();
        return;
    }
    if ($autologout){
		if(!isset($emember_auth->sessionInfo)) return;
    	$login_time = $emember_auth->sessionInfo->login_impression;
        if(empty($login_time)) return;
    	$diff = strtotime(current_time('mysql',1)) - strtotime($login_time);
    	if($diff> ($autologout*60)){
    		eMember_log_debug("Auto logout triggered. Logging out the member!",true);
    		wp_emem_logout();
    		return;
    	}    	
    }   
}
function emember_general_init_code(){
    if(isset($_GET['emember_feed_key'])){
        //$nonce=isset($_REQUEST['_wpnonce'])?$_REQUEST['_wpnonce']:"";
        //if (! wp_verify_nonce($nonce, 'emember-secure-feed-nonce') ) {
        //    eMember_log_debug("feed nonce check failed ",true);       
        //    die("Security check failed on rss feed.");
        //}
        $emember_auth = Emember_Auth::getInstance();
        $emember_auth->login(array('md5ed_id'=>$_GET['emember_feed_key']));
    }
    if(isset($_REQUEST['emember_paypal_ipn'])){
    	include_once('ipn/eMember_handle_paypal_ipn.php');
    	exit;
    }
}
function emember_update_profile_init(){
    if(isset($_POST['eMember_update_profile']))
    {
        $nonce=$_REQUEST['_wpnonce'];
        if (! wp_verify_nonce($nonce, 'emember-update-profile-nonce') ) {
            eMember_log_debug("Profile update nonce check failed ",true);       
            die("Security check failed on profile update");
        }		    
        global $wpdb,$emember_config;
        $emember_config = Emember_Config::getInstance();    
            include_once(ABSPATH . WPINC . '/class-phpass.php');
            global $wp_version;
            if(version_compare($wp_version, '3.1', '< '))
                require_once ( ABSPATH . WPINC . '/registration.php' );

            $resultset  = dbAccess::find(WP_EMEMBER_MEMBERS_TABLE_NAME, ' member_id=' . 
                          $wpdb->escape($_POST['member_id'])); 
            $wp_user_id = username_exists($resultset->user_name);  
            $updatable = true;     
            if(isset($_POST['wp_emember_email'])){
                $emmber_email_owner = emember_email_exists($_POST['wp_emember_email']);
                $wp_email_owner = email_exists($_POST['wp_emember_email']);
                if(!is_email($_POST['wp_emember_email'])){
                    $_POST['eMember_profile_update_result'] = EMEMBER_EMAIL_INVALID;
                    $updatable = false;
                }
                else if(($wp_email_owner&&($wp_email_owner!=$wp_user_id))||($emmber_email_owner &&($emmber_email_owner!=$_POST['member_id']))){
                    $_POST['eMember_profile_update_result']= '<span class="emember_error">'.EMEMBER_EMAIL_UNAVAIL.' </span>';
                    $updatable = false;
                }
        } 
        if (($_POST['wp_emember_pwd'] != $_POST['wp_emember_pwd_r'])){
             $_POST['eMember_profile_update_result']  = '<span class="emember_error">'.EMEMBER_PASSWORD_MISMATCH.'</span>';
             $updatable = false;	    		    	
        } 

            if($updatable)
            {   	    
                $wp_hasher = new PasswordHash(8, TRUE);	    
                $fields = array();
                if(isset($_POST['wp_emember_firstname']))$fields['first_name']      = strip_tags($_POST['wp_emember_firstname']);
                if(isset($_POST['wp_emember_lastname']))$fields['last_name']        = strip_tags($_POST['wp_emember_lastname']);
                if(isset($_POST['wp_emember_email']))$fields['email']               = strip_tags($_POST['wp_emember_email']);
                if(isset($_POST['wp_emember_phone']))$fields['phone']               = strip_tags($_POST['wp_emember_phone']);
                if(isset($_POST['wp_emember_street']))$fields['address_street']     = strip_tags($_POST['wp_emember_street']);
                if(isset($_POST['wp_emember_city']))$fields['address_city']         = strip_tags($_POST['wp_emember_city']);
                if(isset($_POST['wp_emember_state']))$fields['address_state']       = strip_tags($_POST['wp_emember_state']);
                if(isset($_POST['wp_emember_zipcode']))$fields['address_zipcode']   = strip_tags($_POST['wp_emember_zipcode']);
                if(isset($_POST['wp_emember_country']))$fields['country']           = strip_tags($_POST['wp_emember_country']);
                if(isset($_POST['wp_emember_gender']))$fields['gender']             = strip_tags($_POST['wp_emember_gender']);  
                if(isset($_POST['wp_emember_company_name']))$fields['company_name'] = strip_tags($_POST['wp_emember_company_name']);          
                if(!empty($_POST['wp_emember_pwd'])){
                    $password = $wp_hasher->HashPassword(strip_tags($_POST['wp_emember_pwd']));
                    $fields['password'] = $password;            
                }

                if($wp_user_id) {
                    $wp_user_info  = array();
                    $wp_user_info['first_name']    = strip_tags(isset($_POST['wp_emember_firstname'])?$_POST['wp_emember_firstname']:"");
                    $wp_user_info['last_name']     = strip_tags(isset($_POST['wp_emember_lastname'])?$_POST['wp_emember_lastname']:"");
                    $wp_user_info['user_email']    = strip_tags(isset($_POST['wp_emember_email'])?$_POST['wp_emember_email']:"");
                    $wp_user_info['ID']            = $wp_user_id;

                    if(!empty($_POST['wp_emember_pwd'])) $wp_user_info['user_pass'] = $_POST['wp_emember_pwd'];                                            
                    wp_update_user( $wp_user_info );
                }
                $_POST['member_id'] = strip_tags($_POST['member_id']);
                if(count($fields)>0){
                    $ret = dbAccess::update(WP_EMEMBER_MEMBERS_TABLE_NAME, ' member_id ='. $wpdb->escape($_POST['member_id']), $fields);
                    if(isset($_POST['emember_custom'])){
                        $custom_fields = dbAccess::find(WP_EMEMBER_MEMBERS_META_TABLE, ' user_id=' . $wpdb->escape($_POST['member_id']) . ' AND meta_key=\'custom_field\'');
                        if($custom_fields)
                            $wpdb->query('UPDATE ' . WP_EMEMBER_MEMBERS_META_TABLE . 
                            ' SET meta_value ='. '\''.addslashes(serialize($_POST['emember_custom'])). '\' WHERE meta_key = \'custom_field\' AND  user_id=' . $_POST['member_id']);
                        else 
                            $wpdb->query("INSERT INTO " . WP_EMEMBER_MEMBERS_META_TABLE . 
                            '( user_id, meta_key, meta_value ) VALUES(' . $_POST['member_id'] .',"custom_field",' . '\''.addslashes(serialize($_POST['emember_custom'])).'\')');
                    }
                else{
                    $wpdb->query('DELETE FROM ' . WP_EMEMBER_MEMBERS_META_TABLE . 
                    '  WHERE meta_key = \'custom_field\' AND  user_id=' . $wpdb->escape($_POST['member_id']));

                }
                    if($ret === false){
                        $_POST['eMember_profile_update_result'] = 'Failed';
                    }
                    else {
                        $_POST['eMember_profile_update_result'] = EMEMBER_PROFILE_UPDATED;
                        do_action('eMember_profile_updated',$fields,$custom_fields);
                        //Update the affiliate end if using the auto affiliate feature
                        eMember_handle_affiliate_profile_update();
                    }
                }
            }
    }
}
//
add_shortcode("wp_eMember_cancel_subscription_link","wp_eMember_cancel_subscription_link_handler");
add_shortcode("wp_eMember_total_members","emember_total_members_handler");
add_shortcode("wp_eMember_first_name","emember_first_name_handler");
add_shortcode("wp_eMember_last_name","emember_last_name_handler");
add_shortcode("wp_eMember_user_details","wp_eMember_user_details_handler");
add_shortcode('wp_eMember_custom_value', 'get_wp_emember_custom_field_val');/*Use this for getting the custom field value for custom button integration*/

add_shortcode("free_rego_with_email_confirmation","free_rego_with_email_confirmation_handler");
add_shortcode("emember_protected","emember_protected_handler"); // validation error
add_shortcode('wp_eMember_registration_form_for','wp_eMember_registration_form_handler');
add_shortcode('wp_eMember_compact_login','eMember_compact_login_widget');
add_shortcode('wp_eMember_compact_login_custom','eMember_compact_login_widget_custom');
add_shortcode('wp_eMember_renew_membership_for_free', 'wp_eMember_renew_membership_for_free_handler');
add_shortcode('wp_eMember_upgrade_membership_level_to', 'wp_eMember_upgrade_membership_level_to_handler');
add_shortcode('wp_eMember_login','eMember_login_widget');
add_shortcode('wp_eMember_registration','show_registration_form');
add_shortcode('wp_eMember_edit_profile','show_edit_profile_form');
add_shortcode('wp_eMember_user_list','show_eMember_public_user_list');
add_shortcode('wp_eMember_user_bookmarks','print_eMember_bookmark_list');
add_shortcode('wp_eMember_password_reset','print_password_reset_form');

if(!emember_is_first_click()){
    $emember_config = Emember_Config::getInstance();    
    add_filter('the_content','secure_content', 11);
    //add_filter('the_excerpt','secure_content', 11);
    if(!is_admin()&&$emember_config->getValue('eMember_protect_comments_separately')){        
        add_action('comment_text','comment_text_action');
    }
}else{
    add_filter('the_content','emember_first_click_content', 11);
}
add_action('wp_footer', 'wp_emember_footer_callback');
add_filter('the_content', 'do_shortcode',11);
if(!is_admin()){add_filter('widget_text', 'do_shortcode');}
add_filter('the_content', 'filter_eMember_registration_form');
add_filter('the_content', 'filter_eMember_public_user_list');
add_filter('the_content', 'filter_eMember_login_form');
add_filter('the_content', 'filter_eMember_edit_profile_form');
add_filter('the_content', 'filter_eMember_bookmark_list');
add_filter('do_enclose', 'eMember_delete_enclosure' );
add_filter('rss_enclosure', 'eMember_delete_enclosure' );
add_filter('atom_enclosure', 'eMember_delete_enclosure' );
add_filter('mod_rewrite_rules', 'emember_rewrite_rules');

$enable_more_tag = $emember_config->getValue('eMember_enable_more_tag');
if($enable_more_tag) add_filter( 'the_content_more_link', 'eMember_my_more_link', 10, 2 );
$enable_bookmark = $emember_config->getValue('eMember_enable_bookmark');
if($enable_bookmark) add_filter('the_content', 'bookmark_handler');
//to handle jetpack compatibility issues # start
add_filter('emember_filter_post', 'secure_content');
//to handle jetpack compatibility issues#end
$lockdown_domain = $emember_config->getValue('eMember_enable_domain_lockdown');
if($lockdown_domain)add_action('wp_head', 'lockdown_widget');
$eMember_override_avatar = $emember_config->getValue('eMember_override_avatar');
if($eMember_override_avatar)add_filter('get_avatar','emember_replace_avatar',10,5);
add_action('init', 'export_members_to_csv');
add_action('init', 'load_library');
add_action('init', 'init_emember');
add_action('init', 'wp_eMember_widget_init');
add_action('init', 'emember_menu');
add_action('profile_update','sync_emember_profile', 10,2);
add_action('wp_logout', 'logout_handler');
add_action('init', 'del_bookmark');
add_action('wp_ajax_emember_upload_ajax', 'wp_emem_upload_file');
add_action('wp_ajax_nopriv_emember_upload_ajax', 'wp_emem_upload_file');
add_action('wp_ajax_nopriv_check_name', 'wp_emem_check_user_name');
add_action('wp_ajax_item_list_ajax', 'item_list_ajax');
add_action('wp_ajax_access_list_ajax', 'access_list_ajax');
add_action('wp_ajax_send_mail', 'wp_emem_send_mail');
add_action('wp_ajax_nopriv_send_mail', 'wp_emem_send_mail');
add_action('wp_ajax_check_level_name', 'wp_emem_check_level_name');
add_action('wp_ajax_add_bookmark', 'wp_emem_add_bookmark');
add_action('wp_ajax_wp_user_list_ajax', 'wp_emem_wp_user_list_ajax');
add_action('wp_ajax_emember_user_list_ajax', 'wp_emem_user_list_ajax');
add_action('wp_ajax_emember_user_count_ajax', 'wp_emem_user_count_ajax');
add_action('wp_ajax_emember_wp_user_count_ajax','wp_emem_wp_user_count_ajax');
add_action('wp_ajax_nopriv_emember_public_user_list_ajax', 'wp_emem_public_user_list_ajax');
add_action('wp_ajax_nopriv_emember_public_user_profile_ajax', 'wp_emem_public_user_profile_ajax');
add_action('wp_ajax_nopriv_delete_profile_picture', 'wp_emem_delete_image');
add_action('wp_ajax_delete_profile_picture', 'wp_emem_delete_image');
add_action('wp_ajax_get_post_preview', 'wp_emem_get_post_preview');
add_action('wp_ajax_nopriv_openid_login','wp_emem_openid_login');
add_action('wp_ajax_nopriv_openid_logout','wp_emem_openid_logout');
add_action('wp_ajax_nopriv_emember_ajax_login', 'emember_ajax_login');
add_action('wp_ajax_emember_file_upload', 'emember_fileuploader');
add_action('wp_ajax_emember_ajax_login', 'emember_ajax_login');
add_action('wp_ajax_emember_load_membership_form','emember_load_membership_form');
add_action('wp_eMember_email_notifier_event', 'eMember_email_notifier_cronjob');
add_action('wp_eMember_scheduled_membership_upgrade_event', 'wp_eMember_scheduled_membership_upgrade');
add_action('admin_menu', 'eMember_add_custom_box');
add_action('save_post', 'eMember_save_postdata');
add_action('wp_authenticate', 'wp_login_callback', 1, 2 );
add_filter('wp_head','add_dynamicjs');
add_filter('comments_template', 'remove_comments_template_on_pages', 11 );
add_action('user_register', 'wp_eMember_handle_wp_user_registration');
add_filter('attachment_fields_to_save', 'emember_save_attachment_extra',10,2);
//add_filter('get_attached_file', 't');
add_filter('wp_get_attachment_url','emember_protect_attachment');
add_filter('wp_get_attachment_metadata', 'emember_protect_attachment');
function emember_protect_attachment($content){
	if(is_admin()) return $content;
	return auth_check_attachment($content);
}
function emember_save_attachment_extra($post, $attachment){
	eMember_save_postdata( $post['ID'] );
	return $post;
}
function emember_first_click_content($content){
    if(!is_single() && !is_page()) return $content;
    $emember_config = Emember_Config::getInstance();
    $emember_auth = Emember_Auth::getInstance();    
    global $post;
    $is_protected = false;
    if($post->post_type == 'post')
        $is_protected = $emember_auth->is_protected_post($post->ID);
    else if ($post->post_type == 'page')
        $is_protected = $emember_auth->is_protected_page($post->ID);
    $msg = $emember_config->getValue('eMember_google_first_click_free_custom_msg');  
    if($is_protected && !empty($msg)){
		$msg = html_entity_decode(stripslashes($msg), ENT_COMPAT,"UTF-8");
    	$output = '<div class="eMember_first_click_free_message">'. $msg.'</div>';
    	return $output.$content;
    }
    return $content;
}
function wp_eMember_handle_wp_user_registration($user_id){    
    $emember_config = Emember_Config::getInstance();
    $emem_when_wp = $emember_config->getValue('eMember_enable_emem_when_wp');
    $default_level = $emember_config->getValue('eMember_emem_when_wp_default_level');
    $default_acstatus = $emember_config->getValue('eMember_emem_when_wp_default_acstatus');
    
    if(!$emem_when_wp) return;
    if(empty($default_level)) return;
    $user_info = get_userdata($user_id);
    if(emember_username_exists($user_info->user_login)){
    	eMember_log_debug("eMember user account with this username already exists! No new account will be created for this user.",true);
    	return;
    }
    $fields = array();
    $fields['user_name'] = $user_info->user_login;
    $fields['password'] = $user_info->user_pass;
    $fields['email'] = $user_info->user_email;
    $fields['first_name'] = $user_info->first_name;
    $fields['last_name'] = $user_info->last_name;    
    $fields['membership_level'] = $default_level;
    $fields['member_since'] = date('Y-m-d');
    $fields['account_state'] = $default_acstatus;
    $fields['subscription_starts'] = date('Y-m-d');
    create_new_emember_user($fields);
}
function emember_rewrite_rules($rewrite_rules){
    $emember_config = Emember_Config::getInstance();
    $folder_protection = $emember_config->getValue('emember_download_folder_protection');
    if(!$folder_protection) return $rewrite_rules;

    $path = explode(ABSPATH,WP_EMEMBER_PATH);
    $path = $path[1];    
    $error_file = '/'. $path . 'emember_folder_protection_download_error.html';
	$ememberrules="\n# eMember Rules BEGIN\n";
	$ememberrules.="<IfModule mod_rewrite.c>\n";
	$ememberrules.="ErrorDocument 403 $error_file\n";
	$ememberrules.="ErrorDocument 401 $error_file\n";
	$ememberrules.="</IfModule>\n";
	$ememberrules.="# eMember Rules ENDS\n\n";
	
	$rewrite_rules = $ememberrules.$rewrite_rules;	
	return $rewrite_rules;
}
function remove_comments_template_on_pages($file){
    $emember_auth = Emember_Auth::getInstance();
    $emember_config = Emember_Config::getInstance();    
    global $post;
    if(!$emember_config->getValue('eMember_protect_comments_separately')){    
        if(!comments_open($post->ID)&& (get_comments_number($post->ID )=='0')) return $file;
        if(!$emember_auth->is_post_accessible($post->ID))
            return WP_EMEMBER_PATH . 'eMember_comment_template.php';      
    }  
    return $file;
} 
if (is_admin())add_action('admin_menu','wp_eMember_add_admin_menu');
if (is_admin())add_action('admin_notices', 'wp_eMember_plugin_conflict_check');
$emember_allow_comment = $emember_config->getValue('eMember_member_can_comment_only');
if($emember_allow_comment){
    add_action ('init','emember_check_comment');
    add_filter('wp_head','emember_customise_comment_form');
    add_filter('comment_form_defaults','emember_change_comment_field');
    function emember_change_comment_field($fields){
        global $emember_auth; 
        $emember_auth = Emember_Auth::getInstance();
        $emember_config = Emember_Config::getInstance();        
        if(!$emember_auth->isLoggedIn()){
            $fields = array();
            $login_link = EMEMBER_PLEASE ." ". eMember_get_login_link_only_based_on_settings_condition('1'). EMEMBER_TO_COMMENT;
            $fields['comment_field']= $login_link;
        }
        return $fields;
    }    
    function emember_customise_comment_form($c){
        global $emember_auth;
        $emember_auth = Emember_Auth::getInstance();
        $emember_config = Emember_Config::getInstance();        
        if(!$emember_auth->isLoggedIn()){
            $login_link  = EMEMBER_PLEASE ." ". eMember_get_login_link_only_based_on_settings_condition('1'). EMEMBER_TO_COMMENT;
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){ 
                $('#respond').html("<?php echo addslashes($login_link);?>");
            });
        </script>
        <?php
        }
    }
    function emember_check_comment(){
        if(is_admin())return;
        global $emember_auth;
        $emember_auth = Emember_Auth::getInstance();
        $emember_config = Emember_Config::getInstance();        
        if(!$emember_auth->isLoggedIn()){
            if(isset($_POST['comment_post_ID'])){
                $_POST = array();
                wp_die('Comments not allowed.');
            }
        }
    }
}
function add_dynamicjs(){
    global $emember_config;
    $emember_config = Emember_Config::getInstance();    
    include_once('dynamicjs.php');
}
function wp_emember_footer_callback(){
    $emember_config = Emember_Config::getInstance();  
    $join_url = $emember_config->getValue('eMember_payments_page');  
    $eMember_enable_fancy_login = $emember_config->getValue('eMember_enable_fancy_login');
    if($eMember_enable_fancy_login){
        include_once('fancy_login.php');   
    }
    wp_emember_hook_password_reminder();
}
function wp_emember_password_reminder_filter($content){
    global $emember_auth;
    $emember_auth = Emember_Auth::getInstance();
    $emember_config = Emember_Config::getInstance();    
    if($emember_auth->password_reminder_block_added == 'no'){
        $emember_auth->password_reminder_block_added = 'yes';
        ob_start();
        wp_emember_hook_password_reminder();
        $output = ob_get_contents();
        ob_end_clean();
        return $output . $content;
    }
    return $content;
}
/* used to check various common conflicts and report to the user */
function wp_eMember_plugin_conflict_check()
{
	global $emember_config;
    $emember_config = Emember_Config::getInstance();    
	$msg = "";    
	//Check schemea
	$installed_schema_version = get_option("wp_eMember_db_version");
	if($installed_schema_version != WP_EMEMBER_DB_VERSION)
	{
		$msg .= '<div class="error"><p>It looks like you did not follow the <a href="http://www.tipsandtricks-hq.com/wordpress-membership/?p=3" target="_blank">WP eMember upgrade instruction</a> to update the plugin. The database schema is out of sync and need to be updated. Please deactivate the plugin and follow the <a href="http://www.tipsandtricks-hq.com/wordpress-membership/?p=3" target="_blank">upgrade instruction from here</a> to upgrade the plugin and correct this.</p></div>';
	}
	    	
	$activation_flag_value = $emember_config->getValue('wp_eMember_plugin_activation_check_flag');
    if($activation_flag_value != '1' && empty($msg))
    {
        //no need check for conflict
        return;
    }
    		
	if(function_exists('bb2_install'))
	{
		$msg .= '<div class="updated fade">You have the Bad Behavior plugin active! This plugin is known to block PayPal\'s payment notification (IPN). Please see <a href="http://www.tipsandtricks-hq.com/forum/topic/list-of-plugins-that-dont-play-nice-conflicting-plugins" target="_blank">this post</a> for more details.</div>';
	}
	if (function_exists('wp_cache_serve_cache_file')){// WP Supercache is active 
		$sc_integration_incomplete = false;
		global $wp_super_cache_late_init;
		if ( false == isset( $wp_super_cache_late_init ) || ( isset( $wp_super_cache_late_init ) && $wp_super_cache_late_init == 0 ) ){
			$sc_integration_incomplete = true;
		}		
		if(defined('TIPS_AND_TRICKS_SUPER_CACHE_OVERRIDE')){$sc_integration_incomplete=false;}
		if ($sc_integration_incomplete){		
			$msg .= '<div class="updated fade"><p>You have the WP Super Cache plugin active. Please make sure to follow <a href="http://www.tipsandtricks-hq.com/forum/topic/using-the-plugins-together-with-wp-super-cache-plugin" target="_blank">this instruction</a> to make it work with the WP eMember plugin. You can ignore this message if you have already applied the recommended changes.</p></div>';
		}	
	}
	if (function_exists('w3tc_pgcache_flush') && class_exists('W3_PgCache'))
	{				
		$integration_in_place = false;
		$w3_pgcache = & W3_PgCache::instance();
	    foreach ($w3_pgcache->_config->get_array('pgcache.reject.cookie') as $reject_cookie) {
	    	if (strstr($reject_cookie,"eMember_in_use") !== false){
	    		$integration_in_place = true;
	    	}   	
        }	
        if(!$integration_in_place){
        	$msg .= '<div class="updated fade"><p>You have the W3 Total Cache plugin active. Please make sure to follow <a href="http://www.tipsandtricks-hq.com/forum/topic/using-the-plugins-with-w3-total-cache-plugin" target="_blank">these instructions</a> to make it work with the WP eMember plugin.</p></div>';
        }	
	}
	
	//Check for duplicate copies of the eMember plugin
	$plugins_list = get_plugins();
	$plugin_names_arrray = array();
	foreach ($plugins_list as $plugin)
	{
		$plugin_names_arrray[] = $plugin['Name'];
	}
	$plugin_unqiue_count = array_count_values($plugin_names_arrray);
	if($plugin_unqiue_count['WP eMember']>1)
	{
		$msg .= '<div class="error"><br />It looks like you have two copies (potentially different versions) of the WP eMember plugin in your plugins directory. This can be the source of many problems. Please delete every copy of the eMember plugin from your plugins directory to clean it out then upload one fresh copy. <a href="http://www.tipsandtricks-hq.com/wordpress-membership/?p=3" target="_blank">More Info</a><br /><br /></div>';
	}
		
	if(!empty($msg))
	{
		echo $msg;	
	}
	else
	{
		//Set this flag so it does not do the conflict check on every page load
		$emember_config->setValue('wp_eMember_plugin_activation_check_flag','');	
		$emember_config->saveConfig();	
	}
}

/* Adds a custom section to the "advanced" Post and Page edit screens */
function eMember_add_custom_box() {    
    if( function_exists( 'add_meta_box' )) {
        $post_types = get_post_types();
        foreach($post_types as $post_type=>$post_type)
	    add_meta_box( 'eMember_sectionid', __( 'eMember Protection options', 
                'eMember_textdomain' ), 'eMember_inner_custom_box', $post_type, 'advanced' );
	} 
	else {//older version doesn't have custom post type so modification isn't needed.
	    add_action('dbx_post_advanced', 'eMember_old_custom_box' );
	    add_action('dbx_page_advanced', 'eMember_old_custom_box' );
	}
}

function wp_emember_shutdown_chores(){
    $emember_auth = Emember_Auth::getInstance();
    $emember_config = Emember_Config::getInstance();    
    if($emember_auth->loggedin_for_feed)$emember_auth->silent_logout();
}

function eMember_delete_enclosure($enclosure){
    global $post;
    global $emember_auth;
    $emember_auth = Emember_Auth::getInstance();
    $emember_config = Emember_Config::getInstance();    
    $is_protected = false;
    if($post->post_type =='post'){
        if($emember_auth->is_protected_post($post->ID))
            $is_protected = true;      
    }    
    if($post->post_type =='page'){
        if($emember_auth->is_protected_page($post->ID))
            $is_protected = true;
    }
    if($is_protected){
        if($emember_auth->isLoggedIn())
            return $enclosure;                
        return '';
    }
    return $enclosure;
}

/* Prints the inner fields for the custom post/page section */
function eMember_inner_custom_box() {
    global $post;
    global $emember_auth;
    $emember_auth = Emember_Auth::getInstance();
    $emember_config = Emember_Config::getInstance();    
    $id = $post->ID;
	$protection = $emember_auth->protection;
	$all_levels = dbAccess::findAll(WP_EMEMBER_MEMBERSHIP_LEVEL_TABLE, ' id != 1 ', ' id DESC ');
    // Use nonce for verification	
    $is_protected = false;
    $is_in = array();	
    if($post->post_type === 'page'){
		list($is_protected, $is_in) = emember_get_membership_of_post($id,$protection->page_list,$all_levels,'page');
    }else if($post->post_type === 'post'){ 
		list($is_protected, $is_in) = emember_get_membership_of_post($id,$protection->post_list,$all_levels,'post');
    }else if ($post->post_type === 'attachment'){ 
		list($is_protected, $is_in) = emember_get_membership_of_post($id,$protection->attachment_list,$all_levels,'attachment');
	}else if (in_array($post->post_type, get_post_types(array('public'   => true,'_builtin' => false)))){
		list($is_protected, $is_in) = emember_get_membership_of_post($id,$protection->custom_post_list,$all_levels,'custom_post');
	}
    echo '<input type="hidden" name="eMember_noncename" id="eMember_noncename" value="' . 
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
// The actual fields for data entry
    echo '<h4>'.  __("Do you want to protect this content?", 'eMember_textdomain' ) . '</h4>' ;
    echo '  <input type="radio" ' . ((!$is_protected)? 'checked': "") . '  name="eMember_protect_post" value="1" /> No, Do not protect this content. <br/>';
    echo '  <input type="radio" ' . (($is_protected)? 'checked': "") . '  name="eMember_protect_post" value="2" /> Yes, Protect this content.<br/>';  
    echo  '<h4>'.__("Select the membership level that can access this content:", 'eMember_textdomain' )  ."</h4>";  
    foreach ($all_levels as $level)
       echo '<input type="checkbox" ' . (isset($is_in[$level->id])? $is_in[$level->id]:""). ' name="eMember_protection_level['.$level->id .']" value="' . $level->id . '" /> ' .stripslashes($level->alias)  . "<br/>";        
}
function emember_get_membership_of_post($id,$post_list,$all_levels,$type){
    $is_protected = false;
    $is_in = array();
	$p_posts = unserialize( $post_list );
	$p_posts = is_bool($p_posts)? array() : $p_posts;
	$is_protected = in_array($id, $p_posts)? true: false;
	foreach($all_levels as $level){
		$l_posts = unserialize( $level->{$type .'_list'} );
		$l_posts = is_bool($l_posts)? array() : $l_posts;
		$is_in[$level->id] = in_array($id, $l_posts)? "checked='checked'":"";
	}        
	return array($is_protected,$is_in);
}
/* Prints the edit form for pre-WordPress 2.5 post/page */
function eMember_old_custom_box() {
  echo '<div class="dbx-b-ox-wrapper">' . "\n";
  echo '<fieldset id="eMember_fieldsetid" class="dbx-box">' . "\n";
  echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">' . 
        __( 'eMember Protection options', 'eMember_textdomain' ) . "</h3></div>";     
  echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';
  // output editing form
  eMember_inner_custom_box();
  // end wrapper
  echo "</div></div></fieldset></div>\n";
}

/* When the post is saved, saves our custom data */
function eMember_save_postdata( $post_id ) { 
    if(wp_is_post_revision( $post_id )) return;    
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if (isset($_POST['eMember_noncename']) &&!wp_verify_nonce( $_POST['eMember_noncename'], plugin_basename(__FILE__) )) {
        return $post_id;
    }

    // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
    // to do anything
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
        return $post_id;

    // Check permissions
    if ( isset($_POST['post_type']) && ('page' == $_POST['post_type'] )) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return $post_id;
    } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return $post_id;
    }
    if(!isset($_POST['eMember_protect_post'])) return; 
    // OK, we're authenticated: we need to find and save the data
    $enable_protection = array();
    global $emember_auth;
    $emember_auth = Emember_Auth::getInstance();
    $emember_config = Emember_Config::getInstance();
    $_POST['eMember_protect_post'] = strip_tags($_POST['eMember_protect_post']);
    $enable_protection['protect'] = $_POST['eMember_protect_post'];
    $enable_protection['level']   = isset($_POST['eMember_protection_level'])?$_POST['eMember_protection_level']:""; 
    $protection = $emember_auth->protection; 
	$isprotected = ($_POST['eMember_protect_post'] == 2);	
    if(isset($_POST['post_type'])&&('page' == $_POST['post_type'])){        		
        $protection = (array)($protection);
        $protection['page_list'] = emember_update_post_permission_for_widget($post_id, $isprotected, $protection['page_list']);	  	  	              
    }
    else if(isset($_POST['post_type'])&&('post' == $_POST['post_type'])){        		
        $protection = (array)($protection); 
        $protection['post_list']=emember_update_post_permission_for_widget($post_id, $isprotected, $protection['post_list']);	            	 
    }
    else if(isset($_POST['post_type'])&&('attachment' == $_POST['post_type'])){        		
        $protection = (array)($protection);
        $protection['attachment_list']=emember_update_post_permission_for_widget($post_id, $isprotected, $protection['attachment_list']);	            	 
    }
	else if (isset($_POST['post_type'])&&in_array($_POST['post_type'], get_post_types(array('public'   => true,'_builtin' => false)))){
        $protection = (array)($protection); 
        $protection['custom_post_list']=emember_update_post_permission_for_widget($post_id, $isprotected, $protection['custom_post_list']);	            	 
    }	
    dbAccess::update(WP_EMEMBER_MEMBERSHIP_LEVEL_TABLE,' id = 1' , $protection);
    $all_levels = dbAccess::findAll(WP_EMEMBER_MEMBERSHIP_LEVEL_TABLE, ' id != 1 ', ' id DESC ');
    foreach($all_levels as $level){
		$level = (array)($level);
		$isprotected = isset($_POST['eMember_protection_level'][$level['id']]); 
        if(isset($_POST['post_type'])&&('page' == $_POST['post_type'])){			
            $level['page_list'] = emember_update_post_permission_for_widget($post_id, $isprotected, $level['page_list']);			
        }
        else if (isset($_POST['post_type'])&&('post' == $_POST['post_type'])){
            $level['post_list'] = emember_update_post_permission_for_widget($post_id, $isprotected, $level['post_list']);	  	      	  	      	  	  
        }
		else if (isset($_POST['post_type'])&&('attachment' == $_POST['post_type'])){
            $level['attachment_list'] = emember_update_post_permission_for_widget($post_id, $isprotected, $level['attachment_list']);
		}
		else if (isset($_POST['post_type'])&&in_array($_POST['post_type'], get_post_types(array('public'   => true,'_builtin' => false)))){
            $level['custom_post_list'] = emember_update_post_permission_for_widget($post_id, $isprotected, $level['custom_post_list']);
		}
        dbAccess::update(WP_EMEMBER_MEMBERSHIP_LEVEL_TABLE,' id = '.$level['id'],  $level);	    
    }

    return $enable_protection;
}
function emember_update_post_permission_for_widget($post_id, $isprotected, $permission_list){
	$l_posts = unserialize( $permission_list );
	$l_posts = is_bool($l_posts)? array() : $l_posts;
	if($isprotected)
		$l_posts[] = $post_id;
	else 
		foreach($l_posts as $k=>$v)if($v===$post_id) unset($l_posts[$k]);
	$l_posts = array_unique($l_posts);
	$l_posts = serialize($l_posts);
	return $l_posts;
}
////////////////////////////////////////////

function lockdown_widget(){	
    $emember_auth = Emember_Auth::getInstance();
    $emember_config = Emember_Config::getInstance();
	$locking_option =  $emember_config->getValue('eMember_enable_domain_lockdown');    
	$altpopup = strtolower($emember_config->getValue('eMember_domain_lockdown_alt2popup'));	
    $current_url = strtolower(wp_emember_get_current_url());  
    
    $trimmed_cur_url = rtrim($current_url, "/");
    $trimed_altpopup = rtrim($altpopup, "/");
	if($trimed_altpopup == $trimmed_cur_url) return;
	
    $join_url = $emember_config->getValue('eMember_payments_page');
    if(emember_is_first_click()) return ;
    if($current_url == strtolower($join_url)) return;	
    
    $reg_url = $emember_config->getValue('eMember_registration_page'); 
    $reg_url_lowercase = strtolower($reg_url);   
	$reg_url_pos = strpos($current_url, $reg_url_lowercase);
	if ($reg_url_pos !== false){return;}
    
	$login_page_url = $emember_config->getValue('login_page_url');
    $login_page_lowercase = strtolower($login_page_url);
	if (strpos($current_url, $login_page_lowercase) !== false){return;}
		
    $forgot_pass_url = $emember_config->getValue('eMember_password_reset_page');
    if(!empty($forgot_pass_url)){
		if($current_url == strtolower($forgot_pass_url)) return;	
    }
    
    if($locking_option == 1){
	    $excluded_urls = $emember_config->getValue('eMember_domain_lockdown_exclude_url');
	    $excluded_urls = explode(',', trim($excluded_urls));
	    foreach ($excluded_urls as $url){
	    	$url = strtolower(trim($url));	    	
	    	if($trimmed_cur_url == $url){return;}//Need to use == So that when you exclude homepage the full site is not open       	
	    }
	    //Check for pattern matching
	    $excluded_url_patterns = $emember_config->getValue('eMember_domain_lockdown_exclude_url_pattern');
	    $excluded_url_patterns = explode(',', trim($excluded_url_patterns));
		foreach ($excluded_url_patterns as $url){
	    	$url = strtolower(trim($url));
	    	if(stripos($trimmed_cur_url,$url) !== false){return;}        	
	    }	        
    }
    else if ($locking_option == 2){
    	$allowed_url = true;
    	$included_urls = $emember_config->getValue('eMember_domain_lockdown_include_url');
    	$included_urls = explode(',', trim($included_urls));
    	foreach ($included_urls as $url){
    	    $url = trim($url);
    		if(stripos($current_url,$url) !== False) {
    			$allowed_url = false;
    			break;
    		}
    	}
    	if($allowed_url) return;    	    
    } 
    if(!$emember_auth->isLoggedIn()){
        echo '</head><body>';
        wp_emember_hook_password_reminder();
        if(empty($altpopup))	           
            include_once('emember_lockdown_popup.php');
		else{
            $msg = EMEMBER_REDIRECTION_MESSAGE;
		    echo '<html><head><meta http-equiv="refresh" content="1;url='.$altpopup.'" /></head>';
            echo '<body style="padding:25px; border:1px solid #CCC; margin:25px; max-width:350px;margin-left:auto;margin-right:auto;">';
            echo '<h1>'.$msg.'</h1>';
            echo '</body></html>';
        }
		exit;
    }
}

function escape_csv_value($value) {
    $value = str_replace(',',' ',$value);    
    $value = str_replace('"', '""', $value); // First off escape all " and make them ""        
    $value = trim($value, ",");
    if(preg_match('/,/', $value) or preg_match("/\n/", $value) or preg_match('/"/', $value)) { // Check if I have any commas or new lines
        return '"'.$value.'"'; // If I have new lines or commas escape them
    } else {
        return $value; // If no new lines or commas just return the value
    }
}

function export_members_to_csv(){
    global $wpdb;
    if(isset($_POST['wp_emember_export'])){
        $emember_config = Emember_Config::getInstance();    
        $wpememmeta = new WPEmemberMeta(); 
        $membership_tbl = $wpememmeta->get_table('membership_level');  
        $member_meta_tbl = $wpememmeta->get_table('member_meta');
        $membership_levels = $wpdb->get_results("SELECT id,alias FROM $membership_tbl WHERE id!=1", OBJECT );        
        $levels_indexed_by_pk = array();
        $customer_field_indices = array();
        foreach($membership_levels as $level)
            $levels_indexed_by_pk[$level->id] = $level->alias;   
        unset($membership_levels);            
        $member_table = $wpememmeta->get_table('member');
        $ret_member_db = $wpdb->get_results("SELECT * FROM $member_table ORDER BY member_id DESC", OBJECT);

        $csv_output = "User name, ";
        $csv_output.= " First Name,";
        $csv_output.= " Last Name,";
        $csv_output.= " Street,";
        $csv_output.= " City,";
        $csv_output.= " State,";
        $csv_output.= " ZIP,";
        $csv_output.= " Country,";
        $csv_output.= " Email,";
        $csv_output.= " Phone,";
        $csv_output.= " Membership Start,";
        $csv_output.= " Membership Expiry,";
        $csv_output.= " Member Since,";
        $csv_output.= " Membership Level,";
        $csv_output.= " Account State,";
        $csv_output.= " Last Accessed,";
        $csv_output.= " Last Accessed From IP,";
        $csv_output.= " Gender,";
        $csv_output.= " Referrer,";
        $csv_output.= " Reg Code,";
        $csv_output.= " Txn ID,";
        $csv_output.= " Subscr ID,";
        $csv_output.= " Company,";     
        if($emember_config->getValue('eMember_custom_field')){
            $custom_fields = get_option('emember_custom_field_type');
			$custom_names  = $custom_fields['emember_field_name'];
			$custom_types  = $custom_fields['emember_field_type'];
			$custom_extras = $custom_fields['emember_field_extra'];

            foreach($custom_names as $i=>$name){
                $name = stripslashes($name);
                $customer_field_indices[$i] = str_replace(array('\\','\'','(',')','[',']',' ', '"','%','<','>','?'), "_",$name);
                $csv_output .= " " .$name. ",";
            }
        }
        $csv_output.= "\n";
        foreach($ret_member_db as $result){
            $level = isset($levels_indexed_by_pk[$result->membership_level])? $levels_indexed_by_pk[$result->membership_level]:"";
            $csv_output .= escape_csv_value(stripslashes($result->user_name )). ','.
            escape_csv_value(stripslashes($result->first_name)). ', '. 
            escape_csv_value(stripslashes($result->last_name)). ', '. 
            escape_csv_value(stripslashes($result->address_street)). ', '.
            escape_csv_value(stripslashes($result->address_city)). ', '. 
            escape_csv_value(stripslashes($result->address_state)). ', '.
            escape_csv_value(stripslashes($result->address_zipcode)). ', '. 
            escape_csv_value(stripslashes($result->country)). ', '. 
            escape_csv_value(stripslashes($result->email)) . ','.
            escape_csv_value(stripslashes($result->phone)). ', '.
            escape_csv_value(stripslashes($result->subscription_starts)). ','.
            escape_csv_value(stripslashes(emember_get_expiry_by_member_id($result->member_id))). ','.
            escape_csv_value(stripslashes($result->member_since)). ','.
            escape_csv_value(stripslashes($level)). ','.
            escape_csv_value(stripslashes($result->account_state)). ','.
            escape_csv_value(stripslashes($result->last_accessed)). ','.
            escape_csv_value(stripslashes($result->last_accessed_from_ip)). ','.
            escape_csv_value(stripslashes($result->gender)). ', '. 
            escape_csv_value(stripslashes($result->referrer)). ','.
            escape_csv_value(stripslashes($result->reg_code)). ','.
            escape_csv_value(stripslashes($result->txn_id)). ','.
            escape_csv_value(stripslashes($result->subscr_id)). ','.
            escape_csv_value(stripslashes($result->company_name)). ', '; 
            if($emember_config->getValue('eMember_custom_field')){
                $custom_values = $wpdb->get_col("select meta_value from " . $member_meta_tbl 
                                 . ' WHERE  user_id=' . $result->member_id . ' AND meta_key="custom_field"' );
                $custom_values = unserialize(isset($custom_values[0])?$custom_values[0]:"");
				foreach($customer_field_indices as $i=>$n){				
					$v = isset($custom_values[$n])?$custom_values[$n]:"";
					if($custom_types[$i] == 'dropdown'){
						$m = explode(",", stripslashes($custom_extras[$i]));
						$e = array();		
						foreach($m as $k){
							$k = explode("=>", $k);
							$e[$k[0]] = $k[1];
						}							
						$v = $e[$v];
					} 
					$csv_output .= escape_csv_value(stripslashes($v)) . ',';
				}				
            }
            $csv_output .= "\n"; 
        }
        $filename = "member_list_".date("Y-m-d_H-i",time());
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");  
        header("Content-Description: File Transfer");  
        header("Content-Length: " . strlen($csv_output));   
        header("Content-type: text/x-csv");     
        header( "Content-disposition: attachment; filename=".$filename.".csv");
        print $csv_output;
        exit;   
    }
}

function wp_emember_hook_password_reminder(){
	$emember_config = Emember_Config::getInstance();
	$reset_page = $emember_config->getValue('eMember_password_reset_page');
	if(empty($reset_page))include_once('emember_password_sender_box.php');
}

function sync_emember_profile($wp_user_id){
    $wp_user_data = get_userdata($wp_user_id);
    $profile = dbAccess::find(WP_EMEMBER_MEMBERS_TABLE_NAME, ' user_name=\'' . $wp_user_data->user_login . '\'');
    $profile = (array)$profile;
    if(empty($profile)) return;
    $profile['user_name'] = $wp_user_data->user_login; 
    $profile['email']     = $wp_user_data->user_email;
    $profile['password']  = $wp_user_data->user_pass;
    $profile['first_name']= $wp_user_data->user_firstname;
    $profile['last_name'] = $wp_user_data->user_lastname;
    dbAccess::update(WP_EMEMBER_MEMBERS_TABLE_NAME,'member_id = ' . $profile['member_id'], $profile);   
}
function load_library(){
	  global $emember_config;
      $emember_config = Emember_Config::getInstance();
      global $wp_version;
      wp_enqueue_script('jquery');
      
      if(is_admin() &&isset($_GET['page'])&& in_array($_GET['page'],$emember_config->pages)){
	      //Load only on WP eMember admin pages
      	  wp_enqueue_style('eMember.adminstyle', WP_EMEMBER_URL.'/css/eMember_admin_style.css');
          wp_enqueue_style('eMember.style',WP_EMEMBER_URL.'/css/eMember_style.css');          
          wp_enqueue_style('validationEngine.jquery',WP_EMEMBER_URL.'/css/validationEngine.jquery.css');
          wp_enqueue_style('eMember.style.custom',WP_EMEMBER_URL.'/css/eMember_custom_style.css');          
          wp_enqueue_style('jquery.fileuploader',WP_EMEMBER_URL.'/css/jquery.fileuploader.css');          
          wp_enqueue_style('jquery.tools.dateinput',WP_EMEMBER_URL.'/css/jquery.tools.dateinput.css');          
          wp_enqueue_script('jquery.dynamicField',WP_EMEMBER_URL.'/js/jquery.dynamicField-1.0.js');                                
	      wp_enqueue_script('jquery.validationEngine',WP_EMEMBER_URL.'/js/jquery.validationEngine.js');
	      wp_enqueue_script('jquery.hint',WP_EMEMBER_URL.'/js/jquery.hint.js');
		  if(version_compare($wp_version, '3.5', '<')){//fix until jquerytools releases version compatible with jquery 1.8.3
			  wp_enqueue_script('jquery.tools',WP_EMEMBER_URL.'/js/jquery.tools.min.js');
		  }
		  else{
             wp_enqueue_script('jquery.tools',WP_EMEMBER_URL.'/js/jquery.tools18.min.js');
		  }
	      wp_enqueue_script('jquery.libs',WP_EMEMBER_URL.'/js/jquery.libs.js');     
	      wp_enqueue_script('jquery.fileuploader',WP_EMEMBER_URL.'/js/jquery.fileuploader.js');               
		  wp_enqueue_script('jquery.pagination',WP_EMEMBER_URL.'/js/jquery.pagination-2.0rc.js');
		  wp_enqueue_script('jquery.confirm',WP_EMEMBER_URL.'/js/jquery.confirm-1.3.js');
      }
      if(!is_admin()){
      	  //Load on front pages of the site
          wp_enqueue_style('eMember.style',WP_EMEMBER_URL.'/css/eMember_style.css');        
          wp_enqueue_style('eMember.style.custom',WP_EMEMBER_URL.'/css/eMember_custom_style.css');
          wp_enqueue_style('validationEngine.jquery',WP_EMEMBER_URL.'/css/validationEngine.jquery.css');
          wp_enqueue_style('jquery.fileuploader',WP_EMEMBER_URL.'/css/jquery.fileuploader.css');       
	      wp_enqueue_script('jquery.fileuploader',WP_EMEMBER_URL.'/js/jquery.fileuploader.js');                  
	      wp_enqueue_script('jquery.validationEngine',WP_EMEMBER_URL.'/js/jquery.validationEngine.js');
	      wp_enqueue_script('jquery.hint',WP_EMEMBER_URL.'/js/jquery.hint.js');
      	  if(version_compare($wp_version, '3.5', '<')){//fix until jquerytools releases version compatible with jquery 1.8.3
			  wp_enqueue_script('jquery.tools',WP_EMEMBER_URL.'/js/jquery.tools.min.js');
		  }
		  else{
             wp_enqueue_script('jquery.tools',WP_EMEMBER_URL.'/js/jquery.tools18.min.js');
		  }
	      wp_enqueue_script('jquery.libs',WP_EMEMBER_URL.'/js/jquery.libs.js');
          wp_enqueue_script('jquery.pagination',WP_EMEMBER_URL.'/js/jquery.pagination-2.0rc.js');
          wp_enqueue_script('jquery.confirm',WP_EMEMBER_URL.'/js/jquery.confirm-1.3.js');
	  }
}

function wp_eMember_widget_init(){
    $widget_options = array('classname' => 'wp_eMember_widget', 'description' => __( "Display WP eMember Login.") );
    wp_register_sidebar_widget('wp_eMember_widget', __('WP eMember Login'), 'show_wp_eMember_login_widget', $widget_options);
}

//Add the Admin Menus
if (is_admin()){
	$emember_config = Emember_Config::getInstance();
	$selected_permission = $emember_config->getValue('emember_management_permission');
	if(empty($selected_permission)){
		define("EMEMBER_MANAGEMENT_PERMISSION", "activate_plugins"); 
        // instead, can use 8 too. http://codex.wordpress.org/User_Levels#User_Level_Capability_Table
	}else{
		define("EMEMBER_MANAGEMENT_PERMISSION", $selected_permission);
	}
	function wp_eMember_add_admin_menu(){
		add_menu_page(__("WP eMember", 'wp_eMember'), __("WP eMember", 'wp_eMember'), EMEMBER_MANAGEMENT_PERMISSION, __FILE__, "wp_eMember_dashboard");
		add_submenu_page(__FILE__, __("Dashboard WP eMember", 'wp_eMember'), __('Dashboard', 'wp_eMember'), EMEMBER_MANAGEMENT_PERMISSION, __FILE__, "wp_eMember_dashboard");
		add_submenu_page(__FILE__, __("Settings WP eMember", 'wp_eMember'), __("Settings", 'wp_eMember'), EMEMBER_MANAGEMENT_PERMISSION, 'eMember_settings_menu', "wp_eMember_settings");      
		add_submenu_page(__FILE__, __("Members WP eMember", 'wp_eMember'), __("Members", 'wp_eMember'), EMEMBER_MANAGEMENT_PERMISSION, 'wp_eMember_manage', "wp_eMember_members");      
		add_submenu_page(__FILE__, __("Membership Level WP eMember", 'wp_eMember'), __("Membership Level", 'wp_eMember'), EMEMBER_MANAGEMENT_PERMISSION, 'eMember_membership_level_menu', "wp_eMember_membership_level");      
		add_submenu_page(__FILE__, __("Admin Functions", 'wp_eMember'), __("Admin Functions", 'wp_eMember'), EMEMBER_MANAGEMENT_PERMISSION, 'eMember_admin_functions_menu', "wp_eMember_admin_functions_menu");
        do_action("emember_addon_menu");
	}
	//Include menus
	require_once(dirname(__FILE__).'/eMember_members_menu.php');
	require_once(dirname(__FILE__).'/eMember_dashboard_menu.php');
	require_once(dirname(__FILE__).'/eMember_membership_level_menu.php');
	require_once(dirname(__FILE__).'/eMember_settings_menu.php');
	require_once(dirname(__FILE__).'/eMember_admin_functions_menu.php');   
}
// Insert the options page to the admin menu

function emember_menu(){
    global $wpemem_evt;
    $wpemem_evt = isset($_REQUEST['event'])?trim($_REQUEST['event']):"";
    switch($wpemem_evt){
       case 'logout':       
          wp_emem_logout();
          break;
       case 'signout':       
          wp_emem_logout();
          break;          
       case 'delete_account':
          delete_account();
           break;
       case 'check_name':
           do_action( 'wp_ajax_nopriv_check_name');
           die('0');break;
       case 'access_list_ajax':
          do_action( 'wp_ajax_access_list_ajax');
           die('0');break;
       case 'item_list_ajax':
           do_action( 'wp_ajax_item_list_ajax');
           die('0');break;
       case 'check_level_name':
          do_action( 'wp_ajax_check_level_name');
          die('0');break;
       case 'send_mail':
          do_action( 'wp_ajax_send_mail');
          die('0');break;
       case 'bookmark_ajax':
           do_action('wp_ajax_add_bookmark');
           die('0');break;
       case 'wp_user_list_ajax':
           do_action('wp_ajax_wp_user_list_ajax');
           die('0');break;           
       case 'emember_user_list_ajax':
           do_action('wp_ajax_emember_user_list_ajax');
           die('0');break;           
       case 'emember_user_count_ajax':
           do_action('wp_ajax_emember_user_count_ajax');
           die('0');break;           
       case 'emember_upload_ajax':
           do_action('wp_ajax_emember_upload_ajax');
           die('0');break;           
       case 'emember_public_user_list_ajax':
           do_action('wp_ajax_nopriv_emember_public_user_list_ajax');
           die('0');break;           
       case 'emember_public_user_profile_ajax':
           do_action('wp_ajax_nopriv_emember_public_user_profile_ajax');
           die('0');break;            
    }
}

function comment_text_action($content){
    return auth_check_comment($content);
}

function secure_content($content){
    global $post;
	$args=array('public'   => true,'_builtin' => false); 
	$post_types=get_post_types($args);	
    if(is_category()) return auth_check_category($content); 
    if($post->post_type === 'page') return auth_check_page($content);
	if($post->post_type === 'post') return auth_check_post($content);
	//if($post->post_type === 'attachment') return auth_check_attachment($content);
	if(in_array($post->post_type, $post_types))return auth_check_custom_post($content);
	return $content;
}

function filter_eMember_public_user_list($content){
    include_once('public_user_directory.php');
    $pattern = '#\[wp_eMember_public_user_list:end]#';
    preg_match_all ($pattern, $content, $matches);
    foreach ($matches[0] as $match){
        $replacement = print_eMember_public_user_list();
        $content = str_replace ($match, $replacement, $content);
    }	    
    return $content;    	
}

function show_eMember_public_user_list($atts)
{
	extract(shortcode_atts(array(
		'no_email' => '',
	), $atts));		
	include_once('public_user_directory.php');
	return print_eMember_public_user_list($no_email);
}


function delete_account(){        
    global $emember_config;
    global $emember_auth;
    $emember_auth = Emember_Auth::getInstance();
    $emember_config = Emember_Config::getInstance();    
    if(!$emember_auth->isLoggedIn()) return;
    $f = $emember_config->getValue('eMember_allow_account_removal');
    if($f){
        $f = $emember_config->getValue('eMember_allow_wp_account_removal');
        if($f){			
            $wp_user_id = username_exists($emember_auth->getUserInfo('user_name'));
            $ud = get_userdata($wp_user_id);
            if(isset($ud->wp_capabilities['administrator'])||$ud->wp_user_level == 10){
              if($_GET['confirm']!=2){
                  $u = get_bloginfo('wpurl');
                  $_GET['confirm'] = 2;
                  $u .= '?' . http_build_query($_GET);
                  $warning = "<html><body><div id='message' style=\"color:red;\" ><p>You are about to delete an account that has admin privilege.
                  If you are using WordPress user integration then this will delete the corresponding user
                  account from WordPress and you may not be able to log in as admin with this account.
                  Continue? <a href='". $u. "'>yes</a>/<a href='javascript:void(0);' onclick='top.document.location=\"". get_bloginfo('wpurl') . "\";' >no</a></p></div></body></html>";
                  echo $warning;
                  exit;
              }	
            }
            wp_clear_auth_cookie();
            if($wp_user_id){
            	include_once(ABSPATH.'wp-admin/includes/user.php');
            	wp_delete_user( $wp_user_id, 1 ); //assigns all related to this user to admin.
            }
        }
        $ret = dbAccess::delete(WP_EMEMBER_MEMBERS_TABLE_NAME, 'member_id=' . $emember_auth->getUserInfo('member_id'));
        $ret = dbAccess::delete(WP_EMEMBER_MEMBERS_META_TABLE, 'user_id='.$emember_auth->getUserInfo('member_id'));	
        $emember_auth = Emember_Auth::getInstance();
        $emember_config = Emember_Config::getInstance();        
        global $emember_auth;
        $emember_auth->logout();
        wp_emember_redirect_to_url(get_bloginfo('wpurl'));exit;
    }	
}

function wp_eMember_cancel_subscription_link_handler($atts)
{
	$auth = Emember_Auth::getInstance();
	$user_id = $auth->getUserInfo('member_id');
	if (!empty($user_id)){	
		$member_email = $auth->getUserInfo('email');
		$emember_config = Emember_Config::getInstance(); 
		if($emember_config->getValue('eMember_enable_sandbox') == 1){//Sandbox
			$output .= '<a href="https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias='.$member_email.'" _fcksavedurl="https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias='.$member_email.'">';
			$output .= '<img border="0" src="'.WP_EMEMBER_URL.'/images/btn_unsubscribe_LG.gif" alt="Unsubscribe" /></a>';
		}
		else{
			$output .= '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias='.$member_email.'" _fcksavedurl="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias='.$member_email.'">';
			$output .= '<img border="0" src="'.WP_EMEMBER_URL.'/images/btn_unsubscribe_LG.gif" alt="Unsubscribe" /></a>';		
		}
	}
	else{
		$output .= '<p>'.EMEMBER_NOT_LOGGED_IN.'</p>';
	}	
	return $output;
}

function wp_eMember_renew_membership_for_free_handler($atts){
	extract(shortcode_atts(array(
		'level' => '',
	), $atts));	
	//TODO - If level parameter is not empty then also offer to upgrade to this level?    
	$auth = Emember_Auth::getInstance();
	$user_id = $auth->getUserInfo('member_id');
	if (!empty($user_id)){		
		$output = "";
		$output .= '<div class="free_eMember_renewal_form">';
	    if(isset($_POST['eMember_free_renewal'])){
			$member_id = $_POST['eMember_free_renewal'];
			$curr_date = (date ("Y-m-d"));
			dbAccess::update(WP_EMEMBER_MEMBERS_TABLE_NAME,'member_id='.$member_id, array('subscription_starts'=>$curr_date,'account_state'=>'active'));		    	
			$output .= "Membership Renewed!";
	    }
	    else{
			$output .= '<form name="free_eMember_renewal" method="post" action="">';
			$output .= '<input type="hidden" name="eMember_free_renewal" value="'.$user_id.'" />';
			$output .= '<input type="submit" name="eMember_free_renew_submit" value="Renew" />';
			$output .= '</form>';			
	    }
	    $output .= '</div>';
		return $output;
	}
	else
		return "You must be logged in to renew a membership!";
}

function wp_eMember_upgrade_membership_level_to_handler($atts){
	extract(shortcode_atts(array(
		'level' => '',
		'button_text' => 'Upgrade',
		'redirect_to' => '',
	), $atts));
	if(empty($level)){
		return '<div class="emember_error">Error! You must specify a membership level in the level parameter.</div>';
	}

    $emember_auth = Emember_Auth::getInstance();
	$user_id = $emember_auth->getUserInfo('member_id');	
	if (!empty($user_id)){
		$output = "";
		$output .= '<div class="eMember_level_upgrade_form">';
	    if(isset($_POST['eMember_level_upgrade_submit']) && $_POST['emember_form_key_value'] == $level && emember_multi_submit_check()){
			$member_id = $_POST['eMember_level_upgrade'];
			$target_membership_level = $level;
			emember_update_membership_level($member_id, $target_membership_level);
			$firstname = $emember_auth->getUserInfo('first_name');
			$lastname = $emember_auth->getUserInfo('last_name');
			$emailaddress = $emember_auth->getUserInfo('email');
			eMember_level_specific_autoresponder_signup($target_membership_level,$firstname,$lastname,$emailaddress);
			if(!empty($redirect_to)){
				wp_emember_redirect_to_url($redirect_to);
			}
			$output .= '<p>'.EMEMBER_LEVEL_UPDATED.'</p>';
	    }
	    else{
			$output .= '<form name="eMember_level_upgrade_form" method="post" action="">';
			$output .= '<input type="hidden" name="eMember_level_upgrade" value="'.$user_id.'" />';
			$output .= '<input type="hidden" name="emember_form_time_value" value="'.strtotime("now").'" />';
			$output .= '<input type="hidden" name="emember_form_key_value" value="'.$level.'" />';
			$output .= '<input type="submit" name="eMember_level_upgrade_submit" class="eMember_level_upgrade_submit" value="'.$button_text.'" />';
			$output .= '</form>';			
	    }
	    $output .= '</div>';
		return $output;
	}
	else{
		return '<p>'.EMEMBER_MUST_BE_LOGGED_IN_TO_UPDATE_LEVEL.'</p>';
	}
}

function eMember_handle_affiliate_signup($user_name,$pwd,$afirstname,$alastname,$aemail,$referrer)
{    	
	global $wpdb,$emember_config;
    $emember_config = Emember_Config::getInstance();    
	if (function_exists('wp_aff_platform_install'))
	{		
		$members_table_name = $wpdb->prefix . "wp_eMember_members_tbl";
		$query_db = $wpdb->get_row("SELECT * FROM $members_table_name WHERE user_name = '$user_name'", OBJECT);
	    if($query_db)
	    {
	    	$eMember_id = $query_db->member_id;
	    	$membership_level = $query_db->membership_level;
	    	$allowed_levels = $emember_config->getValue('wp_eMember_affiliate_account_restriction_list');//example value "1,2,3";
	    	if(!empty($allowed_levels))//check if this level should be allowed to have an affiliate account
	    	{
		    	$pieces = explode(",", $allowed_levels);
		    	if(!in_array($membership_level,$pieces))//no permission for affilaite account creation
		    	{	    		
		    		return;
		    	}
	    	}
			$commission_level = get_option('wp_aff_commission_level');//This must use the get_option and not getValue
			$date = (date ("Y-m-d"));
			wp_aff_create_affilate($user_name,$pwd,'','',$afirstname,$alastname,'',$aemail,'','','','','','','','',$date,'',$commission_level,$referrer);
		    wp_aff_send_sign_up_email($user_name,$pwd,$aemail);
	    	
	    }
	    else
	    {
	    	echo "<br />Error! This username does not exist in the member database!";
	    }
	}
}
function eMember_handle_affiliate_profile_update(){
	global $emember_config;
    $emember_config = Emember_Config::getInstance();    
	$eMember_auto_affiliate_account_login = $emember_config->getValue('eMember_auto_affiliate_account_login');
	if (function_exists('wp_aff_platform_install') && $eMember_auto_affiliate_account_login){
	    //update the affiliate account profile
		global $wpdb;
		$affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;	  
                $_POST['wp_emember_pwd'] = strip_tags($_POST['wp_emember_pwd']);
                $_POST['wp_emember_firstname'] = strip_tags($_POST['wp_emember_firstname']);
                $_POST['wp_emember_lastname'] = strip_tags($_POST['wp_emember_lastname']);
                $_POST['wp_emember_email'] = strip_tags($_POST['wp_emember_email']);
                $_COOKIE['user_id'] = strip_tags($_COOKIE['user_id']);
		if(!empty($_POST['wp_emember_pwd'])){ 
	    	$password = $_POST['wp_emember_pwd'];
			include_once(ABSPATH.WPINC.'/class-phpass.php');
			$wp_hasher = new PasswordHash(8, TRUE);
			$password = $wp_hasher->HashPassword($password);			 
	    	$updatedb = "UPDATE $affiliates_table_name SET pass = '".$password."', firstname = '".$_POST['wp_emember_firstname']."', lastname = '".$_POST['wp_emember_lastname']."', email = '".$_POST['wp_emember_email']."' WHERE refid = '".$_COOKIE['user_id']."'";
		}
		else{
			$updatedb = "UPDATE $affiliates_table_name SET firstname = '".$_POST['wp_emember_firstname']."', lastname = '".$_POST['wp_emember_lastname']."', email = '".$_POST['wp_emember_email']."' WHERE refid = '".$_COOKIE['user_id']."'";
		}
	    $results = $wpdb->query($updatedb);				
	}
}
function eMember_handle_affiliate_password_reset($aff_email, $encrypted_pass)
{
	if(empty($encrypted_pass) || empty($aff_email)){return;}
	
	$emember_config = Emember_Config::getInstance();    
	$eMember_auto_affiliate_account_login = $emember_config->getValue('eMember_auto_affiliate_account_login');
	if (function_exists('wp_aff_platform_install') && $eMember_auto_affiliate_account_login){	
		global $wpdb;
		$affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
		//wp_aff_check_if_account_exists($aff_email) - Do an additional check if needed
		$updatedb = "UPDATE $affiliates_table_name SET pass = '".$encrypted_pass."' WHERE email = '".$aff_email."'";
		$results = $wpdb->query($updatedb);
		eMember_log_debug("Affiliate password updated for affiliate account with email: ".$aff_email,true);
	}
}

function eMember_get_aff_referrer(){
        $referrer = "";
        if (!empty($_SESSION['ap_id']))
            $referrer = $_SESSION['ap_id'];
        else if (isset($_COOKIE['ap_id']))
            $referrer = $_COOKIE['ap_id'];

        return $referrer;
}
function eMember_is_post_protected ($post_id){
    global $wpdb;
    $wpdb->prefix . "wp_eMember_membership_tbl";
    $query = "SELECT post_list FROM " . $wpdb->prefix . "wp_eMember_membership_tbl WHERE id = 1;";
    $post_list = unserialize($wpdb->get_var($wpdb->prepare($query)));
    if(!$post_list) return false;
    return in_array($post_id, $post_list);
}
function emember_dynamic_js_load(){
    if(isset($_GET['emember_load_js'])){
        header('Content-type: text/javascript');
        switch($_GET['emember_load_js']){
            case 'registration':
            include(WP_EMEMBER_PATH.'/js/registration.php');
            break;
            case 'profile':
            include(WP_EMEMBER_PATH.'/js/registration.php');			
            include(WP_EMEMBER_PATH.'/js/profile.php');
            break;
        }
        exit(0);
    }
}
