<?php
/**
 * This file is part of PHP Skeleton App.
 *
 * (c) 2014 Goran Halusa
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Controller for the Authenticate module.
 *
 * @author Goran Halusa <gor@webcraftr.com>
 * @copyright   2014 Goran Halusa
 * @link        https://github.com/ghalusa/PHP-Skeleton-App
 * @license     https://github.com/ghalusa/PHP-Skeleton-App/wiki/License
 * @version     1.0.0
 * @package     PHP Skeleton App
 */
 
function show_login_form() {
	$app = \Slim\Slim::getInstance();
	global $final_global_template_vars;
	require_once $final_global_template_vars["default_module_list"]["user_account"]["absolute_path_to_this_module"] . "/models/user_account.class.php";
	$env = $app->environment();
	$db_conn = new \slimlocal\models\db($final_global_template_vars["db_connection"]);
	$db_resource = $db_conn->get_resource();
	$user_account = new UserAccount( $db_resource, $final_global_template_vars["session_key"] );

	if(empty($env["default_validation_errors"]) && $_SERVER['REQUEST_METHOD'] == "POST") {

		$landing_page = $final_global_template_vars['landing_page'];

		if(isset($_COOKIE[$final_global_template_vars["redirect_cookie_key"]]) && 
			$_COOKIE[$final_global_template_vars["redirect_cookie_key"]] && 
			$_COOKIE[$final_global_template_vars["redirect_cookie_key"]] != "/") {

			$landing_page = $_COOKIE[$final_global_template_vars["redirect_cookie_key"]];
			setcookie($final_global_template_vars["redirect_cookie_key"], "", time()-3600, "/");
			unset($_COOKIE[$final_global_template_vars["redirect_cookie_key"]]);
			
		}

		// Add role list to session.
		$_SESSION[$final_global_template_vars["session_key"]][$final_global_template_vars["current_user_roles_session_key"]] = \slimlocal\models\utility::array_flatten($user_account->get_user_roles_list($_SESSION[$final_global_template_vars["session_key"]]["user_account_id"]));

		// Add group list to session.
		$tmp_array = array();
		$_SESSION[$final_global_template_vars["session_key"]]["associated_groups"] = \slimlocal\models\utility::array_flatten($user_account->get_user_account_groups($_SESSION[$final_global_template_vars["session_key"]]["user_account_id"]),$tmp_array,'group_id');

		// Landing page exception. If coming from the register page, set to "/modules".
		$final_landing_page = ($landing_page == "/user_account/register/") ? "/modules" : $landing_page;
		// Landing page exception. If coming from the home page, set to "/modules".
		$final_landing_page = ($landing_page == "/") ? "/modules" : $landing_page;

		$app->redirect($final_landing_page);
	}

	// If logged in, don't render the login form.
	if(isset($_SESSION[$final_global_template_vars["session_key"]])) {
		$app->redirect("/modules/");
	}

	$app->render('login_form.php',array(
		"page_title" => "Login"
		,"hide_page_header" => true
		,"errors" => !empty($env["default_validation_errors"]) ? $env["default_validation_errors"] : false
	));
}
?>