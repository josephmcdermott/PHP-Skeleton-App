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
 * Register Account
 *
 * Class for registering accounts.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

namespace PHPSkeleton;
use PDO;

class RegisterAccount
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
  public function __construct( $db_connection = false, $session_key = false )
  {
      if ($db_connection && is_object($db_connection)) {
          $this->db = $db_connection;
      }
      $this->session_key = $session_key;
  }

  /**
   * Account Email Exists
   *
   * Run a query to determine if a user account email exists in the database.
   *
   * @param       string $user_account_email     The data value
   * @return      false|string                   The query result
   */
  public function account_email_exists( $user_account_email )
  {
      $statement = $this->db->prepare("SELECT 
            user_account_id
            ,user_account_email
          FROM user_account
          WHERE user_account_email = :user_account_email");
      $statement->bindValue(":user_account_email", $user_account_email, PDO::PARAM_INT);
      $statement->execute();
      return $statement->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Is Registered
   *
   * Run a query to determine if a user account has been registered.
   *
   * @param       int $user_account_id           The data value
   * @return      array|bool                     The query result
   */
  public function is_registered( $user_account_id )
  {
      $statement = $this->db->prepare("SELECT user_account.acceptable_use_policy
          ,GROUP_CONCAT(user_account_groups.group_id SEPARATOR ', ') AS groups
          FROM user_account
          LEFT JOIN user_account_groups ON user_account.user_account_id = user_account_groups.user_account_id
          WHERE user_account.user_account_id = :user_account_id
          AND user_account.acceptable_use_policy = 1
          GROUP BY user_account.user_account_id
          HAVING groups != ''");
      $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
      $statement->execute();
      return $statement->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Update Emailed Hash
   *
   * Run a query to update an emailed hash.
   *
   * @param       int $user_account_id    The data value
   * @param       string $emailed_hash    The data value
   * @return      null|boolean            The query result
   */
  public function update_emailed_hash( $user_account_id = false, $emailed_hash = false )
  {
      $updated = false;
      if ($user_account_id && $emailed_hash) {
          // UPDATE the emailed_hash in the user_account table.
          $statement = $this->db->prepare("UPDATE user_account
              SET emailed_hash = :emailed_hash, active = 0
              WHERE user_account_id = :user_account_id");
          $statement->bindValue(":emailed_hash", $emailed_hash, PDO::PARAM_STR);
          $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
          $statement->execute();
          $error = $this->db->errorInfo();
          if ($error[0] != "00000") {
              die('The UPDATE user_account emailed_hash query failed.');
          }
          $updated = true;
      }
      return $updated;
  }

  /**
   * Update Password
   *
   * Run a query to update a password.
   *
   * @param       int $user_account_password    The data value
   * @param       int $user_account_id          The data value
   * @param       string $emailed_hash          The data value
   * @return      null|boolean                  True/False
   */
  public function update_password( $user_account_password = false, $user_account_id = false, $emailed_hash = false )
  {
      $updated = false;
      if ($user_account_id && $emailed_hash) {
          // UPDATE the emailed_hash in the user_account table.
          $statement = $this->db->prepare("UPDATE user_account
              SET user_account_password = :user_account_password, active = 1
              WHERE user_account_id = :user_account_id
              AND emailed_hash = :emailed_hash");
          $statement->bindValue(":user_account_password", $user_account_password, PDO::PARAM_STR);
          $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
          $statement->bindValue(":emailed_hash", $emailed_hash, PDO::PARAM_STR);
          $statement->execute();
          $error = $this->db->errorInfo();
          if ($error[0] != "00000") {
              die('The UPDATE user_account password query failed.');
          }
          $updated = true;
      }
      return $updated;
  }

}
