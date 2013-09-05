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
				<h1 ><a href="<?php the_permalink() ?>" class="contentpagetitle" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
				<div class="postmetadata">				
				<?php _e('Posted by:'); ?> <?php the_author_posts_link('namefl'); ?> <?php _e('on:'); ?> <?php the_time('F j Y'); ?> &bull; <?php _e('Categorized in:'); ?> <?php the_category(',') ?>
				</div>
			      <div class="post"><?php the_content('<span></span>'); ?>
				  <?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				  </div>
				  
				  <?php the_tags('<div class="tags"><strong>'.__('Tag Search:').'</strong> ', ', ', '</div>'); ?>	
		<div class="comments"><strong>Comments: </strong><?php comments_popup_link('Leave a Comment', '1 Comment', '% Comments'); ?></div>
			    </div>
				<!-- Pagination -->
			<?php endwhile; ?>
			<?php comments_template(); ?>
			<div id="post-nav" class="clearfix">
				<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
				<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
			</div><!-- /post-nav -->

			<?php else: ?>
			<h2 class="center">Not Found</h2>
			<p class="center">Sorry, but you are looking for something that isn't here.</p>
			<?php endif ?>
			
			
			</div>
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
<!-- end column -->	
</div>
<!-- End innerwrap -->
</div>

<?php get_footer(); ?>