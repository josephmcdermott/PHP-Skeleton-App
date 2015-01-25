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
 * Verify Email
 *
 * Controller for the User Account module.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */

function verify_email()
{
    $app = \Slim\Slim::getInstance();
    $final_global_template_vars = $app->config('final_global_template_vars');
    require_once $final_global_template_vars["absolute_path_to_this_module"] . "/models/user_account.class.php";
    $db_conn = new \PHPSkeleton\models\db($final_global_template_vars["db_connection"]);
    $db_resource = $db_conn->get_resource();
    $get_data = $app->request()->get() ? $app->request()->get() : false;
    $message = array();

    // SELECT this user from the database
    $statement = $db_resource->prepare("SELECT user_account_email
        ,first_name
        ,last_name
        ,emailed_hash
        FROM user_account
        WHERE user_account_email = :user_account_email
        AND emailed_hash = :emailed_hash
        AND active = 0");
    $statement->bindValue(":user_account_email", $get_data['user_account_email'], PDO::PARAM_STR);
    $statement->bindValue(":emailed_hash", $get_data['emailed_hash'], PDO::PARAM_STR);
    $statement->execute();
    $data = $statement->fetch(PDO::FETCH_ASSOC);
    $error = $db_resource->errorInfo();
    if ($error[0] != "00000") {
        die('The SELECT FROM user_account failed.');
    }

    if ($data) {
        // UPDATE this user account to be active
        $statement = $db_resource->prepare("UPDATE user_account
            SET active = 1
            WHERE user_account_email = :user_account_email
            AND emailed_hash = :emailed_hash");
        $statement->bindValue(":user_account_email", $get_data['user_account_email'], PDO::PARAM_STR);
        $statement->bindValue(":emailed_hash", $get_data['emailed_hash'], PDO::PARAM_STR);
        $statement->execute();
        $error = $db_resource->errorInfo();
        if ($error[0] != "00000") {
            die('The UPDATE user_account active flag.');
        }
        $message["success"] = "Email address verification was successful.";
    } else {
        $message["failed"] = "Email address verification failed. Do you already have an active account?";
    }

    $app->render('verify_email.php'
        ,array(
            "page_title" => "Email Address Verification"
            ,"hide_page_header" => true
            ,"message" => $message
        )
    );
}
