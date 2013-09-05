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
<!-- Bottom Module Widgets -->
	 <div id="bottommodules" class="clearfix">
	<div id="sidebar1">
	<?php if ( !function_exists('dynamic_sidebar')
	|| !dynamic_sidebar('Sidebar 2') ) : ?>
	<?php endif; ?>
	</div>
	<div id="sidebar2">
	<?php if ( !function_exists('dynamic_sidebar')
	|| !dynamic_sidebar('Sidebar 3') ) : ?>
	<?php endif; ?>
	</div>
	<div id="sidebar3">
	<?php if ( !function_exists('dynamic_sidebar')
	|| !dynamic_sidebar('Sidebar 4') ) : ?>
	<?php endif; ?>
	
	</div>
	</div>
	<!-- Start footer.php -->
	
	 <div id="footer"> 
            Copyright &copy; <?php echo date('Y');?> All Rights Reserved. <a href="http://windwoman.com" target="_blank">WindWoman Designs</a> | Powered by <a href="http://wordpress.org/">WordPress</a> <br />
Maintained by <a href="http://tysonbrooks.org/" target="_blank">Tyson Brooks Web Services</a> | <a href="http://windwoman.com/wind-woman-tos/">Terms of Service</a>
          </div>
<!-- End footer.php -->
</div>
<!-- End innerwrap -->
</div>
	


  </div>
</div>
  <div id="outerbottom"></div>
</div>
<?php wp_footer(); ?>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-25777101-1']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>

</body>
</html>