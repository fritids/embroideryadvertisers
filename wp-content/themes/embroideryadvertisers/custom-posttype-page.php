<?php
/*
Template Name: Post Type
*/
 get_header();?>
<div id="content">
<?php  
$cat_name = get_post_meta($post->ID, 'cat_name', true);
$post_type = get_post_meta($post->ID, 'post_type', true);
wp_reset_query(); $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;   query_posts("post_type=$post_type&paged=$paged");?>

<div class="post">
<h2 id="post-<?php the_ID(); ?>"><?php the_title();?></h2>
<hr width="125px" class="alignleft" style="margin:-15px 0 0 10px;"><br/>
<? the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
<hr width="700px" style="border:1px solid #000;">
<?php if (have_posts()) : while (have_posts()) : the_post();?>
<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
<div class="entry-meta">
<p>Posted: <?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?> by <?php the_author_posts_link(); ?></p>		
</div><!-- .entry-meta -->


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
        echo '<a href="'.wp_get_attachment_url($attachment->ID).'" rel="lightbox"><img  src="'.wp_get_attachment_url($attachment->ID).'" width="125px" class="alignleft"/></a>';
    }
}
}
?>

<div class="entrytext">
<?php the_excerpt('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
<div style="clear:both; margin-top:15px;"></div>
</div>
<hr width="425px">
<?php endwhile; endif; ?>
</div>
<div id="postnav">
<div class="clear"></div>
<div style="float:left; padding-left:5px;"><?php previous_posts_link('&laquo; Newer Posts') ?></div>
<div class="alignright" style="padding-right:5px;"><?php next_posts_link('Older Posts &raquo;') ?></div>
</div>
</div><!--content-->
<?php get_sidebar();?>
<?php get_footer();?>