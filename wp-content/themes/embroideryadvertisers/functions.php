<?php


//session_start();
if (isset($_POST['file'])) {

	$_SESSION['loggedin']=1;
echo '<meta http-equiv="REFRESH" content="0;url='.get_bloginfo('siteurl').'/files/?file='.$_POST['file'].'&type='.$_POST['type'].'&session='.$_SESSION['loggedin'].'">';

/*
$header  = 'MIME-Version: 1.0' . "\r\n";
$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$header .= 'Reply-To: '.$name.' <'.$from.'>';

$message = '
Thank you for downloading '.$_POST['file'].' today from Embroidery Advertisers!<br/>
We\'d like to know what your thoughts are, please take our short survey, Thanks!
Survey: http://embroideryadvertisers.com/survey/

';

mail('tyson@tysonbrooks.net','Thank you for downloading the freebie!',$message,$header);
*/
}
require 'theme-options.php';
require 'post.limit.php';
//require 'login.out.redirect.php';

if (isset($_POST['emailsend'])) {
	$debug=0;
if ($_POST['to']==NULL) { $to='tyson@embroideryadvertisers.com';} else { $to=$_GET['to']; }

if (isset($_POST['sender_name'])) {$name=$_POST['sender_name'];}
if (isset($_POST['sender_email'])) {$from=$_POST['sender_email'];}
if (isset($_POST['subject'])) {$subject=$_POST['subject'];} else { $subject=$_GET['subject']; }
if (isset($_POST['phone'])) {$phone=$_POST['phone'];}
if (isset($_POST['message'])) {$message=$_POST['message'];}

if ($debug==1) {
	echo 'To: '.$to.'<br>';
	echo 'Sender Name: '.$name.'<br>';
	echo 'Sender Email: '.$from.'<br>';
	echo 'Subject: '.$subject.'<br>';
	echo 'Phone: '.$phone.'<br>';
	echo 'Message: '.$message.'<br>';
}


$header  = 'MIME-Version: 1.0' . "\r\n";
$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$header .= 'Reply-To: '.$name.' <'.$from.'>';

mail($to,'Embroidery Advertisers - '.$subject, 'A customer has tried to contact you via Embroidery Advertisers.<br>Here is their message:<br><br>'.$message.'<br><br>Here is the customers information:<br>Customer Name: '.$name.'<br>Customer Email: '.$from.'<br>Customer Phone Number: '.$phone.'<br><br>You may also reply to this email to reach them.', $header);



//$refering_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';


//if ($_POST['to']==NULL) { header("Location:".$refering_url."?message=Your message was received successfully."); } else { header("Location:".$refering_url."&message=Your message was received successfully."); }
}


if (function_exists('register_sidebar')) {
register_sidebar(array('name'=>'sidebar1'));
}

function custom_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 9999 );



function role_update( $user_id, $new_role ) {
$header  = 'MIME-Version: 1.0' . "\r\n";
$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$header .= 'Reply-To: Tyson Brooks <tyson@embroideryadvertisers.com>';
if ($new_role == 'administrator') {		
        $site_url = get_bloginfo('wpurl');
        $user_info = get_userdata( $user_id );
        $role = "Administrator";
		$to = $user_info->user_email;
        $subject = "Embroidery Advertisers Account Updated";
        $message = "Hello " .$user_info->display_name . " your account has been changed. You are now a ". $new_role.' member. You now have total access to the website, with full rights.';
        wp_mail($to, $subject, $message,$header);
} elseif ($new_role == 'manager') {		
        $site_url = get_bloginfo('wpurl');
        $user_info = get_userdata( $user_id );
		$role = "Manager";
        $to = $user_info->user_email;
        $subject = "Embroidery Advertisers Account Updated";
        $message = "Hello " .$user_info->display_name . " your account has been changed. You are now a ". $new_role.' member. You are now a manager for Embroidery Advetisers. This will give you added abilitys on the site, allow you to manage users, reset passwords or emails, amoung other things.';
        wp_mail($to, $subject, $message,$header);
} elseif ($new_role == 'three_ads') {		
        $site_url = get_bloginfo('wpurl');
        $user_info = get_userdata( $user_id );
		$role = "Three Ads a Day";
        $to = $user_info->user_email;
        $subject = "Embroidery Advertisers Account Updated";
        $message = "Hello " .$user_info->display_name . " your account has been changed. You are now a ". $new_role.' member. You will be limited to three ads within 24 hours.';
        wp_mail($to, $subject, $message,$header);
} elseif ($new_role == 'two_ads') {		
        $site_url = get_bloginfo('wpurl');
        $user_info = get_userdata( $user_id );
		$role = "Two Ads a Day";
        $to = $user_info->user_email;
        $subject = "Embroidery Advertisers Account Updated";
        $message = "Hello " .$user_info->display_name . " your account has been changed. You are now a ". $new_role.' member. You will be limited to two ads within 24 hours.';
        wp_mail($to, $subject, $message,$header);
} elseif ($new_role == 'one_ad') {		
        $site_url = get_bloginfo('wpurl');
        $user_info = get_userdata( $user_id );
		$role = "One Ad a Day";
        $to = $user_info->user_email;
        $subject = "Embroidery Advertisers Account Updated";
        $message = "Hello " .$user_info->display_name . " your account has been changed. You are now a ". $new_role.' member. You will be limited to one ad within 24 hours';
        wp_mail($to, $subject, $message,$header);
} elseif ($new_role == 'one_ad_wk') {		
        $site_url = get_bloginfo('wpurl');
        $user_info = get_userdata( $user_id );
		$role = "One Ad a Week";
        $to = $user_info->user_email;
        $subject = "Embroidery Advertisers Account Updated";
        $message = "Hello " .$user_info->display_name . " your account has been changed. You are now a ". $new_role.' member. You will be limited to posting one ad in 7 days.';
        wp_mail($to, $subject, $message,$header);
} elseif ($new_role == 'chatroom_moderator') {		
        $site_url = get_bloginfo('wpurl');
        $user_info = get_userdata( $user_id );
        $to = $user_info->user_email;
        $subject = "Embroidery Advertisers Account Updated";
        $message = "Hello " .$user_info->display_name . " your account has been changed. You are now a ". $new_role.' member, new features will be added to your account on the website.';
        wp_mail($to, $subject, $message,$header);
} elseif ($new_role == 'deactivated') {		
        $site_url = get_bloginfo('wpurl');
        $user_info = get_userdata( $user_id );
        $to = $user_info->user_email;
        $subject = "Embroidery Advertisers Account Updated";
        $message = "Hello " .$user_info->display_name . " your account has been updated.<br/>
		Your account has been deactivated due to a terms of service violation.<br/>
		You may read the terms of service here: http://embroideryadvertisers.com/account-deactivation/?action=tos<br/>
		If you feel that this message was in error please feel free to reply to the email to appeal this decision.<br/>";
        wp_mail($to, $subject, $message,$header);
}}


add_action( 'set_user_role', 'role_update', 10, 2);

function new_excerpt_more( $more ) {
	return '... <a class="read-more" href="'. get_permalink( get_the_ID() ) . '">[Read More...]</a>';
}
add_filter( 'excerpt_more', 'new_excerpt_more' );


function ea_single_nav_links() {
    echo '<div id="postnav">';
    if ( is_single() ) {
        if ( get_adjacent_post( true, '', true ) ) {
            echo '<div style="float:left; padding-left:5px;">';
            previous_post_link( '&laquo; %link' );
            echo '</div>';
        }
        if ( get_adjacent_post( true, '', false ) ) {
            echo '<div class="alignright" style="padding-right:5px;">';
            next_post_link( '%link &raquo;' );
            echo '</div>';
        }
    }
    echo '</div>';
}
?>
