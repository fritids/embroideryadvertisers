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
							
							
							
							
							                                              
								<?php if (have_posts()) : ?>
			<h1 class="pagetitle">Search Results</h1>
			<?php while (have_posts()) : the_post(); ?>
			<div id="post-<?php the_ID(); ?>" class="post">
				<h3 class="title"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
				
					
					<div class="date"><?php the_time('F j Y') ?>&nbsp; &nbsp;<?php comments_number('No Commented','one Commented','% Commented'); ?>&nbsp; &nbsp;Categorized Under: <?php the_category(', ') ?></div>
				
				<div class="entry">
					<?php the_excerpt(); ?>
				</div>
			</div>
			<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h2 class="center">No posts found. Try a different search?</h2>
		<?php get_search_form(); ?>

	<?php endif; ?>
			
			
			
			
			
			
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