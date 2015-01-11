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
 * Show Access Denied
 *
 * Controller for the Authenticate module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */
 
function show_access_denied()
{
    $app = \Slim\Slim::getInstance();
    $app->render('access_denied.php', array(
        "page_title" => false
    ));
}
