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
 * Update Password
 *
 * Controller for the User Account module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       1.0.0
 */

function update_password(){
  $app = \Slim\Slim::getInstance();
  $env = $app->environment();
  $final_global_template_vars = $app->config('final_global_template_vars');
  
  require_once $_SERVER["PATH_TO_VENDOR"] . "wixel/gump/gump.class.php";
  require_once $final_global_template_vars["absolute_path_to_this_module"] . "/models/user_account.class.php";
  require_once $final_global_template_vars["default_module_list"]["authenticate"]["absolute_path_to_this_module"] . "/models/authenticate.class.php";
  $db_conn = new \slimlocal\models\db( $final_global_template_vars["db_connection"] );
  $db_resource = $db_conn->get_resource();
  $useraccount = new UserAccount( $db_resource, $final_global_template_vars["session_key"] );
  $authenticate = new authenticate( $db_resource, $final_global_template_vars["session_key"] );
  $gump = new GUMP();

  $post = $app->request()->post() ? $app->request()->post() : false;

  // Is the email address in the database?
  if($post) {
    $account_email_exists = $useraccount->account_email_exists( $post["user_account_email"] );

    if(!$account_email_exists) {
      $app->flash('message', 'The entered email address was not found in our database.');
      $app->redirect($final_global_template_vars["path_to_this_module"]."/password/");
    }
  }

  $rules = array();

  if( $account_email_exists ) {
    $rules = array(
      "user_account_password" => "required|max_len,100|min_len,6"
      ,"password_check" => "required|max_len,100|min_len,6"
    );
  }

  $validated = $gump->validate($post, $rules);

  if($post["user_account_password"] != $post["password_check"]) {
    $validated_password_check = array(
      "field" => "user_account_password_check"
      ,"value" => NULL
      ,"rule" => "validate_required"
    );
    if(is_array($validated)) {
      array_push($validated, $validated_password_check);
    } else {
      $validated = array($validated_password_check);
    }
  }

  $errors = array();
  if($validated !== TRUE) {
    $errors = \slimlocal\models\utility::gump_parse_errors($validated);
  }

  if(isset($errors["user_account_password_check"])) {
    $errors["user_account_password_check"] = "Passwords did not match.";
  }

  // If there are no errors, process posted data and email to user
  if( empty($errors) && $post ) {

    // Attempt to update the user_account_password and set the account to active (returns boolean)
    $updated = $useraccount->update_password(
      $authenticate->generate_hashed_password($post["user_account_password"]),
      $account_email_exists['user_account_id'],
      $post["emailed_hash"]
    );

    if($updated) {
      // Prepare email
      $to = $account_email_exists["user_account_email"]; // Send email to our user
      $subject = 'Your Password Has Been Reset'; // Give the email a subject
      $message = '<h2>Your Password Has Been Reset</h2>
      <hr>
      <p>If you did not execute this change, please contact the site administrator as soon as possible.</p>'; // Our message above including the link

      // Send email
      // For the ability to send emails from an AWS EC2 instance
      // If you need this functionality, you can configure the settings accordingly in /default_global_settings.php
      if($final_global_template_vars["hosting_vendor"] && ($final_global_template_vars["hosting_vendor"] == "aws_ec2")) {

        // Since we're on AWS EC2, we need to use PHPMailer. Yes, it sucks.
        require_once($final_global_template_vars["path_to_phpmailer"]);
        require_once($final_global_template_vars["path_to_smtp_settings"]);
        // SMTP Settings
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth   = $email['settings']['smtpauth'];
        $mail->SMTPSecure = $email['settings']['smtpsecure'];
        $mail->Host       = $email['settings']['host'];
        $mail->Username   = $email['settings']['username'];
        $mail->Password   = $email['settings']['password'];

        $mail->SetFrom($final_global_template_vars["send_emails_from"], $final_global_template_vars["site_name"].' Accounts'); // From (verified email address)
        $mail->Subject = $subject; // Subject
        $mail->MsgHTML( $message );
        $mail->AddAddress( $post['user_account_email'] ); // Recipient
        $mail->Send(); // Send email

      } else {

        $headers = 'From:' . $final_global_template_vars["send_emails_from"] . "\r\n"; // Set from headers
        mail($to, $subject, $message, $headers); // Send email

      }

      $app->flash('message', 'Your password has been reset.');
      $app->redirect($final_global_template_vars["path_to_this_module"]."/password/");

    } else {

      $app->flash('message', 'Processing failed.');
      $app->redirect($final_global_template_vars["path_to_this_module"]."/password/");

    }

  } else {

    $app->flash('message', $errors["user_account_password"]);
    $app->redirect($final_global_template_vars["path_to_this_module"]."/reset/?user_account_email=".$account_email_exists['user_account_email']."&emailed_hash=".$post["emailed_hash"]);

  }

}
?>