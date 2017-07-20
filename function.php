<?php

function set_login_loginbycall($redirect_uri, $client_id, $client_secret, $access_token, $login) {

//	$context_access = stream_context_create(array(
//		'http' => array('method' => 'POST',
//			'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
//			'content' => "redirect_uri=$redirect_uri&client_id=$client_id&client_secret=$client_secret&grant_type=refresh_token&refresh_token=$refresh_token"
//			)));
//	$answer = json_decode(@file_get_contents("https://loginbycall.com/oauth/token", false, $context_access));
	$user_info = json_decode(@file_get_contents('https://loginbycall.com/api/oauth/v2/userinfo/get?access_token=' . $access_token));
	if (!$user_info->login) {
		$content_res = http_build_query(array('access_token' => $access_token,
			'login' => $login));
		$context_res = stream_context_create(array(
			'http' => array('method' => 'PUT',
				'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
				'content' => $content_res
				)));


		$res = json_decode(@file_get_contents('https://loginbycall.com/api/oauth/v2/userinfo/update', false, $context_res));
	}
}

/* Функция обмена данными по протоколу */

function loginbycall_oauth_render($redirect_uri, $client_id, $client_secret, $grant_type, $target_token = NULL) {
	if (isset($_GET['code'])) {
		$code = $_GET['code'];
	} else {
		$code = NULL;
	}
	/* Формируем POST запрос к серверу на получение acess_token */
	$context_access = stream_context_create(array(
		'http' => array('method' => 'POST',
			'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
			'content' => "code=$code&redirect_uri=$redirect_uri&client_id=$client_id&client_secret=$client_secret&grant_type=$grant_type"
			)));
	/* POST получение acess_token */
	$answer = json_decode(@file_get_contents("https://loginbycall.com/oauth/token", false, $context_access));
	$refresh_token = $answer->refresh_token;
	/* Обрабатываем ошибку получения access_token */
	if (isset($answer->error)) {
		return (object) array('error' => true, 'error_description' => $answer->error_description);
	}
	/* GET запрос на получение пользовательских данных */
	$access_token = $answer->access_token;
	$answer = json_decode(@file_get_contents('https://loginbycall.com/api/oauth/v2/userinfo/get?access_token=' . $answer->access_token));
	/* Обрабатываем ошибку получения пользовательских данных */
	if (isset($answer->error)) {
		return (object) array('error' => true, 'error_description' => $answer->error_description);
	}
	/* Возвращаем успешный результат */
	if ($answer) {
		if (isset($refresh_token)) {
			return (object) array('error' => false, 'login' => $answer->login, 'target_token' => $answer->target_token, 'nickname' => $answer->nickname, 'email' => $answer->email, 'phone' => $answer->phone, 'access_token' => $access_token, 'refresh_token' => isset($refresh_token) ? $refresh_token : null, 'destroy' => false);
		} else {
			return (object) array('error' => false, 'login' => $answer->login, 'nickname' => $answer->nickname, 'email' => $answer->email, 'phone' => $answer->phone, 'target_token' => $answer->target_token, 'access_token' => $access_token, 'destroy' => false);
		}
	} else {
		return false;
	}
}

/* Функция получения значения единственного поля */

function get_one_field($select, $from, $where, $value) {
	global $wpdb;
	$i_user = $wpdb->get_results("SELECT " . $select . " FROM " . $from . " WHERE " . $where . " = " . $value, ARRAY_A);
	if (count($i_user)) {
		return $i_user[0][$select];
	} else {
		return false;
	}
}

/* функция генерации пароля */

function password_generator($max) {
	if (!$max) {
		$max = 12;
	}
	$password = null;
	$chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
	$size = StrLen($chars) - 1;
	while ($max--)
		$password.= $chars[rand(0, $size)];
	return $password;
}

/* Функция получения ссылки на сервис */

function loginbycall_create_link($login = NULL) {
	($login) ? $new = '&login=' . $login : $new = NULL;
	return 'https://loginbycall.com/oauth/authorize?client_id=' . get_option('loginbycall_client_id') . '&redirect_uri=' . get_option('loginbycall_redirect_uri') . '&response_type=code&scope=nickname+email+phone&display=popup' . $new; //&new_account='.$new_account)
}

function wp_exist_post_by_name($name) {
	global $wpdb;
	$table_name = $wpdb->prefix . "posts";
	$id = $wpdb->get_row("SELECT id FROM " . $table_name . " WHERE post_name = '" . $name . "'", 'ARRAY_A');
	if (count($id)) {
		return $id['id'];
	}
	return false;
}

/*
 * функция проверяет есть Loginbycall settings пост ии нету если нету то создает его если $del установить в true это удалит пост 
 */

function loginbycall_settings_post($del = false) {
	$id = wp_exist_post_by_name('loginbycall-settings');
	if ($id && $del) {
		wp_delete_post($id);
		return false;
	} else if (!$id && !$del) {
		$post = array();
		$post['post_title'] = 'LoginByCall Settings';
		$post['post_name'] = 'loginbycall-settings';
		$post['post_content'] = "[loginbycall_settings]";
		$post['post_status'] = 'publish';
		$post['post_type'] = 'page';
		$post['comment_status'] = 'closed';
		$post['ping_status'] = 'closed';
		$post['post_category'] = array(1); // the default 'Uncatrgorised'
		$the_page_id = wp_insert_post($post);
		return true;
	}
	return true;
}

//функция проверяет есть Loginbycall redirect uri пост ии нету если нету то создает его 
function loginbycall_redirect_uri_post($del = false) {
	$id = wp_exist_post_by_name('loginbycall-redirect-uri');
	if ($id && $del) {
		wp_delete_post($id);
		return false;
	} else if (!$id && !$del) {
		$post = array();
		$post['post_title'] = 'Loginbycall redirect uri';
		$post['post_name'] = 'loginbycall-redirect-uri';
		$post['post_content'] = "[loginbycall_settings]";
		$post['post_status'] = 'publish';
		$post['post_type'] = 'page';
		$post['comment_status'] = 'closed';
		$post['ping_status'] = 'closed';
		$post['post_category'] = array(1); // the default 'Uncatrgorised
		$the_page_id = wp_insert_post($post);
		return true;
	}
	return true;
}

//функция проверяет есть Loginbycall oauth user пост ии нету если нету то создает его 
function loginbycall_oauth_user_post($del = false) {
	$id = wp_exist_post_by_name('oauth-user-loginbycall');
	if ($id && $del) {
		wp_delete_post($id);
		return false;
	} else if (!$id && !$del) {
		$post = array();
		$post['post_title'] = 'Loginbycall oauth user';
		$post['post_name'] = 'oauth-user-loginbycall';
		$post['post_content'] = "[oauth_user_loginbycall]";
		$post['post_status'] = 'publish';
		$post['post_type'] = 'page';
		$post['comment_status'] = 'closed';
		$post['ping_status'] = 'closed';
		$post['post_category'] = array(1); // the default 'Uncatrgorised'
		// Insert the post into the database
		$the_page_id = wp_insert_post($post);
		return true;
	}
	return true;
}

?>