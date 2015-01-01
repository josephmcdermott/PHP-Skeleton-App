<?php
/**
 * The PHP Skeleton App
 *
 * @author      Goran Halusa <gor@webcraftr.com>
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
 * Check Local Account
 *
 * Controller for the Authenticate module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       1.0.0
 */

function check_local_account(){
	$app = \Slim\Slim::getInstance();
	global $final_global_template_vars;
	require_once $final_global_template_vars["default_module_list"]["user_account"]["absolute_path_to_this_module"] . "/models/user_account.class.php";
	$env = $app->environment();
	$db_conn = new \slimlocal\models\db($final_global_template_vars["db_connection"]);
	$db_resource = $db_conn->get_resource();
	$user_account = new UserAccount($db_resource,$final_global_template_vars["session_key"]);

	if(!empty($_SESSION[$final_global_template_vars["session_key"]]) && empty($env["default_validation_errors"])){
		// Check to see if the author has a role in the system and is registered (AUP).
	    $local_user_account = $user_account->is_registered($_SESSION[$final_global_template_vars["session_key"]]['user_account_id']);
		if(!$local_user_account){
			$app->redirect($final_global_template_vars["path_to_this_module"] . "/register");
		}
	}
}
?>