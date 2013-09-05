<?php
if ( is_user_logged_in() && ($action == NULL) ) { ?>
<p>Welcome <?php echo $firstname.' '.$lastname;?>! <span id="logout">(<a href="<? bloginfo('url');?>/wp-login.php?action=logout">Logout</a>)</span><br />
<center><a href="<?php bloginfo('url'); ?>/actions/?action=manage">Manage My Account</a></center></p>
<div id="adspace1" align="center" style="padding:0 0 0 5px"><?php echo adrotate_block(1); ?></div>
<? } elseif ($action=='login') { ?>
<div id="signinform" style="margin-left:5px;"><h3>Please sign in below.</h3><form action="<? bloginfo('url');?>/actions/?action=login" method="post" id="login-form">
<label for="log">Username</label><input type="text" name="log" id="log" class="text" value="<?php echo wp_specialchars(stripslashes($user_login), 1) ?>" size="15" /><br />
<label for="pwd">Password</label><input type="password" name="pwd" id="pwd" class="text" size="15" /><br />
<label for="rememberme"><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> Remember me</label>
<input type="hidden" name="redirect_to" value="<?php echo get_option('siteurl'); ?>" /><br />
<input type="submit" name="submit" value="Log In" class="button" />
    </form>
    </div>
<?php } elseif ($action=='loginfailed') { ?>
<div id="signinform" style="margin-left:5px;"><h3>Login Failed</h3>
<div class="incorrect_login" style="color:#F00; padding:5px 5px 5px 5px;"><strong>The username or password that you entered is incorrect.</strong> <a href="<?php bloginfo('url'); ?>/?action=login" style="color:#FFF;">Please try again</a>.<br />
Forgot your password? <a href="<?php bloginfo('url'); ?>/actions/?action=passreset">Reset It!</a>
</div></div>
<? } elseif ($action=='test') { 
if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Login Box') ) : ?>
           <?php endif; // end of Login Box ?>
?>


<? } elseif (!(current_user_can('level_0'))) { ?>
<? /*<p>Would you like to <a href="<?php bloginfo('url'); ?>/?action=login">log yourself in</a>?<br /> */?>
<p>Would you like to <a href="<?php bloginfo('url'); ?>/wp-login.php">log yourself in</a>?<br />
<? /*Dont have an account? <a href="<?php bloginfo('url'); ?>/actions/?action=register">Register Now</a><br /> */ ?>
Dont have an account? <a href="<?php bloginfo('url'); ?>/wp-login.php?action=register">Register Now</a><br />
<? /* Forgot your password? <a href="<?php bloginfo('url'); ?>/actions/?action=passreset">Reset It!</a> */ ?>
Forgot your password? <a href="<?php bloginfo('url'); ?>/wp-login.php?action=lostpassword">Reset It!</a>
</p>
<div id="adspace1" align="center" style="margin:-5px 0 0 0;"><?php echo adrotate_block(1); ?></div>
<?php } else { ?>
<p>Welcome <?php echo $firstname.' '.$lastname;?>! <span id="logout">(<a href="<? 
bloginfo('url');?>/wp-login.php?action=logout">Logout</a>)</span><br />
<center><a href="<?php bloginfo('url'); ?>/actions/?action=manage">Manage My Account</a></center></p>
<div id="adspace1" align="center" style="padding:0 0 0 5px"><?php echo adrotate_block(1); ?></div>


<? }  ?>
