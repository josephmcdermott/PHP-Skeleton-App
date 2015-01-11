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
 * Datatables Browse User Accounts
 *
 * Controller for the User Account module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

function datatables_browse_user_accounts()
{
    $app = \Slim\Slim::getInstance();
    $final_global_template_vars = $app->config('final_global_template_vars');
    
    require_once $final_global_template_vars["absolute_path_to_this_module"] . "/models/user_account.class.php";
    $db_conn = new \PHPSkeleton\models\db($final_global_template_vars["db_connection"]);
    $db_resource = $db_conn->get_resource();
    $useraccount = new \PHPSkeleton\UserAccount($db_resource, $final_global_template_vars["session_key"]);

    // Determine if user can manage all accounts. If not, limit the query to only the user's user_account_id.
    $has_permission = array_intersect($_SESSION[$final_global_template_vars["session_key"]]["user_role_list"], $final_global_template_vars["role_perm_manage_all_accounts_access"]);
    $role_perm_manage_all_accounts_access = empty($has_permission) ? false : true;
    $user_account_id = !$role_perm_manage_all_accounts_access ? $_SESSION[$final_global_template_vars["session_key"]]["user_account_id"] : false;

    $search = $app->request()->post('search');
    $search_value = !empty($search["value"]) ? $search["value"] : false;

    $data = $useraccount->browse_user_accounts(
        false,
        $app->request()->post('order'),
        $app->request()->post('start'),
        $app->request()->post('length'),
        $search_value,
        $user_account_id
    );

    echo json_encode($data);
    die();
}
