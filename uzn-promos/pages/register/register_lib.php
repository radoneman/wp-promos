<?php
/**
 * Add a new user
 *
 * @param array $user array(
 *      username,
 *      email,
 *      first_name,
 *      last_name,
 *      subscribe
 * )
 * @return array (int user_id, array errors)
 */
function uzn_promos_register_new_user($user)
{
	global $wpdb;

	$return = array(
		'user_id' => 0,
		'errors' => array()
	);

	$error = array();

	if (empty($user['username'])) {
		$errors[] = 'Please enter a username.';

	} else if (!validate_username($user['username'])) {
		$errors[] = 'This username is invalid.  Please enter a valid username.';

	} else if (username_exists( $user['username'])) {
		$errors[] = 'This username is already registered, please choose another one.';
	}

	if (empty($user['email'])) {
		$errors[] = 'Please type your e-mail address.';

	} else if (!is_email($user['email'])) {
		$errors[] = 'The email address isn&#8217;t correct.';

	} else if (email_exists($user['email'])) {
		$errors[] = 'This email is already registered, please choose another one.';
	}

	if (!empty($errors)) {
		$return['errors'] = $errors;
		return $return;
	}

	$user['password'] = wp_generate_password(12, false);

	$user_id = wp_create_user($user['username'], $user['password'], $user['email']);

	if (empty($user_id)) {
		$errors[] = 'Could not save user.';
		$return['errors'] = $errors;
		return $return;
	}

	$user_activation_code = md5($user['username'] . $user['email']);

	$user_address_info = array(
		"user_add1" => '',
		"user_add2" => '',
		"user_city" => '',
		"user_state" => '',
		"user_country" => '',
		"user_postalcode" => '',
		"user_phone" =>	'',
		"user_twitter" =>	'',
		"first_name" =>	$user['first_name'],
		"last_name" =>	$user['last_name'],
		"user_subscribe" => $user['subscribe'],
		"user_active" => $user_activation_code,
	);
	foreach($user_address_info as $key => $val) {
		update_user_meta($user_id, $key, $val);
	}

	$user['name'] = $user['first_name'] . ' ' . $user['last_name'];
	$user['nicename'] = get_user_nice_name($user['name'],'');

	$users_sql = array(
		'user_url' => '',
		'user_nicename' => $user['nicename'],
		'display_name' => $user['name']
	);
	$wpdb->update($wpdb->users, $users_sql, "ID = '" . $user_id . "'");

	// registration email
	/*
	$fromEmail = get_site_emailId();
	$fromEmailName = get_site_emailName();

	$message =
		'<p><strong>Please activate your account</strong> by clicking ' .
		'<a href="' . home_url() . '/?ptype=login&action=activate&user_id=' . $user_id . '&activate='.$user_activation_code.'">here</a>.</p>';
	$message .= '<p>&nbsp;</p>';
	$message .= '<p>You can then log in with the following information:</p>';
	$message .= '<p><strong>Your login Information :</strong></p>';
	$message .= '<p>Username: '. $user['username'] . '</p>';
	$message .= '<p>Password: '. $user['password'] . '</p>';

	try {
		@sendEmail($fromEmail, $fromEmailName, $user['email'], $user['name'], '', $message, $extra='', 'registration', $post_id='','');
	} catch (Exception $e) {
	}
	*/

	$return['user_id'] = $user_id;

	return $return;
}

/**
 * Add user to promo
 *
 * @param array $params array(promo_id, user_id, email, first_name, last_name)
 */
function uzn_promos_add_user_to_promo($params)
{
	global $wpdb;

	// promo entries, one per email
	$check_entry = $wpdb->get_row($wpdb->prepare(
		"SELECT count(*) as total from {$wpdb->prefix}uzn_promos_entries " .
		"WHERE promos_id = %d and email = %s",
		$params['promo_id'],
		$params['email']
	));
	if ($check_entry->total == 0) {
		$sql_data = array(
			'promos_id' => $params['promo_id'],
			'user_id' => $params['user_id'],
			'first_name' => $params['first_name'],
			'last_name' => $params['last_name'],
			'email' => $params['email'],
			'date_added' => date('Y-m-d H:i:s')
		);
		$wpdb->insert("{$wpdb->prefix}uzn_promos_entries" , $sql_data);

		$count_entries = $wpdb->get_row($wpdb->prepare(
			"SELECT count(*) as total from {$wpdb->prefix}uzn_promos_entries WHERE promos_id = %d",
			$params['promo_id']
		));

		$wpdb->query($wpdb->prepare(
			"UPDATE {$wpdb->prefix}uzn_promos SET count_entries = %d WHERE id = %d",
			$count_entries->total,
			$params['promo_id']
		));
	}
}

/**
 * Register via facebook
 * Called by ajax after FB login
 */
function fb_intialize_uzn_promos()
{
}
// add_action - see plugin main file

/**
 * Util functions
 */

/**
 *
 * @param mixed $data
 */
function uzn_promos_output_json($data)
{
	die(json_encode($data));
}


if (!function_exists('get_user_nice_name')) {
function get_user_nice_name($fname,$lname='')
{
	global $wpdb;
	if($lname)
	{
		$uname = $fname.'-'.$lname;
	}else
	{
		$uname = $fname;
	}
	$nicename = strtolower(str_replace(array("'",'"',"?",".","!","@","#","$","%","^","&","*","(",")","-","+","+"," "),array('','','','-','','-','-','','','','','','','','','','-','-',''),$uname));
	$nicenamecount = $wpdb->get_var("select count(user_nicename) from $wpdb->users where user_nicename like \"$nicename\"");
	if($nicenamecount=='0')
	{
		return trim($nicename);
	}else
	{
		$lastuid = $wpdb->get_var("select max(ID) from $wpdb->users");
		return $nicename.'-'.$lastuid;
	}
}}

if (!function_exists('get_site_emailId')) {
function get_site_emailId()
{
	if(get_option('site_email'))
	{
		return get_option('site_email');
	}else
	{
		return get_option('admin_email');
	}
}}

if (!function_exists('get_site_emailName')) {
function get_site_emailName()
{
	if(get_option('site_email_name'))
	{
		return stripslashes(get_option('site_email_name'));
	}else
	{
		return stripslashes(get_option('blogname'));
	}
}}