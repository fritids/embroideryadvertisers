<?php 



/*

Plugin Name: WP User Registration

Plugin URI: http://blog.2i2j.com/plugins/wp-user-registration

Description: this plugin can strengthen wordpress registration function. 使用插件可以在注册中选择更多的可选项，一次完整注册。

Author: 偶爱偶家

Version: 2.4

Author URI: http://blog.2i2j.com/

*/



/*



2006-01-16

			2.4 发布, wp-pwd-register正式终结



2009-01-16

			1. 修改部分bug



2009-01-14

			1. 改名, 增强功能, 大幅度修改插件.



2008-07-01

			1. 再次修改提高遏制两次发送邮件.



2008-04-01

			1. 重新修改, 适合wp2.5



2007-11-14

			1. 增加init_info()函数, 可以使得目录控制更加灵活.



*/





if (!class_exists('wp_user_registration')):

@session_start();

class wp_user_registration{

	var $user_pw ='';

	var $db_options = 'wpuserregistration';

	var $options = array();

	var $userdata = array();



	var	$defaultoptions = array(// 0-disable, 1-required, 2-optional

			'user_pass' => 1,

			'verification' => 0,

			'user_url' => 0,

			'first_name' => 0,

			'last_name' => 0,

			'nickname' => 0,

			'aim' => 0,

			'yim' => 0,

			'jabber' => 0

		);

	var	$data = array(

			'user_pass' => 'Password',

			'verification' => 'Word Verification',

			'user_url' => 'Website',

			'first_name' => 'First Name',

			'last_name' => 'Last Name',

			'nickname' => 'Nickname',

			'aim' => 'AIM',

			'yim' => 'Yahoo IM',

			'jabber' => 'Jabber / Google Talk'

		);

	var	$onlyrequired = '|user_pass|verification|';

	var	$nometa = '|user_url|';



	var	$filters = array(

			'user_nicename' => 'pre_user_nicename',

			'user_url' => 'pre_user_url',

			'display_name' => 'pre_user_display_name',

			'nickname' => 'pre_user_nickname',

			'first_name' => 'pre_user_first_name',

			'last_name' => 'pre_user_last_name',

			'description' => 'pre_user_description'

		);





	function wp_user_registration(){

		$this->inithook();

	}



	function inithook(){

		add_action('admin_menu', array(&$this, 'optionmenu'));

		add_action('init', array(&$this, 'init_textdomain'));

		add_action('register_form', array(&$this, 'wp_register_form'));

		add_action('registration_errors', array(&$this, 'wp_registration_errors'));

		add_action('user_register', array(&$this, 'wp_user_register'));

		add_filter('login_message', array(&$this, 'wp_login_message'));

		if($_GET['action'] === 'register'){

			add_action('login_head', array(&$this, 'custom_login'));

		}

		add_action('deactivate_'.plugin_basename(__FILE__), array(&$this, 'deactivate_wp_user_registration'));

	}

	function initoption(){

		$this->data = array(

			'user_pass' => __('Password', 'wp-user-registration'),

			'verification' => __('Word Verification', 'wp-user-registration'),

			'user_url' => __('Website', 'wp-user-registration'),

			'first_name' => __('First Name', 'wp-user-registration'),

			'last_name' => __('Last Name', 'wp-user-registration'),

			'nickname' => __('Nickname', 'wp-user-registration'),

			'aim' => __('AIM', 'wp-user-registration'),

			'yim' => __('Yahoo IM', 'wp-user-registration'),

			'jabber' => __('Jabber / Google Talk', 'wp-user-registration')

		);



		$optionsFromTable = get_option($this->db_options);

		if (empty($optionsFromTable) || !is_array($optionsFromTable)){

			$this->resetToDefaultOptions();

		}



		$flag = FALSE;

		foreach($this->defaultoptions as $key => $value){

			if(isset($optionsFromTable[$key]) && ($optionsFromTable[$key] === 0 || $optionsFromTable[$key] === 1 || $optionsFromTable[$key] === 2)){

				$this->options[$key] = $optionsFromTable[$key];

			}else{

				$this->options[$key] = $value;

				$flag = TRUE;

			}

		}

		if($flag === TRUE){

			update_option($this->db_options, $this->options);

		}

		unset($optionsFromTable,$flag);

	}



	function resetToDefaultOptions(){

		update_option($this->db_options, $this->defaultoptions);

	}



	function deactivate_wp_user_registration(){

		delete_option('wpuserregistration');

		return true;

	}



	function init_textdomain(){

		$path = basename(str_replace('\\','/',dirname(__FILE__)));

		$path = (strtolower($path) === 'plugins') ? FALSE : $path;

		load_plugin_textdomain('wp-user-registration', FALSE, $path);

		unset($path);

		$this->initoption();

	}



	function custom_login(){

		global $wp_version;



		if($wp_version > 2.69)

			echo '<style type="text/css"><!--#login form input{/*background:#none repeat scroll 0 0;*/ border:1px solid #E5E5E5;margin-bottom:16px;margin-right:6px;margin-top:2px;font-size:20px;padding:3px;width:90%} input.button-primary, button.button-primary, a.button-primary {color:#fff;}--></style>';

		elseif($wp_version > 2.4)

			echo '<style type="text/css"><!--#login form input{font-size:20px;padding:3px;width:90%}--></style>';

	}



	function wp_login_message($message){

		$message .= '<div id="login_error">Please read the <a href="http://embroideryadvertisers.com/subscriber-tos/">Terms of Service</a></div>';

		return $message;

	}



	function custom_option($options=''){

		if(!empty($options) && is_array($options)){

			$newoptions = array();

			foreach($options as $key => $value){

				if('user_pass' === $key && intval($value) === 1)

					$newoptions['user_pass'] = 1;

				elseif('verification' === $key && intval($value) === 1)

					$newoptions['verification'] = 1;

				elseif(intval($value) === 1)

					$newoptions['required'][] = $key;

				elseif(intval($value) === 2)

					$newoptions['optional'][] = $key;

			}

			return $newoptions;

		}

	}



	function wp_register_form(){

		$options = array();

		$options = $this->custom_option($this->options);

		$tabs = 60;

		if(!empty($options)){

			if(isset($options['user_pass'])){

?>

<p>

	<label><?php _e('Password(at least 6 characters):','wp-user-registration'); ?><br />

	<input type="password" name="user_pw1" id="user_pw1" class="input" value="" size="20" tabindex="30" /></label>

</p>

<p>

	<label><?php _e('Confirm Password:','wp-user-registration'); ?><br />

	<input type="password" name="user_pw2" id="user_pw2" class="input" value="" size="20" tabindex="40" /></label>

</p>

<?php

			}

			if(isset($options['verification'])){

?>

<p>

	<label><?php echo $this->data['verification']; ?>:<br />

	<input style="width:60%;" type="text" name="verification" id="verification" class="input" value="" size="20" tabindex="50" /><img src="<?php echo $_SERVER['PHP_SELF']; ?>?imagegd=<?php echo time(); ?>" style="margin-left:5px;border:none;" alt="Verification" /></label>

</p>

<?php

			}

			if(isset($options['required'])){

				unset($value);

				foreach($options['required'] as $value){

?>

<p>

	<label><?php echo $this->data[$value]; ?>:<br />

	<input type="text" name="<?php echo $value; ?>" id="<?php echo $value; ?>" class="input" value="" size="20" tabindex="<?php echo $tabs; ?>" /></label>

</p>

<?php

					$tabs += 10;

				}

			}

			if(isset($options['optional'])){

				unset($value);

				foreach($options['optional'] as $value){

?>

<p>

	<label><?php echo $this->data[$value]; ?>(Optional):<br />

	<input type="text" name="<?php echo $value; ?>" id="<?php echo $value; ?>" class="input" value="" size="20" tabindex="<?php echo $tabs; ?>" /></label>

</p>

<?php

					$tabs += 10;

				}

			}

		}

	}



	function wp_registration_errors($errors){

		if(sanitize_user( $_POST['user_login'] ) != sanitize_user($_POST['user_login'], true)){

			$errors = $this->errors($errors, 'user_name', __('<strong>ERROR</strong>: username is invalid.','wp-user-registration'));

		}



		$options = array();

		$options = $this->custom_option($this->options);

		$tabs = 60;

		if(!empty($options)){

			if(isset($options['verification'])){

				if(empty($_POST['verification']) || $_POST['verification'] == ''){

					$errors = $this->errors($errors, 'verification', __('<strong>ERROR</strong>: Please enter Word Verification.','wp-user-registration'));

				}elseif(strtolower($_POST['verification']) !== strtolower($_SESSION['image_gd'])){

					$errors = $this->errors($errors, 'verification', __('<strong>ERROR</strong>: Word Verification is invalid.','wp-user-registration'));

				}

				$_SESSION['image_gd'] = '';

				unset($_SESSION['image_gd']);

			}

			if(isset($options['user_pass'])){

				if(strlen($_POST['user_pw1'])<6){

					$errors = $this->errors($errors, 'user_pw', __('<strong>ERROR</strong>: Password require at least 6 characters.','wp-user-registration'));

				}elseif(empty($_POST['user_pw2']) || $_POST['user_pw1'] == ''){

					$errors = $this->errors($errors, 'user_pw', __('<strong>ERROR</strong>: Please enter password.','wp-user-registration'));

				}elseif($_POST['user_pw1'] !== $_POST['user_pw2']){

					$errors = $this->errors($errors, 'user_pw', __('<strong>ERROR</strong>: Please type the same password in the two password fields.','wp-user-registration'));

				}else{

					$this->user_pw = $_POST['user_pw1'];

				}

			}

			if(isset($options['required'])){

				unset($value);

				foreach($options['required'] as $value){

					if($_POST[$value] == ''){

						$errors = $this->errors($errors, 'empty_'.$value , sprintf(__('<strong>ERROR</strong>: Please type %s','wp-user-registration'), $this->data[$value]));

					}else{

						if(isset($this->filters[$value]) && !empty($this->filters[$value]))

							$this->userdata[$value] = apply_filters($this->filters[$value], $_POST[$value]);

						else

							$this->userdata[$value] = $_POST[$value];

					}

				}

			}

			if(isset($options['optional'])){

				unset($value);

				foreach($options['optional'] as $value){

					if($_POST[$value] !== ''){

						if(isset($this->filters[$value]) && !empty($this->filters[$value]))

							$this->userdata[$value] = apply_filters($this->filters[$value], $_POST[$value]);

						else

							$this->userdata[$value] = $_POST[$value];

					}

				}

			}

		}

		return $errors;

	}



	function errors($errors, $name, $message){

		global $wp_version;



		if($wp_version < 2.5){

			$errors[$name] = $message;

		}else{

			$errors->add($name,$message);

		}

		return $errors;

	}



	function wp_user_register($userID){

		global $wpdb;



		if(!empty($this->user_pw)){

			$wpdb->query("UPDATE $wpdb->users SET user_pass = md5('$this->user_pw') WHERE ID = $userID");

			wp_new_user_notification($userID, $this->user_pw, 'wp-user-registration');

			add_action('phpmailer_init',array(&$this, 'phpmailer_init'), 99999);

		}

		$this->user_pw ='';

		unset($this->user_pw);



		if(!empty($this->userdata)){

			foreach($this->userdata as $key => $value){

				if(strpos($this->nometa, '|'.$key.'|') === FALSE){

					update_usermeta($userID, $key, $value);

				}else{

					$wpdb->query("UPDATE $wpdb->users SET $key = '$value' WHERE ID = $userID");

				}

			}

		}

	}



	function phpmailer_init($phpmailer){

		remove_action('phpmailer_init',array(&$this, 'phpmailer_init'), 99999);

		$phpmailer->ClearAddresses();

	}



	function optionmenu(){

		add_options_page(__('WP User Registration Option','wp-user-registration'), 'WP User Registration', 5,  __FILE__, array(&$this, 'optionpage'));

	}



	function displayMessage($message='',$status='') {

		if ( $message ) {

?>

			<div id="message" class="<?php echo ($status != '') ? $status :'updated '; ?> fade">

				<p><strong><?php echo $message; ?></strong></p>

			</div>

<?php	

		}

		unset($message,$status);

	}



	function optionpage(){

		if(isset($_POST['updateoptions'])){

			foreach((array) $this->defaultoptions as $key => $oldvalue){

				//if($key === 'dn_hide_note') continue;

				if(isset($_POST[$key]) && ($_POST[$key]==='0' || $_POST[$key]==='1' || $_POST[$key]==='2')){

					$this->options[$key] = intval($_POST[$key]);

				}else{

					$this->options[$key] = $this->defaultoption[$key];

				}

			}

			update_option($this->db_options, $this->options);

			$this->displayMessage(__('Options saved','wp-user-registration'), 'updated');

		}elseif(isset($_POST['reset_options'])){

			$this->displayMessage(__('Plugin confriguration has been reset back to default!','wp-user-registration'), '');

		}else{}

?>



<div class="wrap">

	<style type="text/css">

		div.clearing{border-top:1px solid #2580B2 !important;clear:both;}

	</style>



	<h2>WP User Registration</h2>

	<form method="post" action="">

		<table class="form-table">

			<tbody>

<?php

		foreach($this->data as $key => $value){

			$key = strtolower($key);

			if(isset($this->options[$key])){

				$select = $this->options[$key];

			}

?>

				<tr valign="top">

					<th scope="row">

						<label><?php echo $value; ?></label>

					</th>

					<td>

						<?php echo '<select name="'.$key.'" id="'.$key.'">'; ?>

						<?php echo '<option value="0" ' .((1 !== $select && 2!== $select) ? 'selected="selected"' : '') .' >'.__('Disable', 'wp-user-registration').'</option>'; ?>

						<?php echo '<option value="1" ' .((1 === $select) ? 'selected="selected"' : '') .' >'.__('Required', 'wp-user-registration').'</option>'; ?>

						<?php if(strpos($this->onlyrequired, '|'.$key.'|') === FALSE) echo '<option value="2" ' .((2 === $select) ? 'selected="selected"' : '') .' >'.__('Optional', 'wp-user-registration').'</option>'; ?>

						<?php echo '</select>'; ?>

					</td>

<?php

		}

?>

			</tbody>

		</table>

		<div class="clearing"></div>

		<p class="submit">

			<input type="submit" name="updateoptions" value="<?php _e('Update Options','wp-user-registration'); ?>" />

			<input type="submit" name="reset_options" onclick="return confirm('<?php _e('Do you really want to reset your current configuration?','wp-user-registration'); ?>');" value="<?php _e('Reset Options','wp-user-registration'); ?>" />

		</p>

	</form>

</div>



<?php

	}

}

endif;



$new_wp_user_registration = new wp_user_registration();



function creategd(){

	$imagegd = strtolower(substr(md5(rand()),20,6));

	if($imagegd){

		$_SESSION['image_gd'] = $imagegd;

		if(function_exists('imagecreate')){

			$width = 69;

			$height= 20;

			$im = @imagecreate($width, $height)

				or die ("Cannot initialize new GD image stream!");

			$white = imagecolorallocate($im, 255, 255, 255);

			$black = imagecolorallocate($im, 0, 0, 0);



			for ($i=0;$i<strlen($imagegd);$i++){ 

				imagechar($im,3,$i*$width/6+3,3, $imagegd[$i],$black);

			}

		

			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

			//  HTTP/1.1

			header("Cache-Control: no-store, no-cache, must-revalidate");

			header("Cache-Control: post-check=0, pre-check=0", false);

			//  HTTP/1.0

			header("Pragma: no-cache");

			//  Let it more flexible!

			if(function_exists("imagepng")){

				header("Content-type: image/png");

				imagepng($im);

			}elseif(function_exists("imagegif")){

				header("Content-type: image/gif");

				imagegif($im);

			}elseif(function_exists("imagejpeg")){

				header("Content-type: image/jpeg");

				imagejpeg($im);

			}else{

				die(__("No image support in this PHP server!",'wp-user-registration'));

			}

			imagedestroy($im);

		}else{

			$_SESSION['image_gd'] = 'pw2i2j';

			header("Content-type: image/png");

			header("Content-length: 162");

			echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAEUAAAAUAQMAAADhkWI4AAAABlBMVEX///8AAABVwtN+AAAAV0lEQVR4nGNgIAswNjAwHAAx5BgbbCAsYwaGNCAlz2xgzNyQBhSTbDZgZmxgg7D4GBtkwKwPEowNBkCWPPMHA8aGBCBLglHBnv1BHYgFMZwHzmJvINZBAD/6FAmbVIf5AAAAAElFTkSuQmCC');

		}

	}

}



if(isset($_GET['imagegd']) && preg_match('/^[0-9]+$/', $_GET['imagegd'])){

	creategd();

	die();exit;

}



if(!function_exists('wp_new_user_notification')){

	function wp_new_user_notification($user_id, $plaintext_pass = '',$flag=''){



		if(func_num_args() > 1 && $flag !== 'wp-user-registration')

			return;



		$user = new WP_User($user_id);



		$user_login = stripslashes($user->user_login);

		$user_email = stripslashes($user->user_email);



		$message  = sprintf(__('New user registration on your blog %s:','wp-user-registration'), get_option('blogname')) . "\r\n\r\n";

		$message .= sprintf(__('Username: %s','wp-user-registration'), $user_login) . "\r\n\r\n";

		$message .= sprintf(__('E-mail: %s','wp-user-registration'), $user_email) . "\r\n";



		$header = 'From: "'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])).">\nContent-Type: text/plain; charset=" . get_option('blog_charset') . "\n";



		wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration','wp-user-registration'), get_option('blogname')), $message, $header);



		if(empty($plaintext_pass)){

			unset($message,$header);

			return;

		}



		$message  = sprintf(__('Username: %s','wp-user-registration'), $user_login) . "\r\n";

		$message .= sprintf(__('Password: %s','wp-user-registration'), $plaintext_pass) . "\r\n";

		$message .= get_option('siteurl') . "/wp-login.php\r\n";



		@wp_mail($user_email, sprintf(__('[%s] Your username and password','wp-user-registration'), get_option('blogname')), $message, $header);



		unset($message,$header);

		return;

	}

}



?>