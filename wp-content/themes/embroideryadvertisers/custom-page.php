<?php
/*
Template Name: Custom Page
*/
get_header();
?>
<div id="content">
<?php  
$cat_name = get_post_meta($post->ID, 'cat_name', true);
$page_name = get_post_meta($post->ID, 'page_name', true);

   wp_reset_query(); $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;   query_posts("category_name=$cat_name&paged=$paged");?>
<?php if (have_posts()) : while (have_posts()) : the_post();?>
 <div class="post">
  <h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
  <div class="entry-meta">
			<p>Posted: <?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?> by <?php the_author_posts_link(); ?></p>		</div><!-- .entry-meta -->
  <div class="entrytext">
   <?php the_excerpt('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
     </div>
</div>
 <? if ($cat_name != 'founders-blog') { ?>
 <div id="contactad"><a href="http://embroideryadvertisers.com/contact-us/?to=<? the_author_email();?>&subject=<?the_title(); ?>">Contact this Advertiser</a></div>
<? } endwhile; else: ?> 
 <h2 class="center">Not Found</h2>
	<p class="center">Sorry, but you are looking for something that isn't here.</p>
	<div id="postnav">
<div class="clear"></div>
<div style="float:left; padding-left:5px;"><?php previous_posts_link('&laquo; Newer Posts') ?></div>
<div class="alignright" style="padding-right:5px;"><?php next_posts_link('Older Posts &raquo;') ?></div>
</div>
 <? endif; ?></div><!--content-->
<?php get_sidebar();?>
<?php get_footer();?>
