<?
$c_url=get_settings('siteurl').'/wp-admin/admin.php?page=trivia-settings';
if (isset($_GET['settings'])) {$settings=$_GET['settings']; };
if (isset($_GET['qadd'])) {
	if (get_option('question'.$_GET['qadd'])==NULL) {
		add_option('question'.$_GET['qadd'], $_POST['thequestion'], '', 'yes');
		add_option('answer1'.$_GET['qadd'], $_POST['answer1'], '', 'yes');
		add_option('answer2'.$_GET['qadd'], $_POST['answer2'], '', 'yes');
		add_option('answer3'.$_GET['qadd'], $_POST['answer3'], '', 'yes');
		add_option('answer4'.$_GET['qadd'], $_POST['answer4'], '', 'yes');
		add_option('correcta'.$_GET['qadd'], $_POST['correctanswer'], '', 'yes');
	} else {
		update_option('question'.$_GET['qadd'], $_POST['thequestion'], '', 'yes');
		update_option('answer1'.$_GET['qadd'], $_POST['answer1'], '', 'yes');
		update_option('answer2'.$_GET['qadd'], $_POST['answer2'], '', 'yes');
		update_option('answer3'.$_GET['qadd'], $_POST['answer3'], '', 'yes');
		update_option('answer4'.$_GET['qadd'], $_POST['answer4'], '', 'yes');
		update_option('correcta'.$_GET['qadd'], $_POST['correctanswer'], '', 'yes');
	}
}
if (isset($_GET['padd'])) {
	if (get_option('prizeimage'.$_GET['padd'])==NULL) {
		add_option('prizeimage'.$_GET['padd'], $_POST['prizeimage'], '', 'yes');
		add_option('prizedl'.$_GET['padd'], $_POST['prizedl'], '', 'yes');
	} else {
		update_option('prizeimage'.$_GET['padd'], $_POST['prizeimage'], '', 'yes');
		update_option('prizedl'.$_GET['padd'], $_POST['prizedl'], '', 'yes');
	}
}
if (isset($_GET['setupquestions'])) {
	if (get_option('numquestions')==NULL) {
		add_option('numquestions', $_POST['numquestions'], '', 'yes');
	} else {
		update_option('numquestions', $_POST['numquestions'], '', 'yes');
	}
	if (get_option('numprizes')==NULL) {
		add_option('numprizes', $_POST['numprizes'], '', 'yes');
	} else {
		update_option('numprizes', $_POST['numprizes'], '', 'yes');
	}
	if (get_option('triviaactive')==NULL) {
		add_option('triviaactive', $_POST['triviaactive'], '', 'yes');
	} else {
		update_option('triviaactive', $_POST['triviaactive'], '', 'yes');
	}
}
?>
<style type="text/css">
input{
	background:#e0e0e0;
	}
#pl-wrapper{
	width:960px;
	margin:25px auto 25px auto;
	padding:10px;
    float:left;
	}
#donate{
	float:right;
	}
#trivianav {
	background:#e0e0e0;
	margin:15px 0 15px 0;
	padding:2px 5px 2px 5px;
}
#plugin-rss {
	width:450px;
	border:1px solid #e5e5e5;
	padding:10px;
    float:left;
}
</style>
<div id="pl-wrapper">
<div id="donate">
<center><a href="https://payments.tysonbrooks.net/donate.php" target="new"><img src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif"></a></center>
</div>
<h1>Trivia Settings</h2>
<? echo get_option('question'.$_GET['qadd']); ?>
<div id="trivianav"><a href="<? echo $c_url.'&settings=1';?>">Global Settings</a> | <a href="<? echo $c_url.'&settings=2';?>">Questions</a> | <a href="<? echo $c_url.'&settings=3';?>">Prizes</a></div>
<? if (!isset($settings)) {?>
<div id="plugin-rss">
<?php
include_once(ABSPATH.WPINC.'/rss.php'); // path to include script
//include_once('../../../wp-includes/rss.php'); // path to include script
$feed = fetch_rss('https://s.tysonbrooks.net/category/new-products/feed'); // specify feed url
$items = array_slice($feed->items, 0, 3); // specify first and last item
?>
<?php if (!empty($items)) : ?>
<?php foreach ($items as $item) : ?>
<h2><a href="<?php echo $item['link']; ?>"><?php echo $item['title']; ?></a></h2>
<p><?php echo $item['description']; ?></p>
<?php endforeach; ?>
<?php endif;
    require (ABSPATH . WPINC . '/pluggable.php');
echo bloginfo('url');
 ?>
</div>
<? } elseif ($settings==1) {?>
	<div id="settings">
	<form action="<? echo $c_url;?>/&settings=1&setupquestions=1" method="POST">
	<? $triviaactive = get_option('triviaactive'); ?>	
	<p>Trivia is <? echo $triviaactive;?>:<br>
	<input type="radio" name="triviaactive" value="Enabled" <?if($triviaactive=='Enabled') { ?>checked<?}?>> Enable <input type="radio" name="triviaactive" value="Disabled" <?if($triviaactive=='Disabled' || $triviaactive==NULL) { ?>checked<?}?>> Disable</p>
	<p>How many questions? <input type="text" name="numquestions" value="<? echo get_option('numquestions');?>" size="3" placeholder="3"><br>
	How many prizes? <input type="text" name="numprizes" value="<? echo get_option('numprizes');?>" size="3" placeholder="3"></p>
	<input type="submit" value="Set Options"></form></div>
<? } elseif ($settings==2) { ?>
	<div id="questions">
		<div id="trivianav">
		<? $i=0; $numquestions = get_option('numquestions'); $totalquestions = $numquestions-1;
		while ($i<=$totalquestions) { $i++; echo '<a href="'.$c_url.'&settings=2&question='.$i.'">Question '.$i.'</a> | ';}?>
		</div>
		<? if ((!isset($_GET['question'])) && ($_GET['qadd']==NULL)) {
			echo '<p>Please selecet a question</p>';
		} elseif ($_GET['qadd']!=NULL) {
			echo '<p>Your options have been saved.</p>';
		} else {
			$correcta = get_option('correcta'.$_GET['question']);
		?>
			<h2>Question: <? echo $_GET['question'];?><br></h2>
			<form action="<? echo $c_url;?>/&settings=2&qadd=<? echo $_GET['question'];?>" method="POST">
			<input type="hidden" name="question" value="1">
			<p>The Question:<br><textarea rows="4" cols="125" name="thequestion"><? echo get_option('question'.$_GET['question'].'');?></textarea></p>
			<p>Answers:<br>A: <input type="text" name="answer1" value="<? echo get_option('answer1'.$_GET['question'].'');?>" size="35"><br>B: <input type="text" name="answer2" value="<? echo get_option('answer2'.$_GET['question'].'');?>" size="35"><br>C: <input type="text" name="answer3" value="<? echo get_option('answer3'.$_GET['question'].'');?>" size="35"><br>D: <input type="text" name="answer4" value="<? echo get_option('answer4'.$_GET['question'].'');?>" size="35"></p>
			<p>Correct Answer: <? echo $correcta;?><br><br><input type="radio" name="correctanswer" value="A" <?if($correcta=='A'){?>checked<?}?>> A <input type="radio" name="correctanswer" value="B"<?if($correcta=='B'){?>checked<?}?>> B <input type="radio" name="correctanswer" value="C"<?if($correcta=='C'){?>checked<?}?>> C <input type="radio" name="correctanswer" value="D"<?if($correcta=='D'){?>checked<?}?>> D</p><input type="submit" value="Update Options"></form>
		<? } ?>
	</div>
<? } elseif ($settings==3) { ?>
	<div id="answers">
		<div id="trivianav">
		<? $i=0; $numprizes = get_option('numprizes');
		$totalprizes = $numprizes-1;
		while ($i<=$totalprizes) {
			$i++;
			echo '<a href="'.$c_url.'&settings=3&prize='.$i.'">Prize '.$i.'</a> | ';
		}
		?>
		</div>
		<? if ((!isset($_GET['prize'])) && ($_GET['add']==NULL)) {
			echo '<p>Please selecet a Prize</p>';
		} elseif ($_GET['add']!=NULL) {
			echo '<p>Your options have been saved.</p>';
		} else {
		?>
			<h2>Prize: <? echo $_GET['prize'];?><br></h2>
			<form action="<? echo $c_url;?>/&settings=3&padd=<? echo $_GET['prize'];?>" method="POST">
			<input type="hidden" name="question" value="1">
			Image will appear at the native size.<br>
			Prize Image: <input type="text" name="prizeimage" value="<? echo get_option('prizeimage'.$_GET['prize'].'');?>" size="55"><br>
			Prize dl: <input type="text" name="prizedl" value="<? echo get_option('prizedl'.$_GET['prize'].'');?>" size="55"><br>
			<input type="submit" value="Update Options">
			</form>
		<? } ?>
	</div>
<? } ?>
</div>