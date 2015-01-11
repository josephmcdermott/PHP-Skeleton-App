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
 * Routes for the Authenticate module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

$app->get("/", "show_login_form"); // "force_https",
$app->post("/", "authenticate_user", "check_local_account", "enforce_csrf_guard", "show_login_form");

$app->get("/register", "show_register_form"); // "force_https"
$app->post("/register", "submit_registration", "show_register_form");

$app->get("/access_denied", "show_access_denied");

$app->get("/logout", "logout");
