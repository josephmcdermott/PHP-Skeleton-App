<?php
/**
 * The PHP Skeleton App
 *
 * @author      Goran Halusa
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
 * Show Register Form
 *
 * Controller for the User Account module.
 *
 * @package     PHP Skeleton App
 * @author      Goran Halusa
 * @since       1.0.0
 */

function show_register_form() {
  $app = \Slim\Slim::getInstance();
  $env = $app->environment();
  global $final_global_template_vars;

  $data = $app->request()->post() ? $app->request()->post() : false;

  $app->render('register_form.php',array(
    "page_title" => "Register"
    ,"hide_page_header" => true
    ,"path_to_this_module" => $final_global_template_vars["path_to_this_module"]
    ,"errors" => $env["default_validation_errors"]
    ,"data" => $data
  ));
}
?>