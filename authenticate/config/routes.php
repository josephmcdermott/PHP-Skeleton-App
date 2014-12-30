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
 * Routes for the Authenticate module.
 *
 * @copyright   2014 Goran Halusa
 * @link        https://github.com/ghalusa/PHP-Skeleton-App
 * @license     https://github.com/ghalusa/PHP-Skeleton-App/wiki/License
 * @version     1.0.0
 * @package     PHP Skeleton App
 */

$app->get('/', "show_login_form"); // "force_https",
$app->post('/', "authenticate_user", "check_local_account", "show_login_form");

$app->get('/register', "show_register_form"); // "force_https"
$app->post('/register', "submit_registration", "show_register_form");

$app->get('/access_denied', "show_access_denied");

$app->get('/logout', "logout");
?>