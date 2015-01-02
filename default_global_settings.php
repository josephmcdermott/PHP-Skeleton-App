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
 * Default Global Settings
 *
 * Stores configurations for the application.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       1.0.0
 */

$default_global_settings = array(
  #~site_name~#
  "site_logo" => "/path/to/site/logo"
  ,"core_type" => $_SERVER["CORE_TYPE"]
  ,"session_key" => "session_key"
  #~session_key~#
  ,"logout_url" => "/authenticate/logout"
  ,"login_url" => "/authenticate"
  ,"access_denied_url" => "/authenticate/access_denied"
  ,"log_page_load" => false
  ,"landing_page" => "/dashboard"
  ,"redirect_cookie_key" => "default_redirect"
  ,"hide_public_site" => true
  ,"google_analytics_key" => ""
  ,"module_icon_path" => "/dashboard/library/images/module_default_icon.png"
  // Are we on an AWS EC2 instance? MediaTemple? DigitalOcean? e.g. "aws_ec2" (anything goes)
  ,"hosting_vendor" => false
  // Path to PHPMailer - e.g. /vendor/phpmailer/class.phpmailer.php
  ,"path_to_phpmailer" => false
  // Path to SMTP settings - e.g. /path/to/aws_smtp_settings/settings.php
  ,"path_to_smtp_settings" => false
  // An alternative email address to use instead of the default "admin@".$_SERVER["SERVER_NAME"]
  ,"send_emails_from" => "admin@".$_SERVER["SERVER_NAME"]
  ,"db_connection" => array(
    #~name~#
    #~host~#
    #~user~#
    #~password~#
    "die_on_connection_failure" => true
    ,"connection_error_message" => "The system is currently not accessible."
    ,"email_on_connection_failure" => true
    #~admin_emails~#
  )
  ,"core_templates" => $_SERVER["PATH_TO_VENDOR"] . "default/templates/default_bootstrap"
  ,"menu_template_name" => "default_bootstrap_side_nav.html"
  ,"layout_template_name" => "default_bootstrap_admin.html"
  ,"site_templates" => $_SERVER["DOCUMENT_ROOT"] . "/site/templates"
  ,"default_site_module" => $_SERVER["DOCUMENT_ROOT"] . "/site"
  ,"site_footer" => "footer.php"
  ,"support_form_url" => false
  //
  // ,"additional_header_links" => array(
  //  "/faq" => array(
  //    "text" => "FAQ"
  //  )
  // )
  //
  // A list of all the roles a user is assigned to. This does not take into account groups.
  // If a user is an admin for two groups, and an author for another, this will be ['admin','author']. 
  // This will be used for displayed pages on the side nav.
  ,"current_user_roles_session_key" => "user_role_list"
  // When a user logs in for the first time, the default role they will be given is this.
  // Make sure that this is associative, so when module includes are merged, it will cascade properly.
  ,"default_role_id" => 2
  ,"js_includes" => array(
    // "site_js" => "/site/library/js/javascript.js"
  )
  ,"css_includes" => array(
    // "site_css" => "/site/library/css/styles.css"
  )

  /*
   * Role-based PERMISSIONS
   */

  // Groups module permissions
  ,"role_perm_browse_groups_access" => array(1,6)
  ,"role_perm_manage_groups_access" => array(1,6)

  // Accounts module permissions
  ,"role_perm_browse_accounts_access" => array(1,2,3,4,5,6)
  ,"role_perm_manage_accounts_access" => array(1,2,3,4,5,6)
  ,"role_perm_manage_all_accounts_access" => array(1,6)
  ,"role_perm_delete_user_account" => array(6)
  ,"role_perm_modify_own_account" => array(1,2,3,4,5,6)
  ,"role_perm_modify_own_groups" => array(1,6)
  ,"role_perm_assign_user_account_to_any_group" => array(1,6)
);

// Redirect to the installer if database variables aren't present, and if we aren't already there.
if(
  !isset($default_global_settings["db_connection"]["name"]) && 
  !isset($default_global_settings["db_connection"]["host"]) && 
  !isset($default_global_settings["db_connection"]["user"]) && 
  !isset($default_global_settings["db_connection"]["password"]) && 
  $_SERVER["REQUEST_URI"] !== "/webapp_installer/"
) {
  header("Location: /webapp_installer/");
  exit;
}
?>