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
 * Class for the Authenticate module.
 *
 * @copyright   2014 Goran Halusa
 * @link        https://github.com/ghalusa/PHP-Skeleton-App
 * @license     https://github.com/ghalusa/PHP-Skeleton-App/wiki/License
 * @version     1.0.0
 * @package     PHP Skeleton App
 */

class Authenticate{
	private $session_key = "";
	public $db;

  public function __construct($db_connection=false,$session_key=false){
	if($db_connection && is_object($db_connection)) {
		$this->db = $db_connection;
	}
	$this->session_key = $session_key;
  }

	public function authenticate_local($username, $password) {
		$result = false;
		if($username && $password){
			$statement = $this->db->prepare("
				SELECT user_account_id, user_account_email, first_name, last_name
				FROM user_account
				WHERE user_account_email = :user_account_email
				AND user_account_password = :user_account_password
        AND active = 1
			");
			$statement->bindValue(":user_account_email", $username, PDO::PARAM_STR);
			$statement->bindValue(":user_account_password", sha1($password), PDO::PARAM_STR);
			$statement->execute();
			$result = $statement->fetch(PDO::FETCH_ASSOC);
		}
		return $result;
	}

	public function log_login_attempt( $user_account_id, $cn, $result ) {
		$statement = $this->db->prepare("
        	INSERT INTO core_framework.login_attempt
        	(user_account_id
        	,username
        	,ip_address
        	,result
        	,domain
        	,page
        	,created_date)
        	VALUES
        	(:user_account_id
			,:username
			,:ip_address
			,:result
			,:domain
			,:page
			,NOW())");
		$statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
		$statement->bindValue(":username", $cn, PDO::PARAM_STR);
		$statement->bindValue(":ip_address", $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
		$statement->bindValue(":result", $result, PDO::PARAM_STR);
		$statement->bindValue(":domain", $_SERVER['HTTP_HOST'], PDO::PARAM_STR);
		$statement->bindValue(":page", $_SERVER['REQUEST_URI'], PDO::PARAM_STR);
		$statement->execute();
	}
}

?>