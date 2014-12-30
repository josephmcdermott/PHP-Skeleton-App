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
 * Controller for the User Account module.
 *
 * @author Goran Halusa <gor@webcraftr.com>
 * @copyright   2014 Goran Halusa
 * @link        https://github.com/ghalusa/PHP-Skeleton-App
 * @license     https://github.com/ghalusa/PHP-Skeleton-App/wiki/License
 * @version     1.0.0
 * @package     PHP Skeleton App
 */

function reset_password(){
  $app = \Slim\Slim::getInstance();
  $env = $app->environment();
  global $final_global_template_vars;
  require_once $final_global_template_vars["absolute_path_to_this_module"] . "/models/user_account.class.php";
  $db_conn = new \slimlocal\models\db( $final_global_template_vars["db_connection"] );
  $db_resource = $db_conn->get_resource();
  $useraccount = new UserAccount( $db_resource, $final_global_template_vars["session_key"] );

  $posted_data = $app->request()->post() ? $app->request()->post() : false;

  // Is the email address in the database?
  if($posted_data) {
    $account_email_exists = $useraccount->account_email_exists( $posted_data["user_account_email"] );

    if(!$account_email_exists) {
      $app->flash('message', 'The entered email address was not found in our database.');
      $app->redirect($final_global_template_vars["path_to_this_module"]."/password/");
    }
  }

  // If there are no errors, process posted data and email to user
  if( $account_email_exists && $posted_data ) {

    $emailed_hash = md5( rand(0,1000) );

    // Attempt to update the emailed_hash and set account to inactive (returns boolean)
    $updated = $useraccount->update_emailed_hash( $account_email_exists['user_account_id'], $emailed_hash );

    if($updated) {
      // Prepare email
      $to = $account_email_exists["user_account_email"]; // Send email to our user
      $subject = 'Reset Password'; // Give the email a subject
      $message = '<h2>Reset Your Password</h2>
      <hr>
      <p>Please click this link to reset your password:<br />
      <a href="http://'.$_SERVER["SERVER_NAME"].'/user_account/reset/?user_account_email='.$account_email_exists['user_account_email'].'&emailed_hash='.$emailed_hash.'">http://'.$_SERVER["SERVER_NAME"].'/user_account/reset/?user_account_email='.$account_email_exists['user_account_email'].'&emailed_hash='.$emailed_hash.'</a></p>'; // Our message above including the link

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
        $mail->AddAddress( $posted_data['user_account_email'] ); // Recipient
        $mail->Send(); // Send email

      } else {

        $headers = 'From:' . $final_global_template_vars["send_emails_from"] . "\r\n"; // Set from headers
        mail($to, $subject, $message, $headers); // Send email

      }

      $app->flash('message', 'Thank you. Further instructions are being sent to your email address.');

    } else {

      $app->flash('message', 'Processing failed.');

    }

    $app->redirect($final_global_template_vars["path_to_this_module"]."/password/");
  }

}
?>