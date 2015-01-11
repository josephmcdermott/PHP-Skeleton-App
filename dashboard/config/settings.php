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
 * Settings
 *
 * Settings for the Dashboard module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

/**
 * Note that you are able to use any key that exists in
 * the global settings, and it will overwrite it
 */
$default_module_settings = array(
    "module_name" => "Dashboard"
    ,"module_description" => "Display all available modules."
    ,"module_icon_path" => "/" . $_SERVER["CORE_TYPE"] . "/lib/images/icons/pixelistica-blue-icons/png/64x64/layout_squares_small.png"
    ,"menu_hidden" => true
    ,"pages" => array(
        array(
            "label" => "Dashboard", "path" => "/", "display" => true
        )
    )
    ,"sort_order" => 100
);
