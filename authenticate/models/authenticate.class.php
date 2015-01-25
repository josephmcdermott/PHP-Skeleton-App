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

namespace PHPSkeleton;
use PDO;

/**
 * Authenticate
 *
 * Class for the Authenticate module, providing methods for authentication.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

class Authenticate
{
  /**
   * @var string|bool  $session_key    The session key
   */
  private $session_key = false;

  /**
   * @var object  $db   The database connection object
   */
  public $db;

  /**
   * Constructor
   * @param object   $db_connection   The database connection object
   * @param string   $session_key     The session key
   */
  public function __construct($db_connection=false, $session_key=false)
  {
      if ($db_connection && is_object($db_connection)) {
          $this->db = $db_connection;
      }
      $this->session_key = $session_key;
  }

  /**
   * Generate Hash
   *
   * Hash a password using BCrypt as the hashing technique.
   *
   * @param       string $password    The data value
   * @return      string
   */
  public function generate_hashed_password($password)
  {
      if (defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) {
          $salt = '$2y$11$' . substr(md5(uniqid(rand(), true)), 0, 22);
          return crypt($password, $salt);
      }
  }

  /**
   * Verify Hashed Password
   *
   * Verify a hashed password.
   *
   * @param   string $password        The data value
   * @param   string $hashedPassword  The data value
   * @return  bool
   */
  private function verify_hashed_password($password, $hashedPassword)
  {
      return crypt($password, $hashedPassword) == $hashedPassword;
  }

  /**
   * Authenticate Local
   *
   * Run a query to find an active local user account.
   *
   * @param       string $username     The data value
   * @param       string $password     The data value
   * @return      array|bool           The query result
   */
  public function authenticate_local($username, $password)
  {
      $result = false;
      if ($username && $password) {
          $statement = $this->db->prepare("
        SELECT
           user_account_id
          ,user_account_email
          ,user_account_password
          ,first_name
          ,last_name
        FROM user_account
        WHERE user_account_email = :user_account_email
        AND active = 1
      ");
          $statement->bindValue(":user_account_email", $username, PDO::PARAM_STR);
          $statement->execute();
          $data = $statement->fetch(PDO::FETCH_ASSOC);

          $result = $this->verify_hashed_password($password, $data["user_account_password"]) ? $data : false;
          unset($result["user_account_password"]);
      }
      return $result;
  }

  /**
   * Log Login Attempt
   *
   * Run a query to insert a login attempt.
   *
   * @param string $user_account_email The data value
   * @param string $result The data value
   * @return void
   */
  public function log_login_attempt($user_account_email, $result)
  {
      $statement = $this->db->prepare("
      INSERT INTO login_attempt
        (user_account_email
        ,ip_address
        ,result
        ,page
        ,created_date)
      VALUES
        (:user_account_email
        ,:ip_address
        ,:result
        ,:page
        ,NOW())
    ");
      $statement->bindValue(":user_account_email", $user_account_email, PDO::PARAM_STR);
      $statement->bindValue(":ip_address", $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
      $statement->bindValue(":result", $result, PDO::PARAM_STR);
      $statement->bindValue(":page", $_SERVER['REQUEST_URI'], PDO::PARAM_STR);
      $statement->execute();
  }
}
