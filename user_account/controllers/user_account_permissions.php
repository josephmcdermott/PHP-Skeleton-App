<?php
/**
 * The PHP Skeleton App
 *
 * @author      Goran Halusa
 * @copyright   2015 Goran Halusa
 * @link        https://github.com/ghalusa/PHP-Skeleton-App
 * @license     https://github.com/ghalusa/PHP-Skeleton-App/wiki/License
 * @version     1.0.0
 * @package     PHP Skeleton App
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * User Account Permissions
 *
 * Controller for the User Account module.
 *
 * @package     PHP Skeleton App
 * @author      Goran Halusa
 * @since       1.0.0
 */

global $user_account_permissions;
global $user_account_delete_permissions;
$user_account_permissions = function(\Slim\Route $route){
	$app = \Slim\Slim::getInstance();
	global $final_global_template_vars;
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

$user_account_delete_permissions = function(\Slim\Route $route){
	$app = \Slim\Slim::getInstance();
	global $final_global_template_vars;
	$params = $route->getParams();

	$has_permission = array_intersect($_SESSION[$final_global_template_vars["session_key"]]["user_role_list"], $final_global_template_vars["role_perm_delete_user_account"]);
	if(empty($has_permission)){
		$app->redirect($final_global_template_vars["access_denied_url"]);
	}
};