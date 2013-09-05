<?php
/*
Template Name: Magazine
*/
?>
<?php get_header();?>
<div id="content">
<div id="adspace1" style="padding-left:5px;"><?php echo adrotate_block(3); ?></div>
<?php
 get_currentuserinfo();
$cat_name = 'magazine';
   wp_reset_query(); $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;   query_posts("category_name=$cat_name&paged=$paged&posts_per_page=3");?>
<? if ($user_level>=1) { ?>
<h2>Welcome to Embroidery Advertisers Magazine</h2>
<?php if (have_posts()) : while (have_posts()) : the_post();?>
 <div class="post">
  <h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
  <div class="entrytext">
   <?php /*the_content('<p class="serif">Read the rest of this page &raquo;</p>');*/ the_excerpt(); ?>
     </div>
 </div>
<?php endwhile; else: ?> 
<h2 class="center">Not Found</h2>
<p class="center">Sorry, but you are looking for something that isn't here.</p>
<? endif; ?>
<span class="alignleft" style="padding-left:5px;"> <?php previous_posts_link('&laquo; Newer Posts') ?></span>
<span class="alignright" style="padding-right:5px;"><?php next_posts_link('Older Posts &raquo;') ?></span>
 <? } elseif ($user_level<1) {
 ?>
<div class="post">
        <p class="alignright">Already a Subscriber? Sign in <a href="<? 
bloginfo('url');?>/?action=login">here</a></p>
	<h2>Subscribe Today!</h2>
	<div class="entrytext">
    <center>
    <img src="<?php bloginfo('template_url');?>/images/mag/eamag.png">
    <p>Subscribe to our magazine today!</p>
    <p><table width="200" border="0" cellpadding="4">
  <tr>
    <td>
    <br /><center>1 Month Subscription</center><br />
     <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="Q9PNQDKREKHTQ">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></form><br />
    </td>
    <td>
    <br /><center>3 Month Subscription</center><br />
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="HUSUJHRYSSKUC">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></form><br />
    </td>
    <td>
    <br /><center>6 Month Subscription</center><br />
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="PMX84AKU59S8A">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></form><br />
    </td>
    <td>
    <br /><center>12 Month Subscription</center><br />
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="PJHFFEA5LN5CG">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></form><br />
    </td>
  </tr>
</table></p>
</center>
</center>
<p>Get your subscription to Embroidery Advertisers Magazine today and get access to Embroidery Tips, lessons, Computer Tips and Tricks, Exclusive Freebies and more!<br><br>
Must be a registered user of the website to access the Magazine. Register <a href="<?php bloginfo('url');?>/wp-login.php?action=register&redirect_to=/magazine/">Here</a>
<br><br>
<strong>Please allow 24 to 36 hours for your subscription to become active.</strong></p>
	</div>
</div>
<? } ?>
</div><!--content-->
<?php get_sidebar();?>
<?php get_footer();?>
