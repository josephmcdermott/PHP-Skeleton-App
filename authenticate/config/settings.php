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
 * Settings for the Authenticate module.
 *
 * @copyright   2014 Goran Halusa
 * @link        https://github.com/ghalusa/PHP-Skeleton-App
 * @license     https://github.com/ghalusa/PHP-Skeleton-App/wiki/License
 * @version     1.0.0
 * @package     PHP Skeleton App
 */

$default_module_settings = array(
  "module_name" => "Authenticate"
  ,"module_description" => "Authenticate users"
  ,"menu_hidden" => true
  ,"pages" => array(
    array(
      "label" => "Login", "path" => "/", "display" => true
    )
  )
  ,"auth_session_keys" => array(
    "user_account_id"
    ,"user_account_email"
    ,"first_name"
    ,"last_name"
  )
  ,"display_sign_up" => true
  ,"display_password_reset" => true
);
?>