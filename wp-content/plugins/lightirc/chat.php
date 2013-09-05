<?
/*
Plugin Name: LightIRC Plugin
Plugin URI: http://mach7enterprises.com
Description: Wordpress port of LightIRC
Version: 0.1
Author: Tyson Brooks
Author URI: http://mach7enterprises.com
*/function get_avatar_url($get_avatar){preg_match("/src='(.*?)'/i", $get_avatar, $matches);return $matches[1];}function m7_avatar(){if (isset($_GET['getImage'])){$nick = $_GET['getImage']; /*nickname*/$prefix = $_GET['prefix'];/* Figure out the userID based on the nick in $nick*/$userID = get_user_by( 'login', $_GET['getImage'] );$avatar = get_avatar_url(get_avatar($userID->ID, 15));/* Get the image*/$imageData = file_get_contents($avatar);/* Figure out the content type - looking at the URL suffix could be a good place*/header('Content-Type: image/jpg');/* Send the image*/echo $imageData;/* Prevent WP from doing anything else*/exit;}else{/* getImage not called, continue as normal*/}}
/* Do the avatar check before any headers are sent*/
add_filter('send_headers', 'm7_avatar');
 

add_action('wp_ajax_update_chat_meta', 'update_chat_meta');
function update_chat_meta() {
$current_user = wp_get_current_user(); $username = $current_user->user_login; $userpass = $current_user->user_pass; $useremail = $current_user->user_email; $firstname = $current_user->user_firstname; $lastname = $current_user->user_lastname; $displayname = $current_user->display_name; $userid = $current_user->ID;     $role=get_user_role($userid);
if (isset($_POST['func'])) {
	
	update_usermeta( $userid, $_POST['func'], $_POST['set'] );
	}
}





function m7_livechat($atts) { 
extract(shortcode_atts(array('chatwidth' => '445px','chatheight' => '128px','ircserver' => 'irc.mach7enterprises.com','ircport' => '6667','flashport' => '9024','autojoin' => '#mach7','style' => 'lightblue','fontsize' => '12'), $atts));

$current_user = wp_get_current_user(); $username = $current_user->user_login; $userpass = $current_user->user_pass; $useremail = $current_user->user_email; $firstname = $current_user->user_firstname; $lastname = $current_user->user_lastname; $displayname = $current_user->display_name; $userid = $current_user->ID;     $role=get_user_role($userid);

/* Memory Settings */
$chattime = get_the_author_meta( 'chattime', $userid );

$testsetting=$chattime;

global $current_user;$ul = 'Guest';if ( isset($current_user) ){$rawuser = $current_user->user_login;
$ul = str_replace('_', '',$rawuser);
}

if ($ul=='TysonBrooks') { $loginuser='Tyson_Brooks';} elseif ($ul=='') { $loginuser = 'user_%';} else { $loginuser=$ul;}
?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript">
var params = {};
params.host                         = "irc.mach7enterprises.com";
params.port                         = 6667;
params.policyPort                   = 9024;
params.language                     = "en";
params.styleURL                     = "css/<? echo $style;?>.css";
params.nick                         = "<? echo $loginuser;?>";
params.nickAlternate = "<? echo $loginuser;?>-1";
params.fontSize                     = "<? echo $fontsize;?>";
params.showNickSelection            = false;
params.showIdentifySelection        = false;
params.showRegisterNicknameButton   = false;
params.showRegisterChannelButton    = false;
params.showNewQueriesInBackground   = false;
params.showTimestamps = true;
params.timestampFormat = "[HH:mm:ss]";
params.showInfoMessages	= false;
params.showRichTextControls = false;
params.soundAlerts = true;
params.emoticonPath = "<?echo plugins_url( 'emoticons/',__FILE__);?>";
params.emoticonList = ":)->smile.gif,;)->wink.gif,:D->biggrin.gif,:P->tongue.gif,:(->sad.gif,:$->blushing.gif,:O->ohmy.gif,(H)->cool.gif,:|->mellow.gif,;)->blink.gif,:'(->crying.gif,:S->unsure.gif,:[->mad.gif,8>->duck.gif";
params.showEmoticonsButton = true;
params.showNickChangeButton = false;
params.showRegisterChannelButton = false;
params.showNewQueriesInBackground = true;
params.showRichTextControls = true;
params.showRichTextControlsForegroundColor = true;
params.showRichTextControlsBackgroundColor = true;
params.showNewQueriesInBackground = true;
params.useUserListIcons = true;
params.showJoinChannelButton = false;
params.showPartChannelButton = false;
params.channelHeader = "Users: %users% -- Topic: %topic%";
params.showOptionsButton = false;
//params.customSecurityErrorMessage = "There was an error, please use the link via the contact us page."
params.showServerWindow             = false;
params.userListCustomIcons = "http://embroideryadvertisers.com/members-area/chatroom/?getImage=%nick%";
params.defaultBanmask = "*!*@*%host%*";


//User Diff Roles
<? if ($role == 'chatroom_moderator' || $role == 'administrator') { ?>
params.autojoin                     = "#ea-mods,#embroideryadvertisers";
params.showMenuButton = true;
params.showMenuButton = true;
params.showListButton = false;
params.showRegisterNicknameButton = true;
params.showChannelCentralButton = true;
params.showJoinPartMessages = true;

<? 
/*
$checkpass = get_the_author_meta( 'chatpass', $userid );
//$checkpass = '';
if (empty($checkpass)) { ?>
params.perform = "/msg nickserv register <? echo $userpass.' '.$useremail;?>";
<?
update_usermeta( $userid, 'chatpass', $userpass );

 } else { ?>
params.perform = "/msg nickserv identify <? echo get_the_author_meta( 'chatpass', $userid );?>";
<? }*/?>

<? } else { ?>
params.autojoin                     = "#embroideryadvertisers";
params.showMenuButton = false;
params.showMenuButton = false;
params.showListButton = false;
params.showRegisterNicknameButton = false;
params.showChannelCentralButton = false;
params.showJoinChannelButton = false;
params.showPartChannelButton = false;
params.showJoinPartMessages = false;
<? } ?>

params.navigationPosition           = "bottom";
function sendCommand(command) {
  swfobject.getObjectById('lightIRC').sendCommand(command);
}
function sendMessageToActiveWindow(message) {
  swfobject.getObjectById('lightIRC').sendMessageToActiveWindow(message);
}
function setTextInputContent(content) {
  swfobject.getObjectById('lightIRC').setTextInputContent(content);
}
function onChatAreaClick(nick, ident, realname) {
  //alert("onChatAreaClick: "+nick);
}
function onContextMenuSelect(type, nick, ident, realname) {
  alert("onContextMenuSelect: "+nick+" for type "+type);
}
function onServerCommand(command) {
  return command;
}
window.onbeforeunload = function() {
  swfobject.getObjectById('lightIRC').sendQuit();
}
for(var key in params) {
  params[key] = params[key].toString().replace(/%/g, "%25");
}





function updateOption(option,value)
{
    $.ajax({
       type: "POST",
       url: "http://embroideryadvertisers.com/actions/?action=chatupdate",
       data: "func="+option+"set="+value,
	   success: function(){
						if (option=='chattime') {
							if (value=='on') {
								onTime();
								} else {
								offTime();
								}
						}
       }
     });
	//alert("You sent "+option+" and "+value);

}







function onTime() {
params.showTimestamps				= true;
params.timestampFormat              = "[HH:mm]";
swfobject.embedSWF("<? echo plugins_url( 'lightIRC.swf',__FILE__);?>", "lightIRC", "<? echo $chatwidth;?>", "<? echo $chatheight;?>", "10.0.0", "<?echo plugins_url( 'expressInstall.swf',__FILE__);?>", params);
document.getElementById('timestamp').innerHTML = "<span class=\"link\" onclick=\"updateOption('chattime','off');\"><img src=\"<? echo plugins_url( 'icons/time-on',__FILE__);?>.png\" title=\"Turn Off Timestamps\"></span>";
//alert("Time On");
}
function offTime() {
params.showTimestamps				= false;
params.timestampFormat              = "[HH:mm]";
swfobject.embedSWF("<? echo plugins_url( 'lightIRC.swf',__FILE__);?>", "lightIRC", "<? echo $chatwidth;?>", "<? echo $chatheight;?>", "10.0.0", "<?echo plugins_url( 'expressInstall.swf',__FILE__);?>", params);
document.getElementById('timestamp').innerHTML = "<span class=\"link\" onclick=\"updateOption('chattime','on');\"><img src=\"<? echo plugins_url( 'icons/time-off',__FILE__);?>.png\" title=\"Turn Off Timestamps\"></span>";
//alert("Time Off");
}
function font24() {
swfobject.getObjectById('lightIRC').sendQuit();
params.fontSize = '24';
swfobject.embedSWF("<? echo plugins_url( 'lightIRC.swf',__FILE__);?>", "lightIRC", "<? echo $chatwidth;?>", "<? echo $chatheight;?>", "10.0.0", "<?echo plugins_url( 'expressInstall.swf',__FILE__);?>", params);
document.getElementById('timestamp').innerHTML = '<span class="link" onclick="onTime();"><img src="<? echo plugins_url( 'icons/time-off',__FILE__);?>.png"></span>';
document.getElementById('fontsize').innerHTML = '<span onclick="font22();"><img src="<? echo plugins_url( 'icons/minus.png',__FILE__);?>"></span>Font Size: 24<img src="<? echo plugins_url( 'icons/plus.png',__FILE__);?>">';
}
function font22() {
swfobject.getObjectById('lightIRC').sendQuit();
params.fontSize = '22';
swfobject.embedSWF("<? echo plugins_url( 'lightIRC.swf',__FILE__);?>", "lightIRC", "<? echo $chatwidth;?>", "<? echo $chatheight;?>", "10.0.0", "<?echo plugins_url( 'expressInstall.swf',__FILE__);?>", params);
document.getElementById('timestamp').innerHTML = '<span class="link" onclick="onTime();"><img src="<? echo plugins_url( 'icons/time-off',__FILE__);?>.png"></span>';
document.getElementById('fontsize').innerHTML = '<span onclick="font20();" class="link"><img src="<? echo plugins_url( 'icons/minus.png',__FILE__);?>"></span>Font Size: 22<span onclick="font24();" class="link"><img src="<? echo plugins_url( 'icons/plus.png',__FILE__);?>"></span>';
}
function font20() {
swfobject.getObjectById('lightIRC').sendQuit();
params.fontSize = '20';
swfobject.embedSWF("<? echo plugins_url( 'lightIRC.swf',__FILE__);?>", "lightIRC", "<? echo $chatwidth;?>", "<? echo $chatheight;?>", "10.0.0", "<?echo plugins_url( 'expressInstall.swf',__FILE__);?>", params);
document.getElementById('timestamp').innerHTML = '<span class="link" onclick="onTime();"><img src="<? echo plugins_url( 'icons/time-off',__FILE__);?>.png"></span>';
document.getElementById('fontsize').innerHTML = '<span onclick="font18();" class="link"><img src="<? echo plugins_url( 'icons/minus.png',__FILE__);?>"></span>Font Size: 20<span onclick="font22();" class="link"><img src="<? echo plugins_url( 'icons/plus.png',__FILE__);?>"></span>';
}
function font18() {
swfobject.getObjectById('lightIRC').sendQuit();
params.fontSize = '18';
swfobject.embedSWF("<? echo plugins_url( 'lightIRC.swf',__FILE__);?>", "lightIRC", "<? echo $chatwidth;?>", "<? echo $chatheight;?>", "10.0.0", "<?echo plugins_url( 'expressInstall.swf',__FILE__);?>", params);
document.getElementById('timestamp').innerHTML = '<span class="link" onclick="onTime();"><img src="<? echo plugins_url( 'icons/time-off',__FILE__);?>.png"></span>';
document.getElementById('fontsize').innerHTML = '<span onclick="font16();" class="link"><img src="<? echo plugins_url( 'icons/minus.png',__FILE__);?>"></span>Font Size: 18<span onclick="font20();" class="link"><img src="<? echo plugins_url( 'icons/plus.png',__FILE__);?>"></span>';
}
function font16() {
swfobject.getObjectById('lightIRC').sendQuit();
params.fontSize = '16';
swfobject.embedSWF("<? echo plugins_url( 'lightIRC.swf',__FILE__);?>", "lightIRC", "<? echo $chatwidth;?>", "<? echo $chatheight;?>", "10.0.0", "<?echo plugins_url( 'expressInstall.swf',__FILE__);?>", params);
document.getElementById('timestamp').innerHTML = '<span class="link" onclick="onTime();" class="link"><img src="<? echo plugins_url( 'icons/time-off',__FILE__);?>.png"></span>';
document.getElementById('fontsize').innerHTML = '<img src="<? echo plugins_url( 'icons/minus.png',__FILE__);?>">Font Size: 16<span onclick="font18();" class="link"><img src="<? echo plugins_url( 'icons/plus.png',__FILE__);?>"></span>';
}
function privSoundon() {
if (params.soundAlerts==false) { swfobject.getObjectById('lightIRC').sendQuit(); }
params.soundAlerts = true;
swfobject.embedSWF("<? echo plugins_url( 'lightIRC.swf',__FILE__);?>", "lightIRC", "<? echo $chatwidth;?>", "<? echo $chatheight;?>", "10.0.0", "<?echo plugins_url( 'expressInstall.swf',__FILE__);?>", params);
document.getElementById('privatemessage').innerHTML = '<span class="link" onclick="privSoundoff();"><img src="<? echo plugins_url( 'icons/privatemessageon',__FILE__);?>.png" title="Turn Private Message Sounds Off"></span>';
}
function privSoundoff() {
if (params.soundAlerts==true) { swfobject.getObjectById('lightIRC').sendQuit(); }
params.soundAlerts = false;
swfobject.embedSWF("<? echo plugins_url( 'lightIRC.swf',__FILE__);?>", "lightIRC", "<? echo $chatwidth;?>", "<? echo $chatheight;?>", "10.0.0", "<?echo plugins_url( 'expressInstall.swf',__FILE__);?>", params);
document.getElementById('privatemessage').innerHTML = '<span class="link" onclick="privSoundon();"><img src="<? echo plugins_url( 'icons/privatemessageoff',__FILE__);?>.png" title="Turn Private Message Sounds On"></span>';
}
function chanSoundon() {
if (params.soundOnNewChannelMessage==false) {swfobject.getObjectById('lightIRC').sendQuit();}
params.soundOnNewChannelMessage = true;
swfobject.embedSWF("<? echo plugins_url( 'lightIRC.swf',__FILE__);?>", "lightIRC", "<? echo $chatwidth;?>", "<? echo $chatheight;?>", "10.0.0", "<?echo plugins_url( 'expressInstall.swf',__FILE__);?>", params);
document.getElementById('allmessages').innerHTML = '<span class="link" onclick="chanSoundoff();"><img src="<? echo plugins_url( 'icons/chanmessageon',__FILE__);?>.png" title="Turn New Chat Message Sound Off"></span>';
}
function chanSoundoff() {
if (params.soundOnNewChannelMessage==true) {swfobject.getObjectById('lightIRC').sendQuit();}
params.soundOnNewChannelMessage = false;
swfobject.embedSWF("<? echo plugins_url( 'lightIRC.swf',__FILE__);?>", "lightIRC", "<? echo $chatwidth;?>", "<? echo $chatheight;?>", "10.0.0", "<?echo plugins_url( 'expressInstall.swf',__FILE__);?>", params);
document.getElementById('allmessages').innerHTML = '<span class="link" onclick="chanSoundon();"><img src="<? echo plugins_url( 'icons/chanmessageoff',__FILE__);?>.png" title="Turn New Chat Message Sound On"></span>';
}
function fullScreen() {
swfobject.getObjectById('lightIRC').sendQuit();
var embed = swfobject.embedSWF("<? echo plugins_url( 'lightIRC.swf',__FILE__);?>", "lightIRC", "100%", "100%", "10.0.0", "<?echo plugins_url( 'expressInstall.swf',__FILE__);?>", params);
document.getElementById('wrapper') = embed;
}
function init() {
	onTime();
	privSoundon();
	chanSoundoff();
	document.getElementById('fontsize').innerHTML = '<img src="<? echo plugins_url( 'icons/minus.png',__FILE__);?>">Font Size: 14<span onclick="font16();" class="link"><img src="<? echo plugins_url( 'icons/plus.png',__FILE__);?>"></span>';
}
window.onload=init;
</script>
 <style type="text/css">
	.link {
	cursor: pointer;
	color:blue;
	}
	#controls { width:100%; background:#b2c7d3; margin:2px 0; }
	#timestamp, #fontsize, #privatemessage, #allmessages { float:left;}
	#fontsize img, #timestamp img, #dotlegend img { vertical-align:middle; margin: 0 2px 0 2px; }
	.clear { clear:both; }
	#dotlegend { float:right;}
 </style>
 <p>This chat room requires Adobe Flash to be installed and active to use the chat room.</p>
 <div id="lightIRC" style="height:100%; text-align:center;">
  <p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
 </div>
 <script type="text/javascript">
	
 </script>
 <div id="controls">
 <div id="timestamp"></div><div id="fontsize"></div><div id="privatemessage"></div><div id="allmessages"></div>
 <div id="dotlegend"><img src="<? echo plugins_url( 'icons/owner.png',__FILE__);?>"> Owner <img src="<? echo plugins_url( 'icons/admin.png',__FILE__);?>">Chat Bot <img src="<? echo plugins_url( 'icons/operator.png',__FILE__);?>">Moderators <img src="<? echo plugins_url( 'icons/default.png',__FILE__);?>">General Users </div>
 <div class="clear"></div>
 </div>
<? } add_shortcode( 'm7_livechat', 'm7_livechat' );
add_filter('widget_text', 'do_shortcode'); ?>