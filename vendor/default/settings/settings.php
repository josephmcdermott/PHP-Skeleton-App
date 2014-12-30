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
 * Default settings for the PHP Skeleton App.
 *
 * @author Goran Halusa <gor@webcraftr.com>
 * @copyright   2014 Goran Halusa
 * @link        https://github.com/ghalusa/PHP-Skeleton-App
 * @license     https://github.com/ghalusa/PHP-Skeleton-App/wiki/License
 * @version     1.0.0
 * @package     PHP Skeleton App
 */

$default_core_settings = array(
  // The twitter bootstrap span number (width) you want the main content to have by default.
	"default_span_number" => 9
  // This is just a placeholder. It's unlikely that we would want to include 
  // something automatically across all of our websites.
	,"js_includes" => array()
  // This is just a placeholder. It's unlikely that we would want to include 
  // something automatically across all of our websites.
	,"css_includes" => array()
  // This directory needs to be present, but can be changed at the site level.
	,"default_site_module" => $_SERVER["DOCUMENT_ROOT"]. "/site"
  // Provide the path to a local navbar and include it to override the default navbar.
	,"navbar" => ""
);
?>