<?php

/*
  Plugin Name: LoginByCall
  Plugin URI: http://wordpress.org/plugins/loginbycall/
  Description: LoginByCall
  Version: 3.02
  Author: 2246636
  Author URI: https://loginbycall.com
 */
 
require_once dirname(__FILE__) . '/function.php';

add_action('admin_menu', 'add_loginbycall_page');

function add_loginbycall_page() {
	load_plugin_textdomain('loginbycall', 'wp-content/plugins/loginbycall/i18n');
	//add_options_page('loginbycall', 'loginbycall', 'edit_pages', 'loginbycall', 'loginbycall_options_page');
	add_menu_page( 'Login by call', 'Login by call', 'manage_options', 'loginbycall', 'loginbycall_options_page', 'dashicons-admin-generic', 1001); 
}

//создание глобальных перемееных для хранения настроек loginbycall
function loginbycall_options_page() {
	add_option('loginbycall_redirect_uri', (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/loginbycall-redirect-uri");
	add_option('loginbycall_client_id', '');
	add_option('loginbycall_client_secret', '');
	add_option('loginbycall_grant_type', 'authorization_code');
	add_option('loginbycall_mail', '0');
	add_option('loginbycall_pass', '12');
	add_option('loginbycall_resolution', '0');
	loginbycall_change_options();
}

//реадктированние настроек loginbycall
function loginbycall_change_options() {
	if (isset($_POST['loginbycall_base_setup_btn'])) {
		update_option('loginbycall_redirect_uri', ($_SERVER['HTTPS'] ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . "/loginbycall-redirect-uri");
		update_option('loginbycall_client_id', $_POST['client_id']);
		update_option('loginbycall_client_secret', $_POST['client_secret']);
		update_option('loginbycall_grant_type', 'authorization_code');
		if (preg_match("|^[\d]+$|", $_POST['pass'])) {
			if ($_POST['pass'] < 20) {
				update_option('loginbycall_pass', $_POST['pass']);
			} else {
				echo '<span class="loginbycall-error" style="color:red;">' . __('Input error, too long number, enter a number not more than 20', 'loginbycall') . '.</span>';
			}
		} else {
			echo '<span class="loginbycall-error" style="color:red;">' . __('Input error, enter the number', 'loginbycall') . '.</span>';
		}
		update_option('loginbycall_mail', isset($_POST['mail']));
		update_option('loginbycall_resolution', isset($_POST['resolution']));
	}
	if (isset($_POST['loginbycall_delete_info'])) {
		require_once dirname(__FILE__) . '/template/delete-info.tpl.php';
		echo '<link href="' . plugins_url('css/loginbycall.css', __FILE__) . '" rel="stylesheet" type="text/css" />';
		wp_enqueue_script('resize', plugins_url('loginbycall.js', __FILE__), array('jquery'));
	}
	if (isset($_POST['yes-delete-loginbycall'])) {
		global $wpdb; //required global declaration of WP variable
		$table_name = $wpdb->prefix . 'loginbycall_status';
		$sql = "DELETE FROM  " . $table_name;
		$wpdb->query($sql);
		$table_name = $wpdb->prefix . 'loginbycall_user';
		$sql = "DELETE FROM " . $table_name;
		$wpdb->query($sql);
	}
	//рендер формы настроек LoginByCall 
	echo '<h2>' . __('LoginByCall Settings', 'loginbycall') . '</h2>';
	echo '<form  method="post" action="' . $_SERVER['PHP_SELF'] . '?page=loginbycall&updated=true">';
	echo '<table>
			<tr>
				<td>' . __('Adress callback', 'loginbycall') . '</td>
				<td><input name="redirect_uri" type="text" style="width: 300px; background: gainsboro;"  disabled="disabled"  value="' . get_option('loginbycall_redirect_uri') . '"/></td>
				<td>' . __('Enter the address of your application callback', 'loginbycall') . '</td>
			</tr>
			<tr>
				<td>' . __('ID application', 'loginbycall') . '</td>
				<td><input name="client_id" type="text" style="width: 300px;" value="' . get_option('loginbycall_client_id') . '"/></td>
				<td>' . __('Enter your application ID', 'loginbycall') . '</td>
			</tr>
			<tr>
				<td>' . __('Secret key', 'loginbycall') . '</td>
				<td><input name="client_secret" type="text" style="width: 300px;" value="' . get_option('loginbycall_client_secret') . '"/></td>
				<td>' . __('Enter your secret key', 'loginbycall') . '</td>
			</tr>
			<tr>
				<td>' . __('authorization_code', 'loginbycall') . '</td>
				<td><input name="grant_type" type="text" disabled="disabled"  style="width: 300px; background: gainsboro;"  value="' . get_option('loginbycall_grant_type') . '"/></td>
				<td>' . __('Enter working gtant type. Default: authorization_code', 'loginbycall') . '</td>
			</tr>
			<tr>
				<td>' . __('Password length', 'loginbycall') . '</td>
				<td><input name="pass" type="text" style="width: 300px;" value="' . get_option('loginbycall_pass') . '"/></td>
				<td>' . __('Enter password length for new users', 'loginbycall') . '</td>
			</tr>
			<tr>
				<td>' . __('Resolution', 'loginbycall') . '</td>
				<td><input type="checkbox" id="edit-resolution" name="resolution" value="1" ' . (get_option('loginbycall_resolution') ? 'checked="checked"' : '') . ' class="form-checkbox"></td>
				<td>' . __('Please select if you want to allow users to control the input and destroy account of service LoginByCall', 'loginbycall') . '</td>
			</tr>
			<tr>
				<td><input type="submit" name="loginbycall_base_setup_btn" /></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
			<td><br/></td>
				<td></td>
				<td></td>
			</tr>
						<tr>
				<td><input type="submit" name="loginbycall_delete_info" value="' . __('Clear', 'loginbycall') . '" style="
    background: #B22222;
    color: white;
    border: none;
    height: 30PX;
    width: 100px;
" /></td>
				<td>' . __('Clear the table plugin', 'loginbycall') . '</td>
				<td></td>
			</tr>
		</table>';
	echo '</form>';
}

//инсталяция плагина - создание тапблици и с страниц для работы loginbycall
function loginbycall_install() {
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	global $wpdb;
	$table_name = $wpdb->prefix . "loginbycall_user";
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
  id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  uid int(11) DEFAULT NULL COMMENT 'UID drupal user',
  login varchar(100) DEFAULT NULL COMMENT 'loginbycall user login',
  target_token varchar(255) DEFAULT NULL COMMENT 'loginbycall target_token',
  refresh_token varchar(255) DEFAULT NULL COMMENT 'loginbycall target_token',
  status int(11) DEFAULT NULL COMMENT 'Bind status',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		dbDelta($sql);
	}
	$table_name = $wpdb->prefix . "loginbycall_status";
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
  uid int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  status int(11) DEFAULT NULL COMMENT 'UID  user',
  PRIMARY KEY (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		dbDelta($sql);
	}
	loginbycall_oauth_user_post();
	loginbycall_redirect_uri_post();
	loginbycall_settings_post();
}

// хук успешной авторизации для создания флага что пользователь успешно авторизовался на сайте
function loginbycall_login() {
	setcookie("loginbycall_form_offer", '1', time() + 3600);
}

//хук срабатываюший при перезагрузки страници  
function loginbycall_run() {
	load_plugin_textdomain('loginbycall', 'wp-content/plugins/loginbycall/i18n');
	
	global $wpdb;
	global $user_ID;
	if (isset($_SESSION['loginbycall_form_object'])) {
		$object = $_SESSION['loginbycall_form_object'];
		if (!email_exists($object->email) && !username_exists($object->nickname)) { // такого юзера ещё нет, надо создать для него новый аккаунт
		
			$pass = password_generator(get_option('pass'));
			wp_create_user($object->nickname, $pass, $object->email);
			$redirect_uri = get_option('loginbycall_redirect_uri');
			$client_id = get_option('loginbycall_client_id');
			$client_secret = get_option('loginbycall_client_secret');
			$refresh_token = $object->refresh_token;
			$headers[] = 'Content-type: text/html; charset=utf-8';
			wp_mail($object->email, 'LoginBycall', __('Thanks for signing up through service LoginBycall', 'loginbycall') . ' <br> ' . __('login', 'loginbycall') . ': ' . $_POST['create_login'] . '<br> ' . __('password', 'loginbycall') . ': ' . $pass, $headers);
			set_login_loginbycall($redirect_uri, $client_id, $client_secret, $object->access_token, $object->nickname);

			unset($_SESSION['loginbycall_form_object']);
			$user = get_user_by('login', $object->nickname);
			wp_set_auth_cookie($user->ID, false);
			$table_name = $wpdb->prefix . "loginbycall_user";
			$wpdb->insert(
					$table_name, array('uid' => $user->ID, 'login' => $user->data->user_login, 'target_token' => $object->target_token, 'refresh_token' => $object->refresh_token, 'status' => 1), array('%d', '%s', '%s', '%s', '%d')
			);
			
			// сохраняем признак, что аккаунт был создан именно через loginbycall
			add_user_meta( $user->ID, 'created_via_loginbycall', 2, true );
			add_user_meta( $user->ID, 'loginbycall_phone_number', $object->phone, true ); 
			
			//флаг для меседжа что аккаунт создан
			if ($_SESSION['redirect_url']) {
				$red_url = $_SESSION['redirect_url'];
				unset($_SESSION['redirect_url']);
				wp_redirect($red_url);
				exit('');
			}
		}
		else { // юзер с таким именем или email уже есть, проверим, был ли он зареген через loginbycall
			$user_login = logincall_find_wp_user($object); // проверит, что телефон юзера используется для logincall

			if ($user_login) { 
				$user = get_user_by('login', $user_login);
				wp_set_auth_cookie($user->ID, false);
				if ($_SESSION['redirect_url']) {
					$red_url = $_SESSION['redirect_url'];
					unset($_SESSION['redirect_url']);
					wp_redirect($red_url);
					exit('');
				}
			}
		}
	}
	
	//если пользователь на старници oauth-user-loginbycall и отправил форму $_POST
	if ($_POST && isset($_SESSION['loginbycall_form_object']) && !is_user_logged_in()) {
		$object = $_SESSION['loginbycall_form_object'];
		//если при авторизации через loginbycall если пользователь с таким email уже есть в базе данных сайта 
		if (isset($_SESSION['exists_email']) && isset($_POST['pass'])) {
			if (email_exists($object->email)) {
				//получение данных пользователя по email
				$user = get_userdata(email_exists($object->email));
				$ok = user_pass_ok($user->data->user_login, $_POST['pass']);
				//если пользователь верно ввел данные то удалить флаг $_SESSION['exists_email'] и обект с даннми по привязке контенера 
				if ($ok) {
					unset($_SESSION['exists_email']);
					unset($_SESSION['loginbycall_form_object']);
					wp_set_auth_cookie($user->ID, false);
					$table_name = $wpdb->prefix . "loginbycall_user";
					$wpdb->insert(
							$table_name, array('uid' => $user->ID, 'login' => $user->data->user_login, 'target_token' => $object->target_token, 'refresh_token' => $object->refresh_token, 'status' => 1), array('%d', '%s', '%s', '%s', '%d')
					);
					//проверяем есть ли loginbycall settings пост если поста несушествует создаем его 
					if (loginbycall_settings_post()) {
						//Установить флаг что производиться привязка текушего аккаунта
						if ($_SESSION['redirect_url']) {
							$red_url = $_SESSION['redirect_url'];
							unset($_SESSION['redirect_url']);
							wp_redirect($red_url);
							exit('');
						}
					}
				}
			}
		}
		//если пользователь выбрал привязку сушествующего аккаунта 
		if (isset($_POST['create']) && $_POST['create'] && isset($_POST['login']) && isset($_POST['pass'])) {
			$ok = user_pass_ok($_POST['login'], $_POST['pass']);
			if ($ok) {
				unset($_SESSION['loginbycall_form_object']);
				$user = get_user_by('login', $_POST['login']);
				wp_set_auth_cookie($user->ID, false);
				$table_name = $wpdb->prefix . "loginbycall_user";
				$wpdb->insert(
						$table_name, array('uid' => $user->ID, 'login' => $user->data->user_login, 'target_token' => $object->target_token, 'refresh_token' => $object->refresh_token, 'status' => 1), array('%d', '%s', '%s', '%s', '%d')
				);
				$redirect_uri = get_option('loginbycall_redirect_uri');
				$client_id = get_option('loginbycall_client_id');
				$client_secret = get_option('loginbycall_client_secret');
				set_login_loginbycall($redirect_uri, $client_id, $client_secret, $object->access_token, $_POST['login']);
				//проверяем есть ли loginbycall settings пост если поста несушествет создаем его 
				if (loginbycall_settings_post()) {
					$_SESSION['loginbycall_bind_account'] = true;
					wp_redirect(home_url() . '/loginbycall-settings');
					exit('');
				}
			}
		}
		//если пользователь выбрал создание нового аккаунта
		if (!$_POST['create'] && $_POST['create_login'] && $_POST['create_email']) {
			$email = $_POST['create_email'];
			if (!username_exists($_POST['create_login']) && !email_exists($email)) {
				$pass = password_generator(get_option('pass'));
				wp_create_user($_POST['create_login'], $pass, $email);
				$redirect_uri = get_option('loginbycall_redirect_uri');
				$client_id = get_option('loginbycall_client_id');
				$client_secret = get_option('loginbycall_client_secret');
				$refresh_token = $object->refresh_token;
				$headers[] = 'Content-type: text/html; charset=utf-8';
				wp_mail($email, 'LoginBycall', __('Thanks for signing up through service LoginBycall', 'loginbycall') . ' <br> ' . __('login', 'loginbycall') . ': ' . $_POST['create_login'] . '<br> ' . __('password', 'loginbycall') . ': ' . $pass, $headers);
				set_login_loginbycall($redirect_uri, $client_id, $client_secret, $_SESSION['loginbycall_form_object']->access_token, $_POST['create_login']);
				$lbc_object = $_SESSION['loginbycall_form_object'];
				unset($_SESSION['loginbycall_form_object']);
				$user = get_user_by('login', $_POST['create_login']);
				wp_set_auth_cookie($user->ID, false);
				$table_name = $wpdb->prefix . "loginbycall_user";
				$wpdb->insert(
						$table_name, array('uid' => $user->ID, 'login' => $user->data->user_login, 'target_token' => $object->target_token, 'refresh_token' => $object->refresh_token, 'status' => 1), array('%d', '%s', '%s', '%s', '%d')
				);
				// сохраняем признак, что аккаунт был создан именно через loginbycall
				add_user_meta( $user->ID, 'created_via_loginbycall', 1, true );
				add_user_meta( $user->ID, 'loginbycall_phone_number', $lbc_object->phone, true ); 
				
				//флаг для меседжа что аккаунт создан
				if ($_SESSION['redirect_url']) {
					$red_url = $_SESSION['redirect_url'];
					unset($_SESSION['redirect_url']);
					wp_redirect($red_url);
					exit('');
				}
			}
		}
	}
	
	global $wpdb;
	
	$table_name = $wpdb->prefix . "users";
	//если пользователь успешно авторизовался показываем окно с предложением привязать акк 
	if (isset($_COOKIE['loginbycall_form_offer']) &&
			!get_one_field('uid', $wpdb->prefix . "loginbycall_status", 'uid', $user_ID) &&
			!get_one_field('id', $wpdb->prefix . "loginbycall_user", 'uid', $user_ID)) {
		require_once dirname(__FILE__) . '/template/form-state.tpl.php';
		echo '<link href="' . plugins_url('css/loginbycall.css', __FILE__) . '" rel="stylesheet" type="text/css" />';
		wp_enqueue_script('resize', plugins_url('loginbycall.js', __FILE__), array('jquery'));
	}

	setcookie("loginbycall_form_offer", '1', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
}




/**
 * Проверяет, есть ли уже созданный юзер с таким номером телефона
 * @param $object результат работы функции loginbycall_oauth_render()
 * @return mixed $login равно false, если юзер не найден, или его нику, если найден
 */
function logincall_find_wp_user($object) {
	$target_phone = $object->phone;
	
	global $wpdb;
	
	$lbc_user = $wpdb->get_results("SELECT user_id FROM wp_usermeta WHERE meta_key = 'loginbycall_phone_number' and meta_value = '$target_phone'", ARRAY_A);
	if (count($lbc_user)) {
		$user_id = $lbc_user[0]['user_id'];
	}
	// double check to ensure that this user created by logincall
	if ($user_id) {
		$user = get_user_by('id', $user_id);
		if ($user) {
			$is_created = get_user_meta($user_id, 'created_via_loginbycall');
			if (count($is_created)) {
				if (($is_created[0] == 1) || ($is_created[0] == 2)) {
					$user_phone = get_user_meta($user_id, 'loginbycall_phone_number');
					if (count($user_phone)) {
						if ($user_phone[0] == $target_phone) {
							return $user->user_login;
						}
					}
				}
			}
		}
	}
	return false;
}


// виджет loginbycall
function loginbycall_widget() {
	if (!is_user_logged_in()) {
		
		echo '
		<script>
		}
</script>';
		echo '<div class="widget"><a id="loginbycall-enter-lnk-default" style="position: relative;display: block;" href="#" 
			onclick="window.open(\'\loginbycall-redirect-uri?prev_url=\'+document.location.href,\'displayWindow\',\'width=600,height=460,left=\'+((screen.width-600)/2)+\',top=\'+((screen.height-460)/2)+\',status=no,toolbar=no,menubar=no\').focus(); return false;" ><img height="24" alt="Logo" src="http://loginbycall.com/assets/logo.jpg" /><span style="font-size: 14px;
	text-decoration: none;
	color: #1E90FF;
	padding-bottom: 11px;
	display: block;
	position: absolute;
	top: 4px;
	left: 30px; ">' . __('Sign in / Sign up', 'loginbycall') . '</span></a></div><br/>
		';
	} else {
		echo '<div class="widget"><a id="loginbycall-enter-lnk-default" style="position: relative; display: block;" href="' . get_settings('home') . '/loginbycall-settings"><img height="24" alt="Logo" src="http://loginbycall.com/assets/logo.jpg" /><span style="font-size: 14px;
	text-decoration: none;
	color: #1E90FF;
	padding-bottom: 11px;
	display: block;
	position: absolute;
	top: 4px;
	left: 30px; ">' . __('Settings', 'loginbycall') . '</span></a></div>';
	}
}

function register_my_widget() {
	register_sidebar_widget('Loginbycall', 'loginbycall_widget');
}

add_action('wp_enqueue_scripts', 'prefix_add_my_stylesheet');

function prefix_add_my_stylesheet() {
	// Respects SSL, Style.css is relative to the current file
	wp_register_style('prefix-style', plugins_url('css/loginbycall.css', __FILE__));
	wp_enqueue_style('prefix-style');
}

/**
 * [loginbycall_settings]
 */
function function_loginbycall_settings_user($atts) {
	
	global $user_ID;
	global $user_email;
	global $wpdb;
	
	$t = ''; // в $t будет код формы настроек 
	
	if (is_user_logged_in()) { // Если ползователь авторизован 
		
		$table_name = $wpdb->prefix . "loginbycall_user";
		
		if (count($_POST)) { // если юзер что-то менял в своих настройках  
		
			if (!isset($_POST['user_allowed'])) { // TODO: check this condition. There is no 'user_allowed' input in the form
				$wpdb->update(
						$table_name, array('status' => 0), array('uid' => $user_ID), array('%d'), array('%d')
				);
			} else {
				$wpdb->update(
						$table_name, array('status' => 1), array('uid' => $user_ID), array('%d'), array('%d')
				);
			}
			
			if (isset($_POST['user_unbind'])) {
				$redirect_uri = get_option('loginbycall_redirect_uri');
				$client_id = get_option('loginbycall_client_id');
				$client_secret = get_option('loginbycall_client_secret');
				$refresh_token = get_one_field('refresh_token', $table_name, 'uid', $user_ID);
				$target_token = get_one_field('target_token', $table_name, 'uid', $user_ID);
				$context_access = stream_context_create(array(
					'http' => array('method' => 'POST',
						'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
						'content' => "redirect_uri=$redirect_uri&client_id=$client_id&client_secret=$client_secret&grant_type=refresh_token&refresh_token=$refresh_token"
						)));
				$answer = json_decode(@file_get_contents("https://loginbycall.com/oauth/token", false, $context_access));

				$context_access = stream_context_create(array(
					'http' => array('method' => 'POST',
						'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
						'content' => "access_token=$answer->access_token&target_token=$target_token"
						)));
				$answer = json_decode(@file_get_contents("https://loginbycall.com/api/oauth/v2/userinfo/destroy", false, $context_access));
				$wpdb->delete($table_name, array('uid' => $user_ID));
			}
		}
		// если юзер в таблице loginbycall 
		if (get_one_field('uid', $table_name, 'uid', $user_ID)) {
		
			$i_user = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE uid = " . $user_ID); // находим ID юзера в таблице
			$i_user = $i_user[0];
			
			if (isset($_SESSION['loginbycall_create_account'])) {
				unset($_SESSION['loginbycall_create_account']);
				$t.= '<div class="loginbycall-message">' . __('You account has been successfully created.', 'loginbycall') . '</div>';
			}
			if (isset($_SESSION['loginbycall_bind_account'])) {
				unset($_SESSION['loginbycall_bind_account']);
				$t.= '<div class="loginbycall-message">' . __('Connected successfully', 'loginbycall') . '</div>';
			}
			$t.= '<form action="" method="post" id="loginbycall-user-edit-form" accept-charset="UTF-8">
					<div>
						<input type="hidden" name="user_uid" value="1">
						<div class="form-item form-type-textfield form-item-set-name">
							<label for="edit-set-name">' . __('User name', 'loginbycall') . '&nbsp;&nbsp;&nbsp;-</label> 
							<label type="text" id="edit-set-name" name="set_name" size="60" maxlength="128" class="form-text required">' . $i_user->login . '</label>
						</div>
						<div class="form-item form-type-textfield form-item-set-name">
							<label for="edit-set-name">' . __('User mail', 'loginbycall') . '&nbsp;&nbsp;&nbsp;-</label>
							<label type="text" id="edit-set-name" name="set_name" size="60" maxlength="128" class="form-text required">' . $user_email . '</label>
						</div>
						';
			if (get_option('loginbycall_resolution')) { // Option "allow users to control the input and destroy account of service LoginByCall" is on
				$t.='<div class="form-item form-type-checkbox form-item-user-unbind">
						<input type="checkbox" id="edit-user-unbind" name="user_unbind" value="1" class="form-checkbox">
						<label class="option" for="edit-user-unbind">' . __('Unbind', 'loginbycall') . '</label>
						<div class="description">' . __('Unbind account loginbycall', 'loginbycall') . '</div>
					</div>';
			}
			$t.='<input type="hidden" name="form_id" value="loginbycall_user_edit_form">
				<input type = "submit" id = "edit-submit" name = "loginbycall-user-edit-form" value = "Submit" class = "form-submit">
					</div>
				</form>';
			$t.='</br><a href="https://loginbycall.com/users/home#/dashboard/settings" target="_blank">' . __('My settings LoginByCall', 'loginbycall') . '</a>';
		} else { //если пользователя нет в таблице то показать ему ссобшение с ссылкой на создание связи
			
			$ud = get_userdata($user_ID);
		
			echo '
		<script>
		function openWindow() {
if (window.myWin !== undefined) { window.myWin.focus(); }
		var leftvar = (screen.width-600)/2;
		var topvar = (screen.height-460)/2;
		myWin = window.open("' . loginbycall_create_link($ud->user_login) . '", "displayWindow", "width=600,height=460,left="+leftvar+",top="+topvar+",status=no,toolbar=no,menubar=no");
		}
</script>';
			
			$t = __('This site provides users through the service entrance LoginByCall. At this point, your account is not tied to the service. To add a convenient entrance to the site through the service LoginByCall follow the link below:', 'loginbycall')
					. '<br/><a href="#" onclick="openWindow(); return false;" >' . __('Bind your account', 'loginbycall') . '</a>';
		}
		return $t;
	} else {
		return __('You are not allowed to access this page.', 'loginbycall');
	}
}

add_shortcode('loginbycall_settings', 'function_loginbycall_settings_user');

//[oauth_user_loginbycall]
function function_oauth_user_loginbycall() {
	$form = '';
	if ((isset($_POST['create']) && $_POST['create'] == 1) && isset($_POST['login']) && isset($_POST['pass'])) {
		$ok = user_pass_ok($_POST['login'], $_POST['pass']);
		if (!$ok) {
			$form .= '<span class="loginbycall-error" style="color:red;">' . __('Incorrect login or password', 'loginbycall') . '.</span>';
		}
	}

	if (isset($_SESSION['loginbycall_form_object']) && !is_user_logged_in()) {
		
		$exist_user = false;
		
		$user_nickname = '';
		$user_email = '';
			
		$user_nickname = $_SESSION['loginbycall_form_object']->nickname;
		$user_email = $_SESSION['loginbycall_form_object']->email;
		
		$is_exist_username = username_exists($user_nickname) ? true : false;
		$is_exist_user = email_exists($user_email) ? true : false;
			
		$is_create_user = (isset($_POST['create']) && $_POST['create'] == 0) ? true : false;
		$is_username_already_taken = (isset($_POST['create_login']) && username_exists($_POST['create_login'])) ? true : false;
			
		$form .= '<form action="/oauth-user-loginbycall" method="post" id="loginbycall-user-form" accept-charset="UTF-8">	<div>';
		if (!$exist_user) {
			$form .= '
				<div class="form-item form-type-radios form-item-create">
					<label for="edit-create">' . __('Build account', 'loginbycall') . '</label>
					<div id="edit-create" class="form-radios">
						<div class="form-item form-type-radio form-item-create"> 
							<input type="radio" id="edit-create-0" name="create" value="0" checked="checked" class="form-radio">
							<label class="option" for="edit-create-0">' . __('Create new user account', 'loginbycall') . '</label>
						</div>
						<div class="form-item form-type-radio form-item-create">
							<input type="radio" id="edit-create-1" name="create" value="1" class="form-radio">  <label class="option" for="edit-create-1">' . __('Bind have account', 'loginbycall') . ' </label>
						</div>
					</div>
				</div>
				<div class="form-item form-type-textfield form-item-create-login" style="display: block;">';
			if (($is_create_user && $is_username_already_taken) || $is_exist_username) {
				$form .= '<span  class="loginbycall-error" style="color:red;">' . __('This username is already registered', 'loginbycall') . '.</span></br>';
			}
			$form .= '<label for="edit-create-login" class="input-text-field" >' . __('Enter new login', 'loginbycall') . '</label>
					<input type="text" id="edit-create-login" name="create_login" value="' . $user_nickname . '" size="60" maxlength="128" class="form-text">
					<div class="description">' . __('Enter the your user name', 'loginbycall') . '</div>
				</div>
				<div class="form-item form-type-textfield form-item-create-email" style="display: block;">';
			
			if ($is_exist_user) {
				$form .= '<span  class="loginbycall-error" style="color:red;">' . __('This email is already registered', 'loginbycall') . '.</span>';
			}
			
			$form .= '<label for="edit-create-email"  class="input-text-field" >' . __('Enter email', 'loginbycall') . '</label>
					<input type="text" id="edit-create-email" name="create_email" size="60" maxlength="128" value="' . $user_email . '" class="form-text">
					<div class="description">' . __('Enter the your email', 'loginbycall') . '</div>
				</div>
				<div class = "form-item form-type-textfield form-item-login" style = "display: none;">';

			$form .= '<label for = "edit-login" class="input-text-field" >' . __('Enter Login', 'loginbycall') . '</label>
					<input type = "text" id = "edit-login" name = "login" value="' . $user_nickname . '" size = "60" maxlength = "128" class = "form-text">
					<div class = "description">' . __('Enter the your user name', 'loginbycall') . '</div>
				</div>
				<div class = "form-item form-type-password form-item-pass" style = "display: none;">
					<label for = "edit-pass" class="input-text-field" >' . __('Password', 'loginbycall') . '</label>
					<input type = "password" id = "edit-pass" name = "pass" size = "60" maxlength = "128" class = "form-text">
					<div class = "description">' . __('Enter your password', 'loginbycall') . '</div>
				</div>';
		} else {
			$_SESSION['exists_email'] = true; //флаг - пользователь с таким email есть в базе данных 
			$form .= '<div class="form-item form-type-textfield form-item-create-email" style="display: block;">
					<label for="edit-create-email"  class="input-text-field" >' . __('Enter email', 'loginbycall') . '</label>
					<input type="text" id="edit-create-email" name="exists_email" disabled="disabled" value="' . $user_email . '" size="60" maxlength="128" class="form-text">
					<div class="description">' . __('Enter the your email', 'loginbycall') . '</div>
				</div>
				<div class = "form-item form-type-password form-item-pass" style = "display: block;">
					<label for = "edit-pass">' . __('Password', 'loginbycall') . '</label>
					<input type = "password" id = "edit-pass" name = "pass" size = "60" maxlength = "128" class = "form-text">
					<div class = "description">' . __('Enter your password', 'loginbycall') . '</div>
				</div>';
		}
		
		$form .= '<input type = "submit" id = "loginbycall-edit-submit" name = "op" value = "Submit" class = "form-submit"><input type = "hidden" name = "form_build_id" value = "form_oauth_user">
				<input type = "hidden" name = "form_id" value = "loginbycall_user_form">
			</div>
		</form>';
		echo '<script type = "text/javascript" src = "' . plugins_url('loginbycall.js', __FILE__) . '" >  </script>';
		echo '<link href = "' . plugins_url('css/loginbycall.css', __FILE__) . '" rel = "stylesheet" type = "text/css" />';
	} else {
		$form = __('You are not allowed to access this page.', 'loginbycall');
	}
	return $form;
}

add_shortcode('oauth_user_loginbycall', 'function_oauth_user_loginbycall');

add_filter('page_template', 'loginbycall_redirect_uri_template');

function loginbycall_redirect_uri_template($page_template) {
	
	if (is_page('loginbycall-redirect-uri')) {
		$page_template = dirname(__FILE__) . '/template/page-loginbycall-redirect-uri.php';
	}
	
	return $page_template;
}

function cp_admin_init() {
	if (!session_id()) {
		session_start();
	}
}

function loginbycall_banned_pages($exclude_array) {
	global $wpdb;
	$table_name = $wpdb->prefix . "posts";
	$posts = $wpdb->get_results("SELECT id FROM " . $table_name . " WHERE post_name IN ('oauth-user-loginbycall', 'loginbycall-redirect-uri','loginbycall-settings')");
	foreach ($posts as $post) {
		$exclude_array = array_merge($exclude_array, array($post->id));
	}
	return $exclude_array; //array_merge($exclude_array, array(4, 17));
}

function my_loginbycall() {
	add_filter('wp_list_pages_excludes', 'loginbycall_banned_pages');
}

function loginbycall_form_panel() {
	echo '
		<script>
				if (!window.jQuery) { 
		var script   = document.createElement("script");
script.type  = "text/javascript";
script.src   = "http://code.jquery.com/jquery-2.0.2.min.js"; 
document.body.appendChild(script);
		}
		function openWindow() {
if (window.myWin !== undefined) { window.myWin.focus(); }
		var leftvar = (screen.width-600)/2;
		var topvar = (screen.height-460)/2;
		myWin = window.open("' . loginbycall_create_link() . '", "displayWindow", "width=600,height=460,left="+leftvar+",top="+topvar+",status=no,toolbar=no,menubar=no");
		}
</script>
		<a id="loginbycall-enter-lnk-default" style="position: relative;display: block;" href="#" onclick="openWindow(); return false;" ><img height="24" alt="Logo" src="http://loginbycall.com/assets/logo.jpg" /><span style="font-size: 14px;
	text-decoration: none;
	color: #1E90FF;
	padding-bottom: 11px;
	display: block;
	position: absolute;
	top: 4px;
	left: 30px; ">' . __('Sign in / Sign up', 'loginbycall') . '</span></a><br/>';
}

function loginbycall_uninstall_hook() {
	delete_option('loginbycall_redirect_uri');
	delete_option('loginbycall_client_id');
	delete_option('loginbycall_client_secret');
	delete_option('loginbycall_grant_type');
	delete_option('loginbycall_mail');
	delete_option('loginbycall_pass');
	delete_option('loginbycall_resolution');
//	global $wpdb; //required global declaration of WP variable
//	$table_name = $wpdb->prefix . 'loginbycall_status';
//	$sql = "DROP TABLE " . $table_name;
//	$wpdb->query($sql);
//	$table_name = $wpdb->prefix . 'loginbycall_user';
//	$sql = "DROP TABLE " . $table_name;
	//$wpdb->query($sql);
	$id = wp_exist_post_by_name('loginbycall-settings');
	if ($id) {
		wp_delete_post($id, true);
	}
	$id = wp_exist_post_by_name('loginbycall-redirect-uri');
	if ($id) {
		wp_delete_post($id, true);
	}
	$id = wp_exist_post_by_name('oauth-user-loginbycall');
	if ($id) {
		wp_delete_post($id, true);
	}
}

register_deactivation_hook(__FILE__, 'loginbycall_uninstall_hook');

add_action('login_form', 'loginbycall_form_panel');
add_action('register_form', 'loginbycall_form_panel');

add_action('init', 'my_loginbycall');

add_action('init', 'cp_admin_init');
add_action('wp_login', 'loginbycall_login');
add_action('init', 'loginbycall_run');
add_action('init', 'register_my_widget');
register_activation_hook(__FILE__, 'loginbycall_install');
?>
