<?php require TEMPLATEPATH . '/switch.php'; $action = $_GET['action']; ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>    
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />	
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats please -->
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'template_url' ); ?>/css/<?php echo $css;?>.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'template_url' ); ?>/style.css" />
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_get_archives('type=monthly&format=link'); ?>
<?php //comments_popup_script(); // off by default ?>
<?php wp_head();?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-26376637-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</head>
<body>
<div id="wrapper">
<?php settings_fields( 'sample_options' ); ?>
<?php $options = get_option( 'sample_theme_options' );
$message = $options['sometextarea'];
if ($message==NULL) {} else { echo "<div id='bmessage'><center>$message</center></div>"; } ?>

<div id="header">
            <img id="headerimg" src="<?php bloginfo( 'template_url' ); ?>/images/<?php echo $img;?>.png">
<div id="top-nav">
<div id="access">
<ul>
<li><a href="<?php bloginfo('url');?>/" class="home">Home</a></li>
</ul>
<style type="text/css">.page-item-1541 { display:none; }.page-item-1308 { display:none; }</style>
<?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary', 'exclude' => 'ea-magazine' ) ); ?>
</div>
</div>
</div>
<?php if (is_front_page()) { ?>
<?php global $current_user; $current_user = wp_get_current_user();$firstname = $current_user->user_firstname;$lastname = $current_user->user_lastname;?>
<?php } ?>
