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

if(is_user_logged_in() && is_numeric($_POST['primkey'])) {

    if(isset($wpStoreCart)) {
        $wpStoreCartdevOptions = $wpStoreCart->getAdminOptions();
    }

    $primkey = intval($_POST['primkey']);

    $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$primkey}' AND `user_id`='{$current_user->ID}' LIMIT 0, 1;";
    $results = $wpdb->get_results( $sql , ARRAY_A );
    if(isset($results[0])) {
        echo '<div id="wpscst_meta"><strong>'.base64_decode($results[0]['title']).'</strong> ('.$results[0]['resolution'].' - '.base64_decode($results[0]['type']).')</div>';
        echo '<table style="width:100%;">';
        echo '<thead><tr><th id="wpscst_results_posted_by">'.__('Posted by').' '.$current_user->display_name.' (<span id="wpscst_results_time_posted">'.date('Y-m-d g:i A',$results[0]['time_posted']).'</span>)</th></tr></thead>';
        echo '<tbody><tr><td id="wpscst_results_initial_message"><br />'.strip_tags(base64_decode($results[0]['initial_message']),'<p><br><a><br><strong><b><u><ul><li><strike><sub><sup><img><font>').'</td></tr>';
        echo '</tbody></table>';

        $results = NULL;
        $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_replies` WHERE `ticket_id`='{$primkey}' ORDER BY `timestamp` ASC;";
        $result2 = $wpdb->get_results( $sql , ARRAY_A );
        if(isset($result2)) {
            foreach ($result2 as $results) {
                $user=get_userdata($results['user_id']);
                $userdata = new WP_User($results['user_id']);
                $classModifier1 = NULL;$classModifier2 = NULL;$classModifier3 = NULL;
                if ( $userdata->has_cap('manage_wpsc_support_tickets') ) {
                    $classModifier1 = ' class="wpscst_staff_reply_table" ';
                    $classModifier2 = ' class="wpscst_staff_reply_thead" ';
                    $classModifier3 = ' class="wpscst_staff_reply_tbody" ';
                }
                echo '<br /><table style="width:100%;" '.$classModifier1.'>';
                echo '<thead '.$classModifier2.'><tr><th class="wpscst_results_posted_by">'.__('Posted by').' '.$user->user_nicename.' (<span class="wpscst_results_timestamp">'.date('Y-m-d g:i A',$results['timestamp']).'</span>)</th></tr></thead>';
                echo '<tbody '.$classModifier3.'><tr><td class="wpscst_results_message"><br />'.strip_tags(base64_decode($results['message']),'<p><br><a><br><strong><b><u><ul><li><strike><sub><sup><img><font>').'</td></tr>';
                echo '</tbody></table>';
            }
        }
    }
}

exit();

?>