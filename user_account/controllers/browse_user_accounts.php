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
 * Browse User Accounts
 *
 * Controller for the User Account module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

function browse_user_accounts()
{
    $app = \Slim\Slim::getInstance();
    $final_global_template_vars = $app->config('final_global_template_vars');

  // Determine if user can manage all accounts. If not, limit the query to only the user's user_account_id.
  $has_permission = array_intersect($_SESSION[$final_global_template_vars["session_key"]]["user_role_list"], $final_global_template_vars["role_perm_manage_all_accounts_access"]);
    $role_perm_manage_all_accounts_access = empty($has_permission) ? false : true;
    $user_account_id = !$role_perm_manage_all_accounts_access ? $_SESSION[$final_global_template_vars["session_key"]]["user_account_id"] : false;

    if ($user_account_id) {
        $app->redirect($final_global_template_vars["path_to_this_module"]."/manage/".$user_account_id);
    }

    $app->render('browse_user_accounts.php', array(
        "page_title" => "Browse User Accounts"
    ));
}
