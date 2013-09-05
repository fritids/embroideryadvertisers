<?php
/*
Template Name: Deactivation
*/
if (!isset($_GET['action'])) { $action=NULL;} else { $action=$_GET['action']; }
global $current_user; $current_user = wp_get_current_user();$firstname = $current_user->user_firstname;$lastname = $current_user->user_lastname;
require TEMPLATEPATH . '/switch.php'; $action = $_GET['action']; $redirect_to=$_GET['redirect_to'];
$last_url=$_SERVER['HTTP_REFERER'];

if ($last_url=='http://embroideryadvertisers.com/?action=loginfailed') {
$refering_url = 'http://embroideryadvertisers.com/?action=login';
} else {
$refering_url = get_option('siteurl').$redirect_to;
}
$debug='off';
if ($debug=='on') {
echo 'Referer: '.$_SERVER['HTTP_REFERER'].'<br>';
echo 'Last URL: '.$last_url.'<br>';
echo 'Refering URL: '.$refering_url;
}
$ipaddress = $_SERVER["REMOTE_ADDR"];
if (isset($_GET['ref'])) { $ref = $_GET['ref'];}

$wpdb->get_results( "SELECT * FROM wp_affiliates WHERE ipaddress='$ipaddress'");
$count = $wpdb->num_rows;
if ($count<1) {
if ($ref!=NULL) {
 mysql_query("INSERT INTO `tysonbro_wrd5`.`wp_affiliates` (`id`, `Referalid`, `ipaddress`) VALUES (NULL, '$ref', '$ipaddress')");
}
}
 ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>    
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />	

<link rel="image_src" href="<? bloginfo('template_url');?>/images/share-icon.jpg" />
<meta property="og:image" content="<? bloginfo('template_url');?>/images/share-icon.jpg" />

<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'template_url' ); ?>/css/<?php echo $css;?>.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'template_url' ); ?>/style.css" />
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_get_archives('type=monthly&format=link'); ?>
<?php //comments_popup_script(); // off by default ?>
<?php wp_head();?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-26376637-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>

</head>
<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=383419215092953";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div id="wrapper">
<?php settings_fields( 'sample_options' ); ?>
<?php $options = get_option( 'sample_theme_options' );

if (empty($_GET['message'])) {
	if (empty($options['sometextarea'])) {
	// do nothing
	} else {
		$message = 'ATTN: '.stripslashes ($options['sometextarea']);
	}
} else {
	$message = $_GET['message'];
}

if ($message==NULL) {
} else {
	echo "<div id='bmessage'><center>$message</center></div>";
} ?>

<div id="header">
<img id="headerimg" src="<?php bloginfo( 'template_url' ); ?>/images/<?php echo $img;?>.png">
</div>

<input type="button" value="Back" onclick="window.history.back();">
<div id="content-full">
<? if ($action==NULL) { ?>
<?php if (have_posts()) : while (have_posts()) : the_post();?><div class="post"><h2 id="post-<?php the_ID(); ?>"><?php the_title();?></h2><div class="entrytext"><?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?></div></div><?php endwhile; endif; ?><div style="clear:both;"></div>

<? } elseif ($action=='manage') {?>
<div class="post">
<img class="alignleft" alt="clip-art-monkeys-343290" src="http://embroideryadvertisers.com/wp-content/uploads/2013/08/clip-art-monkeys-343290-270x300.jpg" width="270" height="300">
<h2>Manage Your Account</h2>
<p>Welcome to the manage your account screen, here you can:<br>
<a href="?action=delete">Delete Your Account</a></li>
<div style="clear:both;"></div>
</div>

<? } elseif ($action=='daccount') {
	$id = $current_user->ID;
	mysql_query("DELETE FROM wp_users WHERE id='".$id."'");
	mysql_query("DELETE FROM wp_mailpress_users WHERE id='".$id."'");
	mysql_query("DELETE FROM wp_posts WHERE post_author='".$id."'"); 
	mysql_error();
	$user_info = get_userdata( $id );
	$to = $user_info->user_email;
	$subject = "Your account on ".get_bloginfo('name')." has been deleted";
	$message = "Dear " .$current_user->user_login .",<br>Your account has been deleted. You are no longer a member of Embroidery Advertisers and will not recieve any more email from us. Also you will not be able to login to play games or recieve freebies on our website with out a login.<br>If you have any questions please feel free to contact us (contact@embroideryadvertisers.com) or refer to our <a href=\"http://embroideryadvertisers.com/tos/\">Terms of Service (TOS)</a>.'";
	wp_mail($to, $subject, $message);
	//wp_mail(get_settings('admin_email'), 'User on '.get_bloginfo('name').' has removed their account' , ''.$current_user->user_login.' has removed their account at '.date('g:i A') .' on '.date('F j, Y').'' );
header("Location: http://embroideryadvertisers.com/account-deactivation/?message=".urlencode('Your account has been deleted')."");
    ?>
<?php } elseif ($action=='delete') { ?>
<div class="post">
<img class="alignleft" alt="clip-art-monkeys-343290" src="http://embroideryadvertisers.com/wp-content/uploads/2013/08/clip-art-monkeys-343290-270x300.jpg" width="270" height="300">
<h2>Account Deletion Verification</h2>
<p>Would you like to delete your account?</p>
<a href="http://embroideryadvertisers.com/account-deactivation/?action=daccount">Yes</a> or <a href="http://embroideryadvertisers.com/account-deactivation/">No</a>
<div style="clear:both;"></div>
</div>
<?php } elseif ($action=='appealemail') {
if (isset($_GET['to'])) { $to=$_GET['to'].',tyson@embroideryadvertisers.com'; }
if (isset($_GET['subject'])) { $subject=$_GET['subject']; }
?>

<div class="post">
<h2>You may use this form to send a email to appeal your deactivation</h2>
<img class="alignleft" alt="clip-art-monkeys-343290" src="http://embroideryadvertisers.com/wp-content/uploads/2013/08/clip-art-monkeys-343290-270x300.jpg" width="270" height="300">
<div id="contact" class="alignright" style="margin-right:220px;">
<? if (isset($_POST['emailsend'])) { echo '<p style="color:green;">Your message was succesfully sent.</p>'; } ?>
<p>The following information will only be shared with the individual you're trying to contact.</p>
<form action="" method="POST">
<input type="hidden" name="to" value="<? echo $to; ?>">
Your Name: <input type="text" name="sender_name"><br />
Your Email: <input type="text" name="sender_email"><br />
Your Phone: <input type="nidden" name="phone"><br />
Subject: <input type="text" name="subject" size="58" value="<? echo $subject;?>"><br />
Message:<br>
<textarea name="message" rows="20" cols="45"></textarea><br />
<?php
          require_once('recaptchalib.php');
          $publickey = "6Levwt4SAAAAAOmI_IH1sFSt6rgdmObU5VodNK5X"; // you got this from the signup page
          echo recaptcha_get_html($publickey);
        ?>
<input type="submit" value="Send" name="emailsend" class="send"></form>
</div>
</div>
<div style="clear:both;"></div>

<? } elseif ($action=='tos') { ?>

<div class="post">

<h2>Terms of Service</h2>
<iframe src="http://embroideryadvertisers.com/tos/" width="100%" height="480px" frameborder="0" scrolling="yes"></iframe>
</div>
<? } ?>
</div>
<?php wp_footer();?>
<div id="footer" style="margin-top:25px;">
<p>
&copy; <?php echo date('Y');?> All Rights Reserved <?php bloginfo('name'); ?> | Designed and Maintained by <a href="http://mach7enterprises.com" target="new">Mach7 Enterprises</a> | <a href="http://embroideryadvertisers.com/account-deactivation/?action=tos">Terms Of Service</a>
</p>
</div>
</div><!--wrapper-->
</body>
</html>

