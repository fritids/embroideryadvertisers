<h2 <?php $this->classes('ch2'); ?>>Whats in this issue of Embroidery Advertisers Daily News?</h2>
<?php query_posts('showposts=2'); ?>
				<ul <?php $this->classes('sideul'); ?>>
					<?php while (have_posts()) : the_post(); ?>
						<li style="list-style-type:disc;"><a <?php $this->classes('sidelink'); ?> href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ul>

<table <?php $this->classes('nopmb ctable'); ?>>
<?php while (have_posts()) : the_post(); ?>
	<tr>
		<td <?php $this->classes('nopmb ctd'); ?>>
			<div <?php $this->classes('cdiv'); ?>>
				<h2 <?php $this->classes('ch2'); ?>>
					<a <?php $this->classes('clink'); ?> href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
<?php the_title(); ?>
					</a>
				</h2>
				<small <?php $this->classes('nopmb cdate'); ?>>
				Posted by <?php the_author_posts_link(); ?>
				</small>
				<div <?php $this->classes('nopmb'); ?>>
<div <? $this->classes('cimg');?>>
	<?php $this->the_image(array('unit' => 'px', 'max_width' => 125, 'force_max_width' => 125)); ?>
</div>
<div <? $this->classes('ccontent');?>>
<?php the_excerpt(); ?>
</div>
				</div>
			</div>
		</td>
	</tr>
<?php endwhile; ?>
</table>
<div style="clear:both;"></div>