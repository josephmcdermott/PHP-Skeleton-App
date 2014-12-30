<?php
/**
 * This file is part of PHP Skeleton App.
 *
 * (c) 2014 Goran Halusa
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Routes for the User Account module.
 *
 * @author Goran Halusa <gor@webcraftr.com>
 * @copyright   2014 Goran Halusa
 * @link        https://github.com/ghalusa/PHP-Skeleton-App
 * @license     https://github.com/ghalusa/PHP-Skeleton-App/wiki/License
 * @version     1.0.0
 * @package     PHP Skeleton App
 */

$app->get('/', "check_authenticated", $apply_permissions("role_perm_browse_accounts_access"), "browse_user_accounts");
$app->post('/datatables_browse_user_accounts', "check_authenticated", $apply_permissions("role_perm_browse_accounts_access"), "datatables_browse_user_accounts");

$app->get('/manage(/:user_account_id)', "check_authenticated", $apply_permissions("role_perm_manage_accounts_access"), $user_account_permissions, "show_user_account_form");
$app->post('/manage(/:user_account_id)', "check_authenticated", $apply_permissions("role_perm_manage_accounts_access"), $user_account_permissions,"insert_update_user_account", "show_user_account_form");

$app->get('/find', "check_authenticated", $apply_permissions("role_perm_manage_all_accounts_access"), "show_find_user_account_form");
$app->get('/find/(:q)', "check_authenticated", $apply_permissions("role_perm_manage_all_accounts_access"), "find_user_account");

$app->post('/delete', "check_authenticated", $apply_permissions("role_perm_manage_all_accounts_access"), $user_account_delete_permissions, "delete_user_account");

$app->get('/register/', "show_register_form");
$app->post('/register/', "insert_user_account", "show_register_form");

$app->get('/verify/', "verify_email");

$app->get('/password/', "show_reset_password_form");
$app->post('/password/', "reset_password");

$app->get('/reset/', "show_update_password_form");
$app->post('/reset/', "update_password");
?>