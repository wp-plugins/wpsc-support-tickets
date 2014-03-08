<?php
/*
  Plugin Name: wpsc Support Tickets
  Plugin URI: http://wpscsupporttickets.com/wordpress-support-ticket-plugin/
  Description: An open source help desk and support ticket system for Wordpress using jQuery. Easy to use for both users & admins.
  Version: 4.4.2
  Author: wpStoreCart, LLC
  Author URI: URI: http://wpstorecart.com/
  License: LGPL
  Text Domain: wpsc-support-tickets
 */

/*
  Copyright 2012, 2013, 2014 wpStoreCart, LLC  (email : admin@wpstorecart.com)

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
global $wpscSupportTickets, $wpscSupportTickets_version, $wpscSupportTickets_db_version, $APjavascriptQueue, $wpsct_error_reporting;
$wpscSupportTickets_version = 4.4;
$wpscSupportTickets_db_version = 4.4;
$APjavascriptQueue = NULL;
$wpsct_error_reporting = false;

// Create the proper directory structure if it is not already created
if (!is_dir(WP_CONTENT_DIR . '/uploads/')) {
    mkdir(WP_CONTENT_DIR . '/uploads/', 0777, true);
}
if (!is_dir(WP_CONTENT_DIR . '/uploads/wpscSupportTickets/')) {
    mkdir(WP_CONTENT_DIR . '/uploads/wpscSupportTickets/', 0777, true);
}

/**
 * Action definitions 
 */
function wpscSupportTickets_settings() {
    do_action('wpscSupportTickets_settings');
}

function wpscSupportTickets_saveSettings() {
    do_action('wpscSupportTickets_saveSettings');
}

function wpscSupportTickets_extraTabsIndex() {
    do_action('wpscSupportTickets_extraTabsIndex');
}

function wpscSupportTickets_extraTabsContents() {
    do_action('wpscSupportTickets_extraTabsContents');
}

if(!function_exists('wpsctSlug')) {
        /**
         *
         * Returns a slug of the input string, suitable URLs, HTML and other space/character sensitive operations
         * 
         * @param string $str
         * @return string 
         */
        function wpsctSlug($str) {
                $str = strtolower(trim($str));
                $str = preg_replace('/[^a-z0-9-]/', '_', $str);
                $str = preg_replace('/-+/', "_", $str);
                return $str;
        }
}

if(!function_exists('wpsctPromptForCustomFields')) {
    /**
     * 
     * @global type $wpdb
     * @return string
     */
    function wpsctPromptForCustomFields() {
        global $wpdb;

        $devOptions = get_option('wpscSupportTicketsAdminOptions');
        $output = '';
        if (session_id() == "") {@session_start();};

        // Custom form fields here
        $table_name33 = $wpdb->prefix . "wpstorecart_meta";
        $grabrecord = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-requiredinfo' ORDER BY `foreignkey` ASC;";

        $resultscf = $wpdb->get_results( $grabrecord , ARRAY_A );

        $wpsct_text_fields = array(
            'input (text)','shippingcity','firstname','lastname',
            'shippingaddress','input (numeric)','zipcode'
        );
        $wpsct_states = array(
            "not applicable" => 'Other (Non-US)',
            "AL" => 'Alabama',      "AK" => 'Alaska',       "AZ" => 'Arizona',      "CA" => 'California',
            "CO" => 'Colorado',     "CT" => 'Connecticut',  "DE" => 'Delaware',     "DC" => 'District Of Columbia',
            "FL" => 'Florida',      "GA" => 'Georgia',      "HI" => 'Hawaii',       "ID" => 'Idaho', 
            "IL" => 'Illinois',     "IN" => 'Indiana',      "IA" => 'Iowa',         "KS" => 'Kansas',       
            "KY" => 'Kentucky',     "LA" => 'Louisiana',    "ME" => 'Maine',        "MD" => 'Maryland',
            "MA" => 'Massachusetts',"MI" => 'Michigan',     "MN" => 'Minnesota',    "MS" => 'Mississippi',  
            "MO" => 'Missouri',     "MT" => 'Montana',      "NE" => 'Nebraska',     "NV" => 'Nevada',       
            "NH" => 'New Hampshire',"NJ" => 'New Jersey',   "NM" => 'New Mexico',   "NY" => 'New York',
            "NC" => 'North Carolina',"ND" => 'North Dakota',"OH" => 'Ohio',         "OK" => 'Oklahoma',
            "OR" => 'Oregon',       "PA" => 'Pennsylvania', "RI" => 'Rhode Island', "SC" => 'South Carolina',
            "SD" => 'South Dakota', "TN" => 'Tennessee',    "TX" => 'Texas',        "UT" => 'Utah',
            "VT" => 'Vermont',      "VA" => 'Virginia',     "WA" => 'Washington',   "WV" => 'West Virginia',
            "WI" => 'Wisconsin',    "WY" => 'Wyoming',
        );    
        $wpsct_countries = array(
            "United States", "Canada","United Kingdom","Ireland","Australia","New Zealand","Afghanistan","Albania","Algeria","American Samoa",
            "Andorra","Angola","Anguilla","Antarctica","Antigua and Barbuda","Argentina","Armenia","Aruba","Australia","Austria","Azerbaijan",
            "Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bermuda","Bhutan","Bolivia","Bosnia and Herzegovina",
            "Botswana","Bouvet Island","Brazil","British Indian Ocean Territory","Brunei Darussalam","Bulgaria","Burkina Faso","Burundi","Cambodia",
            "Cameroon","Canada","Cape Verde","Cayman Islands","Central African Republic","Chad","Chile","China","Christmas Island","Cocos (Keeling) Islands",
            "Colombia","Comoros","Congo","Congo, The Democratic Republic of The","Cook Islands","Costa Rica","Cote D\'ivoire","Croatia","Cuba","Cyprus",
            "Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia",
            "Ethiopia","Falkland Islands (Malvinas)","Faroe Islands","Fiji","Finland","France","French Guiana","French Polynesia","French Southern Territories",
            "Gabon","Gambia","Georgia","Germany","Ghana","Gibraltar","Greece","Greenland","Grenada","Guadeloupe","Guam","Guatemala","Guinea","Guinea-bissau",
            "Guyana","Haiti","Heard Island and Mcdonald Islands","Holy See (Vatican City State)","Honduras","Hong Kong","Hungary","Iceland","India","Indonesia",
            "Iran, Islamic Republic of","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, Democratic People\'s Republic of",
            "Korea, Republic of","Kuwait","Kyrgyzstan","Lao People\'s Democratic Republic","Latvia","Lebanon","Lesotho","Liberia","Libyan Arab Jamahiriya",
            "Liechtenstein","Lithuania","Luxembourg","Macao","Macedonia, The Former Yugoslav Republic of","Madagascar","Malawi","Malaysia","Maldives","Mali",
            "Malta","Marshall Islands","Martinique","Mauritania","Mauritius","Mayotte","Mexico","Micronesia, Federated States of","Moldova, Republic of",
            "Monaco","Mongolia","Montserrat","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepal","Netherlands","Netherlands Antilles","New Caledonia",
            "New Zealand","Nicaragua","Niger","Nigeria","Niue","Norfolk Island","Northern Mariana Islands","Norway","Oman","Pakistan","Palau",
            "Palestinian Territory, Occupied","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Pitcairn","Poland","Portugal","Puerto Rico",
            "Qatar","Reunion","Romania","Russian Federation","Rwanda","Saint Helena","Saint Kitts and Nevis","Saint Lucia","Saint Pierre and Miquelon",
            "Saint Vincent and The Grenadines","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles",
            "Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","South Georgia and The South Sandwich Islands","Spain",
            "Sri Lanka","Sudan","Suriname","Svalbard and Jan Mayen","Swaziland","Sweden","Switzerland","Syrian Arab Republic","Taiwan, Province of China",
            "Tajikistan","Tanzania, United Republic of","Thailand","Timor-leste","Togo","Tokelau","Tonga","Trinidad and Tobago","Tunisia","Turkey",
            "Turkmenistan","Turks and Caicos Islands","Tuvalu","Uganda","Ukraine","United Arab Emirates","United States Minor Outlying Islands","Uruguay",
            "Uzbekistan","Vanuatu","Venezuela","Viet Nam","Virgin Islands, British","Virgin Islands, U.S.","Wallis and Futuna","Western Sahara","Yemen",
            "Zambia","Zimbabwe",
        );    

        $wpsct_style_width = '';
        $wpsct_style_inline = '';
        if ($devOptions['disable_inline_styles'] == 'false') {
            $wpsct_style_width ='style="width:100%"';
            $wpsct_style_inline = 'style="display:inline;"';
        }  

        if(isset($resultscf)) {
                foreach ($resultscf as $field) {
                    $specific_items = explode("||", $field['value']);
                    $wpsct_required_item = '';
                    if ($specific_items[1]=='required'){ 
                        $wpsct_required_item = '<ins><div class="wpst-required-symbol" ' . $wpsct_style_inline . '>* </div></ins>';
                    }
                    $prev_val = $_SESSION['wpsct_custom_'.$field['primkey']];
                    foreach ($wpsct_text_fields as $wpsct_text_field) {
                        if($specific_items[2]==$wpsct_text_field) {
                            $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item
                                . '</h3><input  id="wpsct_custom_'.$field['primkey'].'" type="text"  value="'
                                . $_SESSION['wpsct_custom_'.$field['primkey']] .'" name="wpsct_custom_'.$field['primkey'].'" ' 
                                . $wpsct_style_width . '  /></td></tr>';
                        }
                    }
                    if ($specific_items[2] == 'textarea') {
                        $output .= '<tr><td><h3>' . $specific_items[0] . $wpsct_required_item
                            . '</h3><textarea  id="wpsct_custom_' . $field['primkey'] . '" name="wpsct_custom_' 
                            . $field['primkey'] . '" ' . $wpsct_style_width . '>' 
                            . $_SESSION['wpsct_custom_' . $field['primkey']] . '</textarea></td></tr>';
                    }
                    if($specific_items[2]=='states' || $specific_items[2]=='taxstates') {
                        $selected[$prev_val] = ' selected="selected"';
                        $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item 
                            . '</h3><select name="wpsct_custom_'.$field['primkey'].'" class="wpsct-states" ' . $wpsct_style_width . ">\n"
                            . "<option value=\"not applicable\" {$selected['']}>" . __('Other (Non-US)', 'wpsc-support-tickets').'</option>'; 
                        foreach ($wpsct_states as $wpsct_code => $wpcst_state) {
                            $output .="<option value=\"$wpsct_code\" {$selected[$wpsct_code]}>" . __($wpcst_state, 'wpsc-support-tickets').'</option>\n'; 
                        }
                        $output.= '</select></td></tr>';
                    }
                    if($specific_items[2]=='countries' || $specific_items[2]=='taxcountries') {
                        $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item
                            . '</h3><select  name="wpsct_custom_'.$field['primkey'].'" class="wpsct-countries" '. $wpsct_style_width . ">\n";
                        $selected[$prev_val] = ' selected="selected"';
                        foreach ($wpsct_countries as $wpsct_country) {
                            $output .="<option value=\"$wpsct_country\" {$selected[$wpsct_country]}>" . __($wpsct_country, 'wpsc-support-tickets').'</option>\n'; 
                        }
                        $output.= '</select></td></tr>';                    
                    }
                    if($specific_items[2]=='email') {
                        $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item
                            . '</h3><input  id="wpsct_custom_'.$field['primkey'].'" type="text"  value="'
                            . $_SESSION['wpsct_custom_'.$field['primkey']].'" name="wpsct_custom_'.$field['primkey'].'" ' . $wpsct_style_width
                            . ' /></td></tr>';
                    }
                    if($specific_items[2]=='separator') {
                        $output .= '<tr><td><center>'.$specific_items[0].'</center></td></tr>';
                    }
                    if($specific_items[2]=='header') {
                        $output .= '<tr><td><h2>'.$specific_items[0] .'</h2></td></tr>';
                    }
                    if($specific_items[2]=='text') {
                        $output .= '<tr><td>'.$specific_items[0] .'</td></tr>';
                    }

                }
        }       

        return $output;
    }
}

/**
 * ===============================================================================================================
 * Main wpscSupportTickets Class
 */
if (!class_exists("wpscSupportTickets")) {

    class wpscSupportTickets {

        var $adminOptionsName = "wpscSupportTicketsAdminOptions";
        var $wpscstSettings = null;
        var $hasDisplayed = false;
        var $hasDisplayedCompat = false; // hack for Jetpack compatibility
        var $hasDisplayedCompat2 = false; // hack for Jetpack compatibility

        function wpscSupportTickets() { //constructor
            // Let's make sure the admin is always in charge
            if (is_user_logged_in()) {
                if (is_super_admin() || is_admin()) {
                    global $wp_roles;
                    add_role('wpsct_support_ticket_manager', 'Support Ticket Manager', array('manage_wpsct_support_tickets', 'read', 'upload_files', 'publish_posts', 'edit_published_posts', 'publish_pages', 'edit_published_pages'));
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'read');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'upload_files');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'publish_pages');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'publish_posts');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'edit_published_posts');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'edit_published_pages');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'manage_wpsct_support_tickets');
                    $wp_roles->add_cap('administrator', 'manage_wpsct_support_tickets');
                }
            }
        }

        function init() {
            $this->getAdminOptions();
        }

        //Returns an array of admin options
        function getAdminOptions() {

            $apAdminOptions = array('mainpage' => '',
                'turnon_wpscSupportTickets' => 'true',
                'departments' => __('Support', 'wpsc-support-tickets') . '||' . __('Billing', 'wpsc-support-tickets'),
                'email' => get_bloginfo('admin_email'),
                'email_new_ticket_subject' => __('Your support ticket was received.', 'wpsc-support-tickets'),
                'email_new_ticket_body' => __('Thank you for opening a new support ticket.  We will look into your issue and respond as soon as possible.', 'wpsc-support-tickets'),
                'email_new_reply_subject' => __('Your support ticket reply was received.', 'wpsc-support-tickets'),
                'email_new_reply_body' => __('A reply was posted to one of your support tickets.', 'wpsc-support-tickets'),
                'disable_inline_styles' => 'false',
                'allowguests' => 'false',
                'allow_all_tickets_to_be_replied' => 'false',
                'allow_all_tickets_to_be_viewed' => 'false',
                'allow_html' => 'false',
                'allow_closing_ticket' => 'false',
                'allow_uploads' => 'false',
                'custom_field_position' => 'after message',
                'custom_field_frontend_position' => 'after message',
                'use_ticket_in_email' => 'true',
                'use_reply_in_email' => 'true'
            );

            if ($this->wpscstSettings != NULL) {
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

        /**
         * Admin Header 
         */
        function adminHeader() {

            if (function_exists('current_user_can') && !current_user_can('manage_wpsct_support_tickets')) {
                die(__('Unable to Authenticate', 'wpsc-support-tickets'));
            }


            echo '
            
            <div style="padding: 20px 10px 10px 10px;">';

            if (!function_exists('wpscSupportTicketsPRO')) {
                echo '<div style="float:left;"><img src="' . plugins_url() . '/wpsc-support-tickets/images/logo.png" alt="wpscSupportTickets" /></div>';
            } else {
                echo '<div style="float:left;"><img src="' . plugins_url() . '/wpsc-support-tickets-pro/images/logo_pro.png" alt="wpscSupportTickets" /></div>';
            }

            echo '<a href="http://wpbugtracktor.com/bug-tracker/?issue_tracker=bug&wpbt_project=2" target="_blank"><button> <center><img src="' . plugins_url() . '/wpsc-support-tickets/images/bug_report.png" alt="wpscSupportTickets" /><br /> '.__('Report a bug', 'wpsc-support-tickets').'</center></button></a> ';
            echo '<a href="http://wpbugtracktor.com/bug-tracker/?issue_tracker=feature&wpbt_project=2" target="_blank"><button> <center><img src="' . plugins_url() . '/wpsc-support-tickets/images/feature_request.png" alt="wpscSupportTickets" /><br /> '.__('Feature request', 'wpsc-support-tickets').'</center></button></a>';
            
            echo '
            </div>
            <br style="clear:both;" />
            ';
        }

        function printAdminPageSettings() {

            wpscSupportTickets_saveSettings(); // Action hook for saving

            $devOptions = $this->getAdminOptions();

            echo '<div class="wrap">';

            $this->adminHeader();

            if (@isset($_POST['update_wpscSupportTicketsSettings'])) {

                if (isset($_POST['wpscSupportTicketsmainpage'])) {
                    $devOptions['mainpage'] = esc_sql($_POST['wpscSupportTicketsmainpage']);
                }
                if (isset($_POST['turnwpscSupportTicketsOn'])) {
                    $devOptions['turnon_wpscSupportTickets'] = esc_sql($_POST['turnwpscSupportTicketsOn']);
                }
                if (isset($_POST['departments'])) {
                    $devOptions['departments'] = esc_sql($_POST['departments']);
                }
                if (isset($_POST['email'])) {
                    $devOptions['email'] = esc_sql($_POST['email']);
                }
                if (isset($_POST['email_new_ticket_subject'])) {
                    $devOptions['email_new_ticket_subject'] = esc_sql($_POST['email_new_ticket_subject']);
                }
                if (isset($_POST['email_new_ticket_body'])) {
                    $devOptions['email_new_ticket_body'] = stripslashes($_POST['email_new_ticket_body']);
                }
                if (isset($_POST['email_new_reply_subject'])) {
                    $devOptions['email_new_reply_subject'] = esc_sql($_POST['email_new_reply_subject']);
                }
                if (isset($_POST['email_new_reply_body'])) {
                    $devOptions['email_new_reply_body'] = stripslashes($_POST['email_new_reply_body']);
                }
                if (isset($_POST['disable_inline_styles'])) {
                    $devOptions['disable_inline_styles'] = esc_sql($_POST['disable_inline_styles']);
                }
                if (isset($_POST['allow_guests'])) {
                    $devOptions['allow_guests'] = esc_sql($_POST['allow_guests']);
                }
                if (isset($_POST['custom_field_position'])) {
                    $devOptions['custom_field_position'] = esc_sql($_POST['custom_field_position']);
                }     
                if (isset($_POST['custom_field_frontend_position'])) {
                    $devOptions['custom_field_frontend_position'] = esc_sql($_POST['custom_field_frontend_position']);
                }  
                if (isset($_POST['use_ticket_in_email'])) {
                    $devOptions['use_ticket_in_email'] = esc_sql($_POST['use_ticket_in_email']);
                } 
                if (isset($_POST['use_reply_in_email'])) {
                    $devOptions['use_reply_in_email'] = esc_sql($_POST['use_reply_in_email']);
                }                   

                update_option($this->adminOptionsName, $devOptions);

                echo '<div class="updated"><p><strong>';
                _e("Settings Updated.", "wpsc-support-tickets");
                echo '</strong></p></div>';
            }

            echo '
                
            <script type="text/javascript">
                jQuery(function() {
                    jQuery( "#wst_tabs" ).tabs();
                    setTimeout(function(){ jQuery(".updated").fadeOut(); },3000);
                });
            </script>

            <form method="post" action="' . $_SERVER["REQUEST_URI"] . '">
                

        <div id="wst_tabs" style="padding:5px 5px 0px 5px;font-size:1.1em;border-color:#DDD;border-radius:6px;">
            <ul>
                <li><a href="#wst_tabs-1">' . __('Settings', 'wpsc-support-tickets') . '</a></li>
                <li><a href="#wst_tabs-2">' . __('PRO', 'wpsc-support-tickets') . '</a></li>
            </ul>        
            

            <div id="wst_tabs-1">

            <br />
            <h2>' . __('General', 'wpsc-support-tickets') . '</h2>
            <table class="widefat" style="background:transparent;"><tr><td>

                <p><strong>' . __('Main Page', 'wpsc-support-tickets') . ':</strong> ' . __('You need to use a Page as the base for wpsc Support Tickets.', 'wpsc-support-tickets') . '  <br />
                <select name="wpscSupportTicketsmainpage">
                 <option value="">';
                attribute_escape(__('Select page', 'wpsc-support-tickets'));
                echo '</option>';

                $pages = get_pages();
                foreach ($pages as $pagg) {
                    $option = '<option value="' . $pagg->ID . '"';
                    if ($pagg->ID == $devOptions['mainpage']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg->post_title;
                    $option .= '</option>';
                    echo $option;
                }

                echo '
                </select>
                </p>

                <strong>' . __('Departments', 'wpsc-support-tickets') . ':</strong> ' . __('Separate these values with a double pipe, like this ||', 'wpsc-support-tickets') . ' <br /><input name="departments" value="' . $devOptions['departments'] . '" style="width:95%;" /><br /><br />

            </td></tr></table>
            <br /><br /><br />
            <h2>' . __('Email', 'wpsc-support-tickets') . '</h2>
            <table class="widefat" style="background:transparent;"><tr><td>                

                <strong>' . __('Email', 'wpsc-support-tickets') . ':</strong> ' . __('The admin email where all new ticket &amp; reply notification emails will be sent', 'wpsc-support-tickets') . '<br /><input name="email" value="' . $devOptions['email'] . '" style="width:95%;" /><br /><br />

                <strong>' . __('New Ticket Email', 'wpsc-support-tickets') . '</strong> ' . __('The subject &amp; body of the email sent to the customer when creating a new ticket.', 'wpsc-support-tickets') . '<br /><input name="email_new_ticket_subject" value="' . $devOptions['email_new_ticket_subject'] . '" style="width:95%;" />
                <textarea style="width:95%;" name="email_new_ticket_body">' . $devOptions['email_new_ticket_body'] . '</textarea>
                <br /><br />
                
                <p><strong>' . __('Include the ticket in New Ticket Email', 'wpsc-support-tickets') . ':</strong> ' . __('Set this to true if you want the content of the ticket included in the new ticket email.', 'wpsc-support-tickets') . '  <br />
                <select name="use_ticket_in_email">
                 ';

                $pagesY[0] = 'true';
                $pagesY[1] = 'false';
                foreach ($pagesY as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg == $devOptions['use_ticket_in_email']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg;
                    $option .= '</option>';
                    echo $option;
                }

                echo '
                </select>
                </p>

                <strong>' . __('New Reply Email', 'wpsc-support-tickets') . '</strong> ' . __('The subject &amp; body of the email sent to the customer when there is a new reply.', 'wpsc-support-tickets') . '<br /><input name="email_new_reply_subject" value="' . $devOptions['email_new_reply_subject'] . '" style="width:95%;" />
                <textarea style="width:95%;" name="email_new_reply_body">' . $devOptions['email_new_reply_body'] . '</textarea>
                <br /><br />
                
                <p><strong>' . __('Include the reply in New Reply Email', 'wpsc-support-tickets') . ':</strong> ' . __('Set this to true if you want the content of the reply included in the new reply email.', 'wpsc-support-tickets') . '  <br />
                <select name="use_reply_in_email">
                 ';

                $pagesY[0] = 'true';
                $pagesY[1] = 'false';
                foreach ($pagesY as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg == $devOptions['use_reply_in_email']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg;
                    $option .= '</option>';
                    echo $option;
                }

                echo '
                </select>
                </p>

            </td></tr></table>
            <br /><br /><br />
            <h2>' . __('Styling', 'wpsc-support-tickets') . '</h2>
            <table class="widefat" style="background:transparent;"><tr><td> 

                <p><strong>' . __('Disable inline styles', 'wpsc-support-tickets') . ':</strong> ' . __('Set this to true if you want to disable the inline CSS styles.', 'wpsc-support-tickets') . '  <br />
                <select name="disable_inline_styles">
                 ';

                $pagesX[0] = 'true';
                $pagesX[1] = 'false';
                foreach ($pagesX as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg == $devOptions['disable_inline_styles']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg;
                    $option .= '</option>';
                    echo $option;
                }

                echo '
                </select>
                </p>

            </td></tr></table>
            <br /><br /><br />
            <h2>' . __('Guests', 'wpsc-support-tickets') . '</h2>
            <table class="widefat" style="background:transparent;"><tr><td> 

                <p><strong>' . __('Allow Guests', 'wpsc-support-tickets') . ':</strong> ' . __('Set this to true if you want Guests to be able to use the support ticket system.', 'wpsc-support-tickets') . '  <br />
                <select name="allow_guests">
                 ';

                $pagesY[0] = 'true';
                $pagesY[1] = 'false';
                foreach ($pagesY as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg == $devOptions['allow_guests']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg;
                    $option .= '</option>';
                    echo $option;
                }

                echo '
                </select>
                </p>
                
            </td></tr></table>
            <br /><br /><br />
            <h2>' . __('Custom Fields', 'wpsc-support-tickets') . '</h2>
            <table class="widefat" style="background:transparent;"><tr><td> 

                <p><strong>' . __('Place custom form fields', 'wpsc-support-tickets') . ':</strong> ' . __('When creating a ticket, this determines where your custom fields are placed on the ticket submission form.', 'wpsc-support-tickets') . '  <br />
                <select name="custom_field_position">
                 ';

                $pagesXX[0]['valname'] = 'before everything';$pagesXX[0]['displayname'] = __('before everything', 'wpsc-support-tickets');
                $pagesXX[1]['valname'] = 'before message';$pagesXX[1]['displayname'] = __('before message', 'wpsc-support-tickets');
                $pagesXX[2]['valname'] = 'after message';$pagesXX[2]['displayname'] = __('after message', 'wpsc-support-tickets');
                $pagesXX[3]['valname'] = 'after everything';$pagesXX[3]['displayname'] = __('after everything', 'wpsc-support-tickets');

                foreach ($pagesXX as $pagg) {
                    $option = '<option value="' . $pagg['valname'] . '"';
                    if ($pagg['valname'] == $devOptions['custom_field_position']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg['displayname'];
                    $option .= '</option>';
                    echo $option;
                }

            echo '
                </select>
                </p>

                <p><strong>' . __('Display custom fields', 'wpsc-support-tickets') . ':</strong> ' . __('When a ticket creator views a ticket they created, this setting determines where the custom fields are placed on the page.', 'wpsc-support-tickets') . '  <br />
                <select name="custom_field_frontend_position">
                 ';

                $pagesXY[0]['valname'] = 'before everything';$pagesXY[0]['displayname'] = __('before everything', 'wpsc-support-tickets');
                $pagesXY[1]['valname'] = 'before message';$pagesXY[1]['displayname'] = __('before message', 'wpsc-support-tickets');
                $pagesXY[2]['valname'] = 'after message';$pagesXY[2]['displayname'] = __('after message &amp; replies', 'wpsc-support-tickets');

                foreach ($pagesXY as $pagg) {
                    $option = '<option value="' . $pagg['valname'] . '"';
                    if ($pagg['valname'] == $devOptions['custom_field_frontend_position']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg['displayname'];
                    $option .= '</option>';
                    echo $option;
                }

                echo '
                </select>
                </p>
                <br /><br /><br /><br />
            </td></tr></table>


            </div>
            <div id="wst_tabs-2">';

            wpscSupportTickets_settings(); // Action hook

            echo '
                </div>
            

            <input type="hidden" name="update_wpscSupportTicketsSettings" value="update" />
            <div style="float:right;position:relative;top:-20px;"> <input class="button-primary" style="position:relative;z-index:999999;" type="submit" name="update_wpscSupportTicketsSettings_submit" value="';
            _e('Update Settings', 'wpsc-support-tickets');
            echo'" /></div>
            

            </div>
            </div>
            </form>
            

        ';

            if (!function_exists('wpscSupportTicketsPRO')) {
                echo '
                <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery(\'#buypro\').append(\'<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="REYXW5BR8H5MU"><input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></form>\');
                });
                </script>
            ';
            }
        }

        //Prints out the admin page ================================================================================
        function printAdminPageStats() {
            global $wpdb;
            $devOptions = $this->getAdminOptions();
            if (function_exists('current_user_can') && !current_user_can('manage_wpsct_support_tickets')) {
                die(__('Unable to Authenticate', 'wpsc-support-tickets'));
            }
            
            echo '<div class="wrap">';
            
            $this->adminHeader();
                        
            
            
            if (@!function_exists('wpscSupportTicketsPRO') ) {
                echo '<table class="widefat" style="width:98%;"><tr><td>';
                echo '
                                
                                <h2>Upgrade now to wpsc Support Tickets PRO and unlock in depth statistics for the following and more:</h2> 
                                    <ul>
                                        <li>Average ticket resolution time</li>
                                        <li>Number of tickets created in each category</li>
                                        <li>Number of tickets in each severity level</li>
                                        <li>Top 10 users who create the most tickets</li>
                                        <li>The number of completed tickets</li>
                                        <li>Display how long a ticket has been open</li>
                                        <li>Display how long it took to resolve a closed ticket</li>
                                        <li>Bar chart showing the amount of time it took to close the last 30 tickets</li>
                                        <li>And much more, upgrade to PRO today:</li>
                                    </ul>
                                    <div id="buyprostats"><strong>$19.99 USD</strong><br /></div>
                                
                              
                                ';
                echo '</td></tr></table>
                <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery(\'#buyprostats\').append(\'<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="REYXW5BR8H5MU"><input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></form>\');
                });
                </script>  ';                    
            } else {
                if(@function_exists('wstPROStats')) {
                    @set_time_limit(0);
                    echo wstPROStats();
                } else {
                    echo '<table class="widefat" style="width:98%;"><tr><td>';
                    _e('Your version of wpsc Support Tickets is out of date.  Please email admin@wpstorecart.com with your PayPal transaction ID to recieve the latest version.', 'wpsc-support-tickets');
                    echo '</td></tr></table>';
                }

            }
                
            
            
            echo '</div>';
            
        }

        //Prints out the admin page ================================================================================
        function printAdminPage() {
            global $wpdb;

            $output = '';
            $devOptions = $this->getAdminOptions();
            if (function_exists('current_user_can') && !current_user_can('manage_wpsct_support_tickets')) {
                die(__('Unable to Authenticate', 'wpsc-support-tickets'));
            }



            echo '
                        <script type="text/javascript">
                            jQuery(function() {
                                jQuery( "#wst_tabs" ).tabs();
                            });
                        </script>                            
                        <div class="wrap">';

            $this->adminHeader();

            echo '
                        <div id="wst_tabs" style="padding:5px 5px 0px 5px;font-size:1.1em;border-color:#DDD;border-radius:6px;">
                            <ul>
                                <li><a href="#wst_tabs-1">' . __('Open', 'wpsc-support-tickets') . '</a></li>
                                <li><a href="#wst_tabs-2">' . __('Closed', 'wpsc-support-tickets') . '</a></li>';

            wpscSupportTickets_extraTabsIndex();
            echo '
                        </ul>                             

                        ';

            $resolution = 'Open';
            $output .= '<div id="wst_tabs-1">';
            $table_name = $wpdb->prefix . "wpscst_tickets";
            $sql = "SELECT * FROM `{$table_name}` WHERE `resolution`='{$resolution}' ORDER BY `last_updated` DESC;";
            $results = $wpdb->get_results($sql, ARRAY_A);
            if (isset($results) && isset($results[0]['primkey'])) {
                if ($resolution == 'Open') {
                    $output .= '<h3>' . __('View Open Tickets:', 'wpsc-support-tickets') . '</h3>';
                } elseif ($resolution == 'Closed') {
                    $output .= '<h3>' . __('View Closed Tickets:', 'wpsc-support-tickets') . '</h3>';
                }
                $output .= '<table class="widefat" style="width:100%"><thead><tr><th>' . __('Ticket', 'wpsc-support-tickets') . '</th><th>' . __('Status', 'wpsc-support-tickets') . '</th><th>' . __('User', 'wpsc-support-tickets') . '</th><th>' . __('Last Reply', 'wpsc-support-tickets') . '</th></tr></thead><tbody>';
                foreach ($results as $result) {
                    if ($result['user_id'] != 0) {
                        @$user = get_userdata($result['user_id']);
                        $theusersname = $user->user_nicename;
                    } else {
                        $user = false; // Guest
                        $theusersname = __('Guest', 'wpsc-support-tickets');
                    }
                    if (trim($result['last_staff_reply']) == '') {
                        $last_staff_reply = __('ticket creator', 'wpsc-support-tickets');
                    } else {
                        if ($result['last_updated'] > $result['last_staff_reply']) {
                            $last_staff_reply = __('ticket creator', 'wpsc-support-tickets');
                        } else {
                            $last_staff_reply = '<strong>' . __('Staff Member', 'wpsc-support-tickets') . '</strong>';
                        }
                    }
                    $output .= '<tr><td><a href="admin.php?page=wpscSupportTickets-edit&primkey=' . $result['primkey'] . '" style="border:none;text-decoration:none;"><img style="float:left;border:none;margin-right:5px;" src="' . plugins_url('/images/page_edit.png', __FILE__) . '" alt="' . __('View', 'wpsc-support-tickets') . '"  /> ' . base64_decode($result['title']) . '</a></td><td>' . $result['resolution'] . '</td><td><a href="' . get_admin_url() . 'user-edit.php?user_id=' . $result['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $theusersname . '</a></td><td>' . date('Y-m-d g:i A', $result['last_updated']) . ' ' . __('by', 'wpsc-support-tickets') . ' ' . $last_staff_reply . '</td></tr>';
                }
                $output .= '</tbody></table>';
            }
            $output .= '</div>';
            echo $output;

            $resolution = 'Closed';
            $output = '<div id="wst_tabs-2">';
            $table_name = $wpdb->prefix . "wpscst_tickets";
            $sql = "SELECT * FROM `{$table_name}` WHERE `resolution`='{$resolution}' ORDER BY `last_updated` DESC;";
            $results = $wpdb->get_results($sql, ARRAY_A);
            if (isset($results) && isset($results[0]['primkey'])) {
                if ($resolution == 'Open') {
                    $output .= '<h3>' . __('View Open Tickets:', 'wpsc-support-tickets') . '</h3>';
                } elseif ($resolution == 'Closed') {
                    $output .= '<h3>' . __('View Closed Tickets:', 'wpsc-support-tickets') . '</h3>';
                }
                $output .= '<table class="widefat" style="width:100%"><thead><tr><th>' . __('Ticket', 'wpsc-support-tickets') . '</th><th>' . __('Status', 'wpsc-support-tickets') . '</th><th>' . __('User', 'wpsc-support-tickets') . '</th><th>' . __('Last Reply', 'wpsc-support-tickets') . '</th></tr></thead><tbody>';
                foreach ($results as $result) {
                    if ($result['user_id'] != 0) {
                        @$user = get_userdata($result['user_id']);
                        $theusersname = $user->user_nicename;
                    } else {
                        $user = false; // Guest
                        $theusersname = __('Guest', 'wpsc-support-tickets');
                    }
                    if (trim($result['last_staff_reply']) == '') {
                        $last_staff_reply = __('ticket creator', 'wpsc-support-tickets');
                    } else {
                        if ($result['last_updated'] > $result['last_staff_reply']) {
                            $last_staff_reply = __('ticket creator', 'wpsc-support-tickets');
                        } else {
                            $last_staff_reply = '<strong>' . __('Staff Member', 'wpsc-support-tickets') . '</strong>';
                        }
                    }
                    $output .= '<tr><td><a href="admin.php?page=wpscSupportTickets-edit&primkey=' . $result['primkey'] . '" style="border:none;text-decoration:none;"><img style="float:left;border:none;margin-right:5px;" src="' . plugins_url('/images/page_edit.png', __FILE__) . '" alt="' . __('View', 'wpsc-support-tickets') . '"  /> ' . base64_decode($result['title']) . '</a></td><td>' . $result['resolution'] . '</td><td><a href="' . get_admin_url() . 'user-edit.php?user_id=' . $result['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $theusersname . '</a></td><td>' . date('Y-m-d g:i A', $result['last_updated']) . ' ' . __('by', 'wpsc-support-tickets') . ' ' . $last_staff_reply . '</td></tr>';
                }
                $output .= '</tbody></table>';
            }
            $output .= '</div>';
            echo $output;



            wpscSupportTickets_extraTabsContents();

            echo '
			</div></div>';
        }

        
        function printAdminPageFields() {
            global $wpdb;


            if (function_exists('current_user_can') && !current_user_can('manage_wpsct_support_tickets') && is_numeric($_GET['primkey'])) {
                die(__('Unable to Authenticate', 'wpsc-support-tickets'));
            }
            
            if (@isset($_POST['required_info_key']) && @isset($_POST['required_info_name']) && @isset($_POST['required_info_type'])) {
                $arrayCounter = 0;
                $table_name777 = $wpdb->prefix . "wpstorecart_meta";
                foreach ($_POST['required_info_key'] as $currentKey) {
                    $updateSQL = "UPDATE  `{$table_name777}` SET  `value` =  '{$_POST['required_info_name'][$arrayCounter]}||{$_POST['required_info_required_'.$currentKey]}||{$_POST['required_info_type'][$arrayCounter]}' WHERE  `primkey` ={$currentKey};";
                    $wpdb->query($updateSQL);
                    $arrayCounter++;
                }
            }             
            
            echo '<div class="wrap">';

            $this->adminHeader();

            echo '<br style="clear:both;" /><br />
            
            <h2>'.__('','wpsc-support-tickets').'</h2>

             <script type="text/javascript">
                /* <![CDATA[ */

                function addwpscfield() {
                    jQuery.ajax({ url: "'.plugins_url().'/wpsc-support-tickets/php/add_field.php", type:"POST", data:"createnewfieldname="+jQuery("#createnewfieldname").val()+"&createnewfieldtype="+jQuery("#createnewfieldtype").val()+"&createnewfieldrequired="+jQuery("input:radio[name=createnewfieldrequired]:checked").val(), success: function(txt){
                        jQuery("#requiredul").prepend("<li style=\'font-size:90%;cursor:move;background: url('.plugins_url().'/wpsc-support-tickets/images/sort.png) top left no-repeat;width:823px;min-width:823px;height:55px;min-height:55px;padding:4px 0 0 30px;margin-bottom:-8px;\' id=\'requiredinfo_"+txt+"\'><img onclick=\'delwpscfield("+txt+");\' style=\'cursor:pointer;position:relative;top:4px;\' src=\''.plugins_url().'/wpsc-support-tickets/images/delete.png\' /><input type=\'text\' value=\'"+jQuery("#createnewfieldname").val()+"\' name=\'required_info_name[]\' /><input type=\'hidden\' name=\'required_info_key[]\' value=\'"+txt+"\' /><select name=\'required_info_type[]\' id=\'ri_"+txt+"\'><option value=\'firstname\'>'.__('First name', 'wpsc-support-tickets').'</option><option value=\'lastname\'>'.__('Last name', 'wpsc-support-tickets').'</option><option value=\'shippingaddress\'>'.__('Address', 'wpsc-support-tickets').'</option><option value=\'shippingcity\'>'.__('City', 'wpsc-support-tickets').'</option><option value=\'taxstates\'>'.__('US States', 'wpsc-support-tickets').'</option><option value=\'taxcountries\'>'.__('Countries', 'wpsc-support-tickets').'</option><option value=\'zipcode\'>'.__('Zipcode', 'wpsc-support-tickets').'</option><option value=\'email\'>'.__('Email Address', 'wpsc-support-tickets').'</option><option value=\'input (text)\'>'.__('Input (text)', 'wpsc-support-tickets').'</option><option value=\'input (numeric)\'>'.__('Input (numeric)', 'wpsc-support-tickets').'</option><option value=\'textarea\'>'.__('Input textarea', 'wpsc-support-tickets').'</option><option value=\'separator\'>--- '.__('Separator', 'wpsc-support-tickets').' ---</option><option value=\'header\'>'.__('Header', 'wpsc-support-tickets').' &lt;h2&gt;&lt;/h2&gt;</option><option value=\'text\'>'.__('Text', 'wpsc-support-tickets').' &lt;p&gt;&lt;/p&gt;</option></select><label for=\'required_info_required_"+txt+"\'><input type=\'radio\' id=\'required_info_required_"+txt+"_yes\' name=\'required_info_required_"+txt+"\' value=\'required\' /> '.__('Required', 'wpsc-support-tickets').'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for=\'required_info_required_"+txt+"_no\'><input type=\'radio\' id=\'required_info_required_"+txt+"_no\' name=\'required_info_required_"+txt+"\' value=\'optional\' /> '.__('Optional', 'wpsc-support-tickets').'</label></li>");
                        jQuery("#ri_"+txt).val(jQuery("#createnewfieldtype").val());
                        if(jQuery("input:radio[name=createnewfieldrequired]:checked").val()=="required") {
                            jQuery(\'input[name="required_info_required_\'+txt+\'"][value="required"]\').attr("checked", true);
                        } else {
                            jQuery(\'input[name="required_info_required_\'+txt+\'"][value="optional"]\').attr("checked", true);
                        }

                        jQuery("ri_"+txt).val(jQuery("#createnewfieldname").val());

                    }});
                }

                function delwpscfield(keytodel) {
                    jQuery.ajax({ url: "'.plugins_url().'/wpsc-support-tickets/php/del_field.php", type:"POST", data:"delete="+keytodel, success: function(){
                        jQuery("#requiredinfo_"+keytodel).remove();
                    }});
                }

                jQuery(document).ready(function(){

                        jQuery(function() {

                                jQuery("#requiredsort ul").sortable({ opacity: 0.6, cursor: \'move\', update: function() {
                                        var order = jQuery(this).sortable("serialize") + "&action=updateRecordsListings";
                                        jQuery.post("'.plugins_url().'/wpsc-support-tickets/php/sort_fields.php", order, function(theResponse){
                                                jQuery("#requiredsort ul").sortable(\'refresh\');
                                        });
                                }
                                });

                        });


                });

               /* ]]> */
            </script>
            ';

            /**
                 * The options for the checkout fields
                 */
            $theOptionszz[0] = 'firstname';$theOptionszzName[0] = __('First name', 'wpsc-support-tickets');
            $theOptionszz[1] = 'lastname';$theOptionszzName[1] = __('Last name', 'wpsc-support-tickets');
            $theOptionszz[2] = 'shippingaddress';$theOptionszzName[2] = __('Address', 'wpsc-support-tickets');
            $theOptionszz[3] = 'shippingcity';$theOptionszzName[3] = __('City', 'wpsc-support-tickets');
            $theOptionszz[4] = 'taxstates';$theOptionszzName[4] = __('US States', 'wpsc-support-tickets');
            $theOptionszz[5] = 'taxcountries';$theOptionszzName[5] = __('Countries', 'wpsc-support-tickets');
            $theOptionszz[6] = 'zipcode';$theOptionszzName[6] = __('Zipcode', 'wpsc-support-tickets');
            $theOptionszz[7] = 'email';$theOptionszzName[7] = __('Email Address', 'wpsc-support-tickets');
            $theOptionszz[8] = 'input (text)';$theOptionszzName[8] = __('Input (text)', 'wpsc-support-tickets');
            $theOptionszz[9] = 'input (numeric)';$theOptionszzName[9] = __('Input (numeric)', 'wpsc-support-tickets');
            $theOptionszz[10] = 'textarea';$theOptionszzName[10] = __('Input Textarea', 'wpsc-support-tickets');
            //$theOptionszz[11] = 'states';$theOptionszzName[11] = __('US States', 'wpsc-support-tickets');
            //$theOptionszz[12] = 'countries';$theOptionszzName[12] = __('Countries', 'wpsc-support-tickets');             
            $theOptionszz[13] = 'separator';$theOptionszzName[11] = __('--- Separator ---', 'wpsc-support-tickets');
            $theOptionszz[14] = 'header';$theOptionszzName[12] = __('Header &lt;h2&gt;&lt;/h2&gt;', 'wpsc-support-tickets');
            $theOptionszz[15] = 'text';$theOptionszzName[13] = __('Text &lt;p&gt;&lt;/p&gt;', 'wpsc-support-tickets');
            //$theOptionszz[11] = 'dropdown';$theOptionszzName[11] = __('Drop down list', 'wpsc-support-tickets');
            //$theOptionszz[12] = 'checkbox';$theOptionszzName[12] = __('Input Checkbox', 'wpsc-support-tickets');

            echo'
                <form action="#" method="post">
            <table class="widefat">
            <thead><tr><th>'.__('Add New Field', 'wpsc-support-tickets').': <strong>'.__('Name', 'wpsc-support-tickets').': </strong><input type="text" name="createnewfieldname" id="createnewfieldname" value="" /> <strong>'.__('Type', 'wpsc-support-tickets').': </strong><select name="createnewfieldtype" id="createnewfieldtype">';

            $icounter = 0;
            foreach ($theOptionszz as $theOption) {

                    $option = '<option value="'.$theOption.'"';
                    $option .='>';
                    $option .= $theOptionszzName[$icounter];
                    $option .= '</option>';
                    echo $option;
                    $icounter++;
            }

            echo '</select><label for="createnewfieldrequired_yes"><input type="radio" id="createnewfieldrequired_yes" name="createnewfieldrequired" value="required" checked="checked" /> '.__('Required', 'wpsc-support-tickets').'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="createnewfieldrequired_no"><input type="radio" id="createnewfieldrequired_no" name="createnewfieldrequired" value="optional" /> '.__('Optional', 'wpsc-support-tickets').'</label> <a href="#" onclick="addwpscfield();return false;"> &nbsp;  &nbsp; <button class="button-primary"><img style="cursor:pointer;" src="'.plugins_url().'/wpsc-support-tickets/images/Add.png" /> '.__('Save New Field', 'wpsc-support-tickets').'</button></a></th></tr></thead>
            <tbody><tr><td>
            <div id="requiredsort" style="margin:0 auto 0 auto;">
                <ul id="requiredul" style="margin:0 auto 0 auto;list-style:none;">
                ';

                $table_name33 = $wpdb->prefix . "wpstorecart_meta";
                $grabrecord = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-requiredinfo' ORDER BY `foreignkey` ASC;";

                $results = $wpdb->get_results( $grabrecord , ARRAY_A );
                if(isset($results)) {
                        foreach ($results as $result) {
                            $theKey = $result['primkey'];
                            $exploder = explode('||', $result['value']);
                            echo '<li style="font-size:90%;cursor:move;background: url(\''.plugins_url().'/wpsc-support-tickets/images/sort.png\') top left no-repeat;width:823px;min-width:823px;height:55px;min-height:55px;padding:4px 0 0 30px;margin-bottom:-8px;" id="requiredinfo_'.$theKey.'"><img onclick="delwpscfield('.$theKey.');" style="cursor:pointer;position:relative;top:4px;" src="'.plugins_url().'/wpsc-support-tickets/images/delete.png" /><input type="text" value="'.$exploder[0];echo '" name="required_info_name[]" /><input type="hidden" name="required_info_key[]" value="'.$theKey.'" /><select name="required_info_type[]">';

                            $icounter = 0;
                            foreach ($theOptionszz as $theOption) {

                                    $option = '<option value="'.$theOption.'"';
                                    if($theOption == $exploder[2]) {
                                            $option .= ' selected="selected"';
                                    }
                                    $option .='>';
                                    $option .= $theOptionszzName[$icounter];
                                    $option .= '</option>';
                                    echo $option;
                                    $icounter++;
                            }

                            echo '</select><label for="required_info_required_'.$theKey.'"><input type="radio" id="required_info_required_'.$theKey.'_yes" name="required_info_required_'.$theKey.'" value="required" '; if ($exploder[1]=='required') { echo 'checked="checked"'; }; echo '/> '.__('Required', 'wpsc-support-tickets').'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="required_info_required_'.$theKey.'_no"><input type="radio" id="required_info_required_'.$theKey.'_no" name="required_info_required_'.$theKey.'" value="optional" '; if ($exploder[1]=='optional') { echo 'checked="checked"'; }; echo '/> '.__('Optional', 'wpsc-support-tickets').'</label>'; echo '</li>
                                ';
                        }
                }

                echo '
                </ul><br />
            </div>
            <br style="clear:both;" />
            <button class="button-primary">'.__('Save All Edits', 'wpsc-support-tickets').'</button>

            </td></tr></tbody></table>

            </form>
            <br style="clear:both;" /><br />';            
            
            
            echo '</div>';
        }
        
        //END Prints out the admin page ================================================================================		

        function printAdminPageCreateTicket() {
            global $wpdb;

            $devOptions = $this->getAdminOptions();
            $devOptions['disable_inline_styles'] = 'false';
            
            if (function_exists('current_user_can') && !current_user_can('manage_wpsct_support_tickets') && is_numeric($_GET['primkey'])) {
                die(__('Unable to Authenticate', 'wpsc-support-tickets'));
            }
            echo '<div class="wrap">';

            $this->adminHeader();

            echo  '<br style="clear:both;" /><br />';
            
            echo  '<form action="' . plugins_url('/php/submit_ticket.php', __FILE__) . '" method="post" enctype="multipart/form-data">';

            
            

            
            echo  '<input type="hidden" name="admin_created_ticket" value="true" />';
            if (@isset($_POST['guest_email'])) {
                echo  '<input type="hidden" name="guest_email" value="' . esc_sql($_POST['guest_email']) . '" />';
            }
            echo  '<table class="widefat" ';

            echo 'style="width:100%;margin:12px;"';

            echo '><tr><th><img src="' . plugins_url('/images/Chat.png', __FILE__) . '" alt="' . __('Create a New Ticket', 'wpsc-support-tickets') . '" /> ' . __('Create a New Ticket', 'wpsc-support-tickets') . '</th></tr>';

            echo  '<tr><td><h3>' . __('Create ticket on behalf of user', 'wpsc-support-tickets') . ':</h3>';
            echo '<select name="wpscst_ticket_creator_assign" id="wpscst_ticket_creator_assign">';
            global $blog_id; 
            $wpscBlogUsers = get_users("blog_id={$blog_id}&orderby=nicename");
            if(isset($wpscBlogUsers[0])) {
                foreach ($wpscBlogUsers as $wpscTempUser) {
                    echo  "<option value=\"{$wpscTempUser->ID}\">". htmlentities($wpscTempUser->display_name)."</option> ";
                }         
            }
            echo '</select>';            
            echo '</td></tr>';                   
            
            if($devOptions['custom_field_position'] == 'before everything') {
                echo  wpsctPromptForCustomFields();
            }                            

            echo  '<tr><td><h3>' . __('Title', 'wpsc-support-tickets') . '</h3><input type="text" name="wpscst_title" id="wpscst_title" value=""  ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="width:100%;margin:12px;"';
            } echo ' /></td></tr>';

            if($devOptions['custom_field_position'] == 'before message') {
                echo  wpsctPromptForCustomFields();
            }                            

            echo  '<tr><td><h3>' . __('Your message', 'wpsc-support-tickets') . '</h3><div id="wpscst_nic_panel" ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="display:block;width:100%;"';
            } echo '></div> <textarea name="wpscst_initial_message" id="wpscst_initial_message" ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="display:inline;width:100%;margin:0 auto 0 auto;" rows="5"';
            } echo '></textarea></td></tr>';                            

            if($devOptions['custom_field_position'] == 'after message') {
                echo  wpsctPromptForCustomFields();
            }

            if ($devOptions['allow_uploads'] == 'true') {
                echo  '<tr><td><h3>' . __('Attach a file', 'wpsc-support-tickets') . '</h3> <input type="file" name="wpscst_file" id="wpscst_file"></td></tr>';
            }
            $exploder = explode('||', $devOptions['departments']);

            if($devOptions['custom_field_position'] == 'after everything') {
                echo  wpsctPromptForCustomFields();
            }                            

            echo  '<tr><td><h3>' . __('Department', 'wpsc-support-tickets') . '</h3><select name="wpscst_department" id="wpscst_department">';
            if (isset($exploder[0])) {
                foreach ($exploder as $exploded) {
                    echo  '<option value="' . $exploded . '">' . $exploded . '</option>';
                }
            }
            echo  '</select><button class="wpscst-button" id="wpscst_cancel" onclick="cancelAdd();return false;"  ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="float:right;"';
            } echo ' ><img ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="float:left;border:none;margin-right:5px;"';
            } echo ' src="' . plugins_url('/images/stop.png', __FILE__) . '" alt="' . __('Cancel', 'wpsc-support-tickets') . '" /> ' . __('Cancel', 'wpsc-support-tickets') . '</button><button class="wpscst-button" type="submit" name="wpscst_submit" id="wpscst_submit" ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="float:right;"';
            }echo '><img ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="float:left;border:none;margin-right:5px;"';
            } echo ' src="' . plugins_url('/images/page_white_text.png', __FILE__) . '" alt="' . __('Submit Ticket', 'wpsc-support-tickets') . '" /> ' . __('Submit Ticket', 'wpsc-support-tickets') . '</button></td></tr>';


            echo  '</table></form>';
            echo '</div></div></div></div>';

        }
        
        
        function printAdminPageEdit() {
            global $wpdb;

            $output = '';
            $devOptions = $this->getAdminOptions();
            if (function_exists('current_user_can') && !current_user_can('manage_wpsct_support_tickets') && is_numeric($_GET['primkey'])) {
                die(__('Unable to Authenticate', 'wpsc-support-tickets'));
            }
            echo '<div class="wrap">';

            $this->adminHeader();

            echo '<br style="clear:both;" /><br />';





            $primkey = intval($_GET['primkey']);

            $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$primkey}' LIMIT 0, 1;";
            $results = $wpdb->get_results($sql, ARRAY_A);
            if (isset($results[0])) {
                echo '<table class="widefat"><tr><td>';
                if ($results[0]['user_id'] != 0) {
                    @$user = get_userdata($results[0]['user_id']);
                    $theusersname = '<a href="' . get_admin_url() . 'user-edit.php?user_id=' . $results[0]['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $user->user_nicename . ' </a>';
                } else {
                    $user = false; // Guest
                    $theusersname = __('Guest', 'wpsc-support-tickets') . ' - <strong>' . $results[0]['email'] . '</strong>';
                }
                echo '<div id="wpscst_meta"><h1>' . base64_decode($results[0]['title']) . '</h1> (' . $results[0]['resolution'] . ' - ' . base64_decode($results[0]['type']) . ')</div>';
                echo '<table class="widefat" style="width:100%;">';
                echo '<thead><tr><th id="wpscst_results_posted_by">' . __('Posted by', 'wpsc-support-tickets') . ' ' . $theusersname . ' (<span id="wpscst_results_time_posted">' . date('Y-m-d g:i A', $results[0]['time_posted']) . '</span>)</th></tr></thead>';

                $messageData = strip_tags(base64_decode($results[0]['initial_message']), '<p><br><a><br><strong><b><u><ul><li><strike><sub><sup><img><font>');
                $messageData = explode('\\', $messageData);
                $messageWhole = '';
                foreach ($messageData as $messagePart) {
                    $messageWhole .= $messagePart;
                }
                echo '<tbody><tr><td id="wpscst_results_initial_message"><br />' . $messageWhole;
                
                echo '</tbody></table>';

                // Custom fields
                $table_name33 = $wpdb->prefix . "wpstorecart_meta";
                
                $grabrecord = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-requiredinfo' ORDER BY `foreignkey` ASC;";

                $resultscf = $wpdb->get_results( $grabrecord , ARRAY_A );
                if(isset($resultscf)) {
                        echo '<table class="widefat"><tbody>';
                        foreach ($resultscf as $field) {
                            $specific_items = explode("||", $field['value']);
                            $res = $wpdb->get_results("SELECT * FROM `{$table_name33}` WHERE `type`='wpsct_custom_{$field['primkey']}' AND `foreignkey`='{$primkey}';", ARRAY_A);
                            if(@isset($res[0]['primkey'])) {
                                echo '<tr><td><h4 style="display:inline;">'.$specific_items[0].':</h4> '.strip_tags(base64_decode($res[0]['value'])).'</td></tr>';

                            }
                        }
                        echo '</tbody></table>';                        
                }                
                

                $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_replies` WHERE `ticket_id`='{$primkey}' ORDER BY `timestamp` ASC;";
                $result2 = $wpdb->get_results($sql, ARRAY_A);
                if (isset($result2)) {
                    foreach ($result2 as $resultsX) {
                        $styleModifier1 = NULL;
                        $styleModifier2 = NULL;
                        if ($resultsX['user_id'] != 0) {
                            @$user = get_userdata($resultsX['user_id']);
                            @$userdata = new WP_User($resultsX['user_id']);
                            if ($userdata->has_cap('manage_wpsct_support_tickets')) {
                                $styleModifier1 = 'background:#FFF;';
                                $styleModifier2 = 'background:#e5e7fa;" ';
                            }
                            $theusersname = $user->user_nicename;
                        } else {
                            $user = false; // Guest
                            $theusersname = __('Guest', 'wpsc-support-tickets');
                        }

                        echo '<br /><table class="widefat" style="width:100%;' . $styleModifier1 . '">';
                        echo '<thead><tr><th class="wpscst_results_posted_by" style="' . $styleModifier2 . '">' . __('Posted by', 'wpsc-support-tickets') . ' <a href="' . get_admin_url() . 'user-edit.php?user_id=' . $resultsX['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $theusersname . '</a> (<span class="wpscst_results_timestamp">' . date('Y-m-d g:i A', $resultsX['timestamp']) . '</span>)<div style="float:right;"><a onclick="if(confirm(\'' . __('Are you sure you want to delete this reply?', 'wpsc-support-tickets') . '\')){return true;}return false;" href="' . plugins_url('/php/delete_ticket.php', __FILE__) . '?replyid=' . $resultsX['primkey'] . '&ticketid=' . $primkey . '"><img src="' . plugins_url('/images/delete.png', __FILE__) . '" alt="delete" /> ' . __('Delete Reply', 'wpsc-support-tickets') . '</a></div></th></tr></thead>';
                        $messageData = strip_tags(base64_decode($resultsX['message']), '<p><br><a><br><strong><b><u><ul><li><strike><sub><sup><img><font>');
                        $messageData = explode('\\', $messageData);
                        $messageWhole = '';
                        foreach ($messageData as $messagePart) {
                            $messageWhole .= $messagePart;
                        }
                        echo '<tbody><tr><td class="wpscst_results_message"><br />' . $messageWhole . '</td></tr>';
                        echo '</tbody></table>';
                    }
                }
                echo '</td></tr></table>';
            }
            $output .= '
                            <script>
                                jQuery(document).ready(function(){
                                    jQuery(".nicEdit-main").width("100%");
                                    jQuery(".nicEdit-main").parent().width("100%");
                                    jQuery(".nicEdit-main").height("270px");
                                    jQuery(".nicEdit-main").parent().height("270px");                                    
                                    jQuery(".nicEdit-main").parent().css( "background-color", "white" );
                                });
                            </script>
                            ';
            $output .= '<form action="' . plugins_url('/php/reply_ticket.php', __FILE__) . '" method="post" enctype="multipart/form-data"><input type="hidden" name="wpscst_is_staff_reply" value="yes" /><input type="hidden" name="wpscst_edit_primkey" value="' . $primkey . '" /><input type="hidden" name="wpscst_goback" value="yes" /> ';
            $output .= '<table class="wpscst-table" style="width:100%;display:none;">';
            $output .= '<tr><td><h3>' . __('Your message', 'wpsc-support-tickets') . '</h3><div id="wpscst_nic_panel2" style="display:block;width:100%;"></div> <textarea name="wpscst_reply" id="wpscst_reply" style="display:block;width:100%;margin:0 auto 0 auto;background-color:#FFF;" rows="5" columns="6"></textarea>';
            $output .= '</td></tr>';
            $exploder = explode('||', $devOptions['departments']);

            $output .= '<tr><td><div style="float:left;"><h3>' . __('Department', 'wpsc-support-tickets') . '</h3><select name="wpscst_department" id="wpscst_department">';
            if (isset($exploder[0])) {
                foreach ($exploder as $exploded) {
                    $output .= '<option value="' . $exploded . '"';
                    if (base64_decode($results[0]['type']) == $exploded) {
                        $output.= ' selected="selected" ';
                    } $output.='>' . $exploded . '</option>';
                }
            }
            $output .= '</select></div>
                        <div style="float:left;margin-left:20px;"><h3>' . __('Status', 'wpsc-support-tickets') . '</h3><select name="wpscst_status">
                                <option value="Open"';
            if ($results[0]['resolution'] == 'Open') {
                $output.= ' selected="selected" ';
            } $output.='>' . __('Open', 'wpsc-support-tickets') . '</option>
                                <option value="Closed"';
            if ($results[0]['resolution'] == 'Closed') {
                $output.= ' selected="selected" ';
            } $output.='>' . __('Closed', 'wpsc-support-tickets') . '</option>
                        </select></div>
                        <div style="float:left;margin-left:20px;"><h3>' . __('Actions', 'wpsc-support-tickets') . '</h3>
                            <a onclick="if(confirm(\'' . __('Are you sure you want to delete this ticket?', 'wpsc-support-tickets') . '\')){return true;}return false;" href="' . plugins_url('/php/delete_ticket.php', __FILE__) . '?ticketid=' . $primkey . '"><img src="' . plugins_url('/images/delete.png', __FILE__) . '" alt="delete" /> ' . __('Delete Ticket', 'wpsc-support-tickets') . '</a>
                        </div>';
            if ($devOptions['allow_uploads'] == 'true' && @function_exists('wpscSupportTicketsPRO')) {
                $output .= '<div style="float:left;margin-left:20px;"><h3>' . __('Attach a file', 'wpsc-support-tickets') . '</h3> <input type="file" name="wpscst_file" id="wpscst_file"></div>';
            }
            $output .='         
                        <button class="button-secondary" onclick="if(confirm(\'' . __('Are you sure you want to cancel?', 'wpsc-support-tickets') . '\')){window.location = \'' . get_admin_url() . 'admin.php?page=wpscSupportTickets-admin\';}return false;"  style="float:right;" ><img style="float:left;border:none;margin-right:5px;" src="' . plugins_url('/images/stop.png', __FILE__) . '" alt="' . __('Cancel', 'wpsc-support-tickets') . '" /> ' . __('Cancel', 'wpsc-support-tickets') . '</button> <button class="button-primary" type="submit" name="wpscst_submit" id="wpscst_submit" style="float:right;margin:0 5px 0 5px;"><img style="float:left;border:none;margin-right:5px;" src="' . plugins_url('/images/page_white_text.png', __FILE__) . '" alt="' . __('Update Ticket', 'wpsc-support-tickets') . '" /> ' . __('Update Ticket', 'wpsc-support-tickets') . '</button></td></tr>';


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
            $results = $wpdb->get_results($sql, ARRAY_A);
            if (isset($results) && isset($results[0]['primkey'])) {
                $output .= '<table class="widefat" style="width:100%"><thead><tr><th>' . __('Ticket', 'wpsc-support-tickets') . '</th><th>' . __('Status', 'wpsc-support-tickets') . '</th><th>' . __('Last Reply', 'wpsc-support-tickets') . '</th></tr></thead><tbody>';
                foreach ($results as $result) {
                    if ($result['user_id'] != 0) {
                        @$user = get_userdata($result['user_id']);
                        $theusersname = $user->user_nicename;
                    } else {
                        $user = false; // Guest
                        $theusersname = __('Guest', 'wpsc-support-tickets');
                    }
                    if (trim($result['last_staff_reply']) == '') {
                        $last_staff_reply = __('ticket creator', 'wpsc-support-tickets') . ' <a href="' . get_admin_url() . 'user-edit.php?user_id=' . $result['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $theusersname . '</a>';
                    } else {
                        if ($result['last_updated'] > $result['last_staff_reply']) {
                            $last_staff_reply = __('ticket creator', 'wpsc-support-tickets') . ' <a href="' . get_admin_url() . 'user-edit.php?user_id=' . $result['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $theusersname . '</a>';
                        } else {
                            $last_staff_reply = '<strong>' . __('Staff Member', 'wpsc-support-tickets') . '</strong>';
                        }
                    }

                    $output .= '<tr><td><a href="admin.php?page=wpscSupportTickets-edit&primkey=' . $result['primkey'] . '" style="border:none;text-decoration:none;"><img style="float:left;border:none;margin-right:5px;" src="' . plugins_url('/images/page_edit.png', __FILE__) . '" alt="' . __('View', 'wpsc-support-tickets') . '"  /> ' . base64_decode($result['title']) . '</a></td><td>' . $result['resolution'] . '</td><td>' . $last_staff_reply . '</td></tr>';
                }
                $output .= '</tbody></table>';
            } else {
                $output .= '<tr><td><i>' . __('No open tickets!', 'wpsc-support-tickets') . '</i></td><td></td><td></td></tr>';
            }
            echo $output;
        }

        // Create the function use in the action hook
        function wpscSupportTickets_main_add_dashboard_widgets() {
            if (function_exists('current_user_can') && current_user_can('manage_wpsct_support_tickets')) {
                wp_add_dashboard_widget('wpscSupportTickets_main_dashboard_widgets', __('wpscSupportTickets Overview', 'wpsc-support-tickets'), array(&$this, 'wpscSupportTickets_main_dashboard_widget_function'));
            }
        }

        function addHeaderCode() {
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-tabs');
            if (@!class_exists('AGCA')) {
                wp_enqueue_script('wpscstniceditor', plugins_url('/js/nicedit/nicEdit.js', __FILE__), array('jquery'), '1.3.2');
            }
            wp_enqueue_style('plugin_name-admin-ui-css', plugins_url('/css/custom-theme/jquery-ui-1.10.3.custom.css', __FILE__), false, 2, false);
        }
        
        function addFieldsHeaderCode() {
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-sortable');
            if (@!class_exists('AGCA')) {
                wp_enqueue_script('wpscstniceditor', plugins_url('/js/nicedit/nicEdit.js', __FILE__), array('jquery'), '1.3.2');
            }
            wp_enqueue_style('plugin_name-admin-ui-css', plugins_url('/css/custom-theme/jquery-ui-1.10.3.custom.css', __FILE__), false, 2, false);
        }        
        
        function addStatsHeaderCode() {
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-tabs');
            if (@!class_exists('AGCA')) {
                wp_enqueue_script('wpscstniceditor', plugins_url('/js/nicedit/nicEdit.js', __FILE__), array('jquery'), '1.3.2');
            }
            wp_enqueue_style('plugin_name-admin-ui-css', plugins_url('/css/custom-theme/jquery-ui-1.10.3.custom.css', __FILE__), false, 2, false);
            
            wp_enqueue_script('wpscstraphael', plugins_url().'/wpsc-support-tickets-pro/js/tufte-graph/raphael.js', array('jquery'), '1.3.2');
            wp_enqueue_script('wpscstenumerable', plugins_url().'/wpsc-support-tickets-pro/js/tufte-graph/jquery.enumerable.js', array('jquery'), '1.3.2');
            wp_enqueue_script('wpscsttufte', plugins_url().'/wpsc-support-tickets-pro/js/tufte-graph/jquery.tufte-graph.js', array('jquery'), '1.3.2');
            wp_enqueue_style('tufte-admin-ui-css', plugins_url().'/wpsc-support-tickets-pro/js/tufte-graph/tufte-graph.css', false, 2, false);
        }        

        // Installation ==============================================================================================		
        function wpscSupportTickets_install() {
            global $wpdb;
            global $wpscSupportTickets_db_version;

           
            
            $table_name = $wpdb->prefix . "wpscst_tickets";
            if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {

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
            if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {

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
            if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {

                $sql = "
                                    CREATE TABLE {$table_name} (
                                    `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                    `value` TEXT NOT NULL,
                                    `type` VARCHAR(32) NOT NULL,
                                    `foreignkey` INT NOT NULL
                                    );
                                    ";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
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

            if (session_id() == "") {
                @session_start();
            };

            if ($display == null || trim($display) == '') {
                $display = 'tickets';
            }

            $output = '';
            switch ($display) {
                case 'tickets': // =========================================================
                    if ($devOptions['allow_guests'] == 'true' && !is_user_logged_in() && !$this->hasDisplayed) {
                        if (@isset($_POST['guest_email'])) {
                            $_SESSION['wpsct_email'] = esc_sql($_POST['guest_email']);
                        }

                        $output .= '<br />
                                                <form name="wpscst-guestform" id="wpscst-guestcheckoutform" action="#" method="post">
                                                    <table>
                                                    <tr><td>' . __('Enter your email address', 'wpsc-support-tickets') . ': </td><td><input type="text" name="guest_email" value="' . $_SESSION['wpsct_email'] . '" /></td></tr>
                                                    <tr><td></td><td><input type="submit" value="' . __('Submit', 'wpsc-support-tickets') . '" class="wpsc-button wpsc-checkout" /></td></tr>
                                                    </table>
                                                </form>
                                                <br />
                                                ';
                    }
                    if (is_user_logged_in() || @isset($_SESSION['wpsct_email']) || @isset($_POST['guest_email'])) {
                        if (!$this->hasDisplayed) {
                            global $current_user;

                            $output .= '<div id="wpscst_top_page" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="display:inline;"';
                            } $output.='></div><button class="wpscst-button" id="wpscst-new" onclick="jQuery(\'.wpscst-table\').fadeIn(\'slow\');jQuery(\'#wpscst-new\').fadeOut(\'slow\');jQuery(\'#wpscst_edit_div\').fadeOut(\'slow\');jQuery(\'html, body\').animate({scrollTop: jQuery(\'#wpscst_top_page\').offset().top}, 2000);return false;"><img ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:left;border:none;margin-right:5px;"';
                            } $output.=' src="' . plugins_url('/images/Add.png', __FILE__) . '" alt="' . __('Create a New Ticket', 'wpsc-support-tickets') . '" /> ' . __('Create a New Ticket', 'wpsc-support-tickets') . '</button><br /><br />';
                            $output .= '<form action="' . plugins_url('/php/submit_ticket.php', __FILE__) . '" method="post" enctype="multipart/form-data">';
                            if (@isset($_POST['guest_email'])) {
                                $output .= '<input type="hidden" name="guest_email" value="' . esc_sql($_POST['guest_email']) . '" />';
                            }
                            $output .= '<table class="wpscst-table" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="width:100%"';
                            } 
                            $output .='><tr><th><img src="' . plugins_url('/images/Chat.png', __FILE__) . '" alt="' . __('Create a New Ticket', 'wpsc-support-tickets') . '" /> ' . __('Create a New Ticket', 'wpsc-support-tickets') . '</th></tr>';

                            if($devOptions['custom_field_position'] == 'before everything') {
                                $output .= wpsctPromptForCustomFields();
                            }                            
                            
                            $output .= '<tr><td><h3>' . __('Title', 'wpsc-support-tickets') . '</h3><input type="text" name="wpscst_title" id="wpscst_title" value=""  ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="width:100%"';
                            } $output .=' /></td></tr>';
                            
                            if($devOptions['custom_field_position'] == 'before message') {
                                $output .= wpsctPromptForCustomFields();
                            }                            
                            
                            $output .= '<tr><td><h3>' . __('Your message', 'wpsc-support-tickets') . '</h3><div id="wpscst_nic_panel" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="display:block;width:100%;"';
                            } $output.='></div> <textarea name="wpscst_initial_message" id="wpscst_initial_message" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="display:inline;width:100%;margin:0 auto 0 auto;" rows="5"';
                            } $output.='></textarea></td></tr>';                            
                            
                            if($devOptions['custom_field_position'] == 'after message') {
                                $output .= wpsctPromptForCustomFields();
                            }
                            
                            if ($devOptions['allow_uploads'] == 'true') {
                                $output .= '<tr><td><h3>' . __('Attach a file', 'wpsc-support-tickets') . '</h3> <input type="file" name="wpscst_file" id="wpscst_file"></td></tr>';
                            }
                            $exploder = explode('||', $devOptions['departments']);

                            if($devOptions['custom_field_position'] == 'after everything') {
                                $output .= wpsctPromptForCustomFields();
                            }                            
                            
                            $output .= '<tr><td><h3>' . __('Department', 'wpsc-support-tickets') . '</h3><select name="wpscst_department" id="wpscst_department">';
                            if (isset($exploder[0])) {
                                foreach ($exploder as $exploded) {
                                    $output .= '<option value="' . $exploded . '">' . $exploded . '</option>';
                                }
                            }
                            $output .= '</select><button class="wpscst-button" id="wpscst_cancel" onclick="cancelAdd();return false;"  ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:right;"';
                            } $output.=' ><img ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:left;border:none;margin-right:5px;"';
                            } $output.=' src="' . plugins_url('/images/stop.png', __FILE__) . '" alt="' . __('Cancel', 'wpsc-support-tickets') . '" /> ' . __('Cancel', 'wpsc-support-tickets') . '</button><button class="wpscst-button" type="submit" name="wpscst_submit" id="wpscst_submit" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:right;"';
                            }$output.='><img ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:left;border:none;margin-right:5px;"';
                            } $output.=' src="' . plugins_url('/images/page_white_text.png', __FILE__) . '" alt="' . __('Submit Ticket', 'wpsc-support-tickets') . '" /> ' . __('Submit Ticket', 'wpsc-support-tickets') . '</button></td></tr>';


                            $output .= '</table></form>';

                            $output .= '<form action="' . plugins_url('/php/reply_ticket.php', __FILE__) . '" method="post" enctype="multipart/form-data"><input type="hidden" value="0" id="wpscst_edit_primkey" name="wpscst_edit_primkey" />';
                            if (@isset($_POST['guest_email'])) {
                                $output .= '<input type="hidden" name="guest_email" value="' . esc_sql($_POST['guest_email']) . '" />';
                            }

                            $output .= '<div id="wpscst_edit_ticket"><div id="wpscst_edit_ticket_inner"><center><img src="' . plugins_url('/images/loading.gif', __FILE__) . '" alt="' . __('Loading', 'wpsc-support-tickets') . '" /></center></div>
                                                    <table ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="width:100%"';
                            } $output.=' id="wpscst_reply_editor_table"><tbody>
                                                    <tr id="wpscst_reply_editor_table_tr1"><td><h3>' . __('Your reply', 'wpsc-support-tickets') . '</h3><div id="wpscst_nic_panel2" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="display:block;width:100%;"';
                            }$output.='></div> <textarea name="wpscst_reply" id="wpscst_reply" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="display:inline;width:100%;margin:0 auto 0 auto;" rows="5"';
                            } $output .='></textarea></td></tr>
                                                    <tr id="wpscst_reply_editor_table_tr2"><td>';

                            if ($devOptions['allow_uploads'] == 'true') {
                                $output .= '<h3>' . __('Attach a file', 'wpsc-support-tickets') . '</h3> <input type="file" name="wpscst_file" id="wpscst_file">';
                            }

                            if ($devOptions['allow_closing_ticket'] == 'true') {
                                $output .= '
                                                        <select name="wpscst_set_status" id="wpscst_set_status">
                                                                            <option value="Open">' . __('Open', 'wpsc-support-tickets') . '</option>
                                                                            <option value="Closed">' . __('Closed', 'wpsc-support-tickets') . '</option>
                                                                    </select>            
                                                        ';
                            }

                            $output .= '<button class="wpscst-button" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:right;"';
                            } $output.=' onclick="cancelEdit();return false;"><img src="' . plugins_url('/images/stop.png', __FILE__) . '" alt="' . __('Cancel', 'wpsc-support-tickets') . '" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:left;border:none;margin-right:5px;"';
                            } $output.=' /> ' . __('Cancel', 'wpsc-support-tickets') . '</button><button class="wpscst-button" type="submit" name="wpscst_submit2" id="wpscst_submit2" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:right;"';
                            } $output.='><img ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:left;border:none;margin-right:5px;"';
                            } $output.=' src="' . plugins_url('/images/page_white_text.png', __FILE__) . '" alt="' . __('Submit Reply', 'wpsc-support-tickets') . '" /> ' . __('Submit Reply', 'wpsc-support-tickets') . '</button></td></tr>
                                                    </tbody></table>
                                                </div>';
                            $output .= '</form>';

                            // Guest additions here
                            if (is_user_logged_in()) {
                                $wpscst_userid = $current_user->ID;
                                $wpscst_email = $current_user->user_email;
                                $wpscst_username = $current_user->display_name;
                            } else {
                                $wpscst_userid = 0;
                                $wpscst_email = esc_sql($_SESSION['wpsct_email']);
                                $wpscst_username = __('Guest', 'wpsc-support-tickets') . ' (' . $wpscst_email . ')';
                            }

                            $output .= '<div id="wpscst_edit_div">';

                            if ($devOptions['allow_all_tickets_to_be_viewed'] == 'true') {
                                $sql = "SELECT * FROM `{$table_name}` ORDER BY `last_updated` DESC;";
                            }
                            if ($devOptions['allow_all_tickets_to_be_viewed'] == 'false') {
                                $sql = "SELECT * FROM `{$table_name}` WHERE `user_id`={$wpscst_userid} AND `email`='{$wpscst_email}' ORDER BY `last_updated` DESC;";
                            }

                            $results = $wpdb->get_results($sql, ARRAY_A);
                            if (isset($results) && isset($results[0]['primkey'])) {
                                $output .= '<h3>' . __('View Previous Tickets:', 'wpsc-support-tickets') . '</h3>';
                                $output .= '<table class="widefat" ';
                                if ($devOptions['disable_inline_styles'] == 'false') {
                                    $output.='style="width:100%"';
                                }$output.='><tr><th>' . __('Ticket', 'wpsc-support-tickets') . '</th><th>' . __('Status', 'wpsc-support-tickets') . '</th><th>' . __('Last Reply', 'wpsc-support-tickets') . '</th></tr>';
                                foreach ($results as $result) {
                                    if (trim($result['last_staff_reply']) == '') {
                                        if ($devOptions['allow_all_tickets_to_be_viewed'] == 'false') {
                                            $last_staff_reply = __('you', 'wpsc-support-tickets');
                                        } else {
                                            $last_staff_reply = $result['email'];
                                        }
                                    } else {
                                        if ($result['last_updated'] > $result['last_staff_reply']) {
                                            $last_staff_reply = __('you', 'wpsc-support-tickets');
                                        } else {
                                            $last_staff_reply = '<strong>' . __('Staff Member', 'wpsc-support-tickets') . '</strong>';
                                        }
                                    }
                                    if ($devOptions['allow_closing_ticket'] == 'true') {
                                        if ($result['resolution'] == 'Closed') {
                                            $canReopen = 'Reopenable';
                                        } else {
                                            $canReopen = $result['resolution'];
                                        }
                                    } else {
                                        $canReopen = $result['resolution'];
                                    }
                                    $output .= '<tr><td><a href="" onclick="loadTicket(' . $result['primkey'] . ',\'' . $canReopen . '\');return false;" ';
                                    if ($devOptions['disable_inline_styles'] == 'false') {
                                        $output.='style="border:none;text-decoration:none;"';
                                    }$output.='><img';
                                    if ($devOptions['disable_inline_styles'] == 'false') {
                                        $output.=' style="float:left;border:none;margin-right:5px;"';
                                    }$output.=' src="' . plugins_url('/images/page_edit.png', __FILE__) . '" alt="' . __('View', 'wpsc-support-tickets') . '"  /> ' . base64_decode($result['title']) . '</a></td><td>' . $result['resolution'] . '</td><td>' . date('Y-m-d g:i A', $result['last_updated']) . ' ' . __('by', 'wpsc-support-tickets') . ' ' . $last_staff_reply . '</td></tr>';
                                }
                                $output .= '</table>';
                            }
                            $output .= '</div>';
                        }
                    } else {
                        $output .= __('Please', 'wpsc-support-tickets') . ' <a href="' . wp_login_url(get_permalink()) . '">' . __('log in', 'wpsc-support-tickets') . '</a> ' . __('or', 'wpsc-support-tickets') . ' <a href="' . site_url('/wp-login.php?action=register&redirect_to=' . get_permalink()) . '">' . __('register', 'wpsc-support-tickets') . '</a>.';
                    }



                    break;
            }

            // Jetpack incompatibilities hack
            if (@!file_exists(WP_PLUGIN_DIR . '/jetpack/jetpack.php')) {
                $this->hasDisplayed = true;
            } else {
                @include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                if (@is_plugin_active(WP_PLUGIN_DIR . '/jetpack/jetpack.php')) {

                    if ($this->hasDisplayedCompat == true) {
                        if ($this->hasDisplayedCompat2 == true) {
                            $this->hasDisplayed = true;
                        }
                        $this->hasDisplayedCompat2 = true;
                    }
                    $this->hasDisplayedCompat = true;
                } else {
                    $this->hasDisplayed = true;
                }
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
            add_menu_page(__('wpsc Support Tickets', 'wpsc-support-tickets'), __('Support Tickets', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-admin', array(&$wpscSupportTickets, 'printAdminPage'), plugins_url() . '/wpsc-support-tickets/images/controller.png');
            $newTicketPage = add_submenu_page('wpscSupportTickets-admin', __('Create Ticket', 'wpsc-support-tickets'), __('Create Ticket', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-newticket', array(&$wpscSupportTickets, 'printAdminPageCreateTicket'));
            $settingsPage = add_submenu_page('wpscSupportTickets-admin', __('Settings', 'wpsc-support-tickets'), __('Settings', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-settings', array(&$wpscSupportTickets, 'printAdminPageSettings'));
            $editPage = add_submenu_page(NULL, __('Reply to Support Ticket', 'wpsc-support-tickets'), __('Reply to Support Tickets', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-edit', array(&$wpscSupportTickets, 'printAdminPageEdit'));
            $statsPage = add_submenu_page('wpscSupportTickets-admin', __('Statistics', 'wpsc-support-tickets'), __('Statistics', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-stats', array(&$wpscSupportTickets, 'printAdminPageStats'));
            if(@function_exists('wstPROStats')) {
                $statsHeaderCode = 'addStatsHeaderCode';
            } else {
                $statsHeaderCode = 'addHeaderCode';
            }
            $fieldsPage = add_submenu_page('wpscSupportTickets-admin', __('Edit User Fields Collected', 'wpsc-support-tickets'), __('User Fields', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-fields', array(&$wpscSupportTickets, 'printAdminPageFields'));
            add_action("admin_print_scripts-$newTicketPage", array(&$wpscSupportTickets, 'addHeaderCode'));
            add_action("admin_print_scripts-$editPage", array(&$wpscSupportTickets, 'addHeaderCode'));
            add_action("admin_print_scripts-$statsPage", array(&$wpscSupportTickets, $statsHeaderCode));
            add_action("admin_print_scripts-$settingsPage", array(&$wpscSupportTickets, 'addHeaderCode'));            
            add_action("admin_print_scripts-$fieldsPage", array(&$wpscSupportTickets, 'addFieldsHeaderCode'));
        }
    }

}

/**
 * ===============================================================================================================
 * END Initialize the admin panel
 */
function wpscLoadInit() {
    load_plugin_textdomain('wpsc-support-tickets', false, '/wpsc-support-tickets/languages/');

    wp_enqueue_script('wpsc-support-tickets', plugins_url() . '/wpsc-support-tickets/js/wpsc-support-tickets.js', array('jquery'));
    $wpscst_params = array(
        'wpscstPluginsUrl' => plugins_url(),
    );
    wp_localize_script('wpsc-support-tickets', 'wpscstScriptParams', $wpscst_params);
}

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
    add_action('wpsc-support-tickets/wpscSupportTickets.php', array(&$wpscSupportTickets, 'init')); // Create options on activation
    add_action('admin_menu', 'wpscSupportTicketsAdminPanel'); // Create admin panel
    add_action('wp_dashboard_setup', array(&$wpscSupportTickets, 'wpscSupportTickets_main_add_dashboard_widgets')); // Dashboard widget
    //add_action('wp_head', array(&$wpscSupportTickets, 'addHeaderCode')); // Place wpscSupportTickets comment into header
    add_shortcode('wpscSupportTickets', array(&$wpscSupportTickets, 'wpscSupportTickets_mainshortcode'));
    add_action("wp_print_scripts", array(&$wpscSupportTickets, "addHeaderCode"));
    add_action('init', 'wpscLoadInit'); // Load other languages, and javascript
}
/**
 * ===============================================================================================================
 * Call everything
 */
if (!function_exists('wpscSupportTicketsPRO')) {

    function wstPROSettingsFakeForm() {
        echo '<div style="opacity:0.5;">
<p><center><u><h3>wpsc-Support-Tickets PRO Settings:</h3></u></center></p>
        <p><strong>Allow ticket creators to upload file attachments:</strong> Set this to true if you want ticket creators to be able to upload files.  <br />
        <select name="allow_uploads" disabled>
            <option value="true">true</option><option value="false" selected="selected">false</option>
        </select>
        </p>
        
        <p><strong>Who can view &amp; administrate all tickets:</strong> Users with the following roles will have full access to edit, reply to, close, re-open, and delete all tickets.  <br />
            <ul>
            <li><input type="checkbox" name="wstpro_admin[]" value="administrator" checked disabled /> Administrator</li><li><input type="checkbox" name="wstpro_admin[]" value="editor" disabled /> Editor</li><li><input type="checkbox" name="wstpro_admin[]" value="author" disabled /> Author</li><li><input type="checkbox" name="wstpro_admin[]"  value="contributor" disabled /> Contributor</li></ul>
        </p>

        <p><strong>Allow users to close and reopen tickets?:</strong> Setting this to true, allows users (and/or guests, if the setting is turned on) to reopen or close tickets that they have permission to view.    <br />
        <select name="allow_closing_ticket" disabled>
            <option value="false" selected="selected">false</option><option value="true">true</option>
        </select>
        </p>

        <p><strong>Send HTML Emails?:</strong> Set this to true if you want emails to be sent in HTML format.  Note that you will need to add HTML markup to the emails in the Settings tab to take advantage of this feature.  <br />
        <select name="allow_html" disabled>
            <option value="false" selected="selected">false</option><option value="true">true</option>
        </select>
        </p>


        <p><strong>Allow everyone to see all tickets?:</strong> Setting this to true, allows all guests and users to view all tickets created by anyone. Do not use this setting if tickets will contain ANY confidential information, and be sure to inform your users that their information is being posted publically.  <br />
        <select name="allow_all_tickets_to_be_viewed" disabled>
            <option value="false" selected="selected">false</option><option value="true">true</option>
        </select>
        </p>

        <p id="wstpro_reply"><strong>Allow everyone to reply to all open tickets?:</strong> Setting this to true, allows users (and/or guests, if the setting is turned on) to reply to all open tickets created by anyone.  Requires the *Allow everyone to see all tickets* setting to be set to True.  Do not use this setting if tickets will contain ANY confidential information, and be sure to inform your users that their information is being posted publically.  <br />
        <select name="allow_all_tickets_to_be_replied" disabled>
            <option value="false" selected="selected">false</option><option value="true">true</option>
        </select>
        </p>     </div>       
        
        <center><div style="border:3px solid red;width:40%;padding:10px;background:#FFF;position:relative;top:-480px;">Upgrade to wpsc Support Tickets PRO to unlock this page of settings, and much more, including:<br /><br /><ul style="text-align:left;font-size:0.8em;font-weight:bold;"><li>File Uploads & attachments (optional)</li><li>Minimum level to access admin panel can be set by admin</li><li>Bulk edit many tickets at a time</li><li>Send HTML Emails</li><li>Optionally allow all users to see all tickets</li><li>Advanced ticketing system with severity, categories, departments</li><li>Optionally allow users to reopen their own tickets</li> <li>Advanced ticket overview in admin panel</li>       <li>3 Premium Wordpress Themes</li>   </ul><br />$19.99 USD<br /><div  id="buypro"></div></div></center>

        ';
    }

    add_action('wpscSupportTickets_settings', 'wstPROSettingsFakeForm');
}
?>