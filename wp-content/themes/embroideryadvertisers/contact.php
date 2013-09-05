<?php
/*
Template Name: Contact Page
*/
?>
<?php get_header();

if (isset($_GET['to'])) { $to=$_GET['to'].',tyson@embroideryadvertisers.com'; }
if (isset($_GET['subject'])) { $subject=$_GET['subject']; }
?>
<div id="content">

<div id="contact">
<? if (isset($_POST['emailsend'])) { echo '<p style="color:green;">Your message was succesfully sent.</p>'; } ?>
<p>The following information will only be shared with the individual you're trying to contact.</p>
<form action="" method="POST">
<input type="hidden" name="to" value="<? echo $to; ?>">
Your Name: <input type="text" name="sender_name"><br />
Your Email: <input type="text" name="sender_email"><br />
Your Phone: <input type="text" name="phone"><br>(This is here for convenience, some advertisers may elect to contact you by phone, others by email.)<br />
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

</div><!--content-->
<?php get_sidebar();?>
<?php get_footer();?>
