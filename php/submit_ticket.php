<?php
global $wpsc_error_reporting;
if($wpsc_error_reporting==false) {
    error_reporting(0);
}
if (!function_exists('add_action'))
{
    require_once("../../../../wp-config.php");
}

global $current_user, $wpdb, $wpscSupportTickets, $wpStoreCart, $cart, $wpsc, $totalshippingcalculated;

$devOptions = $wpscSupportTickets->getAdminOptions();

if (session_id() == "") {@session_start();};
if(is_user_logged_in() || @isset($_SESSION['wpsc_email'])) {
    

    if(isset($wpStoreCart)) {
        $wpStoreCartdevOptions = $wpStoreCart->getAdminOptions();
    }

    if(trim($_POST['wpscst_initial_message'])=='' || trim($_POST['wpscst_title'])=='') {// No blank messages/titles allowed
        header("HTTP/1.1 301 Moved Permanently");
        header ('Location: '.get_permalink($devOptions['mainpage']));
        exit();
    } 

    $wpscst_title = base64_encode(strip_tags($_POST['wpscst_title']));
    $wpscst_initial_message = base64_encode($_POST['wpscst_initial_message']);
    $wpscst_department = base64_encode(strip_tags($_POST['wpscst_department']));
    
    // Guest additions here
    if(is_user_logged_in()) {
        $wpscst_userid = $current_user->ID;
        $wpscst_email = $current_user->user_email;
    } else {
        $wpscst_userid = 0;
        $wpscst_email = $wpdb->escape($_SESSION['wpsc_email']);      
    }

    $sql = "
    INSERT INTO `{$wpdb->prefix}wpscst_tickets` (
        `primkey`, `title`, `initial_message`, `user_id`, `email`, `assigned_to`, `severity`, `resolution`, `time_posted`, `last_updated`, `last_staff_reply`, `target_response_time`, `type`) VALUES (
            NULL,
            '{$wpscst_title}',
            '{$wpscst_initial_message}',
            '{$wpscst_userid}',
            '{$wpscst_email}',
            '0',
            'Normal',
            'Open',
            '".time()."',
            '".time()."',
            '',
            '2 days',
            '{$wpscst_department}'
        );
    ";

    $wpdb->query($sql);
    $lastID = $wpdb->insert_id;

    $to      = $wpscst_email; // Send this to the ticket creator
    $subject = $devOptions['email_new_ticket_subject'];
    $message = $devOptions['email_new_ticket_body'];
    $headers = 'From: ' . $devOptions['email'] . "\r\n" .
        'Reply-To: ' . $devOptions['email'] .  "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    @mail($to, $subject, $message, $headers);
    

    $to      = $devOptions['email']; // Send this to the admin
    $subject = __("A new support ticket was received.");
    $message = 'There is a new support ticket '.get_admin_url().'admin.php?page=wpscSupportTickets-edit&primkey='.$lastID;
    $headers = 'From: ' . $devOptions['email'] . "\r\n" .
    'Reply-To: ' . $devOptions['email'] .  "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    @mail($to, $subject, $message, $headers);

}

header("HTTP/1.1 301 Moved Permanently");
header ('Location: '.get_permalink($devOptions['mainpage']));
exit();

?>