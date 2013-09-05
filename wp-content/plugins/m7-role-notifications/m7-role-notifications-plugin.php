<?
/*
Plugin Name: M7 Role Notification
Plugin URI: http://mach7enterprises.com
Description: This plugin was written to provide a QUICK way of getting different priority messages out to different roles.
Version: 0.1
Author: Tyson Brooks
Author URI: http://mach7enterprises.com
*/
//$current_user = wp_get_current_user(); $username = $current_user->user_login; $useremail = $current_user->user_email; $firstname = $current_user->user_firstname; $lastname = $current_user->user_lastname; $displayname = $current_user->display_name; $userid = $current_user->ID; $role=get_user_role($userid);

date_default_timezone_set('America/Denver');


if (isset($_GET['message'])) {
	if ($_GET['message']==1 && $_GET['page']=='m7-role-notify') {
		echo '<div class="updated" style="padding:5px;">Plugin Rest</div>';
	}
	if ($_GET['message']==2) {
		echo '<div class="updated" style="padding:5px;">Settings Saved</div>';
	}
}


if (isset($_POST['m7_notify_submit'])) {
	if (get_option('m7notifydate')==NULL) {
		add_option('m7notifydate', date('m/d/y g:i a'), '', 'yes');
	} else {
		update_option('m7notifydate', date('m/d/y g:i a'), '', 'yes');
	}
	if (get_option('m7_notify_prio')==NULL) {
		add_option('m7_notify_prio', $_POST['m7prio'], '', 'yes');
	} else {
		update_option('m7_notify_prio', $_POST['m7prio'], '', 'yes');
	}
	if (get_option('m7notifymessage')==NULL) {
		add_option('m7notifymessage', $_POST['m7notifymessage'], '', 'yes');
	} else {
		update_option('m7notifymessage', $_POST['m7notifymessage'], '', 'yes');
	}
	echo '<div class="updated" style="padding:5px;">Settings Saved</div>';
}
if (isset($_GET['resetm7notify'])) {
delete_option('m7notifydate');delete_option('m7_notify_prio');delete_option('m7notifymessage');
}

/** Step 1. */
function m7_role_notify() {
	add_menu_page( 'M7 Role Notifications', 'M7 Notifications', 'manage_options', 'm7-role-notify', 'm7_role_notify_options' );
}
/** Step 2 (from text above). */
add_action( 'admin_menu', 'm7_role_notify' );

/** Step 3. */
function m7_role_notify_options() {
$option_url=get_settings('siteurl').'/wp-admin/admin.php?page=m7-role-notify';
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	$prio = get_option('m7_notify_prio');
	
	?>

	<div class="wrap">
	<h2>M7 Role Notification</h2>
	
	<h3>Priority Level</h3>
	<form action="" method="POST">
	<select name="m7prio">
		<option value="0" <? if ($prio==0) { ?>selected<? }?>>None</option>
		<option value="1" <? if ($prio==1) { ?>selected<? }?>>Low</option>
		<option value="2" <? if ($prio==2) { ?>selected<? }?>>Medium</option>
		<option value="3" <? if ($prio==3) { ?>selected<? }?>>High</option>
	</select>
	<br/>
	<h3>Message:</h3>
	<textarea cols="125" rows="15" name="m7notifymessage"><? echo htmlspecialchars(get_option('m7notifymessage'));?></textarea>
	<br/>
	<input type="submit" value="Save Options" class="button button-primary button-large" name="m7_notify_submit">
	</form><br/>
	<a href="<? echo $option_url;?>&resetm7notify=1&message=1">Reset the form</a>?
	</div>
<?}
add_action('wp_after_admin_bar_render', 'm7usernotify');

function m7usernotify() {
if (is_admin()) {
	$prio = get_option('m7_notify_prio');
	if ($prio!='0') { ?>
		<style type="text/css">
		.messagenotify {
		padding:25px;
		margin:5px;
		width:95%;
		font-size:14px;
	<?	if ($prio==1) { ?>
		background:#b2ffb3;
		border:1px #0baa00 solid;
	<?	} elseif ($prio==2) { ?>
		background:#ffe0e0;
		border:1px #ff9191 solid;		
	<?	} elseif ($prio==3) { ?>
		background:#ff9696;
		border:1px #ed0000 solid;
		color:#000;
	<?	} ?>
	}
	p.pmessage {
	background:#f1f1f1;
	padding:5px;
	border:1px #000 solid;
	}
		</style>
	<script type="text/javascript">
	window.onload=toRemove;

// click on the div
function toggle( e, id ) {
  var el = document.getElementById(id);
  el.style.display = ( el.style.display == 'none' ) ? 'block' : 'none';

  // save it for hiding
  toggle.el = el;

  // stop the event right here
  if ( e.stopPropagation )
    e.stopPropagation();
  e.cancelBubble = true;
  return false;
}

// click outside the div
document.onclick = function() {
  if ( toggle.el ) {
    toggle.el.style.display = 'none';
  }
}

function toRemove() {
document.getElementById('notifymess').style.display = 'none';
document.getElementById('tgbtn').innerHTML = '<a href="#" onclick="toShow();" style="background:#f1f1f1; border:1px #000 solid; padding:2px; font-size:12px;">show/hide</a>';
}
function toShow() {
document.getElementById('notifymess').style.display = 'block';
document.getElementById('tgbtn').innerHTML = '<a href="#" onclick="toRemove();" style="background:#f1f1f1; border:1px #000 solid; padding:2px; font-size:12px;">show/hide</a>';
}
</script>
	<div class="messagenotify" style="padding:5px;">
	<h3><? if ($prio==1) { echo 'Low';} elseif ($prio==2) { echo 'Medium'; } elseif ($prio==3) { echo 'High';}?> Priority Message: <span id="tgbtn"></span></h3>
	<p>Posted: <? echo get_option('m7notifydate');?></p>
	<p id="notifymess" class="pmessage"><? echo nl2br(get_option('m7notifymessage'),false);?></p>
	</div>
	<?	}
	}
}

/*
add_action('wp_after_admin_bar_render', 'm7_default_mess');
function m7_default_mess() {
$current_user = wp_get_current_user(); if (empty($_GET['user_id'])) {$userid = $current_user->ID; } else { $userid=$_GET['user_id'];} $role=get_user_role($userid);

$allmess = '';
$advertsmess = '
<span style="font-size:12px">
Most of you may have noticed by now, but the scheduling feature on ads has been removed for the time being. This is due to a conflict in the current newsletter system.<br>Once the conflict has been resolved we will reinstate that feature.<br><br>Thank you for your understanding. <br><br>Tyson Brooks
<h3>MUST READ</h3>
<b>UPDATE: (Aug 16, 2013) After seeing two newsletters go out with out me doing anything, I am going to release the scheduling of ads temporary  Meaning.... You will be able to schedule ads for a short period of time. When I see that a few ads have been scheduled, I will then disable it again to continue testing the newsletter system. I am hoping that my suspicions are incorrect that the scheduling system is not interfering with the ability to send a newsletter on a timely manner. What I think I am seeing is this, When some one publishes an ad to go live NOW, and others schedule an ad. I think the newsletter system is only counting those that get published live. Then once 4 ads have been published live, with no scheduling then the newsletter gets sent, in turn skipping those that were scheduled. That is what we will be testing now over the next few days.<br><br>

I have also sent out a memo with this same information to all advertisers via our newsletter system. If you got the memo please, reply to my memo via email.
</b>
</span>
';
$oneadmess = '';
$twoadmess = '';
$threeadmess = '';
$oneadwkmess = '';
$chatmodmess = '';

if (is_admin()) {
	if (!empty($allmess)) {
	echo '<div class="error" style="padding:5px;"><b>ATTN All Users:</b><br>'.$allmess.'</div>';
	}
	if (!empty($advertsmess)) {
		if ($role=='one_ad' || $role=='two_ads' || $role=='three_ads' || $role=='one_ad_wk' || $role=='administrator') {
			echo '<div class="error" style="padding:5px; font-size:16px;"><b>ATTN All Advertisers:</b><br>'.$advertsmess.'</div>';

		}
	}
	if (!empty($oneadmess)) {
		if ($role == 'one_ad' || $role=='administrator') {
			echo '<div class="error" style="padding:5px; font-size:16px;"><b>ATTN Advertiser:</b><br>'.$oneadmess.'</div>';
		}
	}
	if (!empty($twoadmess)) {
		if ($role == 'two_ads' || $role=='administrator') {
			echo '<div class="error" style="padding:5px; font-size:16px;"><b>ATTN Advertiser:</b><br>'.$twoadmess.'</div>';
		}
	}
	if (!empty($threeadmess)) {
		if ($role == 'three_ads' || $role=='administrator') {
			echo '<div class="error" style="padding:5px; font-size:16px;"><b>ATTN Advertiser:</b><br>'.$threeadmess.'</div>';
		}
	}
}
}*/