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
 * Routes for the User Account module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

$app->get('/', "check_authenticated", apply_permissions("role_perm_browse_accounts_access", $final_global_template_vars), "browse_user_accounts");
$app->post('/datatables_browse_user_accounts', "check_authenticated", apply_permissions("role_perm_browse_accounts_access", $final_global_template_vars), "datatables_browse_user_accounts");

$app->get('/manage(/:user_account_id)', "check_authenticated", apply_permissions("role_perm_manage_accounts_access", $final_global_template_vars), $user_account_permissions, "show_user_account_form");
$app->post('/manage(/:user_account_id)', "enforce_csrf_guard", "check_authenticated", apply_permissions("role_perm_manage_accounts_access", $final_global_template_vars), $user_account_permissions, "insert_update_user_account", "show_user_account_form");

$app->get('/find', "check_authenticated", apply_permissions("role_perm_manage_all_accounts_access", $final_global_template_vars), "show_find_user_account_form");
$app->get('/find/(:q)', "check_authenticated", apply_permissions("role_perm_manage_all_accounts_access", $final_global_template_vars), "find_user_account");

$app->post('/delete', "enforce_csrf_guard", "check_authenticated", apply_permissions("role_perm_manage_all_accounts_access", $final_global_template_vars), $user_account_delete_permissions, "delete_user_account");

$app->get('/register/', "show_register_form");
$app->post('/register/', "enforce_csrf_guard", "insert_user_account", "show_register_form");

$app->get('/verify/', "verify_email");

$app->get('/password/', "show_reset_password_form");
$app->post('/password/', "enforce_csrf_guard", "reset_password");

$app->get('/reset/', "show_update_password_form");
$app->post('/reset/', "enforce_csrf_guard", "update_password");
