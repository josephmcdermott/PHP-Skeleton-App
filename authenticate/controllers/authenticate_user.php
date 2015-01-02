<?php
/**
 * The PHP Skeleton App
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @copyright   2015 Goran Halusa
 * @link        https://github.com/ghalusa/PHP-Skeleton-App
 * @license     https://github.com/ghalusa/PHP-Skeleton-App/wiki/License
 * @version     1.0.0
 * @package     PHP Skeleton App
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Authenticate User
 *
 * Controller for the Authenticate module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       1.0.0
 */

function authenticate_user() {
  $app = \Slim\Slim::getInstance();
  $final_global_template_vars = $app->config('final_global_template_vars');
  
  require_once $_SERVER["PATH_TO_VENDOR"] . "wixel/gump/gump.class.php";
  require_once $final_global_template_vars["absolute_path_to_this_module"] . "/models/authenticate.class.php";

  $db_conn = new \slimlocal\models\db( $final_global_template_vars["db_connection"] );
  $db_resource = $db_conn->get_resource();
  $authenticate = new authenticate( $db_resource, $final_global_template_vars["session_key"] );
  $gump = new GUMP();
  $rules = array(
    "user_account_email" => "valid_email"
    ,"password" => "min_len,6"
  );

  $validated = $gump->validate( $app->request()->post(), $rules );

  if($validated === TRUE) {
    $validated = array( array("field"=>"user_account_email", "value"=>"", "rule"=>"") );
    // Query the database for the user_account_email and password.
    try {

      $local_validated = $authenticate->authenticate_local(
        $app->request()->post('user_account_email')
        ,$app->request()->post('password')
      );

    } catch (Exception $e) {
      //echo $e->getMessage();
      $local_validated = FALSE;
    }

    if($local_validated) {
      $validated = TRUE;
      session_regenerate_id();
      foreach($final_global_template_vars["auth_session_keys"] as $single_key) {
        $_SESSION[$final_global_template_vars["session_key"]][$single_key] = $local_validated[$single_key];
      }

      // $authenticate->log_login_attempt($local_validated["user_account_id"]
      //     ,$local_validated["cn"]
      //     ,1);
    }

  }

  if($validated === TRUE) {
    // show_login_form.php redirects to the redirect cookie key instead of doing it here.
  } else {
    // $authenticate->log_login_attempt(false,$app->request()->post('user_account_email'),"fail");
    $env = $app->environment();
    $env["default_validation_errors"] = $validated;
  }
}
?>