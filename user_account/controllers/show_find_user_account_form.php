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
 * Show Find User Account Form
 *
 * Controller for the User Account module.
 *
 * @package     PHP Skeleton App
 * @author      Goran Halusa
 * @since       1.0.0
 */

function show_find_user_account_form(){
	$app = \Slim\Slim::getInstance();
	$app->render('find_user_account.php',array(
		"page_title" => "Find User Account"
	));  
}
?>