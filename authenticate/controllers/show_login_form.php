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
 * Show Login Form
 *
 * Controller for the Authenticate module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */
 
function show_login_form()
{
    $app = \Slim\Slim::getInstance();
    $final_global_template_vars = $app->config('final_global_template_vars');
    
    require_once $final_global_template_vars["default_module_list"]["user_account"]["absolute_path_to_this_module"] . "/models/user_account.class.php";
    $env = $app->environment();
    $db_conn = new \PHPSkeleton\models\db($final_global_template_vars["db_connection"]);
    $db_resource = $db_conn->get_resource();
    $user_account = new \PHPSkeleton\UserAccount($db_resource, $final_global_template_vars["session_key"]);

    if (empty($env["default_validation_errors"]) && $_SERVER['REQUEST_METHOD'] == "POST") {
        $landing_page = $final_global_template_vars['landing_page'];

        if (isset($_COOKIE[$final_global_template_vars["redirect_cookie_key"]]) &&
            $_COOKIE[$final_global_template_vars["redirect_cookie_key"]] &&
            $_COOKIE[$final_global_template_vars["redirect_cookie_key"]] != "/") {
            $landing_page = $_COOKIE[$final_global_template_vars["redirect_cookie_key"]];
            setcookie($final_global_template_vars["redirect_cookie_key"], "", time()-3600, "/");
            unset($_COOKIE[$final_global_template_vars["redirect_cookie_key"]]);
        }

        // Add role list to session.
        $_SESSION[$final_global_template_vars["session_key"]][$final_global_template_vars["current_user_roles_session_key"]] = \phpskeleton\models\utility::array_flatten($user_account->get_user_roles_list($_SESSION[$final_global_template_vars["session_key"]]["user_account_id"]));

        // Add group list to session.
        $tmp_array = array();
        $_SESSION[$final_global_template_vars["session_key"]]["associated_groups"] = \phpskeleton\models\utility::array_flatten($user_account->get_user_account_groups($_SESSION[$final_global_template_vars["session_key"]]["user_account_id"]), $tmp_array, 'group_id');

        // Landing page exceptions.
        switch($landing_page) {
            // If coming from the register page, set the $app->redirect() to the "/dashboard".
            case "/user_account/register/":
                $app->redirect("/dashboard");
                break;
            // If coming from the home page, set the $app->redirect() to the "/dashboard".
            case "/":
                $app->redirect("/dashboard");
                break;
            // Otherwise, set the $app->redirect() to the value of the $landing_page variable.
            default:
                $app->redirect($landing_page);
        }
    }

    // If logged in, don't render the login form.
    if (isset($_SESSION[$final_global_template_vars["session_key"]])) {
        $app->redirect("/dashboard/");
    }

    $app->render('login_form.php', array(
        "page_title" => "Login", "hide_page_header" => true, "errors" => !empty($env["default_validation_errors"]) ? $env["default_validation_errors"] : false
    ));
}
