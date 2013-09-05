<?php
/*
Template Name: Gift Design
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
						<div class="post">
					<?php require 'gift-designs/index.php';?>
					<?php the_content('<span></span>'); ?>
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
