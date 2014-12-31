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
 * Delete User Account
 *
 * Controller for the User Account module.
 *
 * @package     PHP Skeleton App
 * @author      Goran Halusa
 * @since       1.0.0
 */

function delete_user_account(){
	$app = \Slim\Slim::getInstance();
	global $final_global_template_vars;
	require_once $final_global_template_vars["absolute_path_to_this_module"] . "/models/user_account.class.php";
	$db_conn = new \slimlocal\models\db($final_global_template_vars["db_connection"]);
	$db_resource = $db_conn->get_resource();
	$useraccount = new UserAccount($db_resource,$final_global_template_vars["session_key"]);
	$delete_ids = json_decode($app->request()->post("id"));
	foreach($delete_ids as $single_id){
		$useraccount->delete_user_account($single_id);
	}
}
?>