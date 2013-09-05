<? 
if (isset($_GET['type'])) { $type=$_GET['type']; } else { $type='';}
?>
<style type="text/css">
body{
background:#eee;
}
#wrapper {
	background:#fff;
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
#info {
width:350px;
float:left;
margin:25px;
}
#select {
float:right;
width:400px;
margin:65px 65px;
}
.clear{
clear:both;
}
</style>
<div id="wrapper">
<center><img src="images/payment-bg.png"></center>

	<div id="info">
		<h3>Thank you for supporting us!</h3>
		<p>All contributions go towards development and sustaining this website.<br>We strive to be the best that we can be by bringing you fun and new exciting things all the time. Contributions will go towards maintenance of the website and development of new features.</p>
	</div>
	<div id="select">
		Please input the amount here that you are willing to donate. This will be a one time contribution. <b>NOT</b> a monthly contribution.<br/>
		<form action="payment.php" method="GET">
		<label name="amount">Contribution Amount: <input type="text" size="5" name="p" placeholder="10.00"></label><br/>
		<input type="hidden" name="type" value="bn">
		<input type="hidden" name="package" value="One Time Contribution">
		<input type="submit" value="Continue to Paypal">
		</form>
	</div>
<div class="clear"></div>
</div>