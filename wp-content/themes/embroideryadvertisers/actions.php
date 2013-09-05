<?php
/*
Template Name: Action
*/
$action = $_GET['action'];
$verify = $_GET['verify'];
global $current_user;
get_currentuserinfo();

if ($verify!=NULL) { ?>
	<meta http-equiv="refresh" content="0; url=http://embroideryadvertisers.com/wp-login.php?redirect_to=/&amp;action=verifyemail&amp;verification_code=<? echo $verify;?>">
<? }
if ($action=='chatupdate') { get_header();?>

<script>
jQuery(document).ready(function() {

	jQuery(".post-like a").click(function(){
	
		heart = jQuery(this);
	
		// Retrieve post ID from data attribute
		post_id = heart.data("post_id");
		
		// Ajax call
		jQuery.ajax({
			type: "post",
			url: ajax_var.url,
			data: "action=post-like&nonce="+ajax_var.nonce+"&post_like=&post_id="+post_id,
			success: function(count){
				// If vote successful
				if(count != "already")
				{
					heart.addClass("voted");
					heart.siblings(".count").text(count);
				}
			}
		});
		
		return false;
	})
})
</script>

<style type="text/css">
article footer  .post-like{
	margin-top:1em
}

article footer .like{
	background:url(images/icons.png) no-repeat;
	width: 15px;
	height: 16px;
	display: block;
	float:left;
	margin-right: 4px;
	-moz-transition: all 0.2s ease-out 0.1s;
	-webkit-transition: all 0.2s ease-out 0.1s;
	-o-transition: all 0.2s ease-out 0.1s
}

article footer .post-like a:hover .like{
	background-position:-16px 0;
}

article footer .voted .like, article footer .post-like.alreadyvoted{
	background-position:-32px 0;
}


</style>

<p class="post-like">
	<a data-post_id="POST_ID" href="#">
		<span class="qtip like" title="I like this article"></span>
	</a>
	<span class="count">POST_LIKES_COUNT</span>
</p>













<?


} elseif ($action=='register') { ?>
<?php require 'sub-header.php';?>
<div id="content">
<div  style="padding-left:15px;">
<h2>Register for Embroidery Advertisers</h2>
<p>Please fill out the information below to register for our website.</p>
<form name="registerform" id="registerform" action="http://embroideryadvertisers.com/wp-login.php?action=register" method="post" _lpchecked="1">
	<p>
		<label for="user_login">*Username<br>
		<input type="text" name="user_login" id="user_login" class="input" value="" size="20"></label>
	</p>
	<p>
		<label for="user_email">*E-mail<br>
		<input type="text" name="user_email" id="user_email" class="input" value="" size="25"></label>
	</p>

<p id="user_email2-p"><label id="user_email2-label" for="user_email2">*Confirm E-mail<br><input type="text" autocomplete="off" name="user_email2" id="user_email2" class="input" value=""></label></p>
<p id="first_name-p"><label id="first_name-label" for="first_name">*First Name<br><input type="text" name="first_name" id="first_name" class="input" value=""></label></p>
<p id="last_name-p"><label id="last_name-label" for="last_name">*Last Name<br><input type="text" name="last_name" id="last_name" class="input" value=""></label></p>
<p id="user_url-p"><label id="user_url-label" for="user_url">Website<br><input type="text" name="user_url" id="user_url" class="input" value=""></label></p>
<p id="description-p"><label id="description-label" for="description">About Yourself<br>
<span id="description_msg">Share a little biographical information to fill out your profile. This may be shown publicly.</span>
<textarea name="description" id="description"></textarea></label></p>
<p id="pass1-p"><label id="pass1-label" for="pass1">*Password<br><input type="password" autocomplete="off" name="pass1" id="pass1"></label></p>
<p id="pass2-p"><label id="pass2-label" for="pass2">*Confirm Password<br><input type="password" autocomplete="off" name="pass2" id="pass2"></label></p>
<div id="pass-strength-result">Strength Indicator</div>
<p id="pass_strength_msg">Your password must be at least 6 characters long. To make your password stronger, use upper and lower case letters, numbers, and the following symbols !@#$%^&amp;*()</p>
<p id="disclaimer-p">
<label id="disclaimer_title">Disclaimer</label><br>
</p><div id="disclaimer" "="">Testing</div>
<label id="accept_disclaimer-label" class="accept_check" for="accept_disclaimer"><input type="checkbox" name="accept_disclaimer" id="accept_disclaimer" value="1">&nbsp;Accept the Disclaimer</label>
<p></p>	<p id="reg_passmail">A password will be e-mailed to you.</p>
	<br class="clear">
	<input type="hidden" name="redirect_to" value="http://embroideryadvertisers.com/?message=Thank+You+for+registering%2C+Please+check+your+email+for+your+verification+email">
	<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Register"></p>
</form>
</div>
</div><!--content-->
<?php get_sidebar();?>
<?php get_footer();?>
<?php } elseif ($action=='passreset') { ?>
<?php require 'sub-header.php';?>
<div id="content">
<div  style="padding-left:15px;">
<h2>Password Reset</h2>
<p>To reset your password please enter either your username or email address that you signed up with.</p>
	<form method="post\" action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post') ?>" id="lostpasswordform" name="lostpasswordform"><p><label>Username or E-mail:<br /><input type="text" tabindex="10" size="20" value="" class="input" id="user_login" name="user_login" /></label></p><p class="submit"><?php do_action('login_form', 'resetpass'); ?><input type="submit" tabindex="100" value="Get New Password" class="button-primary" id="wp-submit" name="wp-submit" /><input type="hidden" name="redirect_to" value="" /><input type="hidden" name="cookie" value="1" /></p></form>
    <p>A temporary password will be emailed to you.</p>
</div>
</div><!--content-->
<?php get_sidebar();?>
<?php get_footer();?>
<?php 
} elseif ($action=='manage') {?>
<?php require 'sub-header.php';?>
<div id="content" style="padding:15px 0 35px 0;">
<h2>Manage Your Account</h2>
<p>Welcome to the manage your account screen, here you can:<br>
<ul>
<li><a href="<?php bloginfo('url'); ?>/actions/?action=delete">Delete Your Account</a></li>
<li><a href="<?php bloginfo('url'); ?>/?action=lostpassword">Reset your password</a></li>
</ul>
</div><!--content-->
<?php get_sidebar();?>
<?php get_footer();?>
<? } elseif ($action=='daccount') {
	$id = $current_user->ID;
	mysql_query("DELETE FROM wp_users WHERE id='".$id."'");
	mysql_query("DELETE FROM wp_mailpress_users WHERE id='".$id."'");
	mysql_query("DELETE FROM wp_posts WHERE post_author='".$id."'"); 
	mysql_error();
	$user_info = get_userdata( $id );
	$to = $user_info->user_email;
	$subject = "Your account on ".get_bloginfo('name')." has been deleted";
	$message = "Dear " .$current_user->user_login .",<br>Your account has been deleted. You are no longer a member of Embroidery Advertisers and will not recieve any more email from us. Also you will not be able to login to play games or recieve freebies on our website with out a login.<br>If you have any questions please feel free to contact us (contact@embroideryadvertisers.com) or refer to our <a href=\"http://embroideryadvertisers.com/subscriber-tos/\">Terms of Service (TOS)</a>.'";
	wp_mail($to, $subject, $message);
	//wp_mail(get_settings('admin_email'), 'User on '.get_bloginfo('name').' has removed their account' , ''.$current_user->user_login.' has removed their account at '.date('g:i A') .' on '.date('F j, Y').'' );
header("Location: http://embroideryadvertisers.com/?message=".urlencode('Your account has been deleted')."");
    ?>
<?php } elseif ($action=='delete') { ?>
<?php require 'sub-header.php';?>
<div id="content">

<p>Would you like to delete your account?</p>
<a href="<?php bloginfo('url');?>/actions/?action=daccount">Yes</a> or <a href="<?php bloginfo('siteurl');?>">No</a>

</div><!--content-->
<?php get_sidebar();?>
<?php get_footer();?>
<? } elseif ($action=='mailunsubscribe') {
//echo 'Stop clicking on the unsubscribe link, it does absolutely nothing right now! Have a problem? Email tyson@embroideryadvertisers.com';

	$unsubemail = $_GET['email'];
	$newsemail=$unsubemail;
	$findme   = 'yahoogroups';
	$pos = strpos($newsemail, $findme);
	
	
	echo 'Unsub: '.$unsubemail.'<br>';
	echo 'News Email: '.$newsemail.'<br>';
	echo 'Find Me: '.$findme.'<br>';

	if ($pos !== false) {
		// matches email
	header("Location: http://embroideryadvertisers.com/?message=".urlencode('Sorry, you do not have the permission to do this. You are receiving email because the Yahoo Group owner has contracted with Embroidery Advertisers to receive our newsletter on their group.')."");	
	} else {
		// Does not match email
	mysql_query("DELETE FROM wp_users WHERE user_email='".$unsubemail."'");
    mysql_query("DELETE FROM wp_mailpress_users WHERE email='".$unsubemail."'");
	wp_mail($unsubemail, 'Unsubscription Confirmation - Embroidery Advertisers', 'Your account on Embroidery Advertisers has been removed, please remember that all access to member only content such as: games, promotions and all discounts will no longer be able to be accessed. Your always welcome to re-join, all you need to do is simply sign up again. Have a great day!');
	header("Location: http://embroideryadvertisers.com/?message=".urlencode('Your account has been deleted, you will no longer be able to obtain freebies, participate in any games hosted here on Embroidery Advertisers,<br>You will also stop receiving all emails as well.')."");
	}
?>


<?php } elseif ($action=='membersonly') { ?>
<?php require 'sub-header.php';?>
<div id="content" style="padding:15px 0 35px 0;">
<h3>Your trying to access a members only page. Visit the <a href="<?php bloginfo('url');?>">front page</a> of the website to <a href="<?php bloginfo('url');?>/actions/?action=login">login</a> or <a href="<?php bloginfo('url');?>/actions/?action=register">register</a>.</h3>
</div><!--content-->
<?php get_sidebar();?>
<?php get_footer();?>
<? } elseif ($action=='regadvert') { 
$signup=$_GET['signup'];
require 'sub-header.php';?>
<style type="text/css">
#disclaimer { font-size:16px; display: block; width: 425px; padding: 3px; margin-top:2px; margin-right:6px; margin-bottom:8px; background-color:#fff; border:solid 1px #A7A6AA; font-weight:normal; }
.accept_check { display:block; margin-bottom:8px; }
#reg_passmail { display: none; }
.login #pass-strength-result { width: 225px; margin-top: 0px; margin-right: 6px; margin-bottom: 8px; margin-left: 0px; border-width: 1px; border-style: solid; padding: 3px 0; text-align: center; font-weight: bold; display: block; }
#pass-strength-result { background-color: #eee; border-color: #ddd !important; }
#pass-strength-result.bad { background-color: #ffb78c; border-color: #ff853c !important; }
#pass-strength-result.good { background-color: #ffec8b; border-color: #fc0 !important; }
#pass-strength-result.short { background-color: #ffa0a0; border-color: #f04040 !important; }
#pass-strength-result.strong { background-color: #c3ff88; border-color: #8dff1c !important; }
#login form #pass_strength_msg { font-size: smaller; color: #777; margin-top: -8px; margin-bottom: 16px; }
#user_login, #user_email { border:solid 1px #E6DB55;background-color:#FFFFE0;} 
#user_email2 { border:solid 1px #E6DB55;background-color:#FFFFE0; }
#first_name, #last_name { border:solid 1px #E6DB55;background-color:#FFFFE0; }
#pass1, #pass2 { border:solid 1px #E6DB55;background-color:#FFFFE0; }
</style>

<script type="text/javascript">
	try{document.getElementById('user_login').focus();}catch(e){}
	if(typeof wpOnload=='function')wpOnload();
	</script>
	
						<script type="text/javascript">
					jQuery(document).ready(function() {
						jQuery("#user_login").parent().prepend("*");
						jQuery("#user_email").parent().prepend("*");
					});
					</script>
										<script type="text/javascript">
						/* <![CDATA[ */
						pwsL10n={
							empty: "Strength Indicator",
							short: "Too Short",
							bad: "Bad Password",
							good: "Good Password",
							strong: "Strong Password",
							mismatch: "Password Mismatch"
						}
						/* ]]> */
						function check_pass_strength() {
							// HACK support username_is_email in function
							var user = jQuery("#user_login").val();
							var pass1 = jQuery("#pass1").val();
							var pass2 = jQuery("#pass2").val();
							var strength;
							jQuery("#pass-strength-result").removeClass("short bad good strong mismatch");
							if (!pass1) {
								jQuery("#pass-strength-result").html( pwsL10n.empty );
								return;
							}
							strength = passwordStrength(pass1, user, pass2);
							switch (strength) {
								case 2:
									jQuery("#pass-strength-result").addClass("bad").html( pwsL10n['bad'] );
									break;
								case 3:
									jQuery("#pass-strength-result").addClass("good").html( pwsL10n['good'] );
									break;
								case 4:
									jQuery("#pass-strength-result").addClass("strong").html( pwsL10n['strong'] );
									break;
								case 5:
									jQuery("#pass-strength-result").addClass("mismatch").html( pwsL10n['mismatch'] );
									break;
								default:
									jQuery("#pass-strength-result").addClass("short").html( pwsL10n['short'] );
							}
						}
						function passwordStrength(password1, username, password2) {
							// HACK support disable_password_confirmation in function
							password2 = typeof password2 !== 'undefined' ? password2 : '';
							var shortPass = 1, badPass = 2, goodPass = 3, strongPass = 4, mismatch = 5, symbolSize = 0, natLog, score;
							// password 1 !== password 2
							if (password1 !== password2 && password2.length > 0)
								return mismatch
							// password < 6 
							if (password1.length < 6)
								return shortPass
							// password1 === username
							if (password1.toLowerCase() === username.toLowerCase())
								return badPass;
							if (password1.match(/[0-9]/))
								symbolSize +=10;
							if (password1.match(/[a-z]/))
								symbolSize +=26;
							if (password1.match(/[A-Z]/))
								symbolSize +=26;
							if (password1.match(/[^a-zA-Z0-9]/))
								symbolSize +=31;
							natLog = Math.log(Math.pow(symbolSize, password1.length));
								score = natLog / Math.LN2;
							if (score < 40)
								return badPass
							if (score < 56)
								return goodPass
							return strongPass;
						}
						jQuery(document).ready( function() {
							jQuery("#pass1").val("").keyup( check_pass_strength );
							jQuery("#pass2").val("").keyup( check_pass_strength );
						});
					</script>

<div id="content" class="login login-action-register wp-core-ui">
<div  style="padding-left:15px;">

<h2>Register for Embroidery Advertisers</h2>

<p>Please fill out the information below to register for our website.</p>

<form name="registerform" id="registerform" action="http://embroideryadvertisers.com/wp-login.php?action=register" method="post" _lpchecked="1">
	<p>
		<label for="user_login">*Username<br>
		<input type="text" name="user_login" id="user_login" class="input" value="<? echo $current_user->user_login;?>" size="20"></label>
	</p>
	<p>
		<label for="user_email">*E-mail<br>
		<input type="text" name="user_email" id="user_email" class="input" value="<? echo $current_user->user_email;?>" size="25"></label>
	</p>

<p id="user_email2-p"><label id="user_email2-label" for="user_email2">*Confirm E-mail<br><input type="text" autocomplete="off" name="user_email2" id="user_email2" class="input" value=""></label></p>
<p id="first_name-p"><label id="first_name-label" for="first_name">*First Name<br><input type="text" name="first_name" id="first_name" class="input" value="<? echo $current_user->user_firstname;?>"></label></p>
<p id="last_name-p"><label id="last_name-label" for="last_name">*Last Name<br><input type="text" name="last_name" id="last_name" class="input" value="<? echo $current_user->user_lastname;?>"></label></p>
<p id="user_url-p"><label id="user_url-label" for="user_url">Website (This is optional)<br><input type="text" name="user_url" id="user_url" class="input" value=""></label></p>
<p id="description-p"><label id="description-label" for="description">About Yourself<br>
<span id="description_msg">Share a little biographical information to fill out your profile. This may be shown publicly.</span>
<textarea name="description" id="description" cols="62" rows="12"></textarea></label></p>
<p id="pass1-p"><label id="pass1-label" for="pass1">*Password<br><input type="password" autocomplete="off" name="pass1" id="pass1"></label></p>
<p id="pass2-p"><label id="pass2-label" for="pass2">*Confirm Password<br><input type="password" autocomplete="off" name="pass2" id="pass2"></label></p>
<div id="pass-strength-result">Strength Indicator</div>
<p id="pass_strength_msg">Your password must be at least 6 characters long. To make your password stronger, use upper and lower case letters, numbers, and the following symbols !@#$%^&amp;*()</p>
<p id="disclaimer-p">
<label id="disclaimer_title">Terms of Service</label><br>
</p><div id="disclaimer" "="">First and foremost we are an advertising website, and not a freebies website. The freebies are a way to say thank you for subscribing to our website.
<br>It is your responsibility to stay up to date on the terms and conditions of this website.<br>To continue please select the acceptance of the Disclaimer.</div>
<label id="accept_disclaimer-label" class="accept_check" for="accept_disclaimer"><input type="checkbox" name="accept_disclaimer" id="accept_disclaimer" value="1">&nbsp;Accept the Disclaimer</label>
<p></p>	<p id="reg_passmail">A password will be e-mailed to you.</p>
	<br class="clear">
	<input type="hidden" name="redirect_to" value="http://embroideryadvertisers.com/?message=Thank+You+for+registering%2C+Please+check+your+email+for+your+verification+email">
	<?php
          require_once('recaptchalib.php');
          $publickey = "6Levwt4SAAAAAOmI_IH1sFSt6rgdmObU5VodNK5X"; // you got this from the signup page
          echo recaptcha_get_html($publickey);
        ?>
	<p class="submit">
	You will recieve an email verification in your email inbox, please use it to confirm your account.<br>
	<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Register"></p>
</form>
</div>
</div><!--content-->			
<?php get_sidebar();?>
<?php get_footer();?>


<? } elseif ($action=='createuser') { 
$username=$_POST['user_login']; $useremail=$_POST['user_email']; $userpw=$_POST['user_pw1']; $first=$_POST['first_name']; $last=$_POST['last_name']; $nick=$_POST['nickname']; $web=$_POST['user_url']; $package=$_POST['package'];
$users = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users"); $iu = $users++;

if ($package=='one_ad') {
$role='a:1:{s:6:"one_ad";s:1:"1";}';
$role='a:1:{s:6:"one_ad";s:1:"1";}';



$level='1';
} elseif ($package=='two_ads') {
$role='a:1:{s:7:"two_ads";b:1;}';
$level='1';
} elseif ($package=='three_ads') {
$role='a:1:{s:6:"three_ads";s:1:"1";}';
$level='1';
} 
$regtime = date('Y-m-d G:i:s');



$full=$first.'_'.$last;
$real=$first.' '.$last;


//mail($useremail,'Your Embroidery Advertisers Info','Your username is: '.$username.' and your password is '.$userpw, 'You can sign in on the front page of the website. http://embroideryadvertisers.com/');



$count = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users WHERE user_email='".$useremail."'");


if ($count>0) {
$guserid = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_email='".$useremail."'") or die(mysql_error());

mysql_query("UPDATE wp_usermeta SET meta_value='".$role."' WHERE user_id='".$guserid."' AND meta_key='wp_capabilities'") or die(mysql_error());

} else {

mysql_query("INSERT INTO `wp_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) VALUES ('".$iu."', '".$username."', MD5('".$userpw."'), '".$full."', '".$useremail."', '".$web."', '".$regtime."', '', '0', '".$full."')");

mysql_query("INSERT INTO `wp_usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`) VALUES (NULL, '".$iu."', 'wp_capabilities', '".$role."')");
mysql_query("INSERT INTO `wp_usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`) VALUES (NULL, '".$iu."', 'wp_user_level', '".$level."')");



}



echo 'Username: '.$username.'<br> User Email: '.$useremail.'<br>Userpw: '.$userpw.'<br>First: '.$first.'<br> Last: '.$last.'<br> nick: '.$nick.'<br> Web: '.$web.'<br> Package: '.$package.'<br> Role: '.$role.'<br> Level: '.$level;
echo '<br><br>Full: '.$full.'<br>Real: '.$real.'<br>Count: '.$count.'<br>UserID: '.$guserid;








header("Location: http://embroideryadvertisers.com/");



 } elseif ($action=='test') {
	$new_cap = 'a:1:{s:6:"one_ad";b:1;}';
	//$new_cap = 'a:1:{s:7:"two_ads";b:1;}';
	$userid2='10449';
	
	$old_cap = $wpdb->get_var("SELECT meta_value FROM wp_usermeta WHERE user_id='10449' AND meta_key='wp_capabilities'");
	//mysql_query("UPDATE wp_usermeta SET meta_value='".$role2."' WHERE user_id='".$userid2."' AND meta_value='".$old_cap."'");
	//mysql_query("UPDATE wp_usermeta SET meta_value='".$new_cap."' WHERE user_id='10449' AND meta_value='".$old_cap."'");
	
	mysql_query("UPDATE wp_usermeta SET meta_value='".$new_cap."' WHERE user_id='10449' AND meta_key='wp_capabilities'") or die(mysql_error());
	

		echo 'Old Cap: '.$old_cap.'<br>';
		echo 'New Cap: '.$new_cap;
	
	
	//$role2 = 'a:1:{s:7:"two_ads";b:1;}';
	//mysql_query("UPDATE `wp_usermeta` SET  `meta_value` =  '".$role2."' WHERE `userid` =10449;");
	//UPDATE  `tysonbro_wrd5`.`wp_usermeta` SET  `meta_value` =  '".$role2."' WHERE  `wp_usermeta`.`umeta_id` =5838;
	
 } elseif ($action=='loginb') {
get_header(); get_footer();
 } elseif ($action=='sitelogin') {
require 'sub-header.php'; ?>
<div id="content">
<div  style="padding-left:15px;">

Site Login

</div>
</div><!--content-->
<?php get_sidebar();?>
<?php get_footer();?>
<?
 } elseif ($action=='login') { 
	if( isset( $_POST['log'] ) ) { /*$incorrect_login = TRUE;*/ $log = trim( $_POST['log'] );
		$pwd = trim( $_POST['pwd'] );
		if ( username_exists( $log ) ) {
			$user_data = get_userdatabylogin( $log );
			require_once( ABSPATH.'/wp-includes/class-phpass.php');
			$wp_hasher = new PasswordHash( 8, TRUE );
			$check_pwd = $wp_hasher->CheckPassword($pwd, $user_data->user_pass);
		if( $check_pwd ) {
			$credentials = array();
			$credentials['user_login'] = $log;
			$credentials['user_password'] = $pwd;
			$credentials['remember'] = isset($_POST['rememberme']) ? TRUE : FALSE;
			$user_data = wp_signon( $credentials, false );
			header('Location: '.site_url(''));
				} else {
			header('Location: '.site_url('/?action=loginfailed'));
					} } }
 } ?>
