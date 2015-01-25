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
 * Update Password
 *
 * Controller for the User Account module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

function update_password()
{
    $app = \Slim\Slim::getInstance();
    $final_global_template_vars = $app->config('final_global_template_vars');
    require_once $_SERVER["PATH_TO_VENDOR"] . "wixel/gump/gump.class.php";
    require_once $final_global_template_vars["absolute_path_to_this_module"] . "/models/register_account.class.php";
    require_once $final_global_template_vars["default_module_list"]["authenticate"]["absolute_path_to_this_module"] . "/models/authenticate.class.php";
    require_once $_SERVER["PATH_TO_VENDOR"] . "phpmailer/phpmailer/PHPMailerAutoload.php";
    $db_conn = new \PHPSkeleton\models\db($final_global_template_vars["db_connection"]);
    $db_resource = $db_conn->get_resource();
    $register_account = new \PHPSkeleton\RegisterAccount($db_resource, $final_global_template_vars["session_key"]);
    $authenticate = new \PHPSkeleton\Authenticate($db_resource, $final_global_template_vars["session_key"]);
    $gump = new GUMP();
    $mail = new PHPMailer();
    $post = $app->request()->post() ? $app->request()->post() : false;
    $account_email_exists = false;

    // Is the email address in the database?
    if ($post) {
        $account_email_exists = $register_account->account_email_exists($post["user_account_email"]);

        if (!$account_email_exists) {
            $app->flash('message', 'The entered email address was not found in our database.');
            $app->redirect($final_global_template_vars["path_to_this_module"]."/password/");
        }
    }

    $rules = array();

    if ($account_email_exists) {
        $rules = array(
            "user_account_password" => "required|max_len,100|min_len,6"
            ,"password_check" => "required|max_len,100|min_len,6"
        );
    }

    $validated = $gump->validate($post, $rules);

    if ($post["user_account_password"] != $post["password_check"]) {
        $validated_password_check = array(
            "field" => "user_account_password_check"
            ,"value" => null
            ,"rule" => "validate_required"
        );
        if (is_array($validated)) {
            array_push($validated, $validated_password_check);
        } else {
            $validated = array($validated_password_check);
        }
    }

    $errors = array();
    if ($validated !== true) {
        $errors = \phpskeleton\models\utility::gump_parse_errors($validated);
    }

    if (isset($errors["user_account_password_check"])) {
        $errors["user_account_password_check"] = "Passwords did not match.";
    }

    // If there are no errors, process posted data and email to user
    if (empty($errors) && $post) {
        // Attempt to update the user_account_password and set the account to active (returns boolean)
        $updated = $register_account->update_password(
            $authenticate->generate_hashed_password($post["user_account_password"]),
            $account_email_exists['user_account_id'],
            $post["emailed_hash"]
        );

        if ($updated) {
            // Prepare the email...
            // The email subject.
            $subject = 'Your Password Has Been Reset';
            // The message.
            $message = '<h2>Your Password Has Been Reset</h2>
            <hr>
            <p>If you did not execute this change, please contact the site administrator as soon as possible.</p>';

            // For the ability to send emails from an AWS EC2 instance
            // If you need this functionality, you can configure the settings accordingly in /default_global_settings.php
            if ($final_global_template_vars["hosting_vendor"] && ($final_global_template_vars["hosting_vendor"] == "aws_ec2")) {
                $email = array();
                require_once($final_global_template_vars["path_to_smtp_settings"]);
                // SMTP Settings
                $mail = new PHPMailer();
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
            $mail->MsgHTML($message);
            // Recipient
            $mail->AddAddress($post['user_account_email']);
            // Send the email.
            $mail->Send();

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
