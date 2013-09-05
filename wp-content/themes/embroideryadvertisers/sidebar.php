<?php wp_get_current_user(); global $current_user; $username = $current_user->user_login; $useremail = $current_user->user_email; $firstname = $current_user->user_firstname; $lastname = $current_user->user_lastname; $displayname = $current_user->display_name; $userid = $current_user->ID;     $role=get_user_role($userid);



$numids = mysql_query( "SELECT referalid FROM wp_affiliates where referalid=".$userid."");
$numcount = mysql_num_rows($numids);

?>
<script type="text/javascript">
function sideclick(url) {
	newwindow=window.open(url,'Chat','height=700,width=732');
	if (window.focus) {newwindow.focus()}
	return false;
}
</script>

<div id="sidebar">		   
		   <div id="sidebar1" style="padding-top:25px;">		 
		   Join our <a href="http://embroideryadvertisers.com/members-area/chatroom/">Chat room</a>!
		   <? if (is_user_logged_in() && $role=='administrator2') { ?>
            
			<ul>
				<li id="text-5" class="widget widget_text">
				<h2 class="widgettitle">EA Contest</h2>
				<div class="textwidget">
				
				Would you like a chance to win a fantastic prize?!<br>
				Participate in our new contest, Share the following link with all of your friends.<br>
				Be one of the top 3 participants to have shared the link the most, and have had the most friends visit the website via that link and you could win a prize!<br><br>
				Here is your link!
				
				<input type="text" size="38" value="http://embroideryadvertisers.com/?ref=<?echo $userid;?>">
				<p>You have refered: <? echo $numcount;?> visitors.</p>
				</div>
				<hr>
		</li>
			</ul>
			<? } ?>
			<ul>
           <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar1') ) : ?>
           <?php endif; // end of sidebar1 ?>
		   
		   <? if (is_user_logged_in() && $role=='administrator') { ?>

		   <? } ?>
		   
           </ul>
           </div>
 
        <div id="sidebar2" style="padding-bottom:25px;">
            <ul>
           <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar2') ) : ?>
            <?php endif; // end of sidebar1 ?>
            </ul>
 
        </div>
</div>