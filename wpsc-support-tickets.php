<?php
/*
Plugin Name: wpsc Support Tickets
Plugin URI: http://wpstorecart.com/wpsc-support-tickets/
Description: An open source help desk and support ticket system for Wordpress using jQuery. Easy to use for both users & admins.
Version: 0.9.3
Author: wpStoreCart, LLC
Author URI: URI: http://wpstorecart.com/
License: LGPL
*/

/*  
Copyright 2011 wpStoreCart, LLC  (email : admin@wpstorecart.com)

This library is free software; you can redistribute it and/or modify it under the terms 
of the GNU Lesser General Public License as published by the Free Software Foundation; 
either version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License along with this 
library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, 
Boston, MA 02111-1307 USA 
*/

if (file_exists(ABSPATH . 'wp-includes/pluggable.php')) {
    require_once(ABSPATH . 'wp-includes/pluggable.php');
}
			

//Global variables:
global $wpscSupportTickets, $wpscSupportTickets_version, $wpscSupportTickets_db_version, $APjavascriptQueue, $wpsc_error_reporting;
$wpscSupportTickets_version = 0.93;
$wpscSupportTickets_db_version = 0.93;
$APjavascriptQueue = NULL;
$wpsc_error_reporting = false;

// Pre-2.6 compatibility, which is actually frivilous since we use the 2.8+ widget technique
if ( ! defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

// Create the proper directory structure if it is not already created
if(!is_dir(WP_CONTENT_DIR . '/uploads/')) {
	mkdir(WP_CONTENT_DIR . '/uploads/', 0777, true);
}
if(!is_dir(WP_CONTENT_DIR . '/uploads/wpscSupportTickets/')) {
	mkdir(WP_CONTENT_DIR . '/uploads/wpscSupportTickets/', 0777, true);
}

 /**
 * ===============================================================================================================
 * Main wpscSupportTickets Class
 */	
if (!class_exists("wpscSupportTickets")) {
    class wpscSupportTickets {
        var $adminOptionsName = "wpscSupportTicketsAdminOptions";
        var $wpscstSettings = null;
		
        function wpscSupportTickets() { //constructor
            // Let's make sure the admin is always in charge
            if(is_user_logged_in()) {
                if(is_super_admin() || is_admin() ) {
                    global $wp_roles;
                    add_role( 'wpsc_support_ticket_manager', 'Support Ticket Manager', array( 'manage_wpsc_support_tickets', 'read', 'upload_files', 'publish_posts', 'edit_published_posts', 'publish_pages', 'edit_published_pages' ) );
                    $wp_roles->add_cap( 'wpsc_support_ticket_manager', 'read' );
                    $wp_roles->add_cap( 'wpsc_support_ticket_manager', 'upload_files' );
                    $wp_roles->add_cap( 'wpsc_support_ticket_manager', 'publish_pages' );
                    $wp_roles->add_cap( 'wpsc_support_ticket_manager', 'publish_posts' );
                    $wp_roles->add_cap( 'wpsc_support_ticket_manager', 'edit_published_posts' );
                    $wp_roles->add_cap( 'wpsc_support_ticket_manager', 'edit_published_pages' );
                    $wp_roles->add_cap( 'wpsc_support_ticket_manager', 'manage_wpsc_support_tickets' );
                    $wp_roles->add_cap( 'administrator', 'manage_wpsc_support_tickets' );
                }
            }
        }

	function  init() {
            $this->getAdminOptions();
        }
		
		//Returns an array of admin options
        function getAdminOptions() {

            $apAdminOptions = array('mainpage' => '',
                                    'turnon_wpscSupportTickets' => 'true',
                                    'departments' => 'Support||Billing',
                                    'email' => get_bloginfo('admin_email'),
                                    'email_new_ticket_subject' => 'Your support ticket was received.',
                                    'email_new_ticket_body' => 'Thank you for opening a new support ticket.  We will look into your issue and respond as soon as possible.',
                                    'email_new_reply_subject' => 'Your support ticket reply was received.',
                                    'email_new_reply_body' => 'A reply was posted to one of your support tickets.'
                                    );

            if($this->wpscstSettings!=NULL) {
                $devOptions = $this->wpscstSettings;
            } else {
                $devOptions = get_option($this->adminOptionsName);
            }  
            if (!empty($devOptions)) {
                foreach ($devOptions as $key => $option) {
                    $apAdminOptions[$key] = $option;
                }
            }            
            update_option($this->adminOptionsName, $apAdminOptions);
            return $apAdminOptions;
        }

        function adminHeader() {
            global $wpdb;
            $devOptions = $this->getAdminOptions();
            if ( function_exists('current_user_can') && !current_user_can('manage_wpsc_support_tickets') ) {
                    die(__('Cheatin&#8217; uh?'));
            }
            if (isset($_POST['update_wpscSupportTicketsSettings'])) {
                    if (isset($_POST['wpscSupportTicketsmainpage'])) {
                            $devOptions['mainpage'] = $wpdb->escape($_POST['wpscSupportTicketsmainpage']);
                    }
                    if (isset($_POST['turnwpscSupportTicketsOn'])) {
                            $devOptions['turnon_wpscSupportTickets'] = $wpdb->escape($_POST['turnwpscSupportTicketsOn']);
                    }
                    if (isset($_POST['departments'])) {
                            $devOptions['departments'] = $wpdb->escape($_POST['departments']);
                    }
                    if (isset($_POST['email'])) {
                            $devOptions['email'] = $wpdb->escape($_POST['email']);
                    }
                    if (isset($_POST['email_new_ticket_subject'])) {
                            $devOptions['email_new_ticket_subject'] = $wpdb->escape($_POST['email_new_ticket_subject']);
                    }
                    if (isset($_POST['email_new_ticket_body'])) {
                            $devOptions['email_new_ticket_body'] = $wpdb->escape($_POST['email_new_ticket_body']);
                    }
                    if (isset($_POST['email_new_reply_subject'])) {
                            $devOptions['email_new_reply_subject'] = $wpdb->escape($_POST['email_new_reply_subject']);
                    }
                    if (isset($_POST['email_new_reply_body'])) {
                            $devOptions['email_new_reply_body'] = $wpdb->escape($_POST['email_new_reply_body']);
                    }
                    update_option($this->adminOptionsName, $devOptions);

                    echo '<div class="updated"><p><strong>';
                    _e("Settings Updated.", "wpscSupportTickets");
                    echo '</strong></p></div>';

            }

            echo '
            <form method="post" action="'. $_SERVER["REQUEST_URI"].'">
            <div style="padding: 20px 10px 10px 10px;">
            <div style="float:left;"><img src="'.WP_PLUGIN_URL.'/wpsc-support-tickets/images/logo.png" alt="wpscSupportTickets" /></div>
            ';

            echo '
            <div class="updated" style="padding:5px 5px 0px 5px;font-size:11px;border-color:#DDD;background-color:#EFEFEF;width:450px;min-width:450px;max-width:450px;float:right;"><img src="'.plugins_url('/images/info.png' , __FILE__).'" alt="info" style="float:left;" />
            <p><strong>Main Page:</strong> You need to use a Page as the base for wpsc Support Tickets.  <br />
            <select name="wpscSupportTicketsmainpage">
             <option value="">';
              attribute_escape(__('Select page'));
              echo '</option>';

              $pages = get_pages();
              foreach ($pages as $pagg) {
                    $option = '<option value="'.$pagg->ID.'"';
                    if($pagg->ID==$devOptions['mainpage']) {
                            $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg->post_title;
                    $option .= '</option>';
                    echo $option;
              }

            echo '
            </select>

            <div id="wpscst_full_options" style="display:none;margin:0 0 0 10px;">
                <strong>Departments:</strong> Separate these values with a double pipe, like this || <br /><input name="departments" value="'.$devOptions['departments'].'" style="width:360px;" /><br /><br />

                <strong>Email:</strong> The admin email where all new ticket &amp; reply notification emails will be sent<br /><input name="email" value="'.$devOptions['email'].'" style="width:360px;" /><br /><br />

                <strong>New Ticket Email</strong> The subject &amp; body of the email sent to the customer when creating a new ticket.<br /><input name="email_new_ticket_subject" value="'.$devOptions['email_new_ticket_subject'].'" style="width:360px;" />
                <textarea style="width:390px;" name="email_new_ticket_body">'.$devOptions['email_new_ticket_body'].'</textarea>
                <br /><br />

                <strong>New Reply Email</strong> The subject &amp; body of the email sent to the customer when there is a new reply.<br /><input name="email_new_reply_subject" value="'.$devOptions['email_new_reply_subject'].'" style="width:360px;" />
                <textarea style="width:390px;" name="email_new_reply_body">'.$devOptions['email_new_reply_body'].'</textarea>
                <br /><br />
            </div>

            <input type="hidden" name="update_wpscSupportTicketsSettings" value="update" />
            <div style="float:right;margin:0px 10px 10px 10px;"><button onclick="jQuery(\'#wpscst_full_options\').fadeToggle(\'slow\');return false;" style="clear:both;float:left;" class="button-secondary">More Options</button> <input class="button-primary"  type="submit" name="update_wpscSupportTicketsSettings_submit" value="'; _e('Update Settings', 'wpscSupportTickets'); echo'" /></div>
            </form></p>
            </div>';
        }

		//Prints out the admin page ================================================================================
        function printAdminPage() {
			global $wpdb;

			$devOptions = $this->getAdminOptions();
			if ( function_exists('current_user_can') && !current_user_can('manage_wpsc_support_tickets') ) {
				die(__('Cheatin&#8217; uh?'));
			}
			echo '<div class="wrap">';

                        $this->adminHeader();

                        if(isset($_GET['resolution'])) {
                            $resolution = $wpdb->escape($_GET['resolution']);
                        } else {
                            $resolution = 'Open';
                        }

                        echo '<br style="clear:both;" /><br />';
                        echo 'Show tickets: [ <a href="'.get_admin_url().'admin.php?page=wpscSupportTickets-admin&resolution=Open">Open</a> | <a href="'.get_admin_url().'admin.php?page=wpscSupportTickets-admin&resolution=Closed">Closed</a> ] <br /> ';
                        $output .= '<div id="wpscst_edit_div">';
                        $table_name = $wpdb->prefix . "wpscst_tickets";
                        $sql = "SELECT * FROM `{$table_name}` WHERE `resolution`='{$resolution}' ORDER BY `last_updated` DESC;";
                        $results = $wpdb->get_results( $sql , ARRAY_A );
                        if(isset($results) && isset($results[0]['primkey'])) {
                            $output .= '<h3>View '.$resolution.' Tickets:</h3>';
                            $output .= '<table class="widefat" style="width:100%"><thead><tr><th>'.__('Ticket').'</th><th>'.__('Status').'</th><th>'.__('User').'</th><th>'.__('Last Reply').'</th></tr></thead><tbody>';
                            foreach($results as $result) {
                                    $user=get_userdata($result['user_id']);
                                    $output .= '<tr><td><a href="admin.php?page=wpscSupportTickets-edit&primkey='.$result['primkey'].'" style="border:none;text-decoration:none;"><img style="float:left;border:none;margin-right:5px;" src="'.plugins_url('/images/page_edit.png' , __FILE__).'" alt="'.__('View').'"  /> '.base64_decode($result['title']).'</a></td><td>'.$result['resolution'].'</td><td><a href="'.get_admin_url().'user-edit.php?user_id='.$result['user_id'].'&wp_http_referer='.urlencode(get_admin_url().'admin.php?page=wpscSupportTickets-admin').'">'.$user->user_nicename.'</a></td><td>'.date('Y-m-d g:i A',$result['last_updated']).'</td></tr>';
                            }
                            $output .= '</tbody></table>';
                        }
                        $output .= '</div>';
                        echo $output;
			echo '
			</div>';		
		
		}
		//END Prints out the admin page ================================================================================		
		
                function printAdminPageEdit() {
			global $wpdb;

			$devOptions = $this->getAdminOptions();
			if ( function_exists('current_user_can') && !current_user_can('manage_wpsc_support_tickets') && is_numeric($_GET['primkey']) ) {
				die(__('Cheatin&#8217; uh?'));
			}
			echo '<div class="wrap">';

                        $this->adminHeader();

                        echo '<br style="clear:both;" /><br />';

                        echo '

                        <script type="text/javascript">
                        /* <![CDATA[ */
                            jQuery(document).ready(function(){
                                    var myNicEditor = new nicEditor({buttonList : [\'fontSize\',\'bold\',\'italic\',\'underline\',\'strikethrough\',\'ul\', \'subscript\',\'superscript\',\'image\',\'link\',\'unlink\'], iconsPath:"'.plugins_url('/js/nicedit/nicEditorIcons.gif' , __FILE__).'"});
                                    myNicEditor.setPanel("wpscst_nic_panel");
                                    myNicEditor.addInstance("wpscst_reply");
                            });

                        /* ]]> */
                        </script>
                                ';

                        $primkey = intval($_GET['primkey']);

                        $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$primkey}' LIMIT 0, 1;";
                        $results = $wpdb->get_results( $sql , ARRAY_A );
                        if(isset($results[0])) {
                            echo '<table class="widefat"><tr><td>';
                            $user=get_userdata($results[0]['user_id']);
                            echo '<div id="wpscst_meta"><h1>'.base64_decode($results[0]['title']).'</h1> ('.$results[0]['resolution'].' - '.base64_decode($results[0]['type']).')</div>';
                            echo '<table class="widefat" style="width:100%;">';
                            echo '<thead><tr><th id="wpscst_results_posted_by">'.__('Posted by').' <a href="'.get_admin_url().'user-edit.php?user_id='.$results[0]['user_id'].'&wp_http_referer='.urlencode(get_admin_url().'admin.php?page=wpscSupportTickets-admin').'">'.$user->user_nicename.'</a> (<span id="wpscst_results_time_posted">'.date('Y-m-d g:i A',$results[0]['time_posted']).'</span>)</th></tr></thead>';
                            echo '<tbody><tr><td id="wpscst_results_initial_message"><br />'.strip_tags(base64_decode($results[0]['initial_message']),'<p><br><a><br><strong><b><u><ul><li><strike><sub><sup><img><font><span>').'</td></tr>';
                            echo '</tbody></table>';


                            $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_replies` WHERE `ticket_id`='{$primkey}' ORDER BY `timestamp` ASC;";
                            $result2 = $wpdb->get_results( $sql , ARRAY_A );
                            if(isset($result2)) {
                                foreach ($result2 as $resultsX) {
                                    $user=get_userdata($resultsX['user_id']);
                                    $userdata = new WP_User($resultsX['user_id']);
                                    $styleModifier1 = NULL;$styleModifier2 = NULL;
                                    if ( $userdata->has_cap('manage_wpsc_support_tickets') ) {
                                        $styleModifier1 = 'background:#FFF;';
                                        $styleModifier2 = 'background:#e5e7fa;" ';
                                    }
                                    echo '<br /><table class="widefat" style="width:100%;'.$styleModifier1.'">';
                                    echo '<thead><tr><th class="wpscst_results_posted_by" style="'.$styleModifier2.'">'.__('Posted by').' <a href="'.get_admin_url().'user-edit.php?user_id='.$resultsX['user_id'].'&wp_http_referer='.urlencode(get_admin_url().'admin.php?page=wpscSupportTickets-admin').'">'.$user->user_nicename.'</a> (<span class="wpscst_results_timestamp">'.date('Y-m-d g:i A',$resultsX['timestamp']).'</span>)<div style="float:right;"><a onclick="if(confirm(\'Are you sure you want to delete this reply?\')){return true;}return false;" href="'.plugins_url('/php/delete_ticket.php' , __FILE__).'?replyid='.$resultsX['primkey'].'&ticketid='.$primkey.'"><img src="'.plugins_url('/images/delete.png' , __FILE__).'" alt="delete" /> Delete Reply</a></div></th></tr></thead>';
                                    echo '<tbody><tr><td class="wpscst_results_message"><br />'.strip_tags(base64_decode($resultsX['message']),'<p><br><a><br><strong><b><u><ul><li><strike><sub><sup><img><font>').'</td></tr>';
                                    echo '</tbody></table>';
                                }
                            }
                            echo '</td></tr></table>';
                        }

                        $output .= '<form action="'.plugins_url('/php/reply_ticket.php' , __FILE__).'" method="post"><input type="hidden" name="wpscst_edit_primkey" value="'.$primkey.'" /><input type="hidden" name="wpscst_goback" value="yes" /> ';
                        $output .= '<table class="wpscst-table" style="width:100%">';
                        $output .= '<tr><td><h3>'.__('Your message').'</h3><div id="wpscst_nic_panel" style="display:block;width:100%;"></div> <textarea name="wpscst_reply" id="wpscst_reply" style="display:inline;width:100%;margin:0 auto 0 auto;" rows="5"></textarea></td></tr>';
                        $exploder = explode('||', $devOptions['departments']);

                        $output .= '<tr><td><div style="float:left;"><h3>'.__('Department').'</h3><select name="wpscst_department" id="wpscst_department">';
                        if(isset($exploder[0])) {
                            foreach($exploder as $exploded) {
                                $output .= '<option value="'.$exploded.'"'; if(base64_decode($results[0]['type'])==$exploded) {$output.= ' selected="selected" ';} $output.='>'.$exploded.'</option>';
                            }
                        }
                        $output .= '</select></div>
                        <div style="float:left;margin-left:20px;"><h3>'.__('Status').'</h3><select name="wpscst_status">
                                <option value="Open"'; if($results[0]['resolution']=='Open') {$output.= ' selected="selected" ';} $output.='>'.__('Open').'</option>
                                <option value="Closed"'; if($results[0]['resolution']=='Closed') {$output.= ' selected="selected" ';} $output.='>'.__('Closed').'</option>
                        </select></div>
                        <div style="float:left;margin-left:20px;"><h3>'.__('Actions').'</h3>
                            <a onclick="if(confirm(\'Are you sure you want to delete this ticket?\')){return true;}return false;" href="'.plugins_url('/php/delete_ticket.php' , __FILE__).'?ticketid='.$primkey.'"><img src="'.plugins_url('/images/delete.png' , __FILE__).'" alt="delete" /> Delete Ticket</a>
                        </div>
                        <button class="button-secondary" onclick="if(confirm(\'Are you sure you want to cancel?\')){window.location = \''.get_admin_url().'admin.php?page=wpscSupportTickets-admin\';}return false;"  style="float:right;" ><img style="float:left;border:none;margin-right:5px;" src="'.plugins_url('/images/stop.png' , __FILE__).'" alt="'.__('Cancel').'" /> '.__('Cancel').'</button> <button class="button-primary" type="submit" name="wpscst_submit" id="wpscst_submit" style="float:right;margin:0 5px 0 5px;"><img style="float:left;border:none;margin-right:5px;" src="'.plugins_url('/images/page_white_text.png' , __FILE__).'" alt="'.__('Update Ticket').'" /> '.__('Update Ticket').'</button></td></tr>';


                        $output .= '</table></form>';
                        echo $output;
                        
			echo '
			</div>';
                }
		
		
		
		// Dashboard widget code=======================================================================
		function wpscSupportTickets_main_dashboard_widget_function() {
			global $wpdb;
			
			$devOptions = $this->getAdminOptions();

                        $table_name = $wpdb->prefix . "wpscst_tickets";
                        $sql = "SELECT * FROM `{$table_name}` WHERE `resolution`='Open' ORDER BY `last_updated` DESC;";
                        $results = $wpdb->get_results( $sql , ARRAY_A );
                        if(isset($results) && isset($results[0]['primkey'])) {
                            $output .= '<table class="widefat" style="width:100%"><thead><tr><th>'.__('Ticket').'</th><th>'.__('Status').'</th><th>'.__('User').'</th></tr></thead><tbody>';
                            foreach($results as $result) {
                                    $user=get_userdata($result['user_id']);
                                    $output .= '<tr><td><a href="admin.php?page=wpscSupportTickets-edit&primkey='.$result['primkey'].'" style="border:none;text-decoration:none;"><img style="float:left;border:none;margin-right:5px;" src="'.plugins_url('/images/page_edit.png' , __FILE__).'" alt="'.__('View').'"  /> '.base64_decode($result['title']).'</a></td><td>'.$result['resolution'].'</td><td><a href="'.get_admin_url().'user-edit.php?user_id='.$result['user_id'].'&wp_http_referer='.urlencode(get_admin_url().'admin.php?page=wpscSupportTickets-admin').'">'.$user->user_nicename.'</a></td></tr>';
                            }
                            $output .= '</tbody></table>';
                        } else {
                            $output .= '<tr><td><i>'.__('No open tickets!').'</i></td><td></td><td></td></tr>';
                        }
                        echo $output;
		} 
		
		// Create the function use in the action hook
		function wpscSupportTickets_main_add_dashboard_widgets() {
			wp_add_dashboard_widget('wpscSupportTickets_main_dashboard_widgets', 'wpscSupportTickets Overview', array(&$this, 'wpscSupportTickets_main_dashboard_widget_function'));	
		} 
		
		
		
		function  addHeaderCode() {
			wp_enqueue_script('wpscstniceditor', plugins_url('/js/nicedit/nicEdit.js' , __FILE__), array('jquery'),'1.3.2');
                }
		
		function  addContent($content = '') {
                    $content .= "<p>wpscSupportTickets</p>";
                    return $content;
                }
				
		function loadTinyMce() {
			wp_enqueue_script('tiny_mce');
		}

		// Installation ==============================================================================================		
		function wpscSupportTickets_install() {
		   global $wpdb;
		   global $wpscSupportTickets_db_version;

		   $table_name = $wpdb->prefix . "wpscst_tickets";
		   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			  
			$sql = "
				CREATE TABLE `{$table_name}` (
				`primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
				`title` VARCHAR(512) NOT NULL, `initial_message` TEXT NOT NULL, 
				`user_id` INT NOT NULL, `email` VARCHAR(256) NOT NULL, 
				`assigned_to` INT NOT NULL DEFAULT '0', 
				`severity` VARCHAR(64) NOT NULL, 
				`resolution` VARCHAR(64) NOT NULL, 
				`time_posted` VARCHAR(128) NOT NULL, 
				`last_updated` VARCHAR(128) NOT NULL, 
				`last_staff_reply` VARCHAR(128) NOT NULL, 
				`target_response_time` VARCHAR(128) NOT NULL,
                                `type` VARCHAR( 255 ) NOT NULL
				);				
			";
			  

			  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			  dbDelta($sql);
		
		   }
		   
		   $table_name = $wpdb->prefix . "wpscst_replies";
		   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			  
			$sql = "
				CREATE TABLE `{$table_name}` (
				`primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`ticket_id` INT NOT NULL ,
				`user_id` INT NOT NULL ,
				`timestamp` VARCHAR( 128 ) NOT NULL ,
				`message` TEXT NOT NULL
				);				
			";
			  

			  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			  dbDelta($sql);
		
		   }

                   $table_name = $wpdb->prefix . "wpstorecart_meta";
                   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

                            $sql = "
                                    CREATE TABLE {$table_name} (
                                    `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                    `value` TEXT NOT NULL,
                                    `type` VARCHAR(32) NOT NULL,
                                    `foreignkey` INT NOT NULL
                                    );
                                    ";


                          dbDelta($sql);
                   }
			add_option("wpscSupportTickets_db_version", $wpscSupportTickets_db_version);		   
		}
		// END Installation ==============================================================================================
				

		// Shortcode =========================================
		function wpscSupportTickets_mainshortcode($atts) {
			global $wpdb;
			
			$table_name = $wpdb->prefix . "wpscst_tickets";
		
			$devOptions = $this->getAdminOptions();		
		
			extract(shortcode_atts(array(
				'display' => 'tickets'
			), $atts));

			$output = '';
			switch ($display) {
				case 'tickets': // =========================================================
					if(is_user_logged_in()) {
						global $current_user;

						$output .='
						<script type="text/javascript">
						/* <![CDATA[ */	
                                                    jQuery(document).ready(function(){
                                                            var myNicEditor = new nicEditor({buttonList : [\'fontSize\',\'bold\',\'italic\',\'underline\',\'strikethrough\',\'ul\', \'subscript\',\'superscript\',\'image\',\'link\',\'unlink\'], iconsPath:"'.plugins_url('/js/nicedit/nicEditorIcons.gif' , __FILE__).'"});
                                                            myNicEditor.setPanel("wpscst_nic_panel");
                                                            myNicEditor.addInstance("wpscst_initial_message");
                                                            var myNicEditor2 = new nicEditor({buttonList : [\'fontSize\',\'bold\',\'italic\',\'underline\',\'strikethrough\',\'ul\', \'subscript\',\'superscript\',\'image\',\'link\',\'unlink\'], iconsPath:"'.plugins_url('/js/nicedit/nicEditorIcons.gif' , __FILE__).'"});
                                                            myNicEditor2.setPanel("wpscst_nic_panel2");
                                                            myNicEditor2.addInstance("wpscst_reply");
                                                            jQuery(".wpscst-table").toggle();
                                                            jQuery("#wpscst_edit_ticket").toggle();
                                                    });

                                                    function loadTicket(primkey, resolution) {
                                                        jQuery(".wpscst-table").fadeOut("slow");
                                                        jQuery("#wpscst_edit_div").fadeOut("slow");
                                                        jQuery("#wpscst-new").fadeOut("slow");
                                                        jQuery("#wpscst_edit_ticket").fadeIn("slow");
                                                        jQuery("#wpscst_edit_ticket_inner").load("'.plugins_url('/php/load_ticket.php' , __FILE__).'", {"primkey":primkey});
                                                        jQuery("#wpscst_edit_primkey").val(primkey);
                                                        jQuery("html, body").animate({scrollTop: jQuery("#wpscst_top_page").offset().top}, 2000);
                                                        if(resolution=="Closed") {
                                                            jQuery("#wpscst_reply_editor_table_tr1").fadeOut("slow");
                                                            jQuery("#wpscst_submit2").fadeOut("slow");
                                                        }
                                                    }

                                                    function cancelEdit() {
                                                        jQuery("#wpscst_reply_editor_table_tr1").fadeIn("slow");
                                                        jQuery("#wpscst_submit2").fadeIn("slow");
                                                        jQuery("#wpscst_edit_div").fadeIn("slow");
                                                        jQuery("#wpscst-new").fadeIn("slow");
                                                        jQuery("#wpscst_edit_ticket").fadeOut("slow");
                                                        jQuery("#wpscst_edit_primkey").val(0);
                                                        jQuery("#wpscst_reply").html("");
                                                        jQuery(".nicEdit-main").html("");
                                                        jQuery("#wpscst_edit_ticket_inner").html(\'<center><img src="'.plugins_url('/images/loading.gif' , __FILE__).'" alt="'.__('Loading').'" /></center>\');
                                                        jQuery("html, body").animate({scrollTop: jQuery("#wpscst_top_page").offset().top}, 2000);
                                                    }

                                                    function cancelAdd() {
                                                        jQuery("#wpscst_edit_div").fadeIn("slow");
                                                        jQuery("#wpscst-new").fadeIn("slow");
                                                        jQuery(".wpscst-table").fadeOut("slow");
                                                        jQuery("html, body").animate({scrollTop: jQuery("#wpscst_top_page").offset().top}, 2000);
                                                    }
						/* ]]> */
						</script>		
							';
	
						$output .= '<div id="wpscst_top_page" style="display:inline;"></div><button class="wpscst-button" id="wpscst-new" onclick="jQuery(\'.wpscst-table\').fadeIn(\'slow\');jQuery(\'#wpscst-new\').fadeOut(\'slow\');jQuery(\'#wpscst_edit_div\').fadeOut(\'slow\');jQuery(\'html, body\').animate({scrollTop: jQuery(\'#wpscst_top_page\').offset().top}, 2000);"><img style="float:left;border:none;margin-right:5px;" src="'.plugins_url('/images/Add.png' , __FILE__).'" alt="'.__('Create a New Ticket').'" /> '.__('Create a New Ticket').'</button><br /><br />';
                                                $output .= '<form action="'.plugins_url('/php/submit_ticket.php' , __FILE__).'" method="post">';
						$output .= '<table class="wpscst-table" style="width:100%"><tr><th><img src="'.plugins_url('/images/Chat.png' , __FILE__).'" alt="'.__('Create a New Ticket').'" /> '.__('Create a New Ticket').'</th></tr>';
						$output .= '<tr><td><h3>'.__('Title').'</h3><input type="text" name="wpscst_title" id="wpscst_title" value=""  style="width:100%;" /></td></tr>';
						$output .= '<tr><td><h3>'.__('Your message').'</h3><div id="wpscst_nic_panel" style="display:block;width:100%;"></div> <textarea name="wpscst_initial_message" id="wpscst_initial_message" style="display:inline;width:100%;margin:0 auto 0 auto;" rows="5"></textarea></td></tr>';
                                                $exploder = explode('||', $devOptions['departments']);

                                                $output .= '<tr><td><h3>'.__('Department').'</h3><select name="wpscst_department" id="wpscst_department">';
                                                if(isset($exploder[0])) {
                                                    foreach($exploder as $exploded) {
                                                        $output .= '<option value="'.$exploded.'">'.$exploded.'</option>';
                                                    }
                                                }
                                                $output .= '</select><button onclick="cancelAdd();return false;"  style="float:right;" ><img style="float:left;border:none;margin-right:5px;" src="'.plugins_url('/images/stop.png' , __FILE__).'" alt="'.__('Cancel').'" /> '.__('Cancel').'</button><button type="submit" name="wpscst_submit" id="wpscst_submit" style="float:right;"><img style="float:left;border:none;margin-right:5px;" src="'.plugins_url('/images/page_white_text.png' , __FILE__).'" alt="'.__('Submit Ticket').'" /> '.__('Submit Ticket').'</button></td></tr>';
                                           

						$output .= '</table></form>';

                                                $output .= '<form action="'.plugins_url('/php/reply_ticket.php' , __FILE__).'" method="post"><input type="hidden" value="0" id="wpscst_edit_primkey" name="wpscst_edit_primkey" />';
                                                $output .= '<div id="wpscst_edit_ticket"><div id="wpscst_edit_ticket_inner"><center><img src="'.plugins_url('/images/loading.gif' , __FILE__).'" alt="'.__('Loading').'" /></center></div>
                                                    <table style="width:100%" id="wpscst_reply_editor_table"><tbody>
                                                    <tr id="wpscst_reply_editor_table_tr1"><td><h3>'.__('Your reply').'</h3><div id="wpscst_nic_panel2" style="display:block;width:100%;"></div> <textarea name="wpscst_reply" id="wpscst_reply" style="display:inline;width:100%;margin:0 auto 0 auto;" rows="5"></textarea></td></tr>
                                                    <tr id="wpscst_reply_editor_table_tr2"><td><button style="float:right;" onclick="cancelEdit();return false;"><img src="'.plugins_url('/images/stop.png' , __FILE__).'" alt="'.__('Cancel').'" style="float:left;border:none;margin-right:5px;" /> '.__('Cancel').'</button><button type="submit" name="wpscst_submit2" id="wpscst_submit2" style="float:right;"><img style="float:left;border:none;margin-right:5px;" src="'.plugins_url('/images/page_white_text.png' , __FILE__).'" alt="'.__('Submit Reply').'" /> '.__('Submit Reply').'</button></td></tr>
                                                    </tbody></table>
                                                </div>';
                                                $output .= '</form>';

                                                $output .= '<div id="wpscst_edit_div">';
						$sql = "SELECT * FROM `{$table_name}` WHERE `user_id`={$current_user->ID} ORDER BY `last_updated` DESC;";
						$results = $wpdb->get_results( $sql , ARRAY_A );
						if(isset($results) && isset($results[0]['primkey'])) {
                                                    $output .= '<h3>'.__('View Previous Tickets:').'</h3>';
                                                    $output .= '<table class="widefat" style="width:100%"><tr><th>'.__('Ticket').'</th><th>'.__('Status').'</th><th>'.__('Last Reply').'</th></tr>';
                                                    foreach($results as $result) {
                                                            $output .= '<tr><td><a href="" onclick="loadTicket('.$result['primkey'].',\''.$result['resolution'].'\');return false;" style="border:none;text-decoration:none;"><img style="float:left;border:none;margin-right:5px;" src="'.plugins_url('/images/page_edit.png' , __FILE__).'" alt="'.__('View').'"  /> '.base64_decode($result['title']).'</a></td><td>'.$result['resolution'].'</td><td>'.date('Y-m-d g:i A',$result['last_updated']).'</td></tr>';
                                                    }
                                                    $output .= '</table>';
						}
                                                $output .= '</div>';
						
					} else {
						$output .= __('Please').' <a href="'.wp_login_url( get_permalink() ).'">'.__('log in').'</a> '.__('or').' <a href="'.wp_login_url( get_permalink() ).'&action=register">'.__('register').'</a>.';
					}
					break;
				case 'topgames': // Top game shortcode =========================================================
					break;					
			}			
			
			return $output;
		}
		// END SHORTCODE ================================================


				
   
    }
 /**
 * ===============================================================================================================
 * End Main wpscSupportTickets Class
 */	
} 
// The end of the IF statement


 
 
 
 
 
 
 

 /**
 * ===============================================================================================================
 * Initialize the admin panel
 */
if (!function_exists("wpscSupportTicketsAdminPanel")) {
    function wpscSupportTicketsAdminPanel() {
        global $wpscSupportTickets;
        if (!isset($wpscSupportTickets)) {
            return;
        }
        if (function_exists('add_menu_page')) {
            add_menu_page('wpsc Support Tickets', 'Support Tickets', 'manage_wpsc_support_tickets', 'wpscSupportTickets-admin', array(&$wpscSupportTickets, 'printAdminPage'), WP_PLUGIN_URL.'/wpsc-support-tickets/images/controller.png');
            $editPage = add_submenu_page(NULL,'Reply to Support Ticket', 'Reply to Support Tickets', 'manage_wpsc_support_tickets', 'wpscSupportTickets-edit', array(&$wpscSupportTickets, 'printAdminPageEdit'));
            add_action("admin_print_scripts-$editPage", array(&$wpscSupportTickets, 'addHeaderCode') );
        }
    }   
}
 /**
 * ===============================================================================================================
 * END Initialize the admin panel
 */
 
 
 /**
 * ===============================================================================================================
 * Call everything
 */
if (class_exists("wpscSupportTickets")) {
    $wpscSupportTickets = new wpscSupportTickets();
}

//Actions and Filters   
if (isset($wpscSupportTickets)) {
    //Actions
	register_activation_hook(__FILE__, array(&$wpscSupportTickets, 'wpscSupportTickets_install')); // Install DB schema
	add_action('wpsc-support-tickets/wpscSupportTickets.php',  array(&$wpscSupportTickets, 'init')); // Create options on activation
	add_action('admin_menu', 'wpscSupportTicketsAdminPanel'); // Create admin panel
	add_action('wp_dashboard_setup', array(&$wpscSupportTickets, 'wpscSupportTickets_main_add_dashboard_widgets') ); // Dashboard widget
        //add_action('wp_head', array(&$wpscSupportTickets, 'addHeaderCode')); // Place wpscSupportTickets comment into header
	add_shortcode('wpscSupportTickets', array(&$wpscSupportTickets, 'wpscSupportTickets_mainshortcode'));
	add_action('admin_head', array(&$wpscSupportTickets, 'placeAdminHeaderCode'), 1); // Place wpscSupportTickets comment into header
	add_action("wp_print_scripts", array(&$wpscSupportTickets,"addHeaderCode"));
}
 /**
 * ===============================================================================================================
 * Call everything
 */

?>