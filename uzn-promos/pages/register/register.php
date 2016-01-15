<?php
/**
 * Front end enter promo page
 */
function uzn_promo_register()
{
	global $wpdb;

	if (!isset($_GET['promo'])) {
		return;
	}

	$promo_permalink = $_GET['promo'];

	$promo = $wpdb->get_row($wpdb->prepare(
		"SELECT * FROM {$wpdb->prefix}uzn_promos WHERE status = 1 and permalink = '%s'", $promo_permalink
	));

	// error if promo not found
	if (empty($promo)) {
		include_once('views/template_header.php');
		include_once('views/register_404.php');
		include_once('views/template_footer.php');
		exit;
	}

	$action = '';
	$actions_allowed = array('success');
	if (isset($_GET['action']) && in_array($_GET['action'], $actions_allowed)) {
		$action = $_GET['action'];
	}

	$uzn_promos_url_register = home_url() . '/?promo=' . $promo_permalink . '';

	// register_success
	if ($action == 'success') {
		include_once('views/template_header.php');
		include_once('views/register_success.php');
		include_once('views/template_footer.php');

	// register_form
	} else {

		$errors = array();

		$form = array(
			'name' => '',
			'email' => ''
		);

		if ($_POST) {

			$form = shortcode_atts($form, $_REQUEST);

			$name = array(
				'first_name' => '',
				'last_name' => ''
			);
			if (empty($form['name'])) {
				$errors[] = __('Please enter your name', 'uzn_promos');
			} else if (strlen($form['name']) > 120) {
				$errors[] = __('Huh, that\'s a long name', 'uzn_promos');
			} else {
				$form['name'] = preg_replace('/\s+/', ' ', $form['name']);
				$name_array = explode(' ', $form['name'], 2);
				if (isset($name_array[0])) {
					$name['first_name'] = trim($name_array[0]);
				}
				if (isset($name_array[1])) {
					$name['last_name'] = trim($name_array[1]);
				}

				if (empty($name['first_name']) || empty($name['last_name'])) {
					$errors[] = __('Please enter your first name and your last name in Name field.', 'uzn_promos');
				}
			}

			if (empty($form['email'])) {
				$errors[] = __('Please enter your email', 'uzn_promos');
			} else if (!is_email($form['email'])) {
				$errors[] = __('Your email address does not appear to be valid', 'uzn_promos');
			}

			if (empty($errors)) {

				// register on WP
				$register_user = uzn_promos_register_new_user(array(
					'username' => $form['email'],
					'first_name' => $name['first_name'],
					'last_name' => $name['last_name'],
					'email' => $form['email'],
					'subscribe' => 1
				));
				$user_id = $register_user['user_id'];

				// add to promo
				uzn_promos_add_user_to_promo(array(
					'promo_id' => $promo->id,
					'user_id' => $user_id,
 					'first_name' => $name['first_name'],
 					'last_name' => $name['last_name'],
 					'email' => $form['email']
				));

				// redirect
				wp_redirect($uzn_promos_url_register . '&action=success');
			}
		}

		$form_error = '';
		if (!empty($errors)) {
			$form_error = join('<br>', $errors);
		}

		include_once('views/template_header.php');
		include_once('views/register_form.php');
		include_once('views/template_footer.php');
	}

	exit;
}

add_action('init', 'uzn_promo_register');