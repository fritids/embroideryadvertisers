<?php
/**
* Evening Shade 1.2
* Designed by Pixel Theme Studio
* http://www.pixelthemestudio.ca
* studio@pixelthemestudio.ca
* License: Copyright 2009 Pixel Theme Studio 
* Not for distribution or resale without permission
*/

get_header() ?>

<!-- Start page templates -->
	<!-- Top Promo area for full width banners -->
	<?php include (TEMPLATEPATH . '/advertise.php'); ?>
		<!-- Breadcrumbs -->	 
			
<!-- Start two columns -->
<table width="940" border="0" cellspacing="0" cellpadding="0" class="columns">
  <tr>
    <td width="650" id="content">
	<div id="content2">                                               
								<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			  <div id="postwrap-<?php the_ID(); ?>" class="post">
				<h2 ><a href="<?php the_permalink() ?>" class="contentpagetitle" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<div class="postmetadata">				
				<?php _e('Posted by:'); ?> <?php the_author_posts_link('namefl'); ?> <?php _e('on:'); ?> <?php the_time('F j Y'); ?> &bull; <?php _e('Categorized in:'); ?> <?php the_category(',') ?>
				</div>
			      <div class="post"><?php the_content('<span></span>'); ?></div>
				  
		<?php the_tags('<div class="tags"><strong>'.__('Tag Search:').'</strong> ', ', ', '</div>'); ?>	
		<div class="comments"><strong>Comments: </strong><?php comments_popup_link('Leave a Comment', '1 Comment', '% Comments'); ?></div>
		
		
			    </div>
				<?php comments_template(); ?>
				<!-- Pagination -->
				<?php endwhile; ?>
			<div id="post-nav">
				<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?>
				
			</div><!-- /post-nav -->
				<!-- Pagination end -->
				<?php else: ?>
			<h2 class="center">Not Found</h2>
			<p class="center">Sorry, but you are looking for something that isn't here.</p>
			<?php endif ?>
							
	</div>
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