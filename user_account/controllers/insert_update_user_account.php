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
 * Insert/Update User Account
 *
 * Controller for the User Account module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 * @param       array  $route  The route data array
 */

function insert_update_user_account(\Slim\Route $route)
{
    $app = \Slim\Slim::getInstance();
    $final_global_template_vars = $app->config('final_global_template_vars');
  
    require_once $final_global_template_vars["absolute_path_to_this_module"] . "/models/user_account.class.php";
    require_once $final_global_template_vars["default_module_list"]["group"]["absolute_path_to_this_module"] . "/models/group.class.php";
    require_once $final_global_template_vars["default_module_list"]["authenticate"]["absolute_path_to_this_module"] . "/models/authenticate.class.php";
    require_once $_SERVER["PATH_TO_VENDOR"] . "wixel/gump/gump.class.php";
    // URL parameters matched in the route.
    $params = $route->getParams();
    $user_account_id = isset($params["user_account_id"]) ? $params["user_account_id"] : false;
    $db_conn = new \PHPSkeleton\models\db($final_global_template_vars["db_connection"]);
    $db_resource = $db_conn->get_resource();
    $useraccount = new \PHPSkeleton\UserAccount($db_resource, $final_global_template_vars["session_key"]);
    $group = new \PHPSkeleton\Group($db_resource, $final_global_template_vars["session_key"]);
    $authenticate = new \PHPSkeleton\Authenticate($db_resource, $final_global_template_vars["session_key"]);
    $post = $app->request()->post();

    $errors = false;
    $gump = new GUMP();
    $rules_password = array();

    $rules = array(
        "first_name" => "required|alpha_numeric"
        ,"last_name" => "required|alpha_numeric"
        ,"user_account_email" => "required|valid_email"
    );

    if (isset($post["user_account_password"]) && !empty($post["user_account_password"])) {
        $rules_password = array(
            "user_account_password" => "max_len,100|min_len,6"
            ,"password_check" => "required|max_len,100|min_len,6"
        );
    }

    $rules = array_merge($rules, $rules_password);

    $validated = $gump->validate($post, $rules);

    if ($post["user_account_password"] != $post["password_check"]) {
        $validated_password_check = array(
      "field" => "user_account_password_check"
      ,"value" => null
      ,"rule" => "validate_required"
    );
        if (is_array($validated)) {
            array_push($validated, $validated_password_check);
        } else {
            $validated = array($validated_password_check);
        }
    }

    $errors = array();
    if ($validated !== true) {
        $errors = \phpskeleton\models\utility::gump_parse_errors($validated);
    }

    if (isset($errors["user_account_password_check"])) {
        $errors["user_account_password_check"] = "Passwords did not match.";
    }

    $has_permission = array_intersect($_SESSION[$final_global_template_vars["session_key"]]["user_role_list"], $final_global_template_vars["role_perm_manage_all_accounts_access"]);
    $role_perm_manage_all_accounts_access = empty($has_permission) ? false : true;

    if (!empty($post) && $role_perm_manage_all_accounts_access) {
        $current_group_values = $useraccount->get_user_group_roles_map($user_account_id, $final_global_template_vars["proxy_id"]);
        $proposed_group_value = json_decode($post["group_data"], true);
        $changes = array();
        $current_group_role_array = array();
        $proposed_group_role_array = array();
        foreach ($proposed_group_value as $single_group_info) {
            foreach ($single_group_info["roles"] as $single_role_id) {
                $tmp_array = array(
                    "group_id" => $single_group_info["group_id"]
                    ,"role_id" => $single_role_id
                );
                $proposed_group_role_array[] = json_encode($tmp_array);
            }
        }

        if(is_array($current_group_values) && !empty($current_group_values)) {
            foreach ($current_group_values as $single_group_info) {
                foreach ($single_group_info["roles"] as $single_role_id) {
                    $tmp_array = array(
                        "group_id" => $single_group_info["group_id"]
                        ,"role_id" => $single_role_id
                    );
                    $current_group_role_array[] = json_encode($tmp_array);
                }
            }
        }
        $changes = array_diff($proposed_group_role_array, $current_group_role_array);
        $changes = array_merge($changes, array_diff($current_group_role_array, $proposed_group_role_array));

    /**
     * Check to see if the user is trying to hack the system and add a role they are not able to.
     **/
     foreach ($changes as $single_change) {
         $single_change_array = json_decode($single_change, true);
         $show_all = array_intersect($_SESSION[$final_global_template_vars["session_key"]]["user_role_list"], $final_global_template_vars["role_perm_assign_user_account_to_any_group"]);
         if (!empty($show_all)) {
             // This user can add any group to any user.
         } else {
             $group_roles = $useraccount->has_role($_SESSION[$final_global_template_vars["session_key"]]["user_account_id"], $final_global_template_vars["administrator_id"], $single_change_array["group_id"]);
             if (empty($group_roles)) {
                 $failed_group = $group->get_group_record($single_change_array["group_id"]);
                 $errors[] = "You are not able to administor group: " . $failed_group["name"];
             }
         }
     }

    // Check to see if the user is trying to add a role to a group they are not able to.
    foreach ($changes as $single_change) {
        $single_change_array = json_decode($single_change, true);
        if (in_array($single_change_array["role_id"], $final_global_template_vars["exclude_ids_from_selector"])) {
            $errors[] = "You are not able to administer that role.";
        }
    }
    }

    if (!$errors) {
        // Hash the incoming password (with some salt).
    if (!empty($post["user_account_password"])) {
        $post["user_account_password"] = $authenticate->generate_hashed_password($post["user_account_password"]);
    }

        $useraccount->insert_update_user_account($post, $user_account_id, true, $final_global_template_vars["proxy_id"], $role_perm_manage_all_accounts_access);
        $useraccount->insert_addresses($post, $user_account_id, $_SESSION[$final_global_template_vars["session_key"]]["user_account_id"]);
        $app->flash('message', 'Account successfully updated.');
        if ($role_perm_manage_all_accounts_access) {
            $app->redirect($final_global_template_vars["path_to_this_module"]);
        } else {
            $app->redirect($final_global_template_vars["path_to_this_module"]."/manage/".$user_account_id);
        }
    } else {
        $env = $app->environment();
        $env["default_validation_errors"] = $errors;
    }
}
