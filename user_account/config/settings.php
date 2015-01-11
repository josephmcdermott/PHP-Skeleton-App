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
 * Settings
 *
 * Settings for the User Account module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

/**
 * Note that you are able to use any key that exists in
 * the global settings, and it will overwrite it
 */

$user_account_id = isset($_SESSION[$final_global_template_vars["session_key"]]["user_account_id"])
    ? $_SESSION[$final_global_template_vars["session_key"]]["user_account_id"] : "";

$user_role_list = isset($_SESSION[$final_global_template_vars["session_key"]]["user_role_list"])
    ? $_SESSION[$final_global_template_vars["session_key"]]["user_role_list"] : array();

$has_permission = array_intersect($user_role_list, $final_global_template_vars["role_perm_manage_all_accounts_access"]);
$role_perm_manage_all_accounts_access = empty($has_permission) ? false : true;

$default_module_settings = array(
    "module_name" => $role_perm_manage_all_accounts_access ? "User Accounts" : "Your Account"
    ,"module_description" => $role_perm_manage_all_accounts_access ? "Manage user accounts for application." : "Manage your account."
    ,"module_icon_path" => "/" . $_SERVER["CORE_TYPE"] . "/lib/images/icons/pixelistica-blue-icons/png/64x64/Users.png"
    ,"menu_hidden" => isset($_SESSION[$default_global_settings["session_key"]]) && $_SESSION[$default_global_settings["session_key"]] ? false : true
    ,"pages" => $role_perm_manage_all_accounts_access ?
    array(
        array(
            "label" => "Browse User Accounts",
            "path" => "/",
            "display" => apply_permissions(
                "role_perm_browse_accounts_access",
                $final_global_template_vars
            )
        )
        ,array(
            "label" => "Find User Account",
            "path" => "/find",
            "display" => apply_permissions(
                "role_perm_manage_all_accounts_access",
                $final_global_template_vars
            )
        )
    )
    :
    array(
        array(
            "label" => "Manage Your Account",
            "path" => "/manage/".$user_account_id,
            "display" => apply_permissions(
                "role_perm_manage_accounts_access",
                $final_global_template_vars
            )
        )
    )
    ,"sort_order" => 3
    ,"proxy_id" => "3" //this enables user account to add people to proxy role
    ,"administrator_id" => array(1) //used to allow a user to add roles to the group they are an admin for
    ,"exclude_ids_from_selector" => array() //don't allow user to assign these roles
);
