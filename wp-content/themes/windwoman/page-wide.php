<?php
/*
Template Name: Page-Wide
*/
?>
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
			
<!-- Start one column -->
<table width="940" border="0" cellspacing="0" cellpadding="0" class="columns">
	<tr>
		<td id="contentwide">
			<div id="contentwide2">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					<div id="postwrap-<?php the_ID(); ?>" class="post">
						<h1 ><a href="<?php the_permalink() ?>" class="contentpagetitle" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
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
		</td>
	</tr>
</table>
<!-- end column -->	
</div>
<!-- End innerwrap -->
</div>

<?php get_footer(); ?>