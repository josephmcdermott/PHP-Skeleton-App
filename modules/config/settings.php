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
 * Settings for the Modules module.
 *
 * @author Goran Halusa <gor@webcraftr.com>
 * @copyright   2014 Goran Halusa
 * @link        https://github.com/ghalusa/PHP-Skeleton-App
 * @license     https://github.com/ghalusa/PHP-Skeleton-App/wiki/License
 * @version     1.0.0
 * @package     PHP Skeleton App
 */

/**
 * Note that you are able to use any key that exists in
 * the global settings, and it will overwrite it
 */
$default_module_settings = array(
	"module_name" => "Modules"
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
?>