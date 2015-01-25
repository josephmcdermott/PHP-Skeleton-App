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
 * User Account
 *
 * Class for the User Account module, providing methods for browsing and managing users.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

namespace PHPSkeleton;
use PDO;

class UserAccount
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
  public function __construct($db_connection = false, $session_key = false)
  {
      if ($db_connection && is_object($db_connection)) {
          $this->db = $db_connection;
      }
      $this->session_key = $session_key;
  }

  /**
   * Browse User Accounts
   *
   * Run a query to retreive all users in the database.
   *
   * @param   string  $sort_field     The data value
   * @param   string  $sort_order     The data value
   * @param   int  $start_record      The data value
   * @param   int  $stop_record       The data value
   * @param   string  $search         The data value
   * @param   int  $user_account_id   The data value
   * @return  array|bool              The query result
   */
  public function browse_user_accounts(
    $sort_field = false
    ,$sort_order = 'DESC'
    ,$start_record = 0
    ,$stop_record = 20
    ,$search = false
    ,$user_account_id = false)
  {
      $sort = "";
      $search_sql = "";
      $pdo_params = array();
      $data = array();

      $limit_sql = " LIMIT {$start_record}, {$stop_record} ";

      if ($sort_field) {
          switch ($sort_field) {
          case 'last_modified':
              $sort = " ORDER BY user_account_groups.last_modified {$sort_order} ";
          break;
          default:
              $sort = " ORDER BY {$sort_field} {$sort_order} ";
        }
      }

      $and_user_account_id = $user_account_id ? " AND user_account.user_account_id = {$user_account_id} " : "";

      if ($search) {
          $pdo_params[] = '%'.$search.'%';
          $pdo_params[] = '%'.$search.'%';
          $search_sql = "
              AND (
                user_account.last_name LIKE ?
                OR user_account.first_name LIKE ?
              ) ";
      }

      $statement = $this->db->prepare("SELECT SQL_CALC_FOUND_ROWS
              user_account_groups.user_account_id AS manage
              ,user_account_groups.user_account_id
              ,CONCAT(user_account.first_name, ' ', user_account.last_name) AS name
              ,user_account.active
              ,GROUP_CONCAT(DISTINCT group.name SEPARATOR ', ') AS groups
              ,user_account_groups.user_account_id AS DT_RowId
          FROM user_account_groups
          LEFT JOIN user_account ON user_account.user_account_id = user_account_groups.user_account_id
          LEFT JOIN `group` ON `group`.group_id = user_account_groups.group_id
          WHERE 1 = 1
          {$and_user_account_id}
          {$search_sql}
          GROUP BY user_account_groups.user_account_id
          HAVING 1 = 1
          {$sort}
          {$limit_sql}");
      $statement->execute($pdo_params);
      $data["aaData"] = $statement->fetchAll(PDO::FETCH_ASSOC);

      $statement = $this->db->prepare("SELECT FOUND_ROWS()");
      $statement->execute();
      $count = $statement->fetch(PDO::FETCH_ASSOC);
      $data["iTotalRecords"] = $count["FOUND_ROWS()"];
      $data["iTotalDisplayRecords"] = $count["FOUND_ROWS()"];
      return $data;
  }

  /**
   * Get Universal Administrator Emails
   *
   * Run a query to retrieve all administrator emails from the database.
   *
   * @return      array|bool     The query result
   */
  public function get_universal_administrator_emails()
  {
      $statement = $this->db->prepare("SELECT user_account.user_account_email
          FROM user_account
          LEFT JOIN user_account_groups ON user_account_groups.user_account_id = user_account.user_account_id
          WHERE role_id = 6");
      $statement->execute();
      return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get User Account Groups
   *
   * Run a query to retrieve all of a user's groups from the database. Used by get_user_group_roles_map().
   *
   * @param       int $user_account_id    The data value
   * @return      array|bool              The query result
   */
  public function get_user_account_groups($user_account_id)
  {
      $statement = $this->db->prepare("SELECT `group`.group_id
          ,`group`.name AS group_name
          FROM user_account_groups
          LEFT JOIN `group` ON `group`.group_id = user_account_groups.group_id
          WHERE user_account_groups.user_account_id = :user_account_id
          GROUP BY `group`.group_id");
      $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
      $statement->execute();
      return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get User Group Roles
   *
   * Run a query to retrieve all of a user's group roles from the database. Used by get_user_group_roles_map().
   *
   * @param       int $user_account_id    The data value
   * @param       int $group_id           The data value
   * @return      array|bool              The query result
   */
  public function get_user_group_roles($user_account_id, $group_id)
  {
      $statement = $this->db->prepare("SELECT user_account_roles.role_id
          ,user_account_roles.label AS role_label
          FROM user_account_groups
          LEFT JOIN user_account_roles ON user_account_roles.role_id = user_account_groups.role_id
          WHERE user_account_groups.user_account_id = :user_account_id
          AND user_account_groups.group_id = :group_id");
      $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
      $statement->bindValue(":group_id", $group_id, PDO::PARAM_INT);
      $statement->execute();
      return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get Roles
   *
   * Run a query to retrieve all roles from the database.
   *
   * @param       array $exclude_ids      The array
   * @return      array|bool              The query result
   */
  public function get_roles($exclude_ids = array())
  {
      $exclude_id_sql = "";

      if (!empty($exclude_ids)) {
          $exclude_id_sql = " AND user_account_roles.role_id NOT IN (" . implode(",", $exclude_ids) . ") ";
      }

      $statement = $this->db->prepare("SELECT *
          FROM user_account_roles
          WHERE 1=1
          {$exclude_id_sql}");
      $statement->execute();
      return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get User Account Info
   *
   * Run a query to retrieve one user's account from the database.
   *
   * @param       int $user_account_id    The data value
   * @return      array|bool              The query result
   */
  public function get_user_account_info($user_account_id = false)
  {
      $statement = $this->db->prepare("SELECT user_account_email
          ,first_name
          ,last_name
          ,user_account_id
          FROM user_account
          WHERE user_account_id = :user_account_id");
      $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
      $statement->execute();
      return $statement->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Get Addresses
   *
   * Run a query to retrieve one user's addresses from the database.
   *
   * @param       int $user_account_id    The data value
   * @return      array|bool              The query result
   */
  public function get_addresses($user_account_id = false)
  {
      $statement = $this->db->prepare("SELECT *
          FROM user_account_addresses
          WHERE user_account_id = :user_account_id");
      $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
      $statement->execute();
      return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Insert Addresses
   *
   * Run a query to insert addresses into the database.
   *
   * @param       array $data                    The array
   * @param       int $user_account_id           The data value
   * @param       int $editor_user_account_id    The data value
   * @return      void
   */
  public function insert_addresses($data, $user_account_id, $editor_user_account_id)
  {
      $address_data = array();
      $address_fields = array(
          "label"
          ,"address_1"
          ,"address_2"
          ,"city"
          ,"state"
          ,"zip"
      );

      if (isset($data["address_count"])) {
          // First, delete all user's addresses.
          $statement = $this->db->prepare("
              DELETE FROM user_account_addresses
              WHERE user_account_id = :user_account_id");
          $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
          $statement->execute();

          for ($i=1; $i <= $data["address_count"]; $i++) {
              foreach ($address_fields as $field) {
                  $address_data[$field] = $data[$field][$i];
              }

              $statement = $this->db->prepare("
              INSERT INTO user_account_addresses
                  (user_account_id
                  ,address_label
                  ,address_1
                  ,address_2
                  ,city
                  ,state
                  ,zip
                  ,date_created
                  ,created_by_user_account_id
                  ,last_modified_user_account_id)
              VALUES
                  (:user_account_id
                  ,:address_label
                  ,:address_1
                  ,:address_2
                  ,:city
                  ,:state
                  ,:zip
                  ,NOW()
                  ,:editor_user_account_id
                  ,:editor_user_account_id)");
              $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
              $statement->bindValue(":address_label", $address_data["label"], PDO::PARAM_STR);
              $statement->bindValue(":address_1", $address_data["address_1"], PDO::PARAM_STR);
              $statement->bindValue(":address_2", $address_data["address_2"], PDO::PARAM_STR);
              $statement->bindValue(":city", $address_data["city"], PDO::PARAM_STR);
              $statement->bindValue(":state", $address_data["state"], PDO::PARAM_STR);
              $statement->bindValue(":zip", $address_data["zip"], PDO::PARAM_STR);
              $statement->bindValue(":editor_user_account_id", $editor_user_account_id, PDO::PARAM_INT);
              $statement->execute();
          }
      }
  }

  /**
   * Insert/Update User Account
   *
   * Run queries to insert and update user accounts in the database.
   *
   * @uses        UaserAccount::$this->delete_user_groups
   * @param       array $data                                   The array
   * @param       int $user_account_id                          The data value
   * @param       bool $update_groups                           True/False
   * @param       int $proxy_role_id                            The data value
   * @param       bool $role_perm_manage_all_accounts_access    The data value
   * @return      void
   */
  public function insert_update_user_account(
      $data
      ,$user_account_id
      ,$update_groups = true
      ,$proxy_role_id = false
      ,$role_perm_manage_all_accounts_access = false
  ) {
      // Update
      $statement = $this->db->prepare("
          UPDATE user_account
          SET user_account_email = :user_account_email
          ,first_name = :first_name
          ,last_name = :last_name
          ,modified_date = NOW()
          WHERE user_account_id = :user_account_id"
      );
      $statement->bindValue(":user_account_email", $data["user_account_email"], PDO::PARAM_STR);
      $statement->bindValue(":first_name", $data["first_name"], PDO::PARAM_STR);
      $statement->bindValue(":last_name", $data["last_name"], PDO::PARAM_STR);
      $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
      $statement->execute();
      // Update the password if user has entered one.
      if (!empty($data["user_account_password"])) {
          $statement = $this->db->prepare("
              UPDATE user_account
              SET user_account_password = :user_account_password
              ,modified_date = NOW()
              WHERE user_account_id = :user_account_id"
          );
          $statement->bindValue(":user_account_password", $data["user_account_password"], PDO::PARAM_STR);
          $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
          $statement->execute();
      }

      if ($update_groups && $role_perm_manage_all_accounts_access) {

          // Remove all groups/roles because we are going to add them all back in.
          $this->delete_user_groups($user_account_id);

          if (isset($data["group_data"]) && $data["group_data"]) {
              $group_array = array_filter(json_decode($data["group_data"], true));
              foreach ($group_array as $single_group_data) {
                  if (!empty($single_group_data) && !empty($single_group_data["roles"])) {
                      foreach ($single_group_data["roles"] as $single_role) {
                          $statement = $this->db->prepare("
                              INSERT INTO user_account_groups
                              (role_id
                              ,user_account_id
                              ,group_id)
                              VALUES
                              (:role_id
                              ,:user_account_id
                              ,:group_id)");
                          $statement->bindValue(":role_id", $single_role, PDO::PARAM_INT);
                          $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
                          $statement->bindValue(":group_id", $single_group_data["group_id"], PDO::PARAM_INT);
                          $statement->execute();

                          if ($single_role == $proxy_role_id) {
                              if (!empty($single_group_data["proxy_users"])) {
                                  $user_account_groups_id = $this->db->lastInsertId();
                                  foreach ($single_group_data["proxy_users"] as $single_proxy_user) {
                                      $statement = $this->db->prepare("
                                          INSERT INTO user_account_proxy
                                          (user_account_groups_id
                                          ,proxy_user_account_id)
                                          VALUES
                                          (:user_account_groups_id
                                          ,:proxy_user_account_id)");
                                      $statement->bindValue(":user_account_groups_id", $user_account_groups_id, PDO::PARAM_INT);
                                      $statement->bindValue(":proxy_user_account_id", $single_proxy_user["user_account_id"], PDO::PARAM_INT);
                                      $statement->execute();
                                  }
                              }
                          }
                      }
                  }
              }
          }
      }
  }

  /**
   * Find User Account
   *
   * Run a query to search the database for user accounts.
   *
   * @param       string $search                 The data value
   * @return      array|bool                     The query result
   */
  public function find_user_account( $search )
  {
      $statement = $this->db->prepare("
          SELECT CONCAT(first_name, ' ', last_name) AS displayname
              ,first_name
              ,last_name
              ,user_account_id
          FROM user_account
          WHERE first_name LIKE :search
          OR last_name LIKE :search
          LIMIT 20");
      $statement->bindValue(":search", "%".$search ."%", PDO::PARAM_STR);
      $statement->execute();
      return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Delete User Account
   *
   * Run a query to delete a user account, and groups, from the database.
   *
   * @param       int $user_account_id           The data value
   * @return      void
   */
  public function delete_user_account( $user_account_id )
  {
      // Delete the user from the user_account table.
      $statement = $this->db->prepare("DELETE FROM user_account
          WHERE user_account_id = :user_account_id");
      $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
      $statement->execute();

      // Delete the user from the user_account_groups table.
      $this->delete_user_groups( $user_account_id );
  }

  /**
   * Delete User Groups
   *
   * Run a query to delete a user account's groups from the database.
   *
   * @param       int $user_account_id           The data value
   * @return      void
   */
  public function delete_user_groups( $user_account_id )
  {
      $statement = $this->db->prepare("DELETE FROM user_account_groups
          WHERE user_account_id = :user_account_id");
      $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
      $statement->execute();
  }

  /**
   * Get User Roles List
   *
   * Run a query to retrieve a user account's roles.
   *
   * @param       int $user_account_id           The data value
   * @return      array|bool                     The query result
   */
  public function get_user_roles_list( $user_account_id )
  {
      $statement = $this->db->prepare("SELECT DISTINCT role_id
          FROM user_account_groups
          WHERE user_account_id = :user_account_id");
      $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
      $statement->execute();
      return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Update Acceptable Use Policy
   *
   * Run a query to update a user account's acceptable use policy.
   *
   * @param       int $user_account_id    The data value
   * @param       int $value              The data value
   * @return      void
   */
  public function update_acceptable_use_policy( $user_account_id, $value )
  {
      $statement = $this->db->prepare("UPDATE user_account
          SET acceptable_use_policy = :acceptable_use_policy
          WHERE user_account_id = :user_account_id");
      $statement->bindValue(":acceptable_use_policy", $value, PDO::PARAM_INT);
      $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
      $statement->execute();
  }

  /**
   * Get User's Proxies For Groups
   *
   * Run a query to retrieve all the proxies that the user is associated with for a specific group.
   *
   * @param       int $user_account_id    The data value
   * @param       int $group_id           The data value
   * @return      array|bool              The query result
   */
  public function get_users_proxies_for_group( $user_account_id, $group_id )
  {
      $statement = $this->db->prepare("SELECT 
              CONCAT(user_account.first_name, ' ', user_account.last_name) AS displayname
              ,user_account.user_account_id
          FROM user_account_groups
          RIGHT JOIN user_account_proxy ON user_account_proxy.user_account_groups_id = user_account_groups.user_account_groups_id
          LEFT JOIN user_account ON user_account.user_account_id = user_account_proxy.proxy_user_account_id
          WHERE user_account_groups.user_account_id = :user_account_id
          AND user_account_groups.group_id = :group_id");
      $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
      $statement->bindValue(":group_id", $group_id, PDO::PARAM_INT);
      $statement->execute();
      return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get User Group Roles Map
   *
   * Run queries to retrieve a user's current group values.
   *
   * @uses        UserAccount::$this->get_user_account_groups
   * @uses        UserAccount::$this->get_user_group_roles
   * @uses        UserAccount::$this->get_users_proxies_for_group
   * @param       int $user_account_id    The data value
   * @param       int $proxy_id           The data value
   * @return      array|bool              The query result
   */
  public function get_user_group_roles_map( $user_account_id, $proxy_id = false )
  {
      $current_group_values = $this->get_user_account_groups($user_account_id);

      if(is_array($current_group_values) && !empty($current_group_values)) {
          foreach ($current_group_values as $index => $single_group) {
              $roles_array = array();
              $selected_roles = $this->get_user_group_roles($user_account_id, $single_group["group_id"]);
              $proxy_users = array();
              if(is_array($selected_roles) && !empty($selected_roles)) {
                  foreach ($selected_roles as $single_role) {
                      $roles_array[] = $single_role["role_id"];
                      if (!empty($proxy_id) && $single_role["role_id"] == $proxy_id) {
                          $proxy_users = $this->get_users_proxies_for_group($user_account_id, $single_group["group_id"]);
                      }
                  }
              }
              $current_group_values[$index]["roles"] = $roles_array;
              $current_group_values[$index]["proxy_users"] = $proxy_users;
          }
      }
      return $current_group_values;
  }

  /**
   * Has Role
   *
   * Run a query to determine if a user has a role.
   * If assigned a role for a group, that role applies to all of that group's decendants.
   *
   * @param       int $user_account_id    The data value
   * @param       array $roles            The data value
   * @param       int $group_id           The data value
   * @return      array|bool              The query result
   */
  public function has_role( $user_account_id, $roles = array(), $group_id = false )
  {
      $statement = $this->db->prepare("SELECT ancestor
          FROM group_closure_table
          LEFT JOIN user_account_groups ON user_account_groups.group_id = group_closure_table.ancestor
          WHERE descendant = :group_id
          AND user_account_groups.role_id IN (" . implode(",", $roles) . ")
          AND user_account_groups.user_account_id = :user_account_id");
      $statement->bindValue(":group_id", $group_id, PDO::PARAM_INT);
      $statement->bindValue(":user_account_id", $user_account_id, PDO::PARAM_INT);
      $statement->execute();
      return $statement->fetchAll(PDO::FETCH_ASSOC);
  }
  
}
