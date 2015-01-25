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
 * Insert User Account
 *
 * Controller for the User Account module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

function insert_user_account()
{
    $app = \Slim\Slim::getInstance();
    $env = $app->environment();
    $final_global_template_vars = $app->config('final_global_template_vars');
    require_once $_SERVER["PATH_TO_VENDOR"] . "wixel/gump/gump.class.php";
    require_once $final_global_template_vars["absolute_path_to_this_module"] . "/models/user_account.class.php";
    require_once $final_global_template_vars["absolute_path_to_this_module"] . "/models/register_account.class.php";
    require_once $final_global_template_vars["default_module_list"]["authenticate"]["absolute_path_to_this_module"] . "/models/authenticate.class.php";
    require_once $_SERVER["PATH_TO_VENDOR"] . "phpmailer/phpmailer/PHPMailerAutoload.php";
    $db_conn = new \PHPSkeleton\models\db($final_global_template_vars["db_connection"]);
    $db_resource = $db_conn->get_resource();
    $useraccount = new \PHPSkeleton\UserAccount($db_resource, $final_global_template_vars["session_key"]);
    $register_account = new \PHPSkeleton\RegisterAccount($db_resource, $final_global_template_vars["session_key"]);
    $authenticate = new \PHPSkeleton\Authenticate($db_resource, $final_global_template_vars["session_key"]);
    $gump = new GUMP();
    $mail = new PHPMailer();
    $errors = false;

    $posted_data = $app->request()->post() ? $app->request()->post() : false;

    $account_email_exists = $register_account->account_email_exists($posted_data["user_account_email"]);
    if ($account_email_exists)
    {
        $app->flash('message', 'It looks like you already have an account. Email address is already in use.');
        $app->redirect($final_global_template_vars["path_to_this_module"]."/register/");
    }

    // GUMP validation rules
    $rules = array(
        "user_account_email" => "required|valid_email"
        ,"user_account_password" => "required|max_len,100|min_len,6"
        ,"first_name" => "required|alpha_numeric"
        ,"last_name" => "required|alpha_numeric"
    );

    // Validation using GUMP
    if($posted_data)
    {
        $validated = array();
        $errors = array();
        $validated = $gump->validate($posted_data, $rules);
        if($validated !== true)
        {
            $errors = \phpskeleton\models\utility::gump_parse_errors($validated);
        }
        if($errors)
        {
            $env = $app->environment();
            $env["default_validation_errors"] = $errors;
        }
    }

    $default_validation_errors = isset($env["default_validation_errors"]) ? $env["default_validation_errors"] : false;

    // If there are no errors, process posted data and email to user
    if (!$default_validation_errors && $posted_data)
    {
        $emailed_hash = md5(rand(0, 1000));

        // INSERT this user into the user_account table
        $statement = $db_resource->prepare("INSERT INTO user_account
          (user_account_email, user_account_password, first_name, last_name, acceptable_use_policy, created_date, active, emailed_hash)
          VALUES ( :user_account_email, :user_account_password, :first_name, :last_name, 1, NOW(), 0, :emailed_hash )");
        $statement->bindValue(":user_account_email", $posted_data['user_account_email'], PDO::PARAM_STR);
        $statement->bindValue(":user_account_password", $authenticate->generate_hashed_password($posted_data['user_account_password']), PDO::PARAM_STR);
        $statement->bindValue(":first_name", $posted_data['first_name'], PDO::PARAM_STR);
        $statement->bindValue(":last_name", $posted_data['last_name'], PDO::PARAM_STR);
        $statement->bindValue(":emailed_hash", $emailed_hash, PDO::PARAM_STR);
        $statement->execute();
        $error = $db_resource->errorInfo();
        if ($error[0] != "00000")
        {
            die('The INSERT INTO user_account failed.');
        }
        $last_inserted_user_account_id = $db_resource->lastInsertId();

        // INSERT this user into the user_account_groups table with "Author" privileges
        $statement = $db_resource->prepare("INSERT INTO user_account_groups
          (role_id, user_account_id, group_id)
          VALUES ( 2, :user_account_id, 1 )");
        $statement->bindValue(":user_account_id", $last_inserted_user_account_id, PDO::PARAM_INT);
        $statement->execute();
        $error = $db_resource->errorInfo();
        if ($error[0] != "00000")
        {
            die('The INSERT INTO user_account_groups failed.');
        }

        // Send emails

        // Email setup for user
        $to = $posted_data['user_account_email']; // Send email to our user
        $subject = 'Signup | Verification'; // Give the email a subject
        $message = '<h2>Hello '.$posted_data['first_name'].'!</h2>
        <p>Your account has been created, you can login with the following credentials after you have 
        activated your account by accessing the url below.</p>
        <hr>
        <p>Username: '.$posted_data['user_account_email'].'</p>
        <p>Password: (The password you submitted during the registration process.)</p>
        <hr>
        <p>Please click this link to activate your account:<br />
        <a href="http://'.$_SERVER["SERVER_NAME"].'/user_account/verify/?user_account_email='.$posted_data['user_account_email'].'&emailed_hash='.$emailed_hash.'">http://'.$_SERVER["SERVER_NAME"].'/user_account/verify/?user_account_email='.$posted_data['user_account_email'].'&emailed_hash='.$emailed_hash.'</a></p>'; // Our message above including the link

        // Email setup for Universal Administrators

        // First, get all of the "Universal Administrator" email addresses
        $admin_emails = array();
        $universal_administrator_emails = $useraccount->get_universal_administrator_emails();

        // Create a comma-delimited list of email addresses
        if(is_array($universal_administrator_emails) && !empty($universal_administrator_emails)) {
            foreach ($universal_administrator_emails as $email)
            {
                array_push($admin_emails, $email["user_account_email"]);
            }
        }

        $subject_admins = 'New User Registration'; // Give the email a subject
        $message_admins = '<h2>New User</h2>
        <p>A new user has registered.</p>
        <h3>Details</h3>
        <p>Name: '.$posted_data['first_name'].' '.$posted_data['last_name'].'</p>
        <p>Email: '.$posted_data['user_account_email'].'</p>
        <hr>
        <p><a href="http://'.$_SERVER["SERVER_NAME"].'/authenticate/">Login to administer</a></p>'; // Our message above including the link

        // For the ability to send emails from an AWS EC2 instance
        // If you need this functionality, you can configure the settings accordingly in /default_global_settings.php
        if ($final_global_template_vars["hosting_vendor"] && ($final_global_template_vars["hosting_vendor"] == "aws_ec2"))
        {
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

        // Send email to user
        $mail->SetFrom($final_global_template_vars["send_emails_from"], $final_global_template_vars["site_name"].' Accounts'); // From (verified email address)
        $mail->Subject = $subject; // Subject
        $mail->MsgHTML($message);
        $mail->AddAddress($to); // Recipient
        $mail->Send();
        $mail->ClearAllRecipients();

        // Send email to Universal Administrators
        // Subject
        $mail->Subject = $subject_admins;
        $mail->MsgHTML($message_admins);
        // Universal Admin recipients
        if(is_array($universal_administrator_emails) && !empty($universal_administrator_emails)) {
            foreach ($universal_administrator_emails as $email) {
                $mail->AddAddress($email["user_account_email"]);
            }
            $mail->Send();
            $mail->ClearAllRecipients();
        }
    }

    if (!$errors)
    {
        $app->flash('message', 'Account creation was successful. You will receive an email shortly with further instructions.');
        $app->redirect($final_global_template_vars["path_to_this_module"]."/register/");
    } 
    else
    {
        $env = $app->environment();
        $env["default_validation_errors"] = $errors;
    }
}
