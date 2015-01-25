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
 * Show Register Form
 *
 * Controller for the Authenticate module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */
 
function show_register_form()
{
    $app = \Slim\Slim::getInstance();
    $final_global_template_vars = $app->config('final_global_template_vars');
    require_once $final_global_template_vars["default_module_list"]["user_account"]["absolute_path_to_this_module"] . "/models/user_account.class.php";
    require_once $final_global_template_vars["default_module_list"]["register_account"]["absolute_path_to_this_module"] . "/models/register_account.class.php";
    require_once $final_global_template_vars["default_module_list"]["group"]["absolute_path_to_this_module"] . "/models/group.class.php";
    $env = $app->environment();
    $db_conn = new \PHPSkeleton\models\db($final_global_template_vars["db_connection"]);
    $db_resource = $db_conn->get_resource();
    $user_account = new \PHPSkeleton\UserAccount($db_resource, $final_global_template_vars["session_key"]);
    $register_account = new \PHPSkeleton\RegisterAccount($db_resource, $final_global_template_vars["session_key"]);
    $group = new \PHPSkeleton\Group($db_resource, $final_global_template_vars["session_key"]);
    $needs_group = true;

    // Check to see if they are already registered (group selected).
    // If they are already registered, don't let them register again.
    $is_registered = $register_account->is_registered($_SESSION[$final_global_template_vars["session_key"]]["user_account_id"]);

    // Check to see if this user is already assigned to a group - they may have been added by another administrator.
    $current_groups = $user_account->get_user_account_groups($_SESSION[$final_global_template_vars["session_key"]]["user_account_id"]);
    if ($current_groups) {
        $needs_group = false;
    }
    $group_hierarchy = $group->get_group_hierarchy("--");
    $flat_group_hierarchy = $group->flatten_group_hierarchy($group_hierarchy);

    $app->render('register_form.php'
        ,array(
            "page_title" => false
            ,"hide_side_nav" => true
            ,"is_registered" => $is_registered
            ,"groups" => $flat_group_hierarchy
            ,"needs_group" => $needs_group
            ,"submitted_data" => $app->request()->post()
            ,"errors" => !empty($env["default_validation_errors"]) ? $env["default_validation_errors"] : false
        )
    );
}
