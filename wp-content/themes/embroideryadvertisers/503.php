<?php require 'switch.php'; date_default_timezone_set('America/Denver');
 ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="<?php bloginfo('description'); ?>" />
	<title><?php bloginfo('name'); ?> &raquo; <?php echo $this->g_opt['mamo_pagetitle']; ?></title>
<style type="text/css">
  @import url("<?php bloginfo( 'template_url' ); ?>/css/<?php echo $css;?>.css");
#maint{
background:#fff url(<?php bloginfo( 'template_url' ); ?>/images/main-bg.gif);
color:#000;
font-size:150%;
width:80%;
margin:35px auto;
padding:5% 0;
text-align:center;
border-style:solid;
border-width:3px;
border-color:#000;
}
#maint h1{
	  font:Arial, Helvetica, sans-serif;
	  color:#000;
}
a:link {
	color: #000;
}
a:visited {
	color: #000;
}
a:hover {
	color: #000;
}
a:active {
	color: #000;
}
</style>
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
<div id="maint">
<?php echo $this->mamo_template_tag_message(); ?>
</div>

<?php settings_fields( 'sample_options' ); ?>
<?php $options = get_option( 'sample_theme_options' );

if ($options['sometextarea']==NULL) { 
	if ($_GET['message']==NULL) {
		// Do Nothing
	} else {
		$message=$_GET['message'];
	}
} else {
	$message = stripslashes ($options['sometextarea']); 
}
if ($message==NULL) {} else { echo "<div id='bmessage'><center>$message</center></div>"; } ?>
</body>
</html>