<?php 
/*
Template Name: Contact Page
*/
 if (have_posts()) : while (have_posts()) : the_post();?>
   <?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
 <?php endwhile; endif; ?>