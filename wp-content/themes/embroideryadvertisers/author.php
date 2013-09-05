<?php get_header();?>

<div id="content">
<?php if (have_posts()) : while (have_posts()) : the_post();?>
 <div class="post">
  <h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
	<div class="entry-meta">
	<p>Posted: <?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?> by <?php the_author_posts_link(); ?><br/><a href="<? bloginfo('url');?>/contact-us/?to=<?php the_author_email(); ?>&subject=<? the_title();?>"><img src="<?php bloginfo('template_url'); ?>/images/email-author.png" style="vertical-align:middle;"> Email this advertiser</a></p>
	</div><!-- .entry-meta -->
  <div class="entrytext">
   <?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
     </div>
 </div>
 <?php endwhile; endif; ?>
<div id="postnav">
<div class="clear"></div>
<div style="float:left; padding-left:5px;"><?php previous_posts_link('&laquo; Newer Posts') ?></div>
<div class="alignright" style="padding-right:5px;"><?php next_posts_link('Older Posts &raquo;') ?></div>
</div>
 </div><!--content-->

<?php get_sidebar();?>
<?php get_footer();?>
