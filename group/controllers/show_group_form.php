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
 * Show Group Form
 *
 * Controller for the Group module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 * @param 			int  $group_id  The group id
 */

function show_group_form($group_id=false)
{
    $app = \Slim\Slim::getInstance();
    $env = $app->environment();
    $final_global_template_vars = $app->config('final_global_template_vars');
    
    require_once $final_global_template_vars["absolute_path_to_this_module"] . "/models/group.class.php";
    $db_conn = new \PHPSkeleton\models\db($final_global_template_vars["db_connection"]);
    $db_resource = $db_conn->get_resource();
    $group = new \PHPSkeleton\Group($db_resource, $final_global_template_vars["session_key"]);

    $group_hierarchy = $group->get_group_hierarchy("--");
    $flat_group_hierarchy = $group->flatten_group_hierarchy($group_hierarchy);

    $current_values = false;
    if ($app->request()->post()) {
        $current_values = $app->request()->post();
    } elseif ($group_id) {
        $current_values = $group->get_group_record($group_id);
    }

    $title = ($group_id) ? "Update" : "Create";
    $app->render('group_form.php', array(
        "page_title" => "{$title} Group", "group_data" => $current_values, "groups" => $flat_group_hierarchy, "errors" => isset($env["default_validation_errors"]) ? $env["default_validation_errors"] : false
    ));
}
