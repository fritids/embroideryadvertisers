<?php
$action=$_GET['action'];
/**
* Evening Shade 1.2
* Designed by Pixel Theme Studio
* http://www.pixelthemestudio.ca
* studio@pixelthemestudio.ca
* License: Copyright 2009 Pixel Theme Studio 
* Not for distribution or resale without permission
*/

if ( is_user_logged_in() ) {
	$user = new WP_User( $user_ID );
	if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
		foreach ( $user->roles as $role )
			$role;
	}
}


?>

		<?php global $current_user; $current_user = wp_get_current_user();$firstname = $current_user->user_firstname;$lastname = $current_user->user_lastname; $username = $current_user->user_nicename;

if ( is_user_logged_in() && $action==NULL) { ?>
<div class="moduletable">
<h3>Members Box <span id="logout">(<a href="<? bloginfo('url');?>/wp-login.php?action=logout">Logout</a>)</span></h3>
<div class="modcontent">
<p>Welcome <?php echo $username;?>!<br />
Would you like to change your email address? <a href="<? bloginfo('url');?>/wp-admin/profile.php">Click Here</a></p>
</div></div>
<? } elseif ($action=='login') { ?>
<div class="moduletable">
<h3>Members Box</h3>
<div class="modcontent">
<form name="loginform" id="loginform" action="http://windwoman.com/wp-login.php" method="post">
<p><label for="user_login">Username<br><input type="text" name="log" id="user_login" class="input" value="" size="20"></label></p>
<p><label for="user_pass">Password<br><input type="password" name="pwd" id="user_pass" class="input" value="" size="20"></label></p>
<p class="forgetmenot"><label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever"> Remember Me</label></p>
<p class="submit">
<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Log In">
<input type="hidden" name="redirect_to" value="<? echo bloginfo('url');?>">
<input type="hidden" name="testcookie" value="1">
</p>
</form>
</div></div>
<? } else { ?>
<div class="moduletable">
<h3>Members Box</h3>
<div class="modcontent">
<p>Welcome Guest!<br>Would you like to <a href="<? echo bloginfo('url');?>/?action=login" style="color:#fff;">Log yourself in</a>?</p>
<p>Don't have an account? <a href="<? echo bloginfo('url');?>/wp-login.php?action=register" style="color:#fff;">Register Now</a>!</p>
<p>Forget your password? <a href="<? echo bloginfo('url');?>/wp-login.php?action=lostpassword" style="color:#fff;">Recover It Here</a>!</p>
</div>
</div>
<? } ?>
		
			<?php 	/* Widgetized sidebar, if you have the plugin installed. */
					if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
			<!-- Author information is disabled per default. Uncomment and fill in your details if you want to use it.
			<li><h2>Author</h2>
			<p>A little something about you, the author. Nothing lengthy, just an overview.</p>
			</li>
			-->
			<?php if ( is_404() || is_category() || is_day() || is_month() ||
						is_year() || is_search() || is_paged() ) {
			?>

			<?php /* If this is a 404 page */ if (is_404()) { ?>
			<?php /* If this is a category archive */ } elseif (is_category()) { ?>
			<p>You are currently browsing the archives for the <?php single_cat_title(''); ?> category.</p>

			<?php /* If this is a yearly archive */ } elseif (is_day()) { ?>
			<p>You are currently browsing the <a href="<?php bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
			for the day <?php the_time('l, F jS, Y'); ?>.</p>

			<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
			<p>You are currently browsing the <a href="<?php bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
			for <?php the_time('F, Y'); ?>.</p>

			<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
			<p>You are currently browsing the <a href="<?php bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
			for the year <?php the_time('Y'); ?>.</p>

			<?php /* If this is a monthly archive */ } elseif (is_search()) { ?>
			<p>You have searched the <a href="<?php echo bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
			for <strong>'<?php the_search_query(); ?>'</strong>. If you are unable to find anything in these search results, you can try one of these links.</p>

			<?php /* If this is a monthly archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
			<p>You are currently browsing the <a href="<?php echo bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives.</p>

			<?php } ?>

			 <?php }?>

			<?php wp_list_pages('title_li=<h3>Pages</h3>' ); ?>
<div class="moduletable">
			<h3>Archives</h3>
<div class="modcontent">
				<ul>
				<?php wp_get_archives('type=monthly'); ?>
				</ul>
</div>	
</div>		
<div class="moduletable">
			<?php wp_list_categories('show_count=1&title_li=<h3>Categories</h3>'); ?>

			<?php /* If this is the frontpage */ if ( is_home() || is_page() ) { ?>
				
</div>
<div class="moduletable">
				<h3>Meta</h3>
<div class="modcontent">
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
					<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
					<li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>
					<?php wp_meta(); ?>
				</ul>
</div>
</div>				
			<?php } ?>

			<?php endif; ?>
		
	
