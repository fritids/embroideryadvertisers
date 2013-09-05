<?php require TEMPLATEPATH . '/switch.php'; $action = $_GET['action']; $redirect_to=$_GET['redirect_to'];
$last_url=$_SERVER['HTTP_REFERER'];

wp_get_current_user(); global $current_user; $username = $current_user->user_login; $useremail = $current_user->user_email; $firstname = $current_user->user_firstname; $lastname = $current_user->user_lastname; $displayname = $current_user->display_name; $userid = $current_user->ID;     $role=get_user_role($userid);

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

if ($role=='deactivated') {
wp_redirect( bloginfo('url').'/account-deactivation/', 301 ); exit;
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
<div id="top-nav">
<div id="access">
<ul>
<li><a href="<?php bloginfo('url');?>/" class="home">Home</a></li>
</ul>
<?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary', 'exclude' => 'ea-magazine' ) ); ?>
</div>
</div>
</div>
<?php if (is_front_page() && $action!='register' && $action!='lostpassword') { ?>
<?php global $current_user; $current_user = wp_get_current_user();$firstname = $current_user->user_firstname;$lastname = $current_user->user_lastname;?>
<div id="featured-slider"><?php if( function_exists('FA_display_slider') ){FA_display_slider(629);} else { echo "<h2>Please Wait...</h2>";}?></div>


<div id="membersignin">
<?php
if ( is_user_logged_in() && ($action == NULL) ) { ?>
<p>Welcome <?php echo $firstname.' '.$lastname;?>! <span id="logout">(<a href="<?php echo wp_logout_url( home_url() ); ?>">Logout</a>)</span><br />
<center><a href="http://embroideryadvertisers.com/?action=manage">Manage My Account</a></center></p>
<div id="adspace1">
<?php 
$sitead=rand(1,2);
if ($sitead=='1') {
echo adrotate_group(1);
} else {
echo adrotate_group(4);
 } ?>
</div>

<? } elseif ($action=='login') { ?>
<div id="signinform" style="margin-left:5px;"><h3>Please sign in below.</h3>
<form name="loginform" id="loginform" action="http://embroideryadvertisers.com/wp-login.php" method="post">
	<p>
		<label for="user_login">Username<br>
		<input type="text" name="log" id="user_login" class="input" value="" size="20"></label>
	</p>
	<p>
		<label for="user_pass">Password<br>
		<input type="password" name="pwd" id="user_pass" class="input" value="" size="20"></label>
	</p>
	<p class="forgetmenot"><label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever"> Remember Me</label></p>
	<p class="submit">
		<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Log In">
		<input type="hidden" name="redirect_to" value="<? echo $refering_url;?>">
		<input type="hidden" name="testcookie" value="1">
	</p>
	<p>Don't have an account? <a href="http://embroideryadvertisers.com/?action=register">Register now</a>!
</form>
    </div>

<?php } elseif ($action=='loginfailed') { ?>
<div id="signinform" style="margin-left:5px;"><h3>Login Failed</h3>
<div class="incorrect_login" style="color:#F00; padding:5px 5px 5px 5px;"><strong>The username or password that you entered is incorrect.</strong> <a href="<?php bloginfo('url'); ?>/?action=login" style="color:#FFF;">Please try again</a>.<br />
Forgot your password? <a href="<?php bloginfo('url'); ?>/?action=lostpassword">Reset It!</a>
</div></div>



<? } elseif (!(current_user_can('level_0'))) { ?>
<p>Would you like to <a href="<?php bloginfo('url'); ?>/?action=login">log yourself in</a>?<br />
<? /*<p>Would you like to <a href="<?php bloginfo('url'); ?>/wp-login.php">log yourself in</a>?<br /> */?>
Dont have an account? <a href="<?php bloginfo('url'); ?>/?action=register">Register Now</a><br />
<? /* Dont have an account? <a href="<?php bloginfo('url'); ?>/wp-login.php?action=register">Register Now</a><br /> */?>
Forgot your password? <a href="<?php bloginfo('url'); ?>/?action=lostpassword">Reset It!</a>
<? /* Forgot your password? <a href="<?php bloginfo('url'); ?>/wp-login.php?action=lostpassword">Reset It!</a> */?>
</p>
<div id="adspace1">
<?php 
$sitead=rand(1,2);
if ($sitead=='1') {
echo adrotate_group(1);
} else {
echo adrotate_group(4);
} ?>
</div><?php } else { ?>
<p>Welcome <?php echo $firstname.' '.$lastname;?>! <span id="logout">(<a href="<?php echo wp_logout_url( home_url() ); ?>">Logout</a>)</span><br />
<center><a href="<?php bloginfo('url'); ?>/?action=manage">Manage My Account</a></center></p>
<div id="adspace1"><?php echo adrotate_group(1); ?></div>

<? }  ?>
</div>
<?php } ?>
