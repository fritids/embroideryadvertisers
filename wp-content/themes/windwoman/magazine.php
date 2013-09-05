<?php
/*
Template Name: WW Magazine
*/
get_header(); ?>
<style type="text/css">
#content2 {
	font-size:18px;
}
</style>
<?php
//get_currentuserinfo();
/*$cat_name = 'my-embroidery-club';
$page_name = $post->post_name;
wp_reset_query(); 
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
query_posts("category_name=$cat_name&paged=$paged");
*/

get_currentuserinfo();
	$cat_name = get_post_meta($post->ID, 'cat_name', true);
	//$page_name = $post->post_name;
	$page_name= get_post_meta($post->ID, 'page_name', true);
	wp_reset_query(); 
if (is_page($page_name)) {
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	query_posts("category_name=$cat_name&paged=$paged");
}

if ( is_user_logged_in() ) {
	$user = new WP_User( $user_ID );
	if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
		foreach ( $user->roles as $role )
			echo $role;
	}
}

?>
<!-- Start page templates -->
	<!-- Top Promo area for full width banners -->
	<?php include (TEMPLATEPATH . '/advertise.php'); ?>
		<!-- Breadcrumbs -->	 
<!-- Start two columns -->
<table width="940" border="0" cellspacing="0" cellpadding="0" class="columns">
	<tr><br /><br />
		<td width="650" id="content">
                    <div id="content2">
			<? if ($role=='silver_member' || $role=='silver_club' || $role='administrator' ) { ?>

				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					<div id="postwrap-<?php the_ID(); ?>" class="post">
						<h1 ><a href="<?php the_permalink() ?>" class="contentpagetitle" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
                        <div class="postmetadata">				
				<?php _e('Posted by:'); ?> <?php the_author_posts_link('namefl'); ?> <?php _e('on:'); ?> <?php the_time('F j Y'); ?> &bull; <?php _e('Categorized in:'); ?> <?php the_category(',') ?>
				</div>
						<div class="post"><?php the_content('<span></span>'); ?>
						<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
						</div>
						
					</div>
				<!-- Pagination -->
				<?php endwhile; ?>
				<?php else: ?>
					<h2 class="center">Not Found</h2>
						<p class="center">Sorry, but you are looking for something that isn't here.</p>
				<?php endif ?>
                
               </div>
                <span class="alignleft" style="padding-left:5px;"> <?php previous_posts_link('&laquo; Newer Posts') ?></span>
<span class="alignright" style="padding-right:5px;"><?php next_posts_link('Older Posts &raquo;') ?></span>
			<? } /*elseif ($user_level<1) {*/ else {?>
            <div id="content2">
	<h2>Subscribe Today!</h2>
	<div class="entrytext">
    <center>
    <img src="<?php bloginfo('template_url');?>/images/mag/wwmag.png">
    <p>Subscribe to our Newsletter Today!</p>
    <p><table width="200" border="0" cellpadding="4">
  <tr>
    <td>
    <br /><center>1 Month Subscription</center><br />
     <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="M4ZY467HGV7AY">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form><br />
    </td>
    <td>
    <br /><center>3 Month Subscription</center><br />
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="6UQ9NKBA8P67U">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form><br />
    </td>
    <td>
    <br /><center>6 Month Subscription</center><br />
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="7RK4Z9LDQ85QU">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form><br />
    </td>
    <td>
    <br /><center>12 Month Subscription</center><br />
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="9DGMMR7ZJK5GG">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form><br />
    </td>
  </tr>
</table></p>
</center>
<p>Get your subscription to Windwoman Magazine today and get access to Embroidery Tips, Tricks, Lessons, Exclusive Freebies and more!<br><br>
Must be a registered user of the website to access the Magazine. Register <a href="<?php bloginfo('url');?>/wp-login.php?action=register&redirect_to=/magazine/">Here</a>
<br><br>
<strong>Please allow 24 to 36 hours for your subscription to become active.</strong>
</p>
	</div>
				</div>
			<? } ?>

		</td>
		<td width="10">&nbsp;</td>
		<td width="280" id="right">
			<div id="right2">
				<?php get_sidebar(); ?>
			</div>
		</td>
	</tr>
</table>
<!-- end columns -->


</div>
<!-- End innerwrap -->
</div>
<!-- End Page Templates -->
<?php get_footer(); ?>
