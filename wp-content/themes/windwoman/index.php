<?php
get_header() ?>
<?php

$cat_name = 'whats-new';
$page_name = $post->post_name;
wp_reset_query(); 
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
query_posts("category_name=$cat_name&paged=$paged");
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