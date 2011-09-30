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

if ( current_user_can('manage_wpsc_support_tickets')) { // admin edits such as closing tickets should happen here first:
    if(@isset($_POST['wpscst_status']) && @isset($_POST['wpscst_department']) && is_numeric($_POST['wpscst_edit_primkey'])) {
        $wpscst_department = base64_encode(strip_tags($_POST['wpscst_department']));
        $wpscst_status = $wpdb->escape($_POST['wpscst_status']);
        $primkey = intval($_POST['wpscst_edit_primkey']);
        // Update the Last Updated time stamp
        $updateSQL = "UPDATE `{$wpdb->prefix}wpscst_tickets` SET `last_updated` = '".time()."', `type`='{$wpscst_department}', `resolution`='{$wpscst_status}' WHERE `primkey` ='{$primkey}';";
        $wpdb->query($updateSQL);
    }
}

// Next we return users & admins to the last page if they submitted a blank reply
$string = trim(strip_tags(str_replace(chr(173), "", $_POST['wpscst_reply'])));
if($string=='') { // No blank replies allowed
    if($_POST['wpscst_goback']=='yes' && is_numeric($_POST['wpscst_edit_primkey']) ) {
        header("HTTP/1.1 301 Moved Permanently");
        header ('Location: '.get_admin_url().'admin.php?page=wpscSupportTickets-edit&primkey='.$_POST['wpscst_edit_primkey']);
    } else {
        header("HTTP/1.1 301 Moved Permanently");
        header ('Location: '.get_permalink($devOptions['mainpage']));
    }
    exit();
}

// If there is a reply and we're still executing code, now we'll add the reply
if(is_user_logged_in() && is_numeric($_POST['wpscst_edit_primkey'])) {

    if(isset($wpStoreCart)) {
        $wpStoreCartdevOptions = $wpStoreCart->getAdminOptions();
    }

    $primkey = intval($_POST['wpscst_edit_primkey']);

    if ( !current_user_can('manage_wpsc_support_tickets')) {
        $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$primkey}' AND `user_id`='{$current_user->ID}' LIMIT 0, 1;";
    } else {
        // This allows approved users, such as the admin, to reply to any support ticket
        $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$primkey}' LIMIT 0, 1;";
    }
    $results = $wpdb->get_results( $sql , ARRAY_A );
    if(isset($results[0])) {
            $wpscst_message = base64_encode($_POST['wpscst_reply']);

            $sql = "
            INSERT INTO `{$wpdb->prefix}wpscst_replies` (
                `primkey` ,
                `ticket_id` ,
                `user_id` ,
                `timestamp` ,
                `message`
            )
            VALUES (
                NULL , '{$primkey}', '{$current_user->ID}', '".time()."', '{$wpscst_message}'
            );
            ";

            $wpdb->query($sql);
            $lastID = $wpdb->insert_id;

            // Update the Last Updated time stamp
            $updateSQL = "UPDATE `{$wpdb->prefix}wpscst_tickets` SET `last_updated` = '".time()."' WHERE `primkey` ='{$primkey}';";
            $wpdb->query($updateSQL);

            $to      = $results[0]['email']; // Send this to the original ticket creator
            $subject = $devOptions['email_new_reply_subject'];
            $message = $devOptions['email_new_reply_body'];
            $headers = 'From: ' . $devOptions['email'] . "\r\n" .
            'Reply-To: ' . $devOptions['email'] .  "\r\n" .
            'X-Mailer: PHP/' . phpversion();
            @mail($to, $subject, $message, $headers);

            if($devOptions['email']!=$results[0]['email']) { 
                $to      = $devOptions['email']; // Send this to the admin
                $subject = __("Reply to a support ticket was received.");
                $message = 'There is a new reply on support ticket '.get_admin_url().'admin.php?page=wpscSupportTickets-edit&primkey='.$primkey.'';
                $headers = 'From: ' . $devOptions['email'] . "\r\n" .
                'Reply-To: ' . $devOptions['email'] .  "\r\n" .
                'X-Mailer: PHP/' . phpversion();
                @mail($to, $subject, $message, $headers);
            }
    }
}

if($_POST['wpscst_goback']=='yes') {
    header("HTTP/1.1 301 Moved Permanently");
    header ('Location: '.get_admin_url().'admin.php?page=wpscSupportTickets-edit&primkey='.$primkey);
} else {
    header("HTTP/1.1 301 Moved Permanently");
    header ('Location: '.get_permalink($devOptions['mainpage']));
}
exit();

?>