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
 * Class for the Group module.
 *
 * @author Goran Halusa <gor@webcraftr.com>
 * @copyright   2014 Goran Halusa
 * @link        https://github.com/ghalusa/PHP-Skeleton-App
 * @license     https://github.com/ghalusa/PHP-Skeleton-App/wiki/License
 * @version     1.0.0
 * @package     PHP Skeleton App
 */

class Group {

  private $session_key = "";
  public $db;

  public function __construct($db_connection = false, $session_key = false) {
    if($db_connection && is_object($db_connection)) {
      $this->db = $db_connection;
    }
    $this->session_key = $session_key;
  }

  public function browse_groups(
    $sort_field = false
    ,$sort_order = 'DESC'
    ,$start_record = 0
    ,$stop_record = 20
    ,$search = false
  ) {

    $sort = "";
    $search_sql = "";
    $pdo_params = array();

    $limit_sql = " LIMIT {$start_record}, {$stop_record} ";

    if($sort_field){
      switch($sort_field){
        case 'last_modified':
          $sort = " ORDER BY group.last_modified {$sort_order} ";
          break;
        default:
          $sort = " ORDER BY {$sort_field} {$sort_order} ";
        }
    }

    if($search) {
      $pdo_params[] = '%'.$search.'%';
      $pdo_params[] = '%'.$search.'%';
      $pdo_params[] = '%'.$search.'%';
      $search_sql = "
        AND (
          `group`.name LIKE ?
          OR `group`.abbreviation LIKE ?
          OR `group`.description LIKE ?
        ) ";
    }

    $statement = $this->db->prepare("
      SELECT SQL_CALC_FOUND_ROWS
        `group`.group_id AS manage
        ,`group`.group_id
        ,`group`.name
        ,`group`.abbreviation
        ,`group`.description
        ,`group`.address_1 AS address
        ,`group`.city
        ,DATE_FORMAT(`group`.last_modified,'%m/%d/%Y') AS last_modified
        ,`group`.group_id AS DT_RowId
      FROM `group`
      WHERE `group`.active = 1
      {$search_sql}
      GROUP BY `group`.group_id
      HAVING 1=1
      {$sort}
      {$limit_sql}");
    $statement->execute( $pdo_params );
    $data["aaData"] = $statement->fetchAll(PDO::FETCH_ASSOC);

    $statement = $this->db->prepare("SELECT FOUND_ROWS()");
    $statement->execute();
    $count = $statement->fetch(PDO::FETCH_ASSOC);
    $data["iTotalRecords"] = $count["FOUND_ROWS()"];
    $data["iTotalDisplayRecords"] = $count["FOUND_ROWS()"];
    return $data;
  }

  public function delete_group($group_id) {
    $statement = $this->db->prepare("
      UPDATE `group`
      SET active = 0
      WHERE group_id = :group_id");
    $statement->bindValue(":group_id", $group_id, PDO::PARAM_INT);
    $statement->execute();
  }

  public function flatten_group_hierarchy($group_hierarchy) {
    $single_level_array = array();
    foreach($group_hierarchy as $single_node) {
      $descendants = false;
      if(isset($single_node["descendants"]) && $single_node["descendants"]){
        $descendants = $single_node["descendants"];
        unset($single_node["descendants"]);
      }
      $single_level_array[] = $single_node;
      if($descendants){
        $single_level_array = array_merge($single_level_array,$this->flatten_group_hierarchy($descendants));
      }
    }
    return $single_level_array;
  }

  public function get_descendants(&$groups, $level = 0, $indent_char = "-") {
    $level += 1;
    $indent_string = "";
    for($i=1;$i<=$level;$i++) {
      $indent_string .= $indent_char;
    }
    foreach($groups as &$single_group) {
      $statement = $this->db->prepare("
        SELECT descendant AS group_id
          ,name
          ,abbreviation
          ,'{$indent_string}' AS indent
        FROM group_closure_table
        LEFT JOIN `group` ON `group`.group_id = group_closure_table.descendant
        WHERE ancestor = :group_id
        AND ancestor != descendant
        AND pathlength = 1
        GROUP BY descendant
        ORDER BY name ASC");
      $statement->bindValue(":group_id", $single_group["group_id"], PDO::PARAM_INT);
      $statement->execute();
      $descendants = $statement->fetchAll(PDO::FETCH_ASSOC);

      if($descendants) {
        $single_group["descendants"] = $descendants;
        $this->get_descendants($single_group["descendants"],$level,$indent_char);
      }
    }
  }

  public function get_group_hierarchy($indent_char = "-") {
    // Get the root nodes.
    $statement = $this->db->prepare("
      SELECT descendant AS group_id
        ,COUNT(ancestor) AS total_parents
        ,name
        ,abbreviation
      FROM group_closure_table
      LEFT JOIN `group` ON `group`.group_id = group_closure_table.descendant
      WHERE active = 1
      GROUP BY descendant
      HAVING total_parents = 1
      ORDER BY name ASC");
    $statement->execute();
    $root_nodes = $statement->fetchAll(PDO::FETCH_ASSOC);
    $this->get_descendants($root_nodes,0,$indent_char);
    return $root_nodes;
  }

  public function get_group_record($group_id) {
    $statement = $this->db->prepare("
      SELECT *
      FROM `group`
      WHERE active = 1
      AND group_id = :group_id");
    $statement->bindValue(":group_id", $group_id, PDO::PARAM_INT);
    $statement->execute();
    $data = $statement->fetch(PDO::FETCH_ASSOC);

    // Get the parent group.
    $statement = $this->db->prepare("
      SELECT ancestor
      FROM group_closure_table
      WHERE descendant = :group_id
      AND pathlength = 1");
    $statement->bindValue(":group_id", $group_id, PDO::PARAM_INT);
    $statement->execute();
    $parent_group = $statement->fetch(PDO::FETCH_ASSOC);
    $data["group_parent"] = $parent_group["ancestor"];

    return $data;
  }

  public function get_groups($group_ids = false) {
    $pdo_params = array(
      1 //active
    );
    $group_sql = "";
    if($group_ids && is_array($group_ids)) {
      $question_marks = array();
      foreach($group_ids as $single_group_id) {
        $pdo_params[] = $single_group_id;
        $question_marks[] = "?";
      }
      $group_sql = " AND group_id IN (" . implode(",",$question_marks) . ") ";
    } elseif ($group_ids && is_numeric($group_ids)) {
      $pdo_params[] = $group_ids;
      $group_sql = " AND group_id = ? ";
    }

    $statement = $this->db->prepare("
      SELECT group_id
        ,abbreviation
        ,name
      FROM `group`
      WHERE active = ?
      {$group_sql}
      ORDER BY name");
    $statement->execute($pdo_params);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insert_update_group( $data, $group_id = false ) {

      $pdo_params = array(
        $data["name"]
        ,$data["abbreviation"]
        ,$data["description"]
        ,$data["address_1"]
        ,$data["address_2"]
        ,$data["city"]
        ,$data["state"]
        ,$data["zip"]
        ,$_SESSION[$this->session_key]["user_account_id"]
        ,1
      );

      if($group_id) {
        $pdo_params[] = $group_id;
        $statement = $this->db->prepare("
          UPDATE `group`
          SET name = ?
            ,abbreviation = ?
            ,description = ?
            ,address_1 = ?
            ,address_2 = ?
            ,city = ?
            ,state = ?
            ,zip = ?
            ,last_modified_user_account_id = ?
            ,active = ?
            ,last_modified = NOW()
          WHERE group_id = ?");
        $statement->execute($pdo_params);
      } else {
        $pdo_params[] = $_SESSION[$this->session_key]["user_account_id"];
        $statement = $this->db->prepare("
          INSERT INTO `group`
          (name
          ,abbreviation
          ,description
          ,address_1
          ,address_2
          ,city
          ,state
          ,zip
          ,last_modified_user_account_id
          ,active
          ,created_by_user_account_id
          ,last_modified
          ,date_created)
          VALUES
          (?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())");
        $statement->execute($pdo_params);
        $group_id = $this->db->lastInsertId();
      }

      // Update the groups closure table per Bill Karwin's SQL Antipatterns, Chapter 3.
      // The pathlengh column refers to the jumps in between the ancestor and descendant - 
      // self-reference = 0, first child = 1, and so forth...

      // First, check to see if we need to update or insert records.
      $group_parent = (isset($data["group_parent"]) && $data["group_parent"]) ? $data["group_parent"] : false;
      $statement = $this->db->prepare("
        SELECT *
        FROM group_closure_table
        WHERE descendant = :group_id");
      $statement->bindValue(":group_id", $group_id, PDO::PARAM_INT);
      $statement->execute();
      $closure_check = $statement->fetchAll(PDO::FETCH_ASSOC);

      if( $closure_check ) {
        // We need to move everything under it as well.
        // First, detatch the node subtree...
        $statement = $this->db->prepare("
          DELETE FROM group_closure_table
          WHERE descendant IN (
            SELECT tmpdescendant.d FROM (
              SELECT descendant AS d FROM group_closure_table WHERE ancestor = :group_id
            ) AS tmpdescendant
          )
          AND ancestor IN (
          SELECT tmpancestor.a FROM (
            SELECT ancestor AS a FROM group_closure_table WHERE descendant = :group_id2 AND ancestor != descendant
          ) AS tmpancestor
        )");
        $statement->bindValue(":group_id", $group_id, PDO::PARAM_INT);
        $statement->bindValue(":group_id2", $group_id, PDO::PARAM_INT);
        $statement->execute();

        // Now, attach the subtree under the updated group.
        $statement = $this->db->prepare("
          INSERT INTO group_closure_table
            (ancestor, descendant, pathlength)
          SELECT supertree.ancestor, subtree.descendant, subtree.pathlength+1
          FROM group_closure_table AS supertree
          CROSS JOIN group_closure_table AS subtree
          WHERE supertree.descendant = :new_parent
          AND subtree.ancestor = :group_id");
        $statement->bindValue(":new_parent", $group_parent, PDO::PARAM_INT);
        $statement->bindValue(":group_id", $group_id, PDO::PARAM_INT);
        $statement->execute();
      } else {
        // Just insert the leaf node.
        $statement = $this->db->prepare("
          INSERT INTO group_closure_table
            (ancestor, descendant, pathlength)
          SELECT gct.ancestor, :group_id, pathlength+1
          FROM group_closure_table AS gct
          WHERE gct.descendant = :parent_group
          UNION ALL
          SELECT :group_id2, :group_id3,0");
        $statement->bindValue(":group_id", $group_id, PDO::PARAM_INT);
        $statement->bindValue(":parent_group", $group_parent, PDO::PARAM_INT);
        $statement->bindValue(":group_id2", $group_id, PDO::PARAM_INT);
        $statement->bindValue(":group_id3", $group_id, PDO::PARAM_INT);
        $statement->execute();
      }

      return $group_id;
    }

  // Get all the admins and editors for supplied groups,
  // AS WELL AS the admin/editors for their parent groups.
  public function get_admin_info_from_group_list( $group_list ) {
    $statement = $this->db->prepare("
      SELECT user_account.user_account_email
          ,user_account.first_name
          ,user_account.last_name
      FROM user_account
      LEFT JOIN user_account_groups ON user_account_groups.user_account_id = user_account.user_account_id
      LEFT JOIN group_closure_table ON group_closure_table.ancestor = user_account_groups.group_id
      WHERE user_account_groups.role_id IN (4,1)
      AND (user_account_groups.group_id IN($group_list)
        OR group_closure_table.descendant IN (" . $group_list . "))
      GROUP BY user_account.user_account_email");
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }
}
?>