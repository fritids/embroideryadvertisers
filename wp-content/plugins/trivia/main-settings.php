<?
$option_url=get_settings('siteurl').'/wp-admin/options-general.php?page=trivia-api-settings';

if (isset($_GET['reset'])){ 
delete_option('triviausername'); delete_option('triviawebsite'); delete_option('triviaemail'); delete_option('triviaapikey'); 

echo '<div class="updated"><h3>Please wait while settings are reset...</h3></div>';
echo "<meta http-equiv='refresh' content='3;url=$option_url' />";
}
function update_notice(){
				echo '<div class="updated"><h3>Please wait for settings to be saved...</h3></div>';
				echo "<meta http-equiv='refresh' content='3;url=$option_url' />";
			}

if (isset($_POST['username'])) {
	if (get_option('triviausername')==NULL) {
		add_option('triviausername', $_POST['username'], '', 'yes');
		//update_option('triviausername', $_POST['username'], '', 'yes');
	} else {
		update_option('triviausername', $_POST['username'], '', 'yes');
	}
	if (get_option('triviawebsite')==NULL) {
		add_option('triviawebsite', $_POST['website'], '', 'yes');
	} else {
		update_option('triviawebsite', $_POST['website'], '', 'yes');
	}
	if (get_option('triviaemail')==NULL) {
		add_option('triviaemail', $_POST['email'], '', 'yes');
	} else {
		update_option('triviaemail', $_POST['email'], '', 'yes');
	}
	if (get_option('triviaapikey')==NULL) {
		add_option('triviaapikey', $_POST['apikey'], '', 'yes');
	} else {
		update_option('triviaapikey', $_POST['apikey'], '', 'yes');
	}
	update_notice();
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
#api-settings {

}
</style>
<div id="pl-wrapper">
<h2>Please insert your API Settings in order for the trivia plugin to work.</h2>
	<div id="api-settings">
		<form action="" method="POST">
		<p>What are your API settings?</p>
			Username: <input type="text" name="username" size="34" value="<? echo get_option('triviausername');?>" placeholder="usernam2"><br>
			Website: <input type="text" name="website" size="34" value="<? echo get_option('triviawebsite');?>" placeholder="<? echo get_option('siteurl');?>"><br>
			Email: <input type="text" name="email" size="34" value="<? echo get_option('triviaemail');?>" placeholder="<? echo get_option('admin_email');?>"><br>
			API Key: <input type="text" name="apikey" size="34" value="<? echo get_option('triviaapikey');?>" placeholder="<? echo md5('this is a test');?>"><br>
			<input type="submit" name="submit" value="Update Settings">
		</form>
	</div>
	        
	<p>Click <a href="<? echo $option_url;?>&reset=1">here</a> to reset the plugin.</p>
</div>