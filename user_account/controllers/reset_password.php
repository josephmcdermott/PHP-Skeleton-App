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
 * Reset Password
 *
 * Controller for the User Account module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

function reset_password()
{
    $app = \Slim\Slim::getInstance();
    $final_global_template_vars = $app->config('final_global_template_vars');
    require_once $final_global_template_vars["absolute_path_to_this_module"] . "/models/register_account.class.php";
    require_once $_SERVER["PATH_TO_VENDOR"] . "phpmailer/phpmailer/PHPMailerAutoload.php";
    $db_conn = new \PHPSkeleton\models\db($final_global_template_vars["db_connection"]);
    $db_resource = $db_conn->get_resource();
    $register_account = new \PHPSkeleton\RegisterAccount($db_resource, $final_global_template_vars["session_key"]);
    $mail = new PHPMailer();
    $posted_data = $app->request()->post() ? $app->request()->post() : false;
    $account_email_exists = false;

    // Is the email address in the database?
    if ($posted_data) {
        $account_email_exists = $register_account->account_email_exists($posted_data["user_account_email"]);
        if (!$account_email_exists) {
            $app->flash('message', 'The entered email address was not found in our database.');
            $app->redirect($final_global_template_vars["path_to_this_module"]."/password/");
        }
    }

    // If there are no errors, process posted data and email to user
    if ($account_email_exists && $posted_data) {

        $emailed_hash = md5(rand(0, 1000));
        // Attempt to update the emailed_hash and set account to inactive (returns boolean)
        $updated = $register_account->update_emailed_hash($account_email_exists['user_account_id'], $emailed_hash);

        if ($updated) {
            
            // Prepare the email...
            // The email subject.
            $subject = 'Reset Password';
            // The message, including the link.
            $message = '<h2>Reset Your Password</h2>
            <hr>
            <p>Please click this link to reset your password:<br />
            <a href="http://'.$_SERVER["SERVER_NAME"].'/user_account/reset/?user_account_email='.$account_email_exists['user_account_email'].'&emailed_hash='.$emailed_hash.'">http://'.$_SERVER["SERVER_NAME"].'/user_account/reset/?user_account_email='.$account_email_exists['user_account_email'].'&emailed_hash='.$emailed_hash.'</a></p>';

            // For the ability to send emails from an AWS EC2 instance...
            // If you need this functionality, you can configure the settings accordingly in /default_global_settings.php
            if ($final_global_template_vars["hosting_vendor"] && ($final_global_template_vars["hosting_vendor"] == "aws_ec2")) {

                $email = array();
                require_once $final_global_template_vars["path_to_smtp_settings"];
                // SMTP Settings
                $mail->IsSMTP();
                $mail->SMTPAuth   = $email['settings']['smtpauth'];
                $mail->SMTPSecure = $email['settings']['smtpsecure'];
                $mail->Host       = $email['settings']['host'];
                $mail->Username   = $email['settings']['username'];
                $mail->Password   = $email['settings']['password'];

            }

            // From (verified email address).
            $mail->SetFrom($final_global_template_vars["send_emails_from"], $final_global_template_vars["site_name"].' Accounts');
            // Subject
            $mail->Subject = $subject;
            // Message
            $mail->MsgHTML($message);
            // Recipient
            $mail->AddAddress($posted_data['user_account_email']);
            // Send the email.
            $mail->Send();

            $app->flash('message', 'Thank you. Further instructions are being sent to your email address.');
        }
        else
        {
            $app->flash('message', 'Processing failed.');
        }

        $app->redirect($final_global_template_vars["path_to_this_module"]."/password/");
    }
}
