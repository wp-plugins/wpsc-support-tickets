<?php
error_reporting(0);

if (!function_exists('add_action'))
{
    require_once("../../../../wp-config.php");
}


global $current_user, $wpdb;

wp_get_current_user();
if ( 0 == $current_user->ID ) {
    // Not logged in.
} else {

    if (function_exists('current_user_can') && !current_user_can('manage_wpsct_support_tickets') ) {
        die(__('Unable to Authenticate', 'wpsc-support-tickets'));
    }


    $table_name = $wpdb->prefix . "wpstorecart_meta";

    // Grab the sort order
    $ordernum = 1;
    $sortorder = $_POST['requiredinfo'];
    foreach ($sortorder as $sort) {
        $updateSQL = "UPDATE  `{$table_name}` SET  `foreignkey` =  '{$ordernum}' WHERE  `primkey` ={$sort};";
        $results = $wpdb->query($updateSQL);
        $ordernum++;
    }

}

?>