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

namespace phpskeleton\models;

use PDO;

/**
 * DB
 *
 * Database class for the PHP Skeleton App.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */
class db{

	/**
   * @var object  $db   The database connection object
   */
	private $db;

	/**
   * Constructor
   *
   * @param object   $db_connection_params   The database connection object
   */
  public function __construct($db_connection_params = false) {
		if($db_connection_params && is_array($db_connection_params)) {
			$this->db = $this->db_connect($db_connection_params);
		}
  }

  /**
   * DB Connect
   *
   * Connect to a database and establish a resource.
   *
   * @param     object $db_connection_params    The database connection object
   * @return    resource                        The database connection resource
   */
	private function db_connect($db_connection_params) {
		if(empty($db_connection_params['type'])){
			$db_connection_params['type'] = 'mysql';
		}
		$extra_params = array();
		switch($db_connection_params['type']){
			case 'mysql':
				$extra_params = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
				$conn = "mysql:dbname=" . $db_connection_params["name"] . ";charset=UTF8;host=" . $db_connection_params["host"];
			break;
			case 'oci':
				$conn = "oci:dbname=".$db_connection_params["host"];
			break;
		}

		$user = $db_connection_params["user"];
		$password = $db_connection_params["password"];
		try{
			$database_handle = new PDO($conn,$user,$password,$extra_params);
		}catch(PDOException $e){
			if(isset($db_connection_params["email_on_connection_failure"])){
				mail($db_connection_params["admin_emails"], $_SERVER["SERVER_NAME"] . ": Database Connection Failure"
					,"Failed connection on: " . $_SERVER["SERVER_NAME"] . "
					  Connection parameters: " . json_encode($db_connection_params) . "
					  Error Message: " . $e);
			}
			if(isset($db_connection_params["die_on_connection_failure"]) && $db_connection_params["die_on_connection_failure"]){
				die($db_connection_params["connection_error_message"]);
			}
		}
		return $database_handle;
	}

	/**
   * Get Resource
   *
   * Establish the database resource.
   *
   * @return    resource    The database connection resource
   */
	public function get_resource(){
		return $this->db;
	}

	/**
   * Close Connection
   *
   * Set the database resource to null.
   *
   * @return    resource    The database connection resource
   */
	public function close_connection(){
		$this->db = null;
	}
}
?>