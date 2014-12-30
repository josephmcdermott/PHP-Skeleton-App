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
 * Functions for the PHP Skeleton App.
 *
 * @author Goran Halusa <gor@webcraftr.com>
 * @copyright   2014 Goran Halusa
 * @link        https://github.com/ghalusa/PHP-Skeleton-App
 * @license     https://github.com/ghalusa/PHP-Skeleton-App/wiki/License
 * @version     1.0.0
 * @package     PHP Skeleton App
 */

function dump($data = false,$die = true, $ip_address=false){
	if(!$ip_address || $ip_address == $_SERVER["REMOTE_ADDR"]){
		echo '<pre>';
		var_dump($data);
		echo '</pre>';

		if($die) die();
	}
}

function check_authenticated(\Slim\Route $route){
	$app = \Slim\Slim::getInstance();
	global $final_global_template_vars;
	if(!isset($_SESSION[$final_global_template_vars["session_key"]])){
		// Set cookie so user can come back to this page.
		setcookie($final_global_template_vars["redirect_cookie_key"],$_SERVER["REQUEST_URI"], time()+3600, "/");
		$app->redirect($final_global_template_vars["login_url"]);
	}
}

function force_https(\Slim\Route $route){
	$app = \Slim\Slim::getInstance();
	global $final_global_template_vars;
	if(empty($final_global_template_vars["is_dev"])){
		// Means we are on a production box.
		if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]){
			// It's already SSL... do nothing.
		}else{
			$redirect= "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$app->redirect($redirect);
		}
	}
}

/*
 * Needed to create this function because the "force_https" function only forces 
 * https if the server is not marked as "dev".
 */
function force_ssl(\Slim\Route $route = null){
	if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]){
		// It's already SSL... do nothing.
	}else{
		$redirect= "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		header("Location: " . $redirect);
		die();
	}
}

/*
 * Only allow script to be run by a given IP address.
 */
$force_request_address = function( $ip_address=array() ){
	return function () use ($ip_address){
		$app = \Slim\Slim::getInstance();
		if(empty($_SERVER["REMOTE_ADDR"]) || !in_array($_SERVER["REMOTE_ADDR"],$ip_address)){
			$app->halt(403, 'Unauthorized');
		}
	};
}
?>