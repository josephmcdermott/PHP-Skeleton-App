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
 * Routes
 *
 * Routes for the Group module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

$app->get('/', "check_authenticated", apply_permissions("role_perm_browse_groups_access", $final_global_template_vars), "browse_groups");
$app->post('/datatables_browse_groups', "check_authenticated", apply_permissions("role_perm_browse_groups_access", $final_global_template_vars), "datatables_browse_groups");

$app->get('/manage(/:group_id)', "check_authenticated", apply_permissions("role_perm_manage_groups_access", $final_global_template_vars), "show_group_form");
$app->post('/manage(/:group_id)', "enforce_csrf_guard", "check_authenticated", apply_permissions("role_perm_manage_groups_access", $final_global_template_vars), "insert_update_group", "show_group_form");

$app->post('/delete', "enforce_csrf_guard", "check_authenticated", apply_permissions("role_perm_manage_groups_access", $final_global_template_vars), "delete_group");
