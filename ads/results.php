<style type="text/css">
body,td,th {
	color: #FFF;
}
body {
	background-color: #000;
}
#texturl {
	width:588px;
}
</style>
<?php
$host = 'embroideryadvertisers.com';
$website = $_GET['website'];
$image = $_GET['ad-image'];
?>
<h2>Website Ad Codes</h2>
<p>Use this code for an image embed.</p>
<form name="results">
<TEXTAREA name="code" rows="5" cols="70">
<a href="http://<?php echo $host;?>/?utm_source=<?php echo $website;?>&utm_medium=site_ad&utm_campaign=<?php echo $website;?>" target="_new"><img src="http://<?php echo $host;?>/ads/images/<?php echo $image;?>.png"></a>
</TEXTAREA><br><br>
<p>Use this code for a text link</p>
<textarea name="texturl" rows="5" cols="70">
<a href="http://<?php echo $host;?>/?utm_source=<?php echo $website;?>&utm_medium=site_ad&utm_campaign=<?php echo $website;?>">Embroidery Advertisers</a>
</textarea><br />
<br />

<a href="http://<?php echo $host;?>/promote-us/"><input type="button" value="Back"></a>
</form>

Your Ad will look like this:<br><br>
<a href="http://<?php echo $host;?>/?utm_source=<?php echo $website;?>&utm_medium=site_ad&utm_campaign=<?php echo $website;?>" target="_new"><img src="http://<?php echo $host;?>/ads/images/<?php echo $image;?>.png"></a><br><br>