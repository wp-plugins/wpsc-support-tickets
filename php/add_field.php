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
    $createnewfieldname = $wpdb->escape($_POST['createnewfieldname']);
    $createnewfieldtype = $wpdb->escape($_POST['createnewfieldtype']);
    $createnewfieldrequired = $wpdb->escape($_POST['createnewfieldrequired']);

    $insert = "
    INSERT INTO `{$table_name}` (
    `primkey`, `value`, `type`, `foreignkey`)
    VALUES (
            NULL,
            '{$createnewfieldname}||{$createnewfieldrequired}||{$createnewfieldtype}',
            'wpst-requiredinfo',
            '0'
    );
    ";

    $results = $wpdb->query($insert);
    $lastID = $wpdb->insert_id;
    echo $lastID;

    $customfieldmc = $wpdb->escape($_POST['customfieldmc']);
    if (@isset($customfieldmc) && trim($customfieldmc)!='') {
        $insert = "
        INSERT INTO `{$table_name}` (
        `primkey`, `value`, `type`, `foreignkey`)
        VALUES (
                NULL,
                '{$customfieldmc}',
                'wpst-custom-fields-mc',
                '{$lastID}'
        );
        ";

        $results = $wpdb->query($insert);    
    }
}

?>