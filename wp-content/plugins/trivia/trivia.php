<?
/*
Plugin Name: Trivia
Plugin URI: http://tysonbrooks.net
Description: Run trivia from a plugin
Version: 1.1
Author: Tyson Brooks
Author URI: http://tysonbrooks.net
*/


function trivia() {
		
		//if( function_exists('FA_display_slider') ){FA_display_slider(3570);}
		
		
		echo "<center><b>Trivia is sponsored by:</b></center>";
		echo do_shortcode('[adrotate block="2"]');
		echo "<br/><br/>";
		echo "<h3>Welcome to Trivia!</h3>";
		echo "<p>This weekends trivia prizes, an alpha from <a href='http://windwoman.com' target='new'>Windwoman Designs</a>, and three designs by <a href='http://needlelittleembroidery.com' target='new'>Needle Little Embroidery</a></p>
		<p>Would YOU like to collect all of the alpha letters? Not all are listed in the Trivia Game. Stay tuned through the weekend to find out how to locate them all!</p>
		";
		
		
		$triviaactive = get_option('triviaactive');
		//$qduration = get_option('qduration');
		$minutes = 30;
		$timeout = $minutes*60;
		$timeleft= $_SESSION['timeleft'];
		$time = time();
		//$time='1362784248';
		
		if ($time>=$timeleft) {
		if ($triviaactive=='Enabled') {
		if (isset($_GET['answer'])) { $qanswer = $_GET['answer'];}
		$debug=0;
		
		if ($qanswer==1) {
		$guess = $_POST['answer'];
		$ca = $_POST['ca'];
		
		if ($debug==1) {
		echo 'Current Question: '.$_POST['cq'].'<br>';
		echo 'Current prize: '.$cprize.'<br>';
		echo 'Prize Image: '.$prizeimage.'<br>';
		echo 'Prize DL: '.$guess.'<br>';
		echo 'Correct Answer: '.$_POST['ca'].'<br>';
		}
		if ($guess == $ca) {
			session_start();
			$_SESSION['loggedin']=1;
			$_SESSION['triviad'] = time();
			$_SESSION['timeleft']=$time+$timeout;
			echo '<center>
			<div style="font-size:25px;">Congrats! You win!</div>
			<a href="'.$_POST['prizedl'].'"><img src="'.$_POST['prizeimage'].'"></a><br>Click on the picture to get your download!<br><br>Please let us know how we are doing, take a brief survey to give us some feedback! The survey is <a href="http://embroideryadvertisers.com/survey/" target="new">here</a><br><br>
			<a href="http://embroideryadvertisers.com/members-area/trivia">New Question</a><br>Haveing problems with trivia? Please <a href="http://embroideryadvertisers.com/contact-us/?subject=Trivia+Question+'.$_POST['cq'].'">let us know</a>!</center>';
			} else {
			echo 'Sorry, that answer was not correct!<br><a href="javascript: history.go(-1)" style="margin:5px 5px 5px 5px;">Go back to try again</a><br><br><br>Haveing problems with trivia? Please <a href="http://embroideryadvertisers.com/contact-us/?subject=Trivia+Question+'.$_POST['cq'].'">let us know</a>!';
			}
		} else { 
		
		$numquestions = get_option('numquestions');
		$numprizes = get_option('numprizes');
		
		$cquestion = rand(1, $numquestions);
		
		
		
		$cprize = rand(1, $numprizes);
		
		$prizeimage = get_option('prizeimage'.$cprize.'');
		$prizedl = get_option('prizedl'.$cprize.'');
		
		$a1 = get_option('answer1'.$cquestion); $a2 = get_option('answer2'.$cquestion);
		$a3 = get_option('answer3'.$cquestion); $a4 = get_option('answer4'.$cquestion);
		
		$correcta = get_option('correcta'.$cquestion);
		$question = get_option('question'.$cquestion);
		$answer = get_option('answer'.$cquestion);
		
		if ($debug==1) {
		echo 'Current Question: '.$cquestion.'<br>';
		echo 'Current prize: '.$cprize.'<br>';
		echo 'Prize Image: '.$prizeimage.'<br>';
		echo 'Prize DL: '.$prizedl.'<br>';
		echo 'Correct Answer: '.$correcta.'<br>';
		}
		
		?>
		<center><img src="<? echo $prizeimage;?>"></center>
		<p>Question: <i><? echo $question; ?></i></p>
		<p><form action="?answer=1" method="POST">
			<input type="hidden" name="cq" value="<? echo $cquestion;?>">
			<input type="hidden" name="ca" value="<? echo $correcta;?>">
			<input type="hidden" name="prizeimage" value="<? echo $prizeimage;?>">
			<input type="hidden" name="prizedl" value="<? echo $prizedl;?>">
			<input type="radio" name="answer" value="A">A: <? echo $a1;?>
			<input type="radio" name="answer" value="B">B: <? echo $a2;?><br>
			<input type="radio" name="answer" value="C">C: <? echo $a3;?>
			<input type="radio" name="answer" value="D">D: <? echo $a4;?><br>
		</p>
		<p><input type="submit" value="Submit Answer"><br>Question: <? echo $cquestion;?></p>
		</form>
	<? }
	} else {
		echo '<div id="message">Trivia is turned off, please enable it in settings</div>';
	}
	} else {
		echo '<div style="margin:25px 0 20px 0;"><center>You may only answer one question every '.$minutes.' Minutes.<br>Please come back to continue playing soon!<br>While you\'re waiting feel free to browse around the site and see what our designers have to offer!<br><br>Haveing problems with trivia? Please <a href="http://embroideryadvertisers.com/contact-us/?subject=Trivia+Question+Timeout'.$_POST['cq'].'">let us know</a>!</center></div>';
	}
}
add_shortcode( 'mytrivia', 'trivia' );
add_action('admin_menu', 'register_custom_menu_page');
function register_custom_menu_page() {
add_menu_page('Trivia Settings', 'Trivia Settings', 'add_users', 'trivia-settings', 'custom_menu_page', plugins_url('trivia/images/icon.png'), -6); }
function custom_menu_page(){ require (ABSPATH . WPINC . '/pluggable.php'); global $userdata; get_currentuserinfo(); require 'plugin-settings.php'; }


register_activation_hook(__FILE__, 'my_plugin_activate');
add_action('admin_init', 'my_plugin_redirect');

function my_plugin_activate() {
    add_option('my_plugin_do_activation_redirect', true);
}
//define('MY_PLUGIN_SETTINGS_URL', 'http://google.com');
function my_plugin_redirect() {
    if (get_option('my_plugin_do_activation_redirect', false)) {
        delete_option('my_plugin_do_activation_redirect');
        wp_redirect('options-general.php?page=trivia-api-settings');
    }
}

add_action('admin_menu', 'trivia_api_page');

function trivia_api_page() {
	add_submenu_page( 'options-general.php', 'Trivia API Settings', 'Trivia API Settings', 'manage_options', 'trivia-api-settings', 'trivia_custom_api_settings' ); 
}

function trivia_custom_api_settings() {
require 'main-settings.php';
}


	$debug=0;	
	if ($debug==1){ echo '<br>SIP: '.$serverip.'<br>';echo 'LIP: '.$localip.'<br>';	echo 'API1: '.md5($configapi);echo '<br>API2: '.$apikey;}



?>