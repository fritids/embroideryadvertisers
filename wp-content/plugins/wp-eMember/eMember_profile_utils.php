<?php
function filter_eMember_edit_profile_form($content){
    $auth = Emember_Auth::getInstance(); 

    $pattern = '#\[wp_eMember_profile_edit_form:end]#';
    preg_match_all ($pattern, $content, $matches);
    if((count($matches[0])>0)&&!$auth->isLoggedIn()){
        return EMEMBER_PROFILE_MESSAGE;
    }

    foreach ($matches[0] as $match){
        $replacement = print_eMember_edit_profile_form();
        $content = str_replace ($match, $replacement, $content);
    }

    return $content;
}

function print_eMember_edit_profile_form(){
    return show_edit_profile_form();
}

function show_edit_profile_form()
{
    $result = apply_filters('emember_profile_form_override', '');
    if(!empty($result)) return $result; 

    $auth = Emember_Auth::getInstance(); 
    if(!$auth->isLoggedIn()) return EMEMBER_PROFILE_MESSAGE;
	if(isset($_POST['eMember_update_profile']) && isset($_POST['eMember_profile_update_result']))
	{
		$output = $_POST['eMember_profile_update_result'];
		if(!empty($_POST['wp_emember_pwd'])){//Password has been changed
			$output .= '<div class="emember_warning">'.EMEMBER_PASSWORD_CHANGED_RELOG_RECOMMENDED.'</div>';
		}		
		return $output;
	}
    global $wpdb;
    global $emember_config;	    
    global $emember_auth;
    $emember_auth = Emember_Auth::getInstance();
    $emember_config = Emember_Config::getInstance();    
	$d = WP_EMEMBER_URL.'/images/default_image.gif';
    $member_id  = $emember_auth->getUserInfo('member_id');
    $resultset  = dbAccess::find(WP_EMEMBER_MEMBERS_TABLE_NAME, ' member_id=' . $wpdb->escape($member_id));
    $edit_custom_fields = dbAccess::find(WP_EMEMBER_MEMBERS_META_TABLE, ' user_id=' . $wpdb->escape($member_id) . ' AND meta_key=\'custom_field\'');
    $edit_custom_fields = unserialize($edit_custom_fields->meta_value);
    $username   = $resultset->user_name;
    $first_name = $resultset->first_name;
    $last_name  = $resultset->last_name;
    $phone      = $resultset->phone;
    $email      = $resultset->email;
    $password   = $resultset->password;
    $address_street  = $resultset->address_street;
    $address_city    = $resultset->address_city;
    $address_state   = $resultset->address_state;
    $address_zipcode = $resultset->address_zipcode;
    $country         = $resultset->country;
    $gender          = $resultset->gender;
    $company         = $resultset->company_name;    
    $image_url       = null;
    $image_path  = null;
	$upload_dir  = wp_upload_dir();
    $upload_url  = $upload_dir['baseurl'].'/emember/';
	$pro_pic     = $emember_auth->getUserInfo('profile_image'); 
	$use_gravatar = $emember_config->getValue('eMember_use_gravatar');
	if($use_gravatar)
    	$image_url = WP_EMEMBER_GRAVATAR_URL . "/" . md5(strtolower($email)) . "?d=" . urlencode($d) . "&s=" . 96;
    else if(!empty($pro_pic)){
    	$image_url = $upload_url . $pro_pic.'?'. time();
        $pro_pic = $member_id;
    }
    else
    	$image_url = WP_EMEMBER_URL . '/images/default_image.gif';    
    
    $f = $emember_config->getValue('eMember_allow_account_removal');
    $delete_button = empty($f)? '': '<a id="delete_account_btn" href="'.get_bloginfo('wpurl').
                     '?event=delete_account" >'.EMEMBER_DELETE_ACC.'</a> ';
    ob_start();
    echo isset($msg)?'<span class="emember_error">'.$msg . '</span>': '';    
	?>
    <script type="text/javascript" src="<?php echo site_url();?>?emember_load_js=profile&id=wp_emember_profileUpdateForm"></script>        	
    <form action="" method="post" name="wp_emember_profileUpdateForm" id="wp_emember_profileUpdateForm" >
    <input type="hidden" name="member_id" id="member_id" value ="<?php echo $member_id;?>" />
    <?php wp_nonce_field('emember-update-profile-nonce'); ?>
    <table width="95%" border="0" cellpadding="3" cellspacing="3" class="forms">	
	<tr>
		<td><label class="eMember_label"> <?php echo EMEMBER_USERNAME;?>: </label></td>
		<td><label class="eMember_highlight"><?php echo $username;?></label></td>
	</tr>
	<?php if($emember_config->getValue('eMember_profile_thumbnail')):?>
	<tr>
		<td><label class="eMember_label"><?php echo EMEMBER_PROFILE_IMAGE;?>: </label></td>
		<td>
            <div>
                <div>
		            <img id="emem_profile_image" src="<?php echo $image_url; ?>"  width="100px" height="100px"/>
                </div>
                <?php if(empty($use_gravatar)):?>
                <div id="emember-file-uploader">
	                <noscript>			
		                <p>Please enable JavaScript to use file uploader.</p>
		                <!-- or put a simple form for upload here -->
	                </noscript>         
                </div>                
                <div id="emember-profile-remove-cont"  class="qq-remove-file" style="display:none;">
                    <a id="remove_button" href="<?php echo $pro_pic; ?>"><?php echo EMEMBER_REMOVE; ?></a>
                </div>
                <?php endif;?>
                <div class="clear"></div>
            </div>
		</td>
    </tr>
    <?php endif;?>
    <?php if($emember_config->getValue('eMember_edit_firstname')):?>
    <tr>
       <td><label for="wp_emember_firstname" class="eMember_label"><?php echo EMEMBER_FIRST_NAME;?>: </label></td>
       <td><input type="text" id="wp_emember_firstname" name="wp_emember_firstname" size="20" value="<?php echo $first_name;?>" class="<?php echo $emember_config->getValue('eMember_edit_firstname_required')? 'validate[required] ': "";?>eMember_text_input" /></td>
    </tr>
    <?php endif;?>
    <?php if($emember_config->getValue('eMember_edit_lastname')):?>
    <tr>
       <td><label for="wp_emember_lastname" class="eMember_label"><?php echo EMEMBER_LAST_NAME;?>: </label></td>
       <td><input type="text" id="wp_emember_lastname"  name="wp_emember_lastname" size="20" value="<?php echo $last_name;?>" class="<?php echo $emember_config->getValue('eMember_edit_lastname_required')? 'validate[required] ': "";?>eMember_text_input" /></td>
    </tr>
    <?php endif;?>
    <?php if($emember_config->getValue('eMember_edit_company')):?>
    <tr>
       <td><label for="wp_emember_company_name" class="eMember_label"><?php echo EMEMBER_COMPANY ?>: </label></td>
       <td><input type="text" id="wp_emember_company_name"  name="wp_emember_company_name" size="20" value="<?php echo $company ?>" class="<?php echo $emember_config->getValue('eMember_edit_company_required')? 'validate[required] ': "";?>eMember_text_input" /></td>
    </tr>
    <?php endif;?>    
    <?php if($emember_config->getValue('eMember_edit_email')):?>
    <tr>
       <td><label for="wp_emember_email" class="eMember_label"><?php echo EMEMBER_EMAIL;?>: </label></td>
       <td><input type="text" id="wp_emember_email" name="wp_emember_email" size="20" value="<?php echo $email;?>" class="<?php echo $emember_config->getValue('eMember_edit_email_required')? 'validate[required] ': "";?>eMember_text_input" /></td>
    </tr>
    <?php endif;?>
    <?php if($emember_config->getValue('eMember_edit_phone')):?>
    <tr>
       <td><label for="wp_emember_phone" class="eMember_label"><?php echo EMEMBER_PHONE?>: </label></td>
       <td><input type="text" id="wp_emember_phone" name="wp_emember_phone" size="20" value="<?php echo $phone ?>" class="<?php echo $emember_config->getValue('eMember_edit_phone_required')? 'validate[required] ': "";?>eMember_text_input" /></td>
    </tr>    
    <?php endif;?>
    <tr>
       <td><label for="wp_emember_pwd" class="eMember_label"><?php echo EMEMBER_PASSWORD ?>: </label></td>
       <td><input type="password" id="wp_emember_pwd" name="wp_emember_pwd" size="20" value="" class="eMember_text_input" /><br/></td>
    </tr>
    <tr>
       <td><label for="wp_emember_pwd_r" class="eMember_label"><?php echo EMEMBER_PASSWORD_REPEAT ?>: </label></td>
       <td><input type="password" id="wp_emember_pwd_r" name="wp_emember_pwd_r" size="20" value="" class="validate[equals[wp_emember_pwd]] eMember_text_input" /><br/></td>
    </tr>    
    <?php if($emember_config->getValue('eMember_edit_street')):?>    
    <tr>
       <td><label for="wp_emember_street" class="eMember_label"><?php echo EMEMBER_ADDRESS_STREET?>: </label></td>
       <td><input type="text" id="wp_emember_street" name="wp_emember_street" size="20" value="<?php echo $address_street ?>" class="<?php echo $emember_config->getValue('eMember_edit_street_required')? 'validate[required] ': "";?>eMember_text_input" /></td>
    </tr>
    <?php endif;?>
    <?php if($emember_config->getValue('eMember_edit_city')):?>    
    <tr>    
       <td><label for="wp_emember_city" class="eMember_label"><?php echo EMEMBER_ADDRESS_CITY ?>: </label></td>
       <td><input type="text" id="wp_emember_city" name="wp_emember_city" size="20" value="<?php echo $address_city?>" class="<?php echo $emember_config->getValue('eMember_edit_city_required')? 'validate[required] ': "";?>eMember_text_input" /></td>
    </tr>
    <?php endif;?>
    <?php if($emember_config->getValue('eMember_edit_state')):?>    
    <tr>
       <td><label for="wp_emember_state" class="eMember_label"><?php echo EMEMBER_ADDRESS_STATE ?>: </label></td>
       <td><input type="text"  id="wp_emember_status" name="wp_emember_state" size="20" value="<?php echo $address_state ?>" class="<?php echo $emember_config->getValue('eMember_edit_state_required')? 'validate[required] ': "";?>eMember_text_input" /></td>
    </tr>
    <?php endif;?>
    <?php if($emember_config->getValue('eMember_edit_zipcode')):?>    
    <tr>
       <td><label for="wp_emember_zipcode" class="eMember_label"><?php echo EMEMBER_ADDRESS_ZIP ?>: </label></td>
       <td><input type="text"  id="wp_emember_zipcode" name="wp_emember_zipcode" size="20" value="<?php echo $address_zipcode ?>" class="<?php echo $emember_config->getValue('eMember_edit_zipcode_required')? 'validate[required] ': "";?>eMember_text_input" /></td>
    </tr>
    <?php endif;?>
    <?php if($emember_config->getValue('eMember_edit_country')):?>
    
    <tr>
       <td><label for="wp_emember_country" class="eMember_label"><?php echo EMEMBER_ADDRESS_COUNTRY ?>: </label></td>
       <td><input type="text"  id="wp_emember_country" name="wp_emember_country" size="20" value="<?php echo $country ?>" class="<?php echo $emember_config->getValue('eMember_edit_country_required')? 'validate[required] ': "";?>eMember_text_input" /></td>
    </tr>
    <?php endif;?>
    <?php if($emember_config->getValue('eMember_edit_gender')):?>    
	<tr >
		<td > <label for="wp_emember_gender" class="eMember_label"><?php echo EMEMBER_GENDER ?>: </label></td>
		<td>
	   <select name="wp_emember_gender" id="wp_emember_gender">
	      <option  <?php echo (($gender ==='male') ? 'selected=\'selected\'' : '' ) ?> value="male"><?php echo EMEMBER_GENDER_MALE ?></option>
	      <option  <?php echo (($gender ==='female') ? 'selected=\'selected\'' : '' ) ?> value="female"><?php echo EMEMBER_GENDER_FEMALE ?></option>      
	      <option  <?php echo (($gender ==='not specified') ? 'selected=\'selected\'' : '' ) ?> value="not specified"><?php echo EMEMBER_GENDER_UNSPECIFIED ?></option>      
	   </select>
		</td>
	</tr>        
	<?php 
	endif;
	include ('custom_field_template.php');	
	?> 
    <tr>
    <td >
      <?php echo $delete_button ?>
    </td>
    <td>
       <input class="eMember_button" name="eMember_update_profile" type="submit" id="eMember_update_profile" class="button" value="<?php echo EMEMBER_UPDATE ?>" />
    </td>
    </tr>
    </table>
    </form><br />
	<?php 
    $output = ob_get_contents();
    ob_end_clean();	
    return $output;
}
