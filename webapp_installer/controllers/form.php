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
 * Form
 *
 * Controller for the Web App Installer module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

function form()
{
    $app = \Slim\Slim::getInstance();
    $env = $app->environment();
    $final_global_template_vars = $app->config('final_global_template_vars');

  // Redirect to the installer if database variables aren't present, and if we aren't already there.
  if (
    isset($final_global_template_vars["db_connection"]["name"]) &&
    isset($final_global_template_vars["db_connection"]["host"]) &&
    isset($final_global_template_vars["db_connection"]["user"]) &&
    isset($final_global_template_vars["db_connection"]["password"]) &&
    $_SERVER["REQUEST_URI"] == "/webapp_installer/"
  ) {
      header("Location: ".$final_global_template_vars["login_url"]."/");
      exit;
  }

    require_once $_SERVER["PATH_TO_VENDOR"] . "wixel/gump/gump.class.php";
    $gump = new GUMP();

    $data = $posted_data = $app->request()->post() ? $app->request()->post() : false;

  // GUMP validation rules
  $rules = array(
    "user_account_email" => "required"
    ,"user_account_password" => "required"
    ,"first_name" => "required"
    ,"last_name" => "required"
    ,"application_name" => "required"
    ,"session_key" => "required"
    ,"cname" => "required"
    // ,"http_mode" => "required"
    ,"database_host" => "required"
    ,"database_name" => "required"
    ,"database_username" => "required"
    ,"database_password" => "required"
  );

  // Validation using GUMP
  if ($posted_data) {
      $validated = array();
      $errors = array();
      $validated = $gump->validate($posted_data, $rules);
      if ($validated !== true) {
          $errors = \phpskeleton\models\utility::gump_parse_errors($validated);
      }
      if ($errors) {
          $env = $app->environment();
          $env["default_validation_errors"] = $errors;
      }
  }

    $default_validation_errors = isset($env["default_validation_errors"]) ? $env["default_validation_errors"] : false;

  // If there are no errors, begin the second round of checks
  if (!$default_validation_errors && $posted_data) {
      // Check to see if the database user exists
    $link = @mysqli_connect($posted_data['database_host'], $posted_data['database_username'], $posted_data['database_password']);
      if (!$link) {
          // die('Could not connect to the database. Please check your parameters.');
      $app->flash('message', 'Could not connect to the database. Please check your parameters.');
          $app->redirect($final_global_template_vars["path_to_this_module"]);
      }
    // Next, check to see if the database exists by making $posted_data['database_name'] the current db
    $db_selected = mysqli_select_db($link, $posted_data['database_name']);
      if (!$db_selected) {
          // die('Cannot use the "'.$posted_data['database_name'].'" database. Does it exist?');
      $app->flash('message', 'Cannot use the "'.$posted_data['database_name'].'" database. Does it exist?');
          $app->redirect($final_global_template_vars["path_to_this_module"]);
      }

    // If there are no MYSQL errors, overwrite the default_global_settings.php file
    $file_name = "default_global_settings.php";
      $original_file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$file_name);
      $parsed = str_replace('#~site_name~#', '"site_name" => "'.$posted_data['application_name'].'",', $original_file);
      $parsed = str_replace('#~session_key~#', ',"session_key" => "'.$posted_data['session_key'].'"', $parsed);
      $parsed = str_replace('#~name~#', '"name" => ($_SERVER["IS_DEV"] == "true") ? "'.$posted_data['database_name'].'" : "'.$posted_data['database_name'].'"', $parsed);
      $parsed = str_replace('#~host~#', ',"host" => "'.$posted_data['database_host'].'"', $parsed);
      $parsed = str_replace('#~user~#', ',"user" => "'.$posted_data['database_username'].'"', $parsed);
      $parsed = str_replace('#~password~#', ',"password" => "'.$posted_data['database_password'].'",', $parsed);
      $parsed = str_replace('#~admin_emails~#', ',"admin_emails" => "'.$posted_data['user_account_email'].'",', $parsed);
      unlink($_SERVER['DOCUMENT_ROOT'].'/'.$file_name);
      $file_handle = fopen($_SERVER['DOCUMENT_ROOT'].'/'.$file_name, 'w') or die("can't open file");
      fwrite($file_handle, $parsed);
      fclose($file_handle);
      chmod($_SERVER['DOCUMENT_ROOT'].'/'.$file_name, 0664);

    // Overwrite the .htaccess file
    $file_name = ".htaccess";
      $original_file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$file_name);
      $parsed = str_replace('"^([^\.]*)\.com$"', $posted_data['cname'], $original_file);
      unlink($_SERVER['DOCUMENT_ROOT'].'/'.$file_name);
      $file_handle = fopen($_SERVER['DOCUMENT_ROOT'].'/'.$file_name, 'w') or die("can't open file");
      fwrite($file_handle, $parsed);
      fclose($file_handle);
      chmod($_SERVER['DOCUMENT_ROOT'].'/'.$file_name, 0664);

    // Build the database tables
    $db_vars = array(
      "name" => $posted_data['database_name']
      ,"host" => $posted_data['database_host']
      ,"user" => $posted_data['database_username']
      ,"password" => $posted_data['database_password']
    );

      $db_conn = new \PHPSkeleton\models\db($db_vars);
      $db = $db_conn->get_resource();

      require_once $final_global_template_vars["default_module_list"]["authenticate"]["absolute_path_to_this_module"] . "/models/authenticate.class.php";
      $authenticate = new \PHPSkeleton\Authenticate($db, $final_global_template_vars["session_key"]);

      $statement = $db->prepare("CREATE TABLE `user_account` (
      `user_account_id` int(10) NOT NULL AUTO_INCREMENT,
      `user_account_email` varchar(255) NOT NULL,
      `user_account_password` varchar(255) NOT NULL,
      `first_name` varchar(255) NOT NULL,
      `last_name` varchar(255) NOT NULL,
      `acceptable_use_policy` int(1) DEFAULT NULL,
      `active` int(1) NOT NULL DEFAULT '0',
      `emailed_hash` varchar(255) DEFAULT NULL,
      `created_date` datetime DEFAULT NULL,
      `modified_date` datetime DEFAULT NULL,
      PRIMARY KEY (`user_account_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table stores user accounts'");
      $statement->execute();
      $error = $db->errorInfo();
      if ($error[0] != "00000") {
          var_dump($db->errorInfo());
          die('CREATE TABLE `user_account` failed.');
      }

    // INSERT this user into the user_account table
    $statement = $db->prepare("INSERT INTO user_account
      (user_account_email, user_account_password, first_name, last_name, acceptable_use_policy, created_date, active)
      VALUES ( :user_account_email, :user_account_password, :first_name, :last_name, 1, NOW(), 1 )");
      $statement->bindValue(":user_account_email", $posted_data['user_account_email'], PDO::PARAM_STR);
      $statement->bindValue(":user_account_password", $authenticate->generate_hashed_password($posted_data['user_account_password']), PDO::PARAM_STR);
      $statement->bindValue(":first_name", $posted_data['first_name'], PDO::PARAM_STR);
      $statement->bindValue(":last_name", $posted_data['last_name'], PDO::PARAM_STR);
      $statement->execute();
      $error = $db->errorInfo();
      if ($error[0] != "00000") {
          var_dump($db->errorInfo());
          die('The INSERT INTO user_account failed.');
      }
      $last_inserted_user_account_id = $db->lastInsertId();

      $statement = $db->prepare("CREATE TABLE `user_account_addresses` (
      `user_account_addresses_id` int(11) NOT NULL AUTO_INCREMENT,
      `user_account_id` int(11) NOT NULL,
      `address_label` varchar(100) NOT NULL DEFAULT '',
      `address_1` varchar(50) DEFAULT NULL,
      `address_2` varchar(50) DEFAULT NULL,
      `city` varchar(50) NOT NULL DEFAULT '',
      `state` char(2) NOT NULL DEFAULT '',
      `zip` varchar(10) NOT NULL,
      `date_created` datetime NOT NULL,
      `created_by_user_account_id` int(11) NOT NULL,
      `last_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
      `last_modified_user_account_id` int(11) NOT NULL,
      `primary` tinyint(1) NOT NULL DEFAULT '0',
      `active` tinyint(1) NOT NULL DEFAULT '1',
      PRIMARY KEY (`user_account_addresses_id`),
      KEY `created_by_user_account_id` (`created_by_user_account_id`),
      KEY `last_modified_user_account_id` (`last_modified_user_account_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table stores user account addresses'");
      $statement->execute();
      $error = $db->errorInfo();
      if ($error[0] != "00000") {
          var_dump($db->errorInfo());
          die('CREATE TABLE `user_account_addresses` failed.');
      }

      $statement = $db->prepare("CREATE TABLE `group` (
      `group_id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL DEFAULT '',
      `abbreviation` varchar(10) NOT NULL DEFAULT '',
      `description` mediumtext NOT NULL,
      `address_1` varchar(50) DEFAULT NULL,
      `address_2` varchar(50) DEFAULT NULL,
      `city` varchar(50) NOT NULL DEFAULT '',
      `state` char(2) NOT NULL DEFAULT '',
      `zip` varchar(10) NOT NULL,
      `date_created` datetime NOT NULL,
      `created_by_user_account_id` int(11) NOT NULL,
      `last_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
      `last_modified_user_account_id` int(11) NOT NULL,
      `active` tinyint(1) NOT NULL DEFAULT '1',
      PRIMARY KEY (`group_id`),
      KEY `created_by_user_account_id` (`created_by_user_account_id`),
      KEY `last_modified_user_account_id` (`last_modified_user_account_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table stores groups for user accounts'");
      $statement->execute();
      $error = $db->errorInfo();
      if ($error[0] != "00000") {
          var_dump($db->errorInfo());
          die('CREATE TABLE `group` failed.');
      }

      $statement = $db->prepare("INSERT INTO `group` (
      `group_id`
      ,`name`
      ,`abbreviation`
      ,`description`
      ,`address_1`
      ,`address_2`
      ,`city`
      ,`state`
      ,`zip`
      ,`date_created`
      ,`created_by_user_account_id`
      ,`last_modified`
      ,`last_modified_user_account_id`
      ,`active`
    )
    VALUES (1
      ,'Global Group'
      ,'GLOBAL'
      ,'Global Web App Group'
      ,'ADDRESS PLACEHOLDER'
      ,''
      ,'CITY PLACEHOLDER'
      ,'STATE PLACEHOLDER'
      ,'12345'
      ,NOW()
      ,:user_account_id
      ,NOW()
      ,:user_account_id
      ,1)
    ");
      $statement->bindValue(":user_account_id", $last_inserted_user_account_id, PDO::PARAM_INT);
      $statement->execute();
      $error = $db->errorInfo();
      if ($error[0] != "00000") {
          var_dump($db->errorInfo());
          die('The INSERT INTO `group` failed.');
      }

      $statement = $db->prepare("CREATE TABLE `group_closure_table` (
      `ancestor` int(10) NOT NULL DEFAULT '0',
      `descendant` int(10) NOT NULL DEFAULT '0',
      `pathlength` int(10) NOT NULL DEFAULT '0',
      PRIMARY KEY (`ancestor`,`descendant`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table was from the guidance of Mr. Bill Karwin'");
      $statement->execute();
      $error = $db->errorInfo();
      if ($error[0] != "00000") {
          var_dump($db->errorInfo());
          die('The CREATE TABLE `group_closure_table` failed.');
      }

      $statement = $db->prepare("INSERT INTO `group_closure_table` (
      `ancestor`
      ,`descendant`
      ,`pathlength`
    )
    VALUES (1,1,0)
    ");
      $statement->execute();
      $error = $db->errorInfo();
      if ($error[0] != "00000") {
          var_dump($db->errorInfo());
          die('The INSERT INTO `group_closure_table` failed.');
      }

      $statement = $db->prepare("CREATE TABLE `user_account_groups` (
      `role_id` int(10) NOT NULL DEFAULT '0',
      `user_account_id` int(10) NOT NULL DEFAULT '0',
      `group_id` int(10) NOT NULL DEFAULT '0',
      `user_account_groups_id` int(10) NOT NULL AUTO_INCREMENT,
      PRIMARY KEY (`user_account_groups_id`),
      KEY `role_id` (`role_id`),
      KEY `user_account_id` (`user_account_id`),
      KEY `group_id` (`group_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table stores user account groups'");
      $statement->execute();
      $error = $db->errorInfo();
      if ($error[0] != "00000") {
          var_dump($db->errorInfo());
          die('CREATE TABLE `user_account_groups` failed.');
      }

      $statement = $db->prepare("CREATE TABLE `user_account_proxy` (
      `user_account_groups_id` int(10) NOT NULL DEFAULT '0',
      `proxy_user_account_id` int(10) NOT NULL DEFAULT '0',
      PRIMARY KEY (`user_account_groups_id`,`proxy_user_account_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table stores user account proxy users'");
      $statement->execute();
      $error = $db->errorInfo();
      if ($error[0] != "00000") {
          var_dump($db->errorInfo());
          die('CREATE TABLE `user_account_proxy` failed.');
      }

      $statement = $db->prepare("CREATE TABLE `user_account_roles` (
      `role_id` int(10) NOT NULL AUTO_INCREMENT,
      `label` varchar(50) DEFAULT NULL,
      PRIMARY KEY (`role_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table stores user account roles'");
      $statement->execute();
      $error = $db->errorInfo();
      if ($error[0] != "00000") {
          var_dump($db->errorInfo());
          die('CREATE TABLE `user_account_roles` failed.');
      }

      $statement = $db->prepare("INSERT INTO `user_account_roles` (`role_id`,`label`)
      VALUES
      (1, 'Administrator'),
      (2, 'Author'),
      (3, 'Proxy'),
      (4, 'Editor'),
      (5, 'Manager'),
      (6, 'Universal Administrator')
    ");
      $statement->execute();
      $error = $db->errorInfo();
      if ($error[0] != "00000") {
          var_dump($db->errorInfo());
          die('The INSERT INTO `user_account_roles` failed.');
      }

    // INSERT this user into the user_account_groups table with "Universal Administrator" privileges
    $statement = $db->prepare("INSERT INTO user_account_groups
      (role_id, user_account_id, group_id)
      VALUES ( 6, :user_account_id, 1 ), ( 1, :user_account_id, 1 )");
      $statement->bindValue(":user_account_id", $last_inserted_user_account_id, PDO::PARAM_INT);
      $statement->execute();
      $error = $db->errorInfo();
      if ($error[0] != "00000") {
          var_dump($db->errorInfo());
          die('The INSERT INTO user_account_groups failed.');
      }

      $statement = $db->prepare("CREATE TABLE `login_attempt` (
      `login_attempt_id` int(11) NOT NULL AUTO_INCREMENT,
      `user_account_email` varchar(255) NOT NULL,
      `ip_address` varchar(255) NOT NULL DEFAULT '0',
      `result` varchar(255) DEFAULT NULL,
      `page` varchar(255) DEFAULT NULL,
      `created_date` datetime DEFAULT NULL,
      PRIMARY KEY (`login_attempt_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table is used to log login attempts'");
      $statement->execute();
      $error = $db->errorInfo();
      if ($error[0] != "00000") {
          var_dump($db->errorInfo());
          die('The CREATE TABLE `login_attempt` failed.');
      }

    // Don't return the user account password and the CSRF key value.
    unset($data['user_account_password']);
      unset($data['csrf_key']);

      $data['success_message'] = 'installed';
  }

    if (!$posted_data) {
        $data['cname'] = $_SERVER['SERVER_NAME'];
        $data['database_host'] = 'localhost';
    }

    $app->render('form.php', array(
    "page_title" => "Web Application Installer", "hide_page_header" => true, "path_to_this_module" => $final_global_template_vars["path_to_this_module"], "errors" => $default_validation_errors, "data" => $data
  ));
}
