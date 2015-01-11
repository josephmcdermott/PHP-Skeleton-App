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
 * Autoload
 *
 * Autoload for the PHP Skeleton App.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

  session_start();
  $final_global_template_vars = array(
    "is_dev" => false
  );
  if(!empty($_SERVER["IS_DEV"]) && $_SERVER["IS_DEV"] == "true"){
    $final_global_template_vars["is_dev"] = true;
    error_reporting(E_ALL);
  }
  ini_set('session.cookie_httponly',1);

  require_once $_SERVER["PATH_TO_VENDOR"] . 'slim/slim/Slim/Slim.php';
  \Slim\Slim::registerAutoloader();
  require_once $_SERVER["PATH_TO_VENDOR"] . 'slim/views/Twig.php';
  require_once $_SERVER["PATH_TO_VENDOR"] . 'default/functions/functions.php';
  require_once $_SERVER["PATH_TO_VENDOR"] . 'default/models/utility.php';

  // Added due to PHP errors - by Gor, gor@webcraftr.com, 2013-07-16
  // "PHP Strict Standards:  Non-static method phpskeleton\models\utility::subvalue_sort()
  // should not be called statically in /vendor/default/autoload.php on line 169"
  $utility = new \PHPSkeleton\models\utility();

  \phpskeleton\models\utility::include_all_files_in_directory($_SERVER["PATH_TO_VENDOR"] . "default/models");

  // Cet core settings - default settings that propogate across all sites.
  require_once $_SERVER["PATH_TO_VENDOR"] . "default/settings/settings.php";
  $final_global_template_vars = array_merge($final_global_template_vars, $default_core_settings);

  // Get the site settings (default_global_settings) if it exists.
  if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/default_global_settings.php")){
    require_once $_SERVER["DOCUMENT_ROOT"] . "/default_global_settings.php";
    $final_global_template_vars = array_merge($final_global_template_vars, $default_global_settings);
  }else{
    $default_global_settings = array();
  }

  // Determine which module we are in.
  $current_module_name = basename(dirname($_SERVER["PHP_SELF"]));
  if(!$current_module_name){
    // Find the default 'site' module used for housing resources for the entire site, 
    // as well as root level requests.
    $current_module_name = basename($final_global_template_vars["default_site_module"]);
    $current_module_location = $final_global_template_vars["default_site_module"];
  }else{
    $current_module_location = $_SERVER["DOCUMENT_ROOT"]. dirname($_SERVER["PHP_SELF"]);
  }

  $final_global_template_vars["active_module"] = $current_module_name;
  $final_global_template_vars["path_to_this_module"] = dirname($_SERVER["PHP_SELF"]);
  $final_global_template_vars["absolute_path_to_this_module"] = $current_module_location;

  if(!empty($final_global_template_vars["default_site_module"]) && $current_module_location != $final_global_template_vars["default_site_module"]){
    \phpskeleton\models\utility::include_all_files_in_directory($final_global_template_vars["default_site_module"] . '/controllers',true);
  }

  require_once $current_module_location . "/config/settings.php";
  require_once $current_module_location . "/config/includes.php";
  // Include all of the controller functions.
  \phpskeleton\models\utility::include_all_files_in_directory($current_module_location . '/controllers',true);

  // Feel free to unset keys that are in the default_global_settings/default_module_includes 
  // js and css includes if you don't want them in this module.
  $final_global_template_vars["js_includes"] = array_merge($final_global_template_vars["js_includes"], $default_module_includes["js"]);
  $final_global_template_vars["css_includes"] = array_merge($final_global_template_vars["css_includes"], $default_module_includes["css"]);
  // USE THIS VARIABLE IN YOUR ROUTES!! Do not use $default_module_settings.
  $final_global_template_vars = array_merge($final_global_template_vars, $default_module_settings);

  // Prepare the app.
  $app = new \Slim\Slim(array(
      'view' => new \Slim\Views\Twig()
      ,"templates.path" => $_SERVER["DOCUMENT_ROOT"]
      ,"mode" => (empty($final_global_template_vars["is_dev"])) ? "production" : "development"
      ,"debug" => (empty($final_global_template_vars["is_dev"])) ? false : true
      ,'log.enabled' => false
      ,'cookies.httponly' => true
  ));

  // Documentation for Slim Views and Twig integration:
  // https://github.com/codeguy/Slim-Views

  $app->view()->parserDirectory = $_SERVER["PATH_TO_VENDOR"]."twig/twig/lib/Twig";

  // Supply paths to all possible template locations.
  $app->view()->twigTemplateDirs = array(
    $current_module_location . "/templates"
    ,isset($final_global_template_vars["site_templates"]) ? $final_global_template_vars["site_templates"] : null
    ,$final_global_template_vars["core_templates"]
    ,$_SERVER["DOCUMENT_ROOT"]
  );

  $twig = $app->view->getEnvironment();

  $twig->parserOptions = array(
    'debug' => true
  );

  $twig->parserExtensions = array(
    new Twig_Extension_Debug()
  );

  // Redirect to https if we are told to.
  if(!empty($final_global_template_vars["force_ssl"])){
    force_ssl();
  }

  // Add CSRF tokens to session and make available to twig.
  if($app->request()->getMethod() == 'GET' || empty($_SESSION[$final_global_template_vars["csrf_key"]])) {
    $uuid = \phpskeleton\models\utility::gen_uuid();
    $final_global_template_vars["csrf_token"] = $uuid;
    $_SESSION[$final_global_template_vars["csrf_key"]] = $final_global_template_vars["csrf_token"];
  } else {
    $final_global_template_vars["csrf_token"] = $_SESSION[$final_global_template_vars["csrf_key"]];
  }

  // Create an array of all the modules.
  $modules_list_array = array();
  $visible_module_count = 0;

  // Need to know where all the modules are.
  if(empty($final_global_template_vars["module_locations"]) || !is_array($final_global_template_vars["module_locations"])){
    $final_global_template_vars["module_locations"] = array(dirname($current_module_location));
  }
  foreach($final_global_template_vars["module_locations"] as $single_location){

    if ($handle = opendir($single_location)) {

      while (false !== ($entry = readdir($handle))) {

        $default_module_settings = false;

        if ($entry != "." && $entry != ".." && is_dir($single_location . "/" . $entry)) {

          if(is_file($single_location . "/" . $entry . "/config/settings.php") && is_file($single_location . "/" . $entry . "/config/routes.php")) {

            // Check to see if a whitelist exists, and if so, that it is in it.
            $total_list_check = true;
            $white_list_check = true;
            $black_list_check = true;

            if(!empty($final_global_template_vars["module_whitelist"]) && !in_array($entry,$final_global_template_vars["module_whitelist"])) {
              $white_list_check = false;
            } elseif(!empty($final_global_template_vars["module_blacklist"]) && in_array($entry,$final_global_template_vars["module_blacklist"])) {
              $black_list_check = false;
            }
            if(!$white_list_check || !$black_list_check){
              $total_list_check = false;
            }

            if($total_list_check) {

              require $single_location . "/" . $entry . "/config/settings.php";

              $default_module_settings["handle"] = $entry;

              if(empty($default_module_settings["sort_order"])) {
                $default_module_settings["sort_order"] = '100';
              }

              if(!empty($default_module_settings["menu_hidden"])) {
                // The module is hidden, do not check if any pages are visible.
              } else {
                if(!empty($default_module_settings["pages"]) && is_array($default_module_settings["pages"])) {

                  $default_module_settings["menu_hidden"] = true;
                  foreach($default_module_settings["pages"] as $single_page) {
                    // Check to see if the display is callable.
                    if(isset($single_page["display"]) && is_callable($single_page["display"])) {
                      $single_page["display"] = call_user_func($single_page["display"],false);
                    }

                    // Must use isset here.
                    if(isset($single_page["display"]) && $single_page["display"] === false) {
                      // Told not to display, so do nothing.
                    } else {
                      $visible_module_count++;
                      $default_module_settings["menu_hidden"] = false;
                      break;
                    }
                  }
                } else {
                  // If there are no pages to display, don't display the menu.
                  $default_module_settings["menu_hidden"] = true;
                }
              }
              $default_module_settings["absolute_path_to_this_module"] = $single_location . "/" . $entry;
              $default_module_settings["path_to_this_module"] = str_replace($_SERVER["DOCUMENT_ROOT"],"",$default_module_settings["absolute_path_to_this_module"]);
              $modules_list_array[$entry] = array_merge($default_global_settings,$default_module_settings);
            }
          }
        }
      }
      closedir($handle);
    }
  }

  // Changed from static call to instanciated due to PHP errors - by Gor, gor@webcraftr.com, 2013-07-16
  // "PHP Strict Standards:  Non-static method phpskeleton\models\utility::subvalue_sort()
  // should not be called statically in /vendor/default/autoload.php on line 169"
  $modules_list_array = $utility->subvalue_sort($modules_list_array, 'sort_order');
  $final_global_template_vars["default_module_list"] = $modules_list_array;
  $final_global_template_vars["visible_module_count"] = $visible_module_count;

  foreach($final_global_template_vars as $var_name => $var_value){
    $twig->addGlobal($var_name, $var_value);
  }

  $twig->addGlobal("is_authenticated", (isset($final_global_template_vars["session_key"]) && isset($_SESSION[$final_global_template_vars["session_key"]])) ? true : false);
  $twig->addGlobal("session",$_SESSION);
  $twig->addGlobal("request_uri",$_SERVER["REQUEST_URI"]);

  // Define routes.
  require_once $current_module_location . "/config/routes.php";

  // Log page load.
  if(isset($final_global_template_vars["log_page_load"]) && $final_global_template_vars["log_page_load"]){
    $app->hook('slim.after', function () use ($app, $final_global_template_vars) {
      $log_params = array(
        $_SERVER["REMOTE_ADDR"]
        ,$_SERVER["HTTP_USER_AGENT"]
        ,$_SERVER["HTTP_HOST"]
        ,$_SERVER["REQUEST_URI"]
        ,isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : ""
        ,(isset($_SESSION[$final_global_template_vars["session_key"]]) && isset($_SESSION[$final_global_template_vars["session_key"]]['cn'])) ? $_SESSION[$final_global_template_vars["session_key"]]['cn'] : ""
        ,$final_global_template_vars["active_module"]
      );
      $log_db = new \PHPSkeleton\models\db($final_global_template_vars["core_framework_db"]);
      $log_db_resource = $log_db->get_resource();
      $statement = $log_db_resource->prepare("
        INSERT INTO page_load
          (ip_address
          ,http_user_agent
          ,domain
          ,page
          ,created_date
          ,referer
          ,cn
          ,module)
        VALUES
          (?,?,?,?,NOW(),?,?,?)");
      $statement->execute($log_params);
      $log_db->close_connection();
    });
  }
  
  $app->config('final_global_template_vars', $final_global_template_vars);

  // Run the app.
  $app->run();
?>