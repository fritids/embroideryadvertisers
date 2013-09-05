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
	  <!-- Header End -->
		<!-- Breadcrumbs -->	 
			

<!-- Start two columns -->
<table width="940" border="0" cellspacing="0" cellpadding="0" class="columns">
  <tr>
    <td width="650" id="content">
	<div id="content2">
                                               
								<?php if (have_posts()) : ?>
			<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
			<?php /* If this is a category archive */ if (is_category()) { ?>
			<h1 class="pagetitle">Archive for the '<?php single_cat_title(); ?>' Category</h1>
			<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
			<h1 class="pagetitle">Posts Tagged '<?php single_tag_title(); ?>'</h1>
			<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
			<h1 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h1>
			<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
			<h1 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h1>
			<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
			<h1 class="pagetitle">Archive for <?php the_time('Y'); ?></h1>
			<?php /* If this is an author archive */ } elseif (is_author()) { ?>
			<h1 class="pagetitle">Author Archive</h1>
			<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
			<h1 class="pagetitle">Blog Archives</h1>
			<?php } ?>
			<?php while (have_posts()) : the_post(); ?>
			
			
			<div id="postwrap-<?php the_ID(); ?>" class="post">
				<h2 ><a href="<?php the_permalink() ?>" class="contentpagetitle" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<div class="postmetadata">				
				<?php _e('Posted by:'); ?> <?php the_author_posts_link('namefl'); ?> <?php _e('on:'); ?> <?php the_time('F j Y'); ?> &bull; <?php _e('Categorized in:'); ?> <?php the_category(',') ?>
				</div>
			      <div class="entry">
					<div class="post"><?php the_content('<span></span>'); ?></div>
				</div>
			    </div>
			<!-- Pagination -->
			<?php endwhile; ?>
			
			<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>
	<?php else :

		if ( is_category() ) { // If this is a category archive
			printf("<h2 class='center'>Sorry, but there aren't any posts in the %s category yet.</h2>", single_cat_title('',false));
		} else if ( is_date() ) { // If this is a date archive
			echo("<h2>Sorry, but there aren't any posts with this date.</h2>");
		} else if ( is_author() ) { // If this is a category archive
			$userdata = get_userdatabylogin(get_query_var('author_name'));
			printf("<h2 class='center'>Sorry, but there aren't any posts by %s yet.</h2>", $userdata->display_name);
		} else {
			echo("<h2 class='center'>No posts found.</h2>");
		}
		get_search_form();

	endif;
?>
					
							
							
							
							
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