<?php
/**
 * The PHP Skeleton App
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @copyright   2015 Goran Halusa
 * @link        https://github.com/ghalusa/PHP-Skeleton-App
 * @license     https://github.com/ghalusa/PHP-Skeleton-App/wiki/License
 * @version     0.1.1
 * @package     PHP Skeleton App
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Dump
 *
 * For debugging. Outputs data using var_dump(), encapsulated by the <pre> tag, with the option to die() or let it ride.
 * If an IP address is passed, then only that IP address will be able to view the output.
 *
 * @param   mixed   $data         The data value
 * @param   bool    $die          The data value
 * @param   string  $ip_address   The data value
 * @return  mixed   The formatted data
 */

function dump($data = false, $die = true, $ip_address=false){
	if(!$ip_address || $ip_address == $_SERVER["REMOTE_ADDR"]){
		echo '<pre>';
		var_dump($data);
		echo '</pre>';

		if($die) die();
	}
}

/**
 * Check Authenticated
 *
 * If a session key is not set, this function sets a cookie to remember 
 * the $_SERVER["REQUEST_URI"], then redirects to the login URL.
 *
 * @param   array   $route   The route array
 * @return  void
 */
function check_authenticated(\Slim\Route $route){
	$app = \Slim\Slim::getInstance();
	$final_global_template_vars = $app->config('final_global_template_vars');
	if(!isset($_SESSION[$final_global_template_vars["session_key"]])){
		// Set cookie so user can come back to this page.
		setcookie($final_global_template_vars["redirect_cookie_key"],$_SERVER["REQUEST_URI"], time()+3600, "/");
		$app->redirect($final_global_template_vars["login_url"]);
	}
}

/**
 * Force HTTPS
 *
 * Checks to see if the environment is set to production,
 * then redirects to https mode if it's not already running in https mode.
 *
 * @param   array   $route   The route array
 * @return  void
 */
function force_https(\Slim\Route $route){
	$app = \Slim\Slim::getInstance();
	$final_global_template_vars = $app->config('final_global_template_vars');
	if(empty($final_global_template_vars["is_dev"])){
		// Means we are on a production box.
    if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
			// It's already SSL... do nothing.
		} else {
			$redirect= "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$app->redirect($redirect);
		}
	}
}

/**
 * Force SSL
 *
 * Needed to create this function because the "force_https" function only forces 
 * https if the server is not marked as "dev".
 *
 * @param   array   $route   The route array
 * @return  void
 */
function force_ssl(\Slim\Route $route = null){
	if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
		// It's already SSL... do nothing.
	} else {
		$redirect= "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		header("Location: " . $redirect);
		die();
	}
}

/**
 * Enforce CSRF Guard
 *
 * Ability to prevent against CSRF attacks
 *
 * @return void
 */
function enforce_csrf_guard(){
	$app = \Slim\Slim::getInstance();
	$final_global_template_vars = $app->config('final_global_template_vars');
	$submitted_token = $app->request()->post($final_global_template_vars['csrf_key']);
	if(empty($submitted_token) || $submitted_token != $_SESSION[$final_global_template_vars['csrf_key']]){
		$app->halt(400, 'Invalid or missing CSRF token.');
	}
}

/**
 * Apply Permissions
 *
 * This function is used ONLY to make sure if a user has a sufficient role to be on a page...
 * NOT to apply permissions as to what the user can view ON that page.
 *
 * @param       array $role_perm_key
 * @param       array $final_global_template_vars
 * @return      bool|void
 */
function apply_permissions($role_perm_key, $final_global_template_vars) {
	return function ($redirect = true) use ($role_perm_key, &$final_global_template_vars) {
		$user_roles = !empty($_SESSION[$final_global_template_vars["session_key"]]) && !empty($_SESSION[$final_global_template_vars["session_key"]]["user_role_list"]) ? $_SESSION[$final_global_template_vars["session_key"]]["user_role_list"] : array();
		$has_permission = array_intersect($user_roles, $final_global_template_vars[$role_perm_key]);
		if(empty($redirect)) {
			if(empty($has_permission)) {
				return false;
			} else {
				return true;
			}
		} else {
			if(empty($has_permission)) {
				$app = \Slim\Slim::getInstance();
				$app->redirect($final_global_template_vars["access_denied_url"]);
			}
		}
	};
}

/**
 * User Account Permissions
 *
 * Controller for the User Account module.
 *
 * @param   $route  The route data array
 * @return  void
 */
$user_account_permissions = function(\Slim\Route $route){
  $app = \Slim\Slim::getInstance();
  $final_global_template_vars = $app->config('final_global_template_vars');
  $params = $route->getParams();

  $record_user_account_id = isset($params["user_account_id"]) ? $params["user_account_id"] : false;
  $session_user_account_id = !empty($_SESSION[$final_global_template_vars["session_key"]]) && !empty($_SESSION[$final_global_template_vars["session_key"]]["user_account_id"]) ? $_SESSION[$final_global_template_vars["session_key"]]["user_account_id"] : false;

  if(empty($session_user_account_id) || empty($record_user_account_id)){
    $app->redirect($final_global_template_vars["access_denied_url"]);
  }

  // Check to see if the user is trying to modify their own record.
  if($session_user_account_id == $record_user_account_id){
    $has_permission = array_intersect($_SESSION[$final_global_template_vars["session_key"]]["user_role_list"], $final_global_template_vars["role_perm_modify_own_account"]);
    if(empty($has_permission)){
      $app->flash('message', 'You are not able to modify your own user account.');
      $app->redirect($final_global_template_vars["access_denied_url"]);
    }
  }
};

/**
 * User Account Delete Permissions
 *
 * Controller for the User Account module.
 *
 * @param   $route  The route data array
 * @return  void
 */
$user_account_delete_permissions = function(\Slim\Route $route){
  $app = \Slim\Slim::getInstance();
  $final_global_template_vars = $app->config('final_global_template_vars');
  $params = $route->getParams();

  $has_permission = array_intersect($_SESSION[$final_global_template_vars["session_key"]]["user_role_list"], $final_global_template_vars["role_perm_delete_user_account"]);
  if(empty($has_permission)){
    $app->redirect($final_global_template_vars["access_denied_url"]);
  }
};

/**
 * Force Request Address
 *
 * Only allow script to be run by a given IP address.
 *
 * @param   array   $ip_address   The array of allowed IP addresse(s)
 * @return  void
 */
$force_request_address = function( $ip_address=array() ){
	return function () use ($ip_address){
		$app = \Slim\Slim::getInstance();
		if(empty($_SERVER["REMOTE_ADDR"]) || !in_array($_SERVER["REMOTE_ADDR"],$ip_address)){
			$app->halt(403, 'Unauthorized');
		}
	};
}
?>