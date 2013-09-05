<?php
/**
* Evening Shade 1.2
* Designed by Pixel Theme Studio
* http://www.pixelthemestudio.ca
* studio@pixelthemestudio.ca
* License: Copyright 2009 Pixel Theme Studio 
* Not for distribution or resale without permission
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"<?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<meta name="author" content="Pixel Theme Studio" />
<title><?php
if (is_home()) {
	bloginfo('name');
} elseif (is_404()) {
	echo '404 Not Found'; echo ' | '; bloginfo('name');
} elseif (is_category()) {
	echo 'Category:'; wp_title(''); echo ' | '; bloginfo('name');
} elseif (is_search()) {
	echo 'Search Results'; echo ' | '; bloginfo('name');
} elseif ( is_day() || is_month() || is_year() ) {
	echo 'Archives:'; wp_title(''); echo ' | '; bloginfo('name');
} else {
	echo wp_title(''); echo ' | '; bloginfo('name');
}
 ?>
</title>
<link href='http://fonts.googleapis.com/css?family=Merienda+One' rel='stylesheet' type='text/css'>
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<?php wp_head(); ?>

<!--[if IE 7]>
<link href="ie7_css.css" rel="stylesheet" type="text/css" />
<![endif]-->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-27991617-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>


<style type="text/css">
.page-item-1255 {
	display:none;
}
</style>


</head>
<body>
<div id="wrapper980" class="clearfix"><div id="header">
              <!-- The top Sub Menu Goes here -->
        <div id="title"><h1><?php bloginfo('name'); ?></h1></div>
              <div id="tagline"><h2><?php bloginfo('description'); ?></h2></div>
      </div>
<!-- Main navigation -->
      <div id="navwrapper">
	  <div id="horiz-menu" class="nav">
	  <ul>
      <li><a href="<?php echo get_option('home'); ?>/">Home</a></li>
		<?php wp_list_pages('sort_column=menu_order&title_li=&exclude='); // change the exclude= ID number to the new home page ID ?>
		</li>
    </ul>
	  </div>
		<div id="searchwrap"><div class="rounded">
		<form method="get" action="<?php bloginfo('siteurl');?>/" id="search-form">
<div class="search">
	<input type="text" name="s" id="mod_search_searchword" maxlength="20" size="20" class="inputbox" value="Search Here..." onfocus="this.value=''" />
</div>
	<input type="hidden" value="" />
</form>
		</div></div> 
	  </div>
	  <div id="leftglow">
  <div id="rightglow">
    <div id="innerwrapper">      
      <div id="innerwrap" class="clearfix">
<!-- End header.php -->
