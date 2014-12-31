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
 * Top-level Index
 *
 * Checks to see if the .htaccess needs the PATH_TO_FRAMEWORKS environment variable set.
 * Includes the autoload.php script at the top-level.
 *
 * @package     PHP Skeleton App
 * @author      Goran Halusa
 * @since       1.0.0
 */

$file_name = ".htaccess";
$original_file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$file_name);

if(stristr($original_file, "frameworks_path_placeholder")) {
  $parsed = str_replace('frameworks_path_placeholder', $_SERVER['DOCUMENT_ROOT'].'/vendor/', $original_file);
  unlink($_SERVER['DOCUMENT_ROOT'].'/'.$file_name);
  $file_handle = fopen($_SERVER['DOCUMENT_ROOT'].'/'.$file_name, 'w') or die("can't open file");
  fwrite($file_handle, $parsed);
  fclose($file_handle);
  chmod($_SERVER['DOCUMENT_ROOT'].'/'.$file_name, 0664);
  header("Location: /");
  exit;
}

include_once($_SERVER["PATH_TO_FRAMEWORKS"] . "default/autoload.php");
?>