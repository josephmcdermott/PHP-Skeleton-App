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
 * Insert/Update Group
 *
 * Controller for the Group module.
 *
 * @param \Slim\Route $route The route data array
 * @return void
 */

function insert_update_group(\Slim\Route $route)
{
    $app = \Slim\Slim::getInstance();
    $final_global_template_vars = $app->config('final_global_template_vars');
    
    require_once $final_global_template_vars["absolute_path_to_this_module"] . "/models/group.class.php";
    require_once $_SERVER["PATH_TO_VENDOR"] . "wixel/gump/gump.class.php";
    // URL parameters matched in the route.
    $params = $route->getParams();
    $group_id = isset($params["group_id"]) ? $params["group_id"] : false;
    $db_conn = new \PHPSkeleton\models\db($final_global_template_vars["db_connection"]);
    $db_resource = $db_conn->get_resource();
    $group = new \PHPSkeleton\Group($db_resource, $final_global_template_vars["session_key"]);
    $gump = new GUMP();
    $rules = array(
        "name" => "required"
        ,"abbreviation" => "required|alpha_numeric"
        ,"state" => "alpha_numeric"
        ,"zip" => "numeric|exact_len,5"
        ,"group_parent" => "numeric"
    );
    $validated = $gump->validate($app->request()->post(), $rules);
    $errors = array();
    if ($validated !== true) {
        $errors = \phpskeleton\models\utility::gump_parse_errors($validated);
    }

    if (!$errors) {
        $group->insert_update_group($app->request()->post(), $group_id);
        // If group_id is true, then the group was modified. Otherwise, it was created.
        if ($group_id) {
            $app->flash('message', 'The group has been successfully modified.');
        } else {
            $app->flash('message', 'New group has been successfully created.');
        }
        $app->redirect($final_global_template_vars["path_to_this_module"]);
    } else {
        $env = $app->environment();
        $env["default_validation_errors"] = $errors;
    }
}
