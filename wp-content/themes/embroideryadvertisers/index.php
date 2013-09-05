<?php  get_header(); $action=$_GET['action']; 
$refering_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''; ?>
<style type="text/css">
#disclaimer { font-size:16px; display: block; width: 525px; padding: 3px; margin-top:2px; margin-right:6px; margin-bottom:8px; background-color:#fff; border:solid 1px #A7A6AA; font-weight:normal; }
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
<? if ($action=='register') { ?>

<div id="content" class="login login-action-register wp-core-ui">
<div  style="padding-left:15px;">

<h2>Register for Embroidery Advertisers</h2>

<p>Please fill out the information below to register for our website.</p>

<form name="registerform" id="registerform" action="http://embroideryadvertisers.com/wp-login.php?action=register" method="post" _lpchecked="1">
	<p>
		<label for="user_login">*Username (Please do not use your email as a username.)<br>
		<input type="text" name="user_login" id="user_login" class="input" value="" size="20"></label>
	</p>
	<p>
		<label for="user_email">*E-mail<br>
		<input type="text" name="user_email" id="user_email" class="input" value="" size="25"></label>
	</p>

<p id="user_email2-p"><label id="user_email2-label" for="user_email2">*Confirm E-mail<br><input type="text" autocomplete="off" name="user_email2" id="user_email2" class="input" value=""></label></p>
<p id="first_name-p"><label id="first_name-label" for="first_name">*First Name<br><input type="text" name="first_name" id="first_name" class="input" value=""></label></p>
<p id="last_name-p"><label id="last_name-label" for="last_name">*Last Name<br><input type="text" name="last_name" id="last_name" class="input" value=""></label></p>
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
</p><div id="disclaimer"><iframe src="http://embroideryadvertisers.com/tos/" width="100%" height="250px" scrolling="yes" frameborder="0"></iframe></div>
<label id="accept_disclaimer-label" class="accept_check" for="accept_disclaimer"><input type="checkbox" name="accept_disclaimer" id="accept_disclaimer" value="1">&nbsp;Accept the Terms of Service</label>
<p></p>	<p id="reg_passmail">A password will be e-mailed to you.</p>
	<br class="clear">
	<input type="hidden" name="redirect_to" value="http://embroideryadvertisers.com/?message=Thank+you+for+registering%2E+Please+check+your+email+for+your+verification%2E">
	<?php
          require_once('recaptchalib.php');
          $publickey = "6Levwt4SAAAAAOmI_IH1sFSt6rgdmObU5VodNK5X"; // you got this from the signup page
          echo recaptcha_get_html($publickey);
        ?>
	<p class="submit">
	You will recei	ve an email verification in your email inbox, please use it to confirm your account.<br>
	<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Register"></p>
</form>
</div>
</div><!--content-->
<?php get_sidebar();
 } elseif ($action=='lostpassword') { ?>

<div id="content">
<div  style="padding-left:15px;">

<h2>Recover password for Embroidery Advertisers</h2>

<p>Please fill out the information below to recover your password.</p>

<form name="lostpasswordform" id="lostpasswordform" action="http://embroideryadvertisers.com/wp-login.php?action=lostpassword" method="post">
	<p>
		<label for="user_login">Username or E-mail:<br>
		<input type="text" name="user_login" id="user_login" class="input" value="" size="20"></label>
	</p>
	<input type="hidden" name="redirect_to" value="">
	<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Get New Password"></p>
</form>


</div>
</div><!--content-->

<?php get_sidebar();
 } elseif ($action==NULL) { ?>
<div id="content">
<?php  
   $cat_name = 'newsletter';
   wp_reset_query(); $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;   query_posts("category_name=$cat_name&paged=$paged");?>
<?php if (have_posts()) : while (have_posts()) : the_post();?>
 <div class="post">
  <h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
	<div class="entry-meta">
	<p>Posted: <?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?> by <?php the_author_posts_link(); edit_post_link('(edit)', '<span style="margin-left:4px;">', '</span>'); ?><br/><a href="<? bloginfo('url');?>/contact-us/?to=<?php the_author_email(); ?>&subject=<? the_title();?>"><img src="<?php bloginfo('template_url'); ?>/images/email-author.png" style="vertical-align:middle;"> Email this advertiser</a></p>
	</div><!-- .entry-meta -->
  <div class="entrytext">
   <?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
     </div>
	 <div id="sharebtns">
		<? require 'social.php';?>
	 </div>
 </div>
 <hr style="width:80%;">
 <?php endwhile; else: ?> 
 <h2 class="center">Not Found</h2>
<p class="center">Sorry, but you are looking for something that isn't here.</p>
 <? endif; ?> 
<div id="postnav">
<div class="clear"></div>
<div style="float:left; padding-left:5px;"><?php previous_posts_link('&laquo; Newer Posts') ?></div>
<div class="alignright" style="padding-right:5px;"><?php next_posts_link('Older Posts &raquo;') ?></div>
</div>
</div><!--content-->
<?php get_sidebar();
}elseif ($action=='manage') {?>
<div id="content" style="padding:15px 0 35px 0;">
<h2>Manage Your Account</h2>
<p>Welcome to the manage your account screen, here you can:<br>
<ul>
<li><a href="<?php bloginfo('url'); ?>/?action=delete">Delete Your Account</a></li>
<li><a href="<?php bloginfo('url'); ?>/?action=lostpassword">Reset your password</a></li>
</ul>
</div><!--content-->
<?php get_sidebar();?>
<? } elseif ($action=='daccount') {
f
    ?>
<?php } elseif ($action=='delete') { ?>
<div id="content">

<p>Would you like to delete your account?</p>
<a href="<?php bloginfo('url');?>/?action=daccount">Yes</a> or <a href="<?php bloginfo('siteurl');?>">No</a>

</div><!--content-->
<?php get_sidebar();?>
<?php  } elseif ($action=='mailunsubscribe') {
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
	$username = mysql_query("SELECT * FROM wp_users WHERE user_email='".$unsubemail."'");
	mysql_query("DELETE FROM wp_users WHERE user_email='".$unsubemail."'");
    mysql_query("DELETE FROM wp_mailpress_users WHERE email='".$unsubemail."'");
	wp_mail($unsubemail, 'Unsubscription Confirmation - Embroidery Advertisers', 'Your account on Embroidery Advertisers has been removed, please remember that all access to member only content such as: games, promotions and all discounts will no longer be able to be accessed. Your always welcome to re-join, all you need to do is simply sign up again. Have a great day!');
	wp_mail('tyson@embroideryadvertisers.com','User has unsubscribed from EA',$username. 'has unsubscribed, and deleted their account from EA.');
	header("Location: http://embroideryadvertisers.com/?message=".urlencode('Your account has been deleted, you will no longer be able to obtain freebies, participate in any games hosted here on Embroidery Advertisers,<br>You will also stop receiving all emails as well.')."");
	}
?>


<?php }

 get_footer(); ?>
