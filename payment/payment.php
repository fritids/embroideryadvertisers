	<?php
if(isset($_GET['type'])) { $type = $_GET['type']; } else { $type=''; }
if(isset($_GET['package'])) { $package = $_GET['package']; } else { $package=''; }
if(isset($_GET['p'])) { $totalprice = $_GET['p']; } else { $totalprice=''; }
if(isset($_GET['term'])) { $term = $_GET['term']; } else { $term=''; }
if(isset($_GET['c'])) { $contract = $_GET['c']; } else { $contract=''; }

if(isset($_GET['d'])){ $dis=1; $discount=$_GET['d']; } else { $dis=0; $discount=''; }
if(isset($_GET['dt'])) { $discterm = $_GET['dt']; }
if(isset($_GET['dc'])) { $dc = $_GET['dc']; }


/// Instant download vars
if(isset($_GET['ftype'])) { $ftype=$_GET['ftype']; } else { $ftype=''; }
if(isset($_GET['file'])) { $file=$_GET['file']; } else { $file=''; }

if(isset($_GET['debug'])){ $debug = $_GET['debug']; } else { $debug=0; }
 ?>
<script type="text/javascript">
function myfunc () {var frm = document.getElementById("subscribe");frm.submit();}window.onload = function() { setTimeout(myfunc, 1000); }
</script>
<style type="text/css">
#wrapper {
	width:960px;
	border:1px solid #000;
	margin:25px auto 0 auto;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	border-radius: 10px; /* future proofing */
	-khtml-border-radius: 10px; /* for old Konqueror browsers */
	border:1px solid #d80000;
	box-shadow: 10px 10px 5px #888888;
}
#loadingimg {
	margin:0 0 25px 0;
}
</style>
<div id="wrapper">
<center><img src="images/payment-bg.png"><p>In just a moment you will be redirected to paypal.</p></center>
<? if ($type=='signup') { 

if ($package=='EA-Direct-OAAD') {
	$signup='one_ad';
} elseif ($package=='EA-Direct-TwAAD') {
	$signup='two_ads';
} elseif ($package=='EA-Direct-ThAD') {
	$signup='three_ads';
}
?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="subscribe">
 <input type="hidden" name="cmd" value="_xclick-subscriptions" />
  <input type="hidden" name="business" value="tyson@tysonbrooks.net">
  <input type="hidden" name="item_name" value="<? echo $package;?>-<?php echo date('Y').'-'.date('n').'-'.date('d');?>">
  <input type="hidden" name="no_shipping" value="1">
  <? if ($_GET['adpack']>0) { ?>
  <input type="hidden" name="return" value="http://embroideryadvertisers.com/actions/?action=regadvert&signup=<? echo $signup;?>&adpack=<? echo $_GET['adpack']; ?>">
  <? } else { ?>
  <input type="hidden" name="return" value="http://embroideryadvertisers.com">
  <? } ?>
  <input type="hidden" name="cancel_return" value="http://embroideryadvertisers.com/">
<? if($discterm>=1) { ?>
  <input type="hidden" name="a1" value="<? echo $discount;?>">
  <input type="hidden" name="p1" value="1">
  <input type="hidden" name="t1" value="<? echo $dc;?>">
<? }
	if ($discterm>1) { 
	$dcount=$discterm-1;
	?>
	<input type="hidden" name="a2" value="<? echo $discount;?>">
	<input type="hidden" name="p2" value="<? echo $dcount;?>">
	<input type="hidden" name="t2" value="<? echo $dc;?>">
<? } ?>
  <input type="hidden" name="a3" value="<? echo $totalprice;?>">
  <input type="hidden" name="p3" value="<? echo $term;?>">
  <input type="hidden" name="t3" value="<? echo $contract;?>">
  <input type="hidden" name="src" value="1">
  <input type="hidden" name="sra" value="1">
  <input type="hidden" name="no_note" value="1">
  <input type="hidden" name="usr_manage" value="0">
<center>  <input type="image" src="images/loading.gif" border="0" name="submit" id="loadingimg"></center>
</form>
<? } elseif ($type=='bnid') { ?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="subscribe">
 <input type="hidden" name="cmd" value="_xclick" />
  <input type="hidden" name="business" value="tyson@tysonbrooks.net">
  <input type="hidden" name="item_name" value="<? echo $package;?>">
  <input type="hidden" name="no_shipping" value="1">
  <input type="hidden" name="return" value="http://embroideryadvertisers.com/files/?session=1&type=<? echo $ftype;?>&file=<? echo $file;?>">
  <input type="hidden" name="amount" value="<? echo $totalprice;?>">
  <input type="hidden" name="discount_amount" value="<? echo $discount;?>">

<center>  <input type="image" src="images/loading.gif" border="0" name="submit" id="loadingimg"></center>
</form>
<? } elseif ($type=='bn') { ?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="subscribe">
 <input type="hidden" name="cmd" value="_xclick" />
  <input type="hidden" name="business" value="tyson@tysonbrooks.net">
  <input type="hidden" name="item_name" value="<? echo $package;?>-<?php echo date('Y').'-'.date('n').'-'.date('d');?>">
  <input type="hidden" name="no_shipping" value="1">
  <input type="hidden" name="return" value="http://embroideryadvertisers.com/">
  <input type="hidden" name="amount" value="<? echo $totalprice;?>">
  <input type="hidden" name="discount_amount" value="<? echo $discount;?>">

<center>  <input type="image" src="images/loading.gif" border="0" name="submit" id="loadingimg"></center>
</form>
<? } ?>
</div>