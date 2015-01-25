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
 * Show User Account Form
 *
 * Controller for the User Account module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 * @param       int  $user_account_id  The user account id
 */

function show_user_account_form( $user_account_id = false )
{
    $app = \Slim\Slim::getInstance();
    $env = $app->environment();
    $final_global_template_vars = $app->config('final_global_template_vars');
    require_once $final_global_template_vars["absolute_path_to_this_module"] . "/models/user_account.class.php";
    require_once $final_global_template_vars["default_module_list"]["group"]["absolute_path_to_this_module"] . "/models/group.class.php";
    $db_conn = new \PHPSkeleton\models\db($final_global_template_vars["db_connection"]);
    $db_resource = $db_conn->get_resource();
    $useraccount = new \PHPSkeleton\UserAccount($db_resource, $final_global_template_vars["session_key"]);
    $group = new \PHPSkeleton\Group($db_resource, $final_global_template_vars["session_key"]);
    $post = $app->request()->post();
    $address_data = array();

    // Check to see if user has permissions to access all accounts.
    $has_permission = array_intersect($_SESSION[$final_global_template_vars["session_key"]]["user_role_list"], $final_global_template_vars["role_perm_manage_all_accounts_access"]);
    $role_perm_manage_all_accounts_access = empty($has_permission) ? false : true;
    // Redirect if user does not have permissions to access all accounts.
    if (!$role_perm_manage_all_accounts_access && ((int)$user_account_id != $_SESSION[$final_global_template_vars["session_key"]]["user_account_id"])) {
        $app->flash('message', 'Access denied.');
        $app->redirect("/authenticate/access_denied");
    }

    $current_group_values = $useraccount->get_user_group_roles_map((int)$user_account_id, $final_global_template_vars["proxy_id"]);
    $roles = $useraccount->get_roles($final_global_template_vars["exclude_ids_from_selector"]);

    $group_hierarchy = $group->get_group_hierarchy("--");
    $flat_group_hierarchy = $group->flatten_group_hierarchy($group_hierarchy);
    foreach ($flat_group_hierarchy as $array_key => &$single_group_info) {
        $single_group_info["admin"] = false;
        $show_all = array_intersect($_SESSION[$final_global_template_vars["session_key"]]["user_role_list"], $final_global_template_vars["role_perm_assign_user_account_to_any_group"]);
        if (!empty($show_all)) {
            $single_group_info["admin"] = true;
        } else {
            $group_roles = $useraccount->has_role($_SESSION[$final_global_template_vars["session_key"]]["user_account_id"], $final_global_template_vars["administrator_id"], $single_group_info["group_id"]);
            if (!empty($group_roles)) {
                $single_group_info["admin"] = true;
            }
        }
    }

    $has_permission = array_intersect($_SESSION[$final_global_template_vars["session_key"]]["user_role_list"], $final_global_template_vars["role_perm_modify_own_groups"]);
    $role_perm_modify_own_groups = empty($has_permission) ? false : true;

    $current_user_account_info = $useraccount->get_user_account_info((int)$user_account_id);
    $user_account_info = $post ? $post : $useraccount->get_user_account_info((int)$user_account_id);

    $address_fields = array(
        "label"
        ,"address_1"
        ,"address_2"
        ,"city"
        ,"state"
        ,"zip"
    );

    if (isset($post["address_count"]) && !empty($post["address_count"])) {
        for ($i=1; $i <= count($post["address_count"]); $i++) {
            foreach ($address_fields as $field) {
                $address_data[$i-1][$field] = $post[$field][$i];
            }
        }
    } else {
        $address_data = $useraccount->get_addresses((int)$user_account_id);
    }

    $app->render('user_account_form.php'
        ,array(
            "page_title" => "Manage User Account"
            ,"address_data" => $address_data
            ,"role_perm_modify_own_groups" => $role_perm_modify_own_groups
            ,"roles" => $roles
            ,"groups" => $flat_group_hierarchy
            ,"current_user_account_info" => $current_user_account_info
            ,"account_info" => $user_account_info
            ,"user_account_groups" => $current_group_values
            ,"errors" => isset($env["default_validation_errors"]) ? $env["default_validation_errors"] : false
        )
    );
}
