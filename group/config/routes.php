<?php
/**
 * The PHP Skeleton App
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @copyright   2015 Goran Halusa
 * @link        https://github.com/ghalusa/PHP-Skeleton-App
 * @license     https://github.com/ghalusa/PHP-Skeleton-App/wiki/License
 * @version     1.0.0
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
 * @since       1.0.0
 */

$app->get('/', "check_authenticated", $apply_permissions("role_perm_browse_groups_access"), "browse_groups"); 
$app->post('/datatables_browse_groups', "check_authenticated", $apply_permissions("role_perm_browse_groups_access"), "datatables_browse_groups"); 

$app->get('/manage(/:group_id)', "check_authenticated", $apply_permissions("role_perm_manage_groups_access"), "show_group_form"); 
$app->post('/manage(/:group_id)', "check_authenticated", $apply_permissions("role_perm_manage_groups_access"), "insert_update_group", "show_group_form"); 

$app->post('/delete', "check_authenticated", $apply_permissions("role_perm_manage_groups_access"), "delete_group");
?>