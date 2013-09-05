<?php
/*
Template Name: Club
*/
get_header() ?>
<style type="text/css">
#content2 {
	font-size:18px;
}
</style>
<?php
get_currentuserinfo();
$cat_name = 'my-embroidery-club';
$page_name = $post->post_name;
wp_reset_query(); 
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
query_posts("category_name=$cat_name&paged=$paged");

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
			<? if ($role=='club' || $role=='silver_club' || $role='administrator' ) { ?>
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
	<h2>Welcome to the Club!</h2>
	<div class="entrytext">
	<center>
	<p>We can see your not a member, <a href="http://www.myembroideryclub.com/amember/signup.php" target="_new">Click here</a> to subscribe today!!!</p>
	</center>
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