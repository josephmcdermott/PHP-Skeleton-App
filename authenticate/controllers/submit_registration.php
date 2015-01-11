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
 * Submit Registration
 *
 * Controller for the Authenticate module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 * @param       array  $route  The route data array
 */
 
function submit_registration(\Slim\Route $route)
{
    $app = \Slim\Slim::getInstance();
    $final_global_template_vars = $app->config('final_global_template_vars');
    
    require_once $final_global_template_vars["default_module_list"]["user_account"]["absolute_path_to_this_module"] . "/models/user_account.class.php";
    require_once $final_global_template_vars["default_module_list"]["group"]["absolute_path_to_this_module"] . "/models/group.class.php";
    require_once $_SERVER["PATH_TO_VENDOR"] . "wixel/gump/gump.class.php";
    $env = $app->environment();
    $db_conn = new \PHPSkeleton\models\db($final_global_template_vars["db_connection"]);
    $db_resource = $db_conn->get_resource();
    $user_account = new \PHPSkeleton\UserAccount($db_resource, $final_global_template_vars["session_key"]);
    $gump = new GUMP();
    $errors = array();

    $user_account_id = $_SESSION[$final_global_template_vars["session_key"]]["user_account_id"];

    // Check to see if this user is already assigned to a group - they may have been added by another administrator.
    $current_groups = $user_account->get_user_account_groups($user_account_id);
    if (!$current_groups) {
        // Validate the group that they submitted.
        $rules = array(
            "group" => "required|integer"
        );
        $validated = $gump->validate($app->request()->post(), $rules);
        if ($validated !== true) {
            $errors = \phpskeleton\models\utility::gump_parse_errors($validated);
        }
    }

    // Validate the acceptable use policy.
    $rules = array(
        "acceptable_use_policy" => "required|integer"
    );
    $validated = $gump->validate($app->request()->post(), $rules);
    if ($validated !== true) {
        $errors = array_merge($errors, \phpskeleton\models\utility::gump_parse_errors($validated));
    }

    if (!$errors) {
        // Create the actual user account.
        $user_data = array(
            "group_data" => '{"0":{"group_id":"' . $app->request()->post("group") . '","roles":["' . $final_global_template_vars["default_role_id"] . '"]}}'
        );
        $update_groups = (!empty($current_groups)) ? false : true;
        // Get the existing user account info.
        $existing_user_data = $user_account->get_user_account_info($user_account_id);
        // Merge the data.
        $user_data = array_merge($user_data, $existing_user_data);
        // Insert/update
        $user_account->insert_update_user_account($user_data, $user_account_id, $update_groups);

        // Update acceptable use policy.
        $user_account->update_acceptable_use_policy($user_account_id, 1);

        $landing_page = $final_global_template_vars['landing_page'];
        if (isset($_COOKIE[$final_global_template_vars["redirect_cookie_key"]]) && $_COOKIE[$final_global_template_vars["redirect_cookie_key"]]) {
            $landing_page = $_COOKIE[$final_global_template_vars["redirect_cookie_key"]];
            setcookie($final_global_template_vars["redirect_cookie_key"], "", time()-3600, "/");
            unset($_COOKIE[$final_global_template_vars["redirect_cookie_key"]]);
        }

        // Add role list to session.
        $_SESSION[$final_global_template_vars["session_key"]][$final_global_template_vars["current_user_roles_session_key"]] = \phpskeleton\models\utility::array_flatten($user_account->get_user_roles_list($user_account_id));

        // Add group to session.
        $_SESSION[$final_global_template_vars["session_key"]]["associated_groups"] = array((int)$app->request()->post("group"));
        $app->redirect($landing_page);
    } else {
        $env["default_validation_errors"] = $errors;
    }
}
