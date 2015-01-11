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
 * Settings for the Group module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

/**
 * Note that you are able to use any key that exists in
 * the global settings, and it will overwrite it
 */

$default_module_settings = array(
    "module_name" => "Groups"
    ,"module_description" => "Manage groups for application"
    ,"module_icon_path" => "/" . $_SERVER["CORE_TYPE"] . "/lib/images/icons/pixelistica-blue-icons/png/64x64/wired.png"
    ,"menu_hidden" => isset($_SESSION[$default_global_settings["session_key"]]) && $_SESSION[$default_global_settings["session_key"]] ? false : true
    ,"pages" => array(
        array(
            "label" => "Browse Groups",
            "path" => "/",
            "display" => apply_permissions(
                "role_perm_browse_groups_access",
                $final_global_template_vars
            )
        )
        ,array(
            "label" => "Create Group",
            "path" => "/manage",
            "display" => apply_permissions(
                "role_perm_manage_groups_access",
                $final_global_template_vars
            )
        )
    )
    ,"sort_order" => 4
);
