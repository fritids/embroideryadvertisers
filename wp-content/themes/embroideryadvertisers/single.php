<?php get_header();
$cat = get_the_category();
$category = $cat[0]->cat_name;
?>
<div id="content">
<?php if (have_posts()) : while (have_posts()) : the_post();?>
 <div class="post">
  <h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
	<div class="entry-meta">
	<p>Posted: <?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?> by <?php the_author_posts_link(); ?><br/>
	<? if ($category == 'Newsletter') {?>
	<a href="<? bloginfo('site_url');?>/contact-us/?to=<?php the_author_email(); ?>&subject=<? the_title();?>"><img src="<?php bloginfo('template_url'); ?>/images/email-author.png" style="vertical-align:middle;"> Email this advertiser</a>
	<? } ?>
	</p>
	</div><!-- .entry-meta -->
  <div class="entrytext">
   <?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
   <?php
if ('craft_project'==get_post_type()) {
$args = array(
    'post_type' => 'attachment',
    'numberposts' => null,
    'post_status' => null,
    'post_parent' => $post->ID
);
$attachments = get_posts($args);
if ($attachments) {
    foreach ($attachments as $attachment) {
        echo '<a href="'.wp_get_attachment_url($attachment->ID).'" rel="lightbox"><img  src="'.wp_get_attachment_url($attachment->ID).'" width="225px"/></a>';
    }
}
}
?>
     </div>
	 	 
 </div>
 <div style="clear:both;"></div>

 	<? if ($category == 'Newsletter') {?>
<div id="aboutadvertiser">
<h3><u>About this Advertiser</u></h3>
	<div id="advertiserimg" class="alignleft">
<?php echo get_avatar( get_the_author_meta( 'ID' ), 215 ); ?>
	</div>
	<div id="advertiserdesc">
	<? the_author_description(); ?>
	</div>
	<div class="clear:both;"></div>
</div>
 <? } //comments_template(); 
 endwhile; endif;

$prev_post = get_adjacent_post(true, '', true);
$next_post = get_adjacent_post(true, '', false); ?>

<?php if ($prev_post) : $prev_post_url = get_permalink($prev_post->ID); $prev_post_title = get_the_title($prev_post->ID);?>
<div style="float:left; padding-left:5px;"><a href='<?php echo $prev_post_url; ?>' rel='prev'><? echo '&laquo; '.$prev_post_title;?></a></div>
<?php endif; ?>

<?php if ($next_post) : $next_post_url = get_permalink($next_post->ID); $next_post_title = get_the_title($next_post->ID);?>
<div class="alignright" style="padding-right:5px;"><a href='<?php echo $next_post_url; ?>' rel='next'><? echo $next_post_title.' &raquo;';?></a></div>
<?php endif; ?>
</div><!--content-->
<?php get_sidebar();?>
<?php get_footer();?>
