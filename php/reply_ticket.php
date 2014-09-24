<?php
global $wpsc_error_reporting;
if($wpsc_error_reporting==false) {
    error_reporting(0);
}
if (!function_exists('add_action'))
{
    require_once("../../../../wp-config.php");
}

global $current_user, $wpdb, $wpscSupportTickets;

$devOptions = $wpscSupportTickets->getAdminOptions();

if (session_id() == "") {@session_start();};

if ( current_user_can('manage_wpsct_support_tickets')) { // admin edits such as closing tickets should happen here first:
    if(@isset($_POST['wpscst_status']) && @isset($_POST['wpscst_department']) && is_numeric($_POST['wpscst_edit_primkey'])) {
        $wpscst_department = base64_encode(strip_tags($_POST['wpscst_department']));
        $wpscst_status = $wpdb->escape($_POST['wpscst_status']);
        $wpscst_severity = $wpdb->escape($_POST['wpscst_severity']);
        $primkey = intval($_POST['wpscst_edit_primkey']);
        // Update the Last Updated time stamp
        $updateSQL = "UPDATE `{$wpdb->prefix}wpscst_tickets` SET `last_updated` = '".current_time( 'timestamp' )."', `type`='{$wpscst_department}', `resolution`='{$wpscst_status}', `severity`='{$wpscst_severity}' WHERE `primkey` ='{$primkey}';";
        $wpdb->query($updateSQL);
    }
}

// Update the status if applicable
if( @isset( $_POST['wpscst_set_status'] ) && $devOptions['allow_closing_ticket']=='true' ) {
    $primkey = intval($_POST['wpscst_edit_primkey']);
    $wpscst_set_status = esc_sql($_POST['wpscst_set_status']);
    $updateSQL = "UPDATE `{$wpdb->prefix}wpscst_tickets` SET `resolution`='{$wpscst_set_status}' WHERE `primkey` ='{$primkey}';";
    $wpdb->query($updateSQL);

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
if((is_user_logged_in() || @isset($_SESSION['wpsct_email'])) && is_numeric($_POST['wpscst_edit_primkey'])) {

    // Guest additions here
    if(is_user_logged_in()) {
        $wpscst_userid = $current_user->ID;
        $wpscst_email = $current_user->user_email;
    } else {
        $wpscst_userid = 0;
        $wpscst_email = $wpdb->escape($_SESSION['wpsct_email']);  
        if(trim($wpscst_email)=='') {
            $wpscst_email = @$wpdb->escape($_POST['guest_email']);
        }        
    }    
    
    $primkey = intval($_POST['wpscst_edit_primkey']);

    if ( !current_user_can('manage_wpsct_support_tickets')) {
        
        if($devOptions['allow_all_tickets_to_be_replied']=='true' && $devOptions['allow_all_tickets_to_be_viewed']=='true') {
            $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$primkey}' LIMIT 0, 1;";
        }                                                
        if($devOptions['allow_all_tickets_to_be_replied']=='false' || $devOptions['allow_all_tickets_to_be_viewed']=='false') {
            $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$primkey}' AND `user_id`='{$wpscst_userid}' AND `email`='{$wpscst_email}' LIMIT 0, 1;";
        }        
    } else {
        // This allows approved users, such as the admin, to reply to any support ticket
        $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$primkey}' LIMIT 0, 1;";
    }
    $results = $wpdb->get_results( $sql , ARRAY_A );
    if(isset($results[0])) {
        

            $wpscst_message = '';

            if($devOptions['allow_uploads']=='true' && function_exists('wpscSupportTicketsPRO') && @isset($_FILES["wpscst_file"]) && @$_FILES["wpscst_file"]["error"] != 4 ) {
                /* Handles the error output. This error message will be sent to the uploadSuccess event handler.  The event handler
                will have to check for any error messages and react as needed. */
                function HandleError($message) {
                        echo '<script type="text/javascript">alert("'.$message.'");</script>'.$message.'';
                }

                // Code for Session Cookie workaround
                        if (isset($_POST["PHPSESSID"])) {
                                session_id($_POST["PHPSESSID"]);
                        } else if (isset($_GET["PHPSESSID"])) {
                                session_id($_GET["PHPSESSID"]);
                        }

                        session_start();

                // Check post_max_size (http://us3.php.net/manual/en/features.file-upload.php#73762)
                        $POST_MAX_SIZE = @ini_get('post_max_size');
                        if(@$POST_MAX_SIZE == NULL || $POST_MAX_SIZE < 1) {$POST_MAX_SIZE=9999999999999;};
                        $unit = strtoupper(substr($POST_MAX_SIZE, -1));
                        $multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));

                        if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
                                header("HTTP/1.1 500 Internal Server Error"); // This will trigger an uploadError event in SWFUpload
                                _e("POST exceeded maximum allowed size.", 'wpsc-support-tickets');
                        }

                // Settings
                        $wpsc_wordpress_upload_dir = wp_upload_dir();
                        $save_path = $wpsc_wordpress_upload_dir['basedir']. '/wpsc-support-tickets/';
                        if(!is_dir($save_path)) {
                                @mkdir($save_path);
                        }                
                        $upload_name = "wpscst_file";
                        $max_file_size_in_bytes = 2147483647;				// 2GB in bytes
                        $valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';				// Characters allowed in the file name (in a Regular Expression format)

                // Other variables	
                        $MAX_FILENAME_LENGTH = 260;
                        $file_name = "";
                        $file_extension = "";
                        $uploadErrors = array(
                                0=>__("There is no error, the file uploaded with success", 'wpsc-support-tickets'),
                                1=>__("The uploaded file exceeds the upload_max_filesize directive in php.ini", 'wpsc-support-tickets'),
                                2=>__("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form", 'wpsc-support-tickets'),
                                3=>__("The uploaded file was only partially uploaded", 'wpsc-support-tickets'),
                                4=>__("No file was uploaded", 'wpsc-support-tickets'),
                                6=>__("Missing a temporary folder", 'wpsc-support-tickets')
                        );


                // Validate the upload
                        if (!isset($_FILES[$upload_name])) {
                            //
                        } else if (isset($_FILES[$upload_name]["error"]) && $_FILES[$upload_name]["error"] != 0) {
                                HandleError($uploadErrors[$_FILES[$upload_name]["error"]]);
                        } else if (!isset($_FILES[$upload_name]["tmp_name"]) || !@is_uploaded_file($_FILES[$upload_name]["tmp_name"])) {
                                HandleError(__("Upload failed is_uploaded_file test.", 'wpsc-support-tickets'));
                        } else if (!isset($_FILES[$upload_name]['name'])) {
                                HandleError(__("File has no name.", 'wpsc-support-tickets'));
                        }

                // Validate the file size (Warning: the largest files supported by this code is 2GB)
                        $file_size = @filesize($_FILES[$upload_name]["tmp_name"]);
                        if (!$file_size || $file_size > $max_file_size_in_bytes) {
                                HandleError(__("File exceeds the maximum allowed size", 'wpsc-support-tickets'));
                        }

                        if ($file_size <= 0) {
                                HandleError(__("File size outside allowed lower bound", 'wpsc-support-tickets'));
                        }


                // Validate file name (for our purposes we'll just remove invalid characters)
                        $file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($_FILES[$upload_name]['name']));
                        if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH) {
                                HandleError(__("Invalid file name", 'wpsc-support-tickets'));
                        }


                        if (!@move_uploaded_file($_FILES[$upload_name]["tmp_name"], $save_path.$file_name)) {
                                HandleError(__("File could not be saved.", 'wpsc-support-tickets'));
                        } else {
                            // SUCCESS
                            $wpscst_message .= '<br /><p class="wpsc-support-ticket-attachment"';
                            if($devOptions['disable_inline_styles']=='false'){
                                $wpscst_message .=  ' style="border: 1px solid #DDD;padding:8px;" ';
                            }
                            $wpscst_message .= '>';
                            $wpscst_message .= '<img src="'.plugins_url().'/wpsc-support-tickets-pro/images/attachment.png" alt="" /> <strong>'.__('ATTACHMENT','wpsc-support-tickets').'</strong>: <a href="'.$wpsc_wordpress_upload_dir['baseurl'].'/wpsc-support-tickets/'.$file_name.'" target="_blank">'.$wpsc_wordpress_upload_dir['baseurl'].'/wpsc-support-tickets/'.$file_name.'</a></p>';
                        }       
            }        
        
        
        
            $wpscst_message = base64_encode(nl2br($_POST['wpscst_reply'] . $wpscst_message));

            $sql = "
            INSERT INTO `{$wpdb->prefix}wpscst_replies` (
                `primkey` ,
                `ticket_id` ,
                `user_id` ,
                `timestamp` ,
                `message`
            )
            VALUES (
                NULL , '{$primkey}', '{$wpscst_userid}', '".current_time( 'timestamp' )."', '{$wpscst_message}'
            );
            ";

            $wpdb->query($sql);


            // Update the Last Updated time stamp
            if($_POST['wpscst_is_staff_reply']=='yes' && current_user_can('manage_wpsct_support_tickets')) {
                    // This is a staff reply from the admin panel
                    $updateSQL = "UPDATE `{$wpdb->prefix}wpscst_tickets` SET `last_updated` = '".current_time( 'timestamp' )."', `last_staff_reply` = '".time()."' WHERE `primkey` ='{$primkey}';";
            } else {
                    // This is a reply from the front end
                    $updateSQL = "UPDATE `{$wpdb->prefix}wpscst_tickets` SET `last_updated` = '".current_time( 'timestamp' )."' WHERE `primkey` ='{$primkey}';";
            }
            $wpdb->query($updateSQL);

            if (@isset($_POST['wpsctnoemail']) && $_POST['wpsctnoemail'] == 'on' && $results[0]['email'] != $wpscst_email) {
                $to      = $results[0]['email']; // Send this to the original ticket creator
                $subject = $devOptions['email_new_reply_subject'] .' "'. strip_tags(base64_decode($results[0]['title'])).'"';
                $message = $devOptions['email_new_reply_body'];
                if($devOptions['use_reply_in_email']=='true') {
                    $message .= "\r\n";
                    $message .= "\r\n";
                    $cleaned_message = __("The content of the reply is: ", 'wpsc-support-tickets'). '"'. strip_tags($_POST['wpscst_reply']) .'"';
                    $message .= $cleaned_message;
                }            
                $headers = '';
                if($devOptions['allow_html']=='true') {
                    $headers .= 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";                
                }
                $headers .= 'From: ' . $devOptions['email'] . "\r\n" .
                'Reply-To: ' . $devOptions['email'] .  "\r\n" .
                'X-Mailer: PHP/' . phpversion();
                wp_mail($to, $subject, $message, $headers);
            }
            
            if( $devOptions['email']!=$results[0]['email'] && $results[0]['email'] != $wpscst_email) { 
                $to      = $devOptions['email']; // Send this to the admin
                $subject = __("Reply to a support ticket was received.", 'wpsc-support-tickets').' "'. strip_tags(base64_decode($results[0]['title'])).'"';
                $message = __('There is a new reply on support ticket: ','wpsc-support-tickets').get_admin_url().'admin.php?page=wpscSupportTickets-edit&primkey='.$primkey.'';
                if($devOptions['use_reply_in_email']=='true') {
                    $message .= "\r\n";
                    $message .= "\r\n";
                    $cleaned_message = __("The content of the reply is: ", 'wpsc-support-tickets'). '"'. strip_tags($_POST['wpscst_reply']) .'"';
                    $message .= $cleaned_message;
                }
                $headers = '';
                if($devOptions['allow_html']=='true') {
                    $headers .= 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";                
                }                
                $headers .= 'From: ' . $devOptions['email'] . "\r\n" .
                'Reply-To: ' . $devOptions['email'] .  "\r\n" .
                'X-Mailer: PHP/' . phpversion();
                wp_mail($to, $subject, $message, $headers);
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