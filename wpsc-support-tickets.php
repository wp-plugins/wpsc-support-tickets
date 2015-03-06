<?php
/*
  Plugin Name: IDB Support Tickets
  Plugin URI: http://indiedevbundle.com/app/idb-ultimate-wordpress-bundle/#idbsupporttickets
  Description: An open source help desk and support ticket system for Wordpress using jQuery. Easy to use for both users & admins.
  Version: 4.9.49
  Author: IndieDevBundle.com
  Author URI: URI: http://indiedevbundle.com/app/idb-ultimate-wordpress-bundle/#idbsupporttickets
  License: LGPL
  Text Domain: wpsc-support-tickets
 */

/*
  Copyright 2012, 2013, 2014, 2015 Jeff Quindlen  (email : blacklodgegames@gmail.com)

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

$wpscSupportTickets_version = 4.9;
$wpscSupportTickets_db_version = 4.9;
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

function wpscSupportTickets_departmentsHook() {
    do_action('wpscSupportTickets_departmentsHook');
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

if (!function_exists('wpscSupportTickets_mail')) {
    /**
     * wpsc Support Tickets email function
     * 
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string $headers
     */
    function wpscSupportTickets_mail($to, $subject, $message, $headers='', $cc=null) {
        $devOptions = get_option('wpscSupportTicketsAdminOptions');
        if($devOptions['disable_all_emails']=='false') {
            $headers .= 'MIME-Version: 1.0' . "\r\n";

            $email_content_type = 'text/plain';
            if($devOptions['allow_html']=='true') {
                $email_content_type = 'text/html';
            }

            $headers .= "Content-type: {$email_content_type}; charset={$devOptions['email_encoding']} \r\n";                

            $headers .= 'From: ' . $devOptions['email'] . "\r\n" .
            'Reply-To: ' . $devOptions['email'] .  "\r\n";
            
            if($cc != null) {
                $headers .= "CC: " . $cc . "\r\n";                
            }
            
            $headers .= 'X-Mailer: PHP/' . phpversion();        
            wp_mail($to, $subject, $message, $headers);
        }
    }
}

require_once(WP_PLUGIN_DIR . '/wpsc-support-tickets/php/adminajax.php'); 
require_once(WP_PLUGIN_DIR . '/wpsc-support-tickets/php/publicajax.php'); 

if(!function_exists('wpsctPromptForCustomFields')) {
    /**
     * 
     * @global object $wpdb
     * @return string
     */
    function wpsctPromptForCustomFields() {
        global $wpdb;

        $devOptions = get_option('wpscSupportTicketsAdminOptions');
        $output = '';
        if (session_id() == "") {@session_start();};

        // Custom form fields here
        $table_name33 = $wpdb->prefix . 'wpstorecart_meta';
        $grabrecord = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-requiredinfo' ORDER BY `foreignkey` ASC;";

        $resultscf = $wpdb->get_results( $grabrecord , ARRAY_A );

        $wpsct_text_fields = array(
            'input (text)','shippingcity','firstname','lastname','shippingaddress','input (numeric)','zipcode'
        );
        
        $wpsct_states = array(
            'not applicable' => 'Other (Non-US)',
            'AL' => __('Alabama', 'wpsc-support-tickets'),      'AK' => __('Alaska', 'wpsc-support-tickets'),       'AZ' => __('Arizona', 'wpsc-support-tickets'),      'CA' => __('California', 'wpsc-support-tickets'),
            'CO' => __('Colorado', 'wpsc-support-tickets'),     'CT' => __('Connecticut', 'wpsc-support-tickets'),  'DE' => __('Delaware', 'wpsc-support-tickets'),     'DC' => __('District Of Columbia', 'wpsc-support-tickets'),
            'FL' => __('Florida', 'wpsc-support-tickets'),      'GA' => __('Georgia', 'wpsc-support-tickets'),      'HI' => __('Hawaii', 'wpsc-support-tickets'),       'ID' => __('Idaho', 'wpsc-support-tickets'), 
            'IL' => __('Illinois', 'wpsc-support-tickets'),     'IN' => __('Indiana', 'wpsc-support-tickets'),      'IA' => __('Iowa', 'wpsc-support-tickets'),         'KS' => __('Kansas', 'wpsc-support-tickets'),       
            'KY' => __('Kentucky', 'wpsc-support-tickets'),     'LA' => __('Louisiana', 'wpsc-support-tickets'),    'ME' => __('Maine', 'wpsc-support-tickets'),        'MD' => __('Maryland', 'wpsc-support-tickets'),
            'MA' => __('Massachusetts', 'wpsc-support-tickets'),'MI' => __('Michigan', 'wpsc-support-tickets'),     'MN' => __('Minnesota', 'wpsc-support-tickets'),    'MS' => __('Mississippi', 'wpsc-support-tickets'),  
            'MO' => __('Missouri', 'wpsc-support-tickets'),     'MT' => __('Montana', 'wpsc-support-tickets'),      'NE' => __('Nebraska', 'wpsc-support-tickets'),     'NV' => __('Nevada', 'wpsc-support-tickets'),       
            'NH' => __('New Hampshire', 'wpsc-support-tickets'),'NJ' => __('New Jersey', 'wpsc-support-tickets'),   'NM' => __('New Mexico', 'wpsc-support-tickets'),   'NY' => __('New York', 'wpsc-support-tickets'),
            'NC' => __('North Carolina', 'wpsc-support-tickets'),'ND' => __('North Dakota', 'wpsc-support-tickets'),'OH' => __('Ohio', 'wpsc-support-tickets'),         'OK' => __('Oklahoma', 'wpsc-support-tickets'),
            'OR' => __('Oregon', 'wpsc-support-tickets'),       'PA' => __('Pennsylvania', 'wpsc-support-tickets'), 'RI' => __('Rhode Island', 'wpsc-support-tickets'), 'SC' => __('South Carolina', 'wpsc-support-tickets'),
            'SD' => __('South Dakota', 'wpsc-support-tickets'), 'TN' => __('Tennessee', 'wpsc-support-tickets'),    'TX' => __('Texas', 'wpsc-support-tickets'),        'UT' => __('Utah', 'wpsc-support-tickets'),
            'VT' => __('Vermont', 'wpsc-support-tickets'),      'VA' => __('Virginia', 'wpsc-support-tickets'),     'WA' => __('Washington', 'wpsc-support-tickets'),   'WV' => __('West Virginia', 'wpsc-support-tickets'),
            'WI' => __('Wisconsin', 'wpsc-support-tickets'),    'WY' => __('Wyoming', 'wpsc-support-tickets'),
        );    
        
        $wpsct_countries = array(
            __('United States', 'wpsc-support-tickets'), __('Canada', 'wpsc-support-tickets'),__('United Kingdom', 'wpsc-support-tickets'),__('Ireland', 'wpsc-support-tickets'),__('Australia', 'wpsc-support-tickets'),__('New Zealand', 'wpsc-support-tickets'),__('Afghanistan', 'wpsc-support-tickets'),__('Albania', 'wpsc-support-tickets'),__('Algeria', 'wpsc-support-tickets'),__('American Samoa', 'wpsc-support-tickets'),
            __('Andorra', 'wpsc-support-tickets'),__('Angola', 'wpsc-support-tickets'),__('Anguilla', 'wpsc-support-tickets'),__('Antarctica', 'wpsc-support-tickets'),__('Antigua and Barbuda', 'wpsc-support-tickets'),__('Argentina', 'wpsc-support-tickets'),__('Armenia', 'wpsc-support-tickets'),__('Aruba', 'wpsc-support-tickets'),__('Austria', 'wpsc-support-tickets'),__('Azerbaijan', 'wpsc-support-tickets'),
            __('Bahamas', 'wpsc-support-tickets'),__('Bahrain', 'wpsc-support-tickets'),__('Bangladesh', 'wpsc-support-tickets'),__('Barbados', 'wpsc-support-tickets'),__('Belarus', 'wpsc-support-tickets'),__('Belgium', 'wpsc-support-tickets'),__('Belize', 'wpsc-support-tickets'),__('Benin', 'wpsc-support-tickets'),__('Bermuda', 'wpsc-support-tickets'),__('Bhutan', 'wpsc-support-tickets'),__('Bolivia', 'wpsc-support-tickets'),__('Bosnia and Herzegovina', 'wpsc-support-tickets'),
            __('Botswana', 'wpsc-support-tickets'),__('Bouvet Island', 'wpsc-support-tickets'),__('Brazil', 'wpsc-support-tickets'),__('British Indian Ocean Territory', 'wpsc-support-tickets'),__('Brunei Darussalam', 'wpsc-support-tickets'),__('Bulgaria', 'wpsc-support-tickets'),__('Burkina Faso', 'wpsc-support-tickets'),__('Burundi', 'wpsc-support-tickets'),__('Cambodia', 'wpsc-support-tickets'),
            __('Cameroon', 'wpsc-support-tickets'),__('Cape Verde', 'wpsc-support-tickets'),__('Cayman Islands', 'wpsc-support-tickets'),__('Central African Republic', 'wpsc-support-tickets'),__('Chad', 'wpsc-support-tickets'),__('Chile', 'wpsc-support-tickets'),__('China', 'wpsc-support-tickets'),__('Christmas Island', 'wpsc-support-tickets'),__('Cocos (Keeling) Islands', 'wpsc-support-tickets'),
            __('Colombia', 'wpsc-support-tickets'),__('Comoros', 'wpsc-support-tickets'),__('Congo', 'wpsc-support-tickets'),__('Congo, The Democratic Republic of The', 'wpsc-support-tickets'), __('Cook Islands', 'wpsc-support-tickets'),__('Costa Rica', 'wpsc-support-tickets'),__('Cote D\'ivoire', 'wpsc-support-tickets'),__('Croatia', 'wpsc-support-tickets'),__('Cuba', 'wpsc-support-tickets'),__('Cyprus', 'wpsc-support-tickets'),
            __('Czech Republic', 'wpsc-support-tickets'),__('Denmark', 'wpsc-support-tickets'),__('Djibouti', 'wpsc-support-tickets'),__('Dominica', 'wpsc-support-tickets'),__('Dominican Republic', 'wpsc-support-tickets'),__('East Timor', 'wpsc-support-tickets'),__('Ecuador', 'wpsc-support-tickets'),__('Egypt', 'wpsc-support-tickets'),__('El Salvador', 'wpsc-support-tickets'),__('Equatorial Guinea', 'wpsc-support-tickets'),__('Eritrea', 'wpsc-support-tickets'),__('Estonia', 'wpsc-support-tickets'),
            __('Ethiopia', 'wpsc-support-tickets'),__('Falkland Islands (Malvinas)', 'wpsc-support-tickets'),__('Faroe Islands', 'wpsc-support-tickets'),__('Fiji', 'wpsc-support-tickets'),__('Finland', 'wpsc-support-tickets'),__('France', 'wpsc-support-tickets'),__('French Guiana', 'wpsc-support-tickets'),__('French Polynesia', 'wpsc-support-tickets'),__('French Southern Territories', 'wpsc-support-tickets'),
            __('Gabon', 'wpsc-support-tickets'),__('Gambia', 'wpsc-support-tickets'),__('Georgia', 'wpsc-support-tickets'),__('Germany', 'wpsc-support-tickets'),__('Ghana', 'wpsc-support-tickets'),__('Gibraltar', 'wpsc-support-tickets'),__('Greece', 'wpsc-support-tickets'),__('Greenland', 'wpsc-support-tickets'),__('Grenada', 'wpsc-support-tickets'),__('Guadeloupe', 'wpsc-support-tickets'),__('Guam', 'wpsc-support-tickets'),__('Guatemala', 'wpsc-support-tickets'),__('Guinea', 'wpsc-support-tickets'),__('Guinea-bissau', 'wpsc-support-tickets'),
            __('Guyana', 'wpsc-support-tickets'),__('Haiti', 'wpsc-support-tickets'),__('Heard Island and Mcdonald Islands', 'wpsc-support-tickets'),__('Holy See (Vatican City State)', 'wpsc-support-tickets'),__('Honduras', 'wpsc-support-tickets'),__('Hong Kong', 'wpsc-support-tickets'),__('Hungary', 'wpsc-support-tickets'),__('Iceland', 'wpsc-support-tickets'),__('India', 'wpsc-support-tickets'),__('Indonesia', 'wpsc-support-tickets'),
            __('Iran, Islamic Republic of', 'wpsc-support-tickets'),__('Iraq', 'wpsc-support-tickets'),__('Israel', 'wpsc-support-tickets'),__('Italy', 'wpsc-support-tickets'),__('Jamaica', 'wpsc-support-tickets'),__('Japan', 'wpsc-support-tickets'),__('Jordan', 'wpsc-support-tickets'),__('Kazakhstan', 'wpsc-support-tickets'),__('Kenya', 'wpsc-support-tickets'),__('Kiribati', 'wpsc-support-tickets'),__('Korea, Democratic People\'s Republic of', 'wpsc-support-tickets'),
            __('Korea, Republic of', 'wpsc-support-tickets'),__('Kosovo', 'wpsc-support-tickets'),__('Kuwait', 'wpsc-support-tickets'),__('Kyrgyzstan', 'wpsc-support-tickets'),__('Lao People\'s Democratic Republic', 'wpsc-support-tickets'),__('Latvia', 'wpsc-support-tickets'),__('Lebanon', 'wpsc-support-tickets'),__('Lesotho', 'wpsc-support-tickets'),__('Liberia', 'wpsc-support-tickets'),__('Libyan Arab Jamahiriya', 'wpsc-support-tickets'),
            __('Liechtenstein', 'wpsc-support-tickets'),__('Lithuania', 'wpsc-support-tickets'),__('Luxembourg', 'wpsc-support-tickets'),__('Macao', 'wpsc-support-tickets'),__('Macedonia, The Republic of', 'wpsc-support-tickets'),__('Madagascar', 'wpsc-support-tickets'),__('Malawi', 'wpsc-support-tickets'),__('Malaysia', 'wpsc-support-tickets'),__('Maldives', 'wpsc-support-tickets'),__('Mali', 'wpsc-support-tickets'),
            __('Malta', 'wpsc-support-tickets'),__('Marshall Islands', 'wpsc-support-tickets'),__('Martinique', 'wpsc-support-tickets'),__('Mauritania', 'wpsc-support-tickets'),__('Mauritius', 'wpsc-support-tickets'),__('Mayotte', 'wpsc-support-tickets'),__('Mexico', 'wpsc-support-tickets'),__('Micronesia, Federated States of', 'wpsc-support-tickets'),__('Moldova, Republic of', 'wpsc-support-tickets'),
            __('Monaco', 'wpsc-support-tickets'),__('Mongolia', 'wpsc-support-tickets'),__('Montenegro', 'wpsc-support-tickets'),__('Montserrat', 'wpsc-support-tickets'),__('Morocco', 'wpsc-support-tickets'),__('Mozambique', 'wpsc-support-tickets'),__('Myanmar', 'wpsc-support-tickets'),__('Namibia', 'wpsc-support-tickets'),__('Nauru', 'wpsc-support-tickets'),__('Nepal', 'wpsc-support-tickets'),__('Netherlands', 'wpsc-support-tickets'),__('Netherlands Antilles', 'wpsc-support-tickets'),__('New Caledonia', 'wpsc-support-tickets'),
            __('Nicaragua', 'wpsc-support-tickets'),__('Niger', 'wpsc-support-tickets'),__('Nigeria', 'wpsc-support-tickets'),__('Niue', 'wpsc-support-tickets'),__('Norfolk Island', 'wpsc-support-tickets'),__('Northern Mariana Islands', 'wpsc-support-tickets'),__('Norway', 'wpsc-support-tickets'),__('Oman', 'wpsc-support-tickets'),__('Pakistan', 'wpsc-support-tickets'),__('Palau', 'wpsc-support-tickets'),
            __('Palestinian Territory, Occupied', 'wpsc-support-tickets'),__('Panama', 'wpsc-support-tickets'),__('Papua New Guinea', 'wpsc-support-tickets'),__('Paraguay', 'wpsc-support-tickets'),__('Peru', 'wpsc-support-tickets'),__('Philippines', 'wpsc-support-tickets'),__('Pitcairn', 'wpsc-support-tickets'),__('Poland', 'wpsc-support-tickets'),__('Portugal', 'wpsc-support-tickets'),__('Puerto Rico', 'wpsc-support-tickets'),
            __('Qatar', 'wpsc-support-tickets'),__('Reunion', 'wpsc-support-tickets'),__('Romania', 'wpsc-support-tickets'),__('Russian Federation', 'wpsc-support-tickets'),__('Rwanda', 'wpsc-support-tickets'),__('Saint Helena', 'wpsc-support-tickets'),__('Saint Kitts and Nevis', 'wpsc-support-tickets'),__('Saint Lucia', 'wpsc-support-tickets'),__('Saint Pierre and Miquelon', 'wpsc-support-tickets'),
            __('Saint Vincent and The Grenadines', 'wpsc-support-tickets'),__('Samoa', 'wpsc-support-tickets'),__('San Marino', 'wpsc-support-tickets'),__('Sao Tome and Principe', 'wpsc-support-tickets'),__('Saudi Arabia', 'wpsc-support-tickets'),__('Senegal', 'wpsc-support-tickets'),__('Serbia', 'wpsc-support-tickets'),__('Seychelles', 'wpsc-support-tickets'),
            __('Sierra Leone', 'wpsc-support-tickets'),__('Singapore', 'wpsc-support-tickets'),__('Slovakia', 'wpsc-support-tickets'),__('Slovenia', 'wpsc-support-tickets'),__('Solomon Islands', 'wpsc-support-tickets'),__('Somalia', 'wpsc-support-tickets'),__('South Africa', 'wpsc-support-tickets'),__('South Georgia and The South Sandwich Islands', 'wpsc-support-tickets'),__('South Sudan', 'wpsc-support-tickets'),__('Spain', 'wpsc-support-tickets'),
            __('Sri Lanka', 'wpsc-support-tickets'),__('Sudan', 'wpsc-support-tickets'),__('Suriname', 'wpsc-support-tickets'),__('Svalbard and Jan Mayen', 'wpsc-support-tickets'),__('Swaziland', 'wpsc-support-tickets'),__('Sweden', 'wpsc-support-tickets'),__('Switzerland', 'wpsc-support-tickets'),__('Syrian Arab Republic', 'wpsc-support-tickets'),__('Taiwan, Province of China', 'wpsc-support-tickets'),
            __('Tajikistan', 'wpsc-support-tickets'),__('Tanzania, United Republic of', 'wpsc-support-tickets'),__('Thailand', 'wpsc-support-tickets'),__('Timor-leste', 'wpsc-support-tickets'),__('Togo', 'wpsc-support-tickets'),__('Tokelau', 'wpsc-support-tickets'),__('Tonga', 'wpsc-support-tickets'),__('Trinidad and Tobago', 'wpsc-support-tickets'),__('Tunisia', 'wpsc-support-tickets'),__('Turkey', 'wpsc-support-tickets'),
            __('Turkmenistan', 'wpsc-support-tickets'),__('Turks and Caicos Islands', 'wpsc-support-tickets'),__('Tuvalu', 'wpsc-support-tickets'),__('Uganda', 'wpsc-support-tickets'),__('Ukraine', 'wpsc-support-tickets'),__('United Arab Emirates', 'wpsc-support-tickets'),__('United States Minor Outlying Islands', 'wpsc-support-tickets'),__('Uruguay', 'wpsc-support-tickets'),
            __('Uzbekistan', 'wpsc-support-tickets'),__('Vanuatu', 'wpsc-support-tickets'),__('Venezuela', 'wpsc-support-tickets'),__('Vietnam', 'wpsc-support-tickets'),__('Virgin Islands, British', 'wpsc-support-tickets'),__('Virgin Islands, U.S.', 'wpsc-support-tickets'),__('Wallis and Futuna', 'wpsc-support-tickets'),__('Western Sahara', 'wpsc-support-tickets'),__('Yemen', 'wpsc-support-tickets'),
            __('Zambia', 'wpsc-support-tickets'),__('Zimbabwe', 'wpsc-support-tickets'),
        );    

        $wpsct_style_width = '';
        $wpsct_style_inline = '';
        if ($devOptions['disable_inline_styles'] === 'false') {
            $wpsct_style_width ='style="width:100%"';
            $wpsct_style_inline = 'style="display:inline;"';
        }  

        if(isset($resultscf)) {
                foreach ($resultscf as $field) {
                    $specific_items = explode('||', $field['value']);
                    $wpsct_required_item = '';
                    if ($specific_items[1]=='required'){ 
                        $wpsct_required_item = '<ins><div class="wpst-required-symbol" ' . $wpsct_style_inline . '>* </div></ins>';
                    }
                    @$prev_val = $_SESSION['wpsct_custom_'.$field['primkey']];
                    foreach ($wpsct_text_fields as $wpsct_text_field) {
                        if($specific_items[2]==$wpsct_text_field) {
                            @$output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item
                                . '</h3><input  id="wpsct_custom_'.$field['primkey'].'" type="text"  value="'
                                . $_SESSION['wpsct_custom_'.$field['primkey']] .'" name="wpsct_custom_'.$field['primkey'].'" ' 
                                . $wpsct_style_width . '  /></td></tr>';
                        }
                    }
                    if ($specific_items[2] === 'textarea') {
                        $output .= '<tr><td><h3>' . $specific_items[0] . $wpsct_required_item
                            . '</h3><textarea  id="wpsct_custom_' . $field['primkey'] . '" name="wpsct_custom_' 
                            . $field['primkey'] . '" ' . $wpsct_style_width . '>' 
                            . $_SESSION['wpsct_custom_' . $field['primkey']] . '</textarea></td></tr>';
                    }
                    if($specific_items[2]==='states' || $specific_items[2]==='taxstates') {
                        $selected[$prev_val] = ' selected="selected"';
                        $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item 
                            . '</h3><select name="wpsct_custom_'.$field['primkey'].'" class="wpsct-states" ' . $wpsct_style_width . ">\n"
                            . "<option value=\"not applicable\" {$selected['']}>" . __('Other (Non-US)', 'wpsc-support-tickets').'</option>'; 
                        foreach ($wpsct_states as $wpsct_code => $wpcst_state) {
                            $output .="<option value=\"$wpsct_code\" {$selected[$wpsct_code]}>" . __($wpcst_state, 'wpsc-support-tickets').'</option>\n'; 
                        }
                        $output.= '</select></td></tr>';
                    }
                    if($specific_items[2]==='countries' || $specific_items[2]==='taxcountries') {
                        $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item
                            . '</h3><select  name="wpsct_custom_'.$field['primkey'].'" class="wpsct-countries" '. $wpsct_style_width . ">\n";
                        $selected[$prev_val] = ' selected="selected"';
                        foreach ($wpsct_countries as $wpsct_country) {
                            $output .="<option value=\"$wpsct_country\" {$selected[$wpsct_country]}>" . __($wpsct_country, 'wpsc-support-tickets').'</option>\n'; 
                        }
                        $output.= '</select></td></tr>';                    
                    }
                    if($specific_items[2]==='email') {
                        $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item
                            . '</h3><input  id="wpsct_custom_'.$field['primkey'].'" type="text"  value="'
                            . $_SESSION['wpsct_custom_'.$field['primkey']].'" name="wpsct_custom_'.$field['primkey'].'" ' . $wpsct_style_width
                            . ' /></td></tr>';
                    }
                    if($specific_items[2]==='separator') {
                        $output .= '<tr><td><center>'.$specific_items[0].'</center></td></tr>';
                    }
                    if($specific_items[2]==='header') {
                        $output .= '<tr><td><h2>'.$specific_items[0] .'</h2></td></tr>';
                    }
                    if($specific_items[2]==='text') {
                        $output .= '<tr><td>'.$specific_items[0] .'</td></tr>';
                    }
                    /** Added in 4.4.4 **/
                    if($specific_items[2]==='dropdown') {
                        $grabrecordz = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-custom-fields-mc' AND `foreignkey`='{$field['primkey']}';";
                        $resultszz = $wpdb->get_results( $grabrecordz , ARRAY_A );                         
                        if(@isset($resultszz[0]['primkey'])) {
                            $zzzoptions = explode(',', rtrim($resultszz[0]['value'], ',' ) );
                            $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item . '</h3>';
                            $output .= '<select id="wpsct_custom_'.$field['primkey'].'" name="wpsct_custom_'.$field['primkey'].'">';
                            foreach ($zzzoptions as $zzzoption) {
                                $output .= '   <option value="'.$zzzoption.'">'.$zzzoption.'</option>';
                            }
                            $output .= '</select>';
                            $output .= '</td></tr>';
                        }
                    }
                    if($specific_items[2]==='checkbox') {
                        $grabrecordz = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-custom-fields-mc' AND `foreignkey`='{$field['primkey']}';";
                        $resultszz = $wpdb->get_results( $grabrecordz , ARRAY_A );                         
                        if(@isset($resultszz[0]['primkey'])) {
                            $zzzoptions = explode(',', rtrim($resultszz[0]['value'], ',' ) );
                            $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item . '</h3>';
                            foreach ($zzzoptions as $zzzoption) {
                                $output .= '   <input type="checkbox" name="wpsct_custom_'.$field['primkey'].'[]" value="'.$zzzoption.'"></input> '.$zzzoption.'<br />';
                            }
                            $output .= '</td></tr>';
                        }                        
                    }
                    if($specific_items[2]==='radio') {
                        $grabrecordz = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-custom-fields-mc' AND `foreignkey`='{$field['primkey']}';";
                        $resultszz = $wpdb->get_results( $grabrecordz , ARRAY_A );                         
                        if(@isset($resultszz[0]['primkey'])) {
                            $zzzoptions = explode(',', rtrim($resultszz[0]['value'], ',' ) );
                            $output .= '<tr><td><h3>'. $specific_items[0] . $wpsct_required_item . '</h3>';
                            foreach ($zzzoptions as $zzzoption) {
                                $output .= '   <input type="radio" name="wpsct_custom_'.$field['primkey'].'" value="'.$zzzoption.'"></input> '.$zzzoption.'<br />';
                            }
                            $output .= '</td></tr>';
                        }                              
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

        /**
         * 
         * @global object $wp_roles
         */
        function wpscSupportTickets() { //constructor
            // Let's make sure the admin is always in charge
            if (!is_multisite()) {
                $this->addPermissions(); // 
            }
            $wsetting = $this->getAdminOptions();
            if ($wsetting['override_mysql_timezone']=='true') {
                $this->forceTimezoneSync();
            }
            unset($wsetting);
        }
       
        function forceTimezoneSync() {
            global $wpdb;
            unset($wpdb);
            $wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
            $wpdb->db_connect();
            $dt = new DateTime();
            $offset = $dt->format("P");
            $wpdb->query("SET time_zone='$offset';");            
        }
        
        /**
         * Beginnings of the new permission system for 5.0
         */
        function checkPermissions() {
            if (!is_super_admin()) {
                if ( function_exists('current_user_can') && !current_user_can('manage_wpsct_support_tickets') ) {
                    wp_die(__('Unable to Authenticate', 'wpsc-support-tickets'));
                }            
            }
        }
        
        function addPermissions() {
            if (is_user_logged_in()) {
                if (is_super_admin() || is_admin()) {
                    global $wp_roles;
                    add_role('wpsct_support_ticket_manager', 'Support Ticket Manager', array('manage_wpsct_support_tickets', 'read', 'upload_files', 'publish_posts', 'edit_published_posts', 'publish_pages', 'edit_published_pages', 'Keymaster', 'keymaster', 'keep_gate', 'bbp_forums_admin', 'bbp_topics_admin', 'bbp_replies_admin'));
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'read');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'upload_files');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'publish_pages');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'publish_posts');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'edit_published_posts');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'edit_published_pages');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'manage_wpsct_support_tickets');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'keep_gate');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'bbp_forums_admin');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'bbp_topics_admin');
                    $wp_roles->add_cap('wpsct_support_ticket_manager', 'bbp_replies_admin');
                    $wp_roles->add_cap('bbp_keymaster', 'manage_wpsct_support_tickets');
                    $wp_roles->add_cap('administrator', 'manage_wpsct_support_tickets');
                }
            }            
        }
        
        /**
         * 
         * @param string $e
         * @return string
         */
        function change_mail_from( $e ) {
            $settings = $this->getAdminOptions();
            return $settings['overrides_email'];
        }
        
        /**
         * 
         * @param string $n
         * @return string
         */         
        function change_mail_name( $n ) {
            $settings = $this->getAdminOptions();
            return $settings['email_name'];
        }       
        
        
        /**
         * Returns an array of admin options
         * @return array
         */
        function getAdminOptions() {

            // Default values.  
            $apAdminOptions = array('mainpage' => '',
                'turnon_wpscSupportTickets' => 'true',
                'departments' => '',
                'email' => get_bloginfo('admin_email'),
                'email_new_ticket_subject' => __('Your support ticket was received.', 'wpsc-support-tickets'),
                'email_new_ticket_body' => __('Thank you for opening a new support ticket.  We will look into your issue and respond as soon as possible.', 'wpsc-support-tickets'),
                'email_new_reply_subject' => __('Your support ticket reply was received.', 'wpsc-support-tickets'),
                'email_new_reply_body' => __('A reply was posted to one of your support tickets.', 'wpsc-support-tickets'),
                'disable_inline_styles' => 'false',
                'allow_guests' => 'false',
                'allow_all_tickets_to_be_replied' => 'false',
                'allow_all_tickets_to_be_viewed' => 'false',
                'allow_html' => 'false',
                'allow_closing_ticket' => 'false',
                'allow_uploads' => 'false',
                'custom_field_position' => 'after message',
                'custom_field_frontend_position' => 'after message',
                'use_ticket_in_email' => 'true',
                'use_reply_in_email' => 'true',
                'department_admins' => 'default',
                'email_name' => __('Support', 'wpsc-support-tickets'),
                'hide_email_on_frontend_list' => 'false',
                'email_encoding' => 'utf-8',
                'hide_email_on_support_tickets' => 'false',
                'enable_beta_testing' => 'false',
                'disable_all_emails' => 'false',
                'override_wordpress_email' => 'false',
                'overrides_email' => get_bloginfo('admin_email'),
                'custom_title' => __('Title', 'wpsc-support-tickets'),
                'custom_message' => __('Your message', 'wpsc-support-tickets'),
                'show_login_text' => 'true',
                'override_mysql_timezone' => 'false',
                'show_advanced_options' => 'false',
                'converted_departments_phase2' => 'false',
                'custom_new_ticket_button_text' => __('Create a New Ticket', 'wpsc-support-tickets'),
                'cc_all_new_tickets' => 'false',
                'cc_all_new_tickets_to_email' => get_bloginfo('admin_email'),
                'cc_all_user_replies' => 'false',
                'cc_all_user_replies_to_email' => get_bloginfo('admin_email'),
                'cc_all_admin_replies' => 'false',
                'cc_all_admin_replies_to_email' => get_bloginfo('admin_email'),
                'allow_search' => 'false'
            );             
            
            if ($this->wpscstSettings != NULL) { // If we haven't cached stuff already
                $devOptions = $this->wpscstSettings; // Caches the settings array so that we don't keep reinitializing it
            } else {
                $devOptions = get_option($this->adminOptionsName);
            }
            if (!empty($devOptions)) { // If the default values don't exist.
                     
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

            $this->checkPermissions();

            echo '
            
            <div style="padding: 20px 10px 10px 10px;">';

            if (!function_exists('wpscSupportTicketsPRO')) {
                echo '<div style="float:left;"><img src="' , plugins_url() , '/wpsc-support-tickets/images/logo.png" alt="wpscSupportTickets" /></div>';
            } else {
                echo '<div style="float:left;"><img src="' , plugins_url() , '/wpsc-support-tickets/images/logo_pro.png" alt="wpscSupportTickets" /></div>';
            }

            if (function_exists('wpscSupportTicketsPRO') && !function_exists('wpsctFourNineFortyOne')) {
                echo '<div style="float:left;border:1px solid red;padding:6px;background:pink;"><h3>Your version of IDB Support Tickets PRO is out of date and will not work correctly until you upgrade to the latest version.  There is now a much more powerful Departments system, but you must upgrade your PRO version to take advantage of the new features.  Failure to do so will result in bugs related to your Departments.  You can upgrade by logging in and redownloading your purchase here: <a href="http://indiedevbundle.com/bundles/your-downloads/">http://indiedevbundle.com/bundles/your-downloads/</a>  If you are having problems accessing your downloads please contact me here: <a href="http://indiedevbundle.com/contact/">http://indiedevbundle.com/contact/</a></h3></div><br />';
            }

            echo '
            </div>
            <br style="clear:both;" />
            ';
        }


        /*
         * Admin page for departments
         * Added in wpsc Support Tickets v5.0
         */
        function printAdminPageDepartments() {
      
            $devOptions = $this->getAdminOptions();

            echo '<div class="wrap">';
            $this->adminHeader();

            wpscSupportTickets_departmentsHook(); // Action hook for departments

            echo '</div>';
        }        
        
        /*
         * Admin page for Settings
         */
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
                if (isset($_POST['email'])) {
                    $devOptions['email'] = esc_sql($_POST['email']);
                }
                if (isset($_POST['email_new_ticket_subject'])) {
                    $devOptions['email_new_ticket_subject'] = esc_sql($_POST['email_new_ticket_subject']);
                }
                if (isset($_POST['email_new_ticket_body'])) {
                    $devOptions['email_new_ticket_body'] = esc_sql($_POST['email_new_ticket_body']);
                }
                if (isset($_POST['email_new_reply_subject'])) {
                    $devOptions['email_new_reply_subject'] = esc_sql($_POST['email_new_reply_subject']);
                }
                if (isset($_POST['email_new_reply_body'])) {
                    $devOptions['email_new_reply_body'] = esc_sql($_POST['email_new_reply_body']);
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
                if (isset($_POST['display_severity_on_create'])) {
                    $devOptions['display_severity_on_create'] = esc_sql($_POST['display_severity_on_create']);
                }
                if(isset($_POST['email_name'])) {
                    $devOptions['email_name'] = esc_sql($_POST['email_name']);
                }
                if(isset($_POST['hide_email_on_frontend_list'])) {
                    $devOptions['hide_email_on_frontend_list'] = esc_sql($_POST['hide_email_on_frontend_list']);
                }    
                if(isset($_POST['email_encoding'])) {
                    $devOptions['email_encoding'] = esc_sql($_POST['email_encoding']);
                }                 
                if(isset($_POST['hide_email_on_support_tickets'])) {
                    $devOptions['hide_email_on_support_tickets'] = esc_sql($_POST['hide_email_on_support_tickets']);
                } 
                if(isset($_POST['enable_beta_testing'])) {
                    $devOptions['enable_beta_testing'] = esc_sql($_POST['enable_beta_testing']);
                }      
                if(isset($_POST['disable_all_emails'])) {
                    $devOptions['disable_all_emails'] = esc_sql($_POST['disable_all_emails']);
                }                      
                if(isset($_POST['override_wordpress_email'])) {
                    $devOptions['override_wordpress_email'] = esc_sql($_POST['override_wordpress_email']);
                }
                if(isset($_POST['overrides_email'])) {
                    $devOptions['overrides_email'] = esc_sql($_POST['overrides_email']);
                }                
                if(isset($_POST['custom_title'])) {
                    $devOptions['custom_title'] = esc_sql($_POST['custom_title']);
                }
                if(isset($_POST['custom_message'])) {
                    $devOptions['custom_message'] = esc_sql($_POST['custom_message']);
                }                
                if(isset($_POST['show_login_text'])) {
                    $devOptions['show_login_text'] = esc_sql($_POST['show_login_text']);
                }                      
                if(isset($_POST['override_mysql_timezone'])) {
                    $devOptions['override_mysql_timezone'] = esc_sql($_POST['override_mysql_timezone']);
                }
                if(isset($_POST['show_advanced_options'])) {
                    $devOptions['show_advanced_options'] = esc_sql($_POST['show_advanced_options']);
                }
                if(isset($_POST['custom_new_ticket_button_text'])) {
                    $devOptions['custom_new_ticket_button_text'] = esc_sql($_POST['custom_new_ticket_button_text']);
                }                
 
                if(isset($_POST['cc_all_new_tickets'])) {
                    $devOptions['cc_all_new_tickets'] = esc_sql($_POST['cc_all_new_tickets']);
                }  
                if(isset($_POST['cc_all_new_tickets_to_email'])) {
                    $devOptions['cc_all_new_tickets_to_email'] = esc_sql($_POST['cc_all_new_tickets_to_email']);
                }  
                if(isset($_POST['cc_all_user_replies'])) {
                    $devOptions['cc_all_user_replies'] = esc_sql($_POST['cc_all_user_replies']);
                }                  
                if(isset($_POST['cc_all_user_replies_to_email'])) {
                    $devOptions['cc_all_user_replies_to_email'] = esc_sql($_POST['cc_all_user_replies_to_email']);
                }  
                if(isset($_POST['cc_all_admin_replies'])) {
                    $devOptions['cc_all_admin_replies'] = esc_sql($_POST['cc_all_admin_replies']);
                }  
                if(isset($_POST['cc_all_admin_replies_to_email'])) {
                    $devOptions['cc_all_admin_replies_to_email'] = esc_sql($_POST['cc_all_admin_replies_to_email']);
                }  
                if(isset($_POST['allow_search'])) {
                    $devOptions['allow_search'] = esc_sql($_POST['allow_search']);
                }                  
                update_option($this->adminOptionsName, $devOptions);

                echo '<div class="updated"><p><strong>';
                _e('Settings Updated.', 'wpsc-support-tickets');
                echo '</strong></p></div>';
            }

            echo '
                
            <script type="text/javascript">
                jQuery(function() {
                    jQuery( "#wst_tabs" ).tabs();
                    setTimeout(function(){ jQuery(".updated").fadeOut(); },3000);
                });
            </script>

            <form method="post" action="' , $_SERVER["REQUEST_URI"] , '">
                

        <div id="wst_tabs" style="padding:5px 5px 0px 5px;font-size:1.1em;border-color:#DDD;border-radius:6px;">
            <ul>
                <li><a href="#wst_tabs-1">' , __('General', 'wpsc-support-tickets') , '</a></li>
                <li><a href="#wst_tabs-3">' , __('Email', 'wpsc-support-tickets') , '</a></li>
                <li><a href="#wst_tabs-4">' , __('Styling', 'wpsc-support-tickets') , '</a></li>    
                <li><a href="#wst_tabs-5">' , __('Guests', 'wpsc-support-tickets') , '</a></li>    
                <li><a href="#wst_tabs-6">' , __('Custom Fields', 'wpsc-support-tickets') , '</a></li>    
                <li><a href="#wst_tabs-2">' , __('PRO', 'wpsc-support-tickets') , '</a></li>
            </ul>        
            
            
            <div id="wst_tabs-1">

            <br />
            <h1>' , __('General', 'wpsc-support-tickets') , '</h1>
            <table class="widefat" style="background:#FFF;"><tr><td>

                <p><strong>' , __('Main Page', 'wpsc-support-tickets') , ':</strong> ' , __('You need to use a Page as the base for wpsc Support Tickets.', 'wpsc-support-tickets') , '  <br />
                <select name="wpscSupportTicketsmainpage">
                 <option value="">';
                esc_attr(__('Select page', 'wpsc-support-tickets'));
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
                    </p>';

                echo '<p><strong>' , __('Allow user to select Severity on ticket creation?', 'wpsc-support-tickets') , ':</strong> ' , __('Set this to true if you want the user to select the severity of their ticket when creating it.', 'wpsc-support-tickets') , '  <br />
                <select name="display_severity_on_create">
                 ';

                $pagesYXX[0] = 'true';
                $pagesYXX[1] = 'false';
                foreach ($pagesYXX as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg === $devOptions['display_severity_on_create']) {
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
                
                <p><strong>' , __('Show Advanced Settings?', 'wpsc-support-tickets') , ':</strong> ' , __('By default, some advanced settings are hidden that are either rarely used or that can problems.  Set this to true if you want to enable Advanced Settings.  Note that you will need to save your settings before any change will occur.', 'wpsc-support-tickets') , '  <br />
                <select name="show_advanced_options">
                 ';

                $pagesYXX[0] = 'true';
                $pagesYXX[1] = 'false';
                foreach ($pagesYXX as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg === $devOptions['show_advanced_options']) {
                        $option .= ' selected="selected"';
                    }
                    $option .='>';
                    $option .= $pagg;
                    $option .= '</option>';
                    echo $option;
                }

                echo '
                </select>
                </p> ';

                if($devOptions['show_advanced_options']=='true') {
                
                            echo '
                            <p><strong>' , __('Enabled & Test Beta Features?', 'wpsc-support-tickets') , ':</strong> ' , __('Set this to true ONLY ON TEST websites.  This will enable new beta features as they are released. ', 'wpsc-support-tickets') , '  <br />
                            <select name="enable_beta_testing">
                             ';

                            $pagesYXX[0] = 'true';
                            $pagesYXX[1] = 'false';
                            foreach ($pagesYXX as $pagg) {
                                $option = '<option value="' . $pagg . '"';
                                if ($pagg === $devOptions['enable_beta_testing']) {
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

                            <p style="padding:5px;border:1px dotted black;">
            ';  

            // New search system, now in beta                            
            if ($devOptions['enable_beta_testing'] == 'true') {
                                        echo '<br />
                                        <strong>', __('BETA', 'wpsc-support-tickets') ,': ' , __('Enable Ticket Search?', 'wpsc-support-tickets') , ':</strong> ' , __('Set this to "true" to show a ticket search box on the "Main Page" you set. If you have "Allow Guests" set to "false" and "Allow everyone to see all tickets" (PRO only) set to "false", then search will only work for registered users and they can only search their own tickets.  If you have "Allow Guests" set to "true", but "Allow everyone to see all tickets" (PRO only) set to "false", then users and guests can search, but are limited to their own tickets.   If you have "Allow Guests" set to "true", and "Allow everyone to see all tickets" (PRO only) set to "true", then users and guests can search everyone\'s tickets, making everything publicly searchable. ', 'wpsc-support-tickets') , '  <br />
                                        <select name="allow_search">
                                         ';

                                        $pagesYXX[0] = 'true';
                                        $pagesYXX[1] = 'false';
                                        foreach ($pagesYXX as $pagg) {
                                            $option = '<option value="' . $pagg . '"';
                                            if ($pagg === $devOptions['allow_search']) {
                                                $option .= ' selected="selected"';
                                            }
                                            $option .='>';
                                            $option .= $pagg;
                                            $option .= '</option>';
                                            echo $option;
                                        }

                                        echo '
                                        </select>
                                        <br /><br /> ';    
            }                            
                            
            echo ' 
            <img src="' , plugins_url() , '/wpsc-support-tickets/images/bug_report.png" alt="' , __('Warning', 'wpsc-support-tickets') , '" style="float:left;" /> <strong style="font-size:1.2em;">' , __('Warning', 'wpsc-support-tickets') , ' - ' , __('This may fix issues on incorrectly configured servers, but it comes at a performance cost of an additional database connection and an additional query on every page load.  Generally, you should only turn this on if tickets do not change who replied last, and always say the Last Poster was the ticket creator, no matter how many times an admin makes a reply.  You should not change this setting unless you believe that your PHP timezone and MySQL are not set to the same thing, as evidence by the Last Poster issue.  If you turn this on when it is not needed, you will only slow down the performance of your website with no benefits. ', 'wpsc-support-tickets') , '</strong><br style="clear:both;"  /><br />
            <strong>' , __('Force Sync MySQL timezone to PHP timezone?', 'wpsc-support-tickets') , ':</strong> ' , __('Set this to true if you want to make emails come from your wpsc Support Ticket admin email below, and to change your sent from name to your Blog\'s name.', 'wpsc-support-tickets') , '  <br />
                            <select name="override_mysql_timezone">
                             ';

                            $pagesY[0] = 'true';
                            $pagesY[1] = 'false';
                            foreach ($pagesY as $pagg) {
                                $option = '<option value="' . $pagg . '"';
                                if ($pagg === $devOptions['override_mysql_timezone']) {
                                    $option .= ' selected="selected"';
                                }
                                $option .='>';
                                $option .= $pagg;
                                $option .= '</option>';
                                echo $option;
                            }

                            echo '
                            </select>
                            <br />
                                </p>';
                }
                
            echo '

            </td></tr></table>
            <br /><br /><br />
            </div>
            <div id="wst_tabs-3">            
            <h1>' , __('Email', 'wpsc-support-tickets') , '</h1>
            <table class="widefat" style="background:#FFF;"><tr><td>                

                <strong>' , __('Email', 'wpsc-support-tickets') , ':</strong> ' , __('The admin email where all new ticket &amp; reply notification emails will be sent', 'wpsc-support-tickets') , '<br /><input name="email" value="' . $devOptions['email'] . '" style="width:95%;" /><br /><br />
                
                <strong>' , __('New Ticket Email', 'wpsc-support-tickets') , '</strong> ' , __('The subject &amp; body of the email sent to the customer when creating a new ticket.', 'wpsc-support-tickets') , '<br /><input name="email_new_ticket_subject" value="' , stripslashes(stripslashes($devOptions['email_new_ticket_subject'])) , '" style="width:95%;" />
                <textarea style="width:95%;" name="email_new_ticket_body">' , stripslashes(stripslashes($devOptions['email_new_ticket_body'])) , '</textarea>
                <br /><br />
                
                <p><strong>' , __('Include the ticket in New Ticket Email', 'wpsc-support-tickets') , ':</strong> ' , __('Set this to true if you want the content of the ticket included in the new ticket email.', 'wpsc-support-tickets') , '  <br />
                <select name="use_ticket_in_email">
                 ';

                $pagesY[0] = 'true';
                $pagesY[1] = 'false';
                foreach ($pagesY as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg === $devOptions['use_ticket_in_email']) {
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

                <strong>' , __('New Reply Email', 'wpsc-support-tickets') , '</strong> ' , __('The subject &amp; body of the email sent to the customer when there is a new reply.', 'wpsc-support-tickets') , '<br /><input name="email_new_reply_subject" value="' , stripslashes(stripslashes($devOptions['email_new_reply_subject'])) , '" style="width:95%;" />
                <textarea style="width:95%;" name="email_new_reply_body">' , stripslashes(stripslashes($devOptions['email_new_reply_body'])) , '</textarea>
                <br /><br />
                
                <p><strong>' , __('Include the reply in New Reply Email', 'wpsc-support-tickets') , ':</strong> ' , __('Set this to true if you want the content of the reply included in the new reply email.', 'wpsc-support-tickets') , '  <br />
                <select name="use_reply_in_email">
                 ';

                $pagesY[0] = 'true';
                $pagesY[1] = 'false';
                foreach ($pagesY as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg === $devOptions['use_reply_in_email']) {
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
                
                <p><strong>' , __('Email Charset Encoding', 'wpsc-support-tickets') , ':</strong> ' , __('You may need to change this if email text is encoding incorrectly.  The default is utf-8.', 'wpsc-support-tickets') , '  <br />
                <select name="email_encoding">
                 ';

                $pagesYxxx[0] = 'utf-8';
                $pagesYxxx[1] = 'iso-8859-1';
                $pagesYxxx[2] = 'koi8-r';
                $pagesYxxx[3] = 'gb2312';
                $pagesYxxx[4] = 'big5';
                $pagesYxxx[5] = 'shift_jis';
                $pagesYxxx[5] = 'euc-jp';
                foreach ($pagesYxxx as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg === $devOptions['email_encoding']) {
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

                <p><strong>' , __('Disable All Emails', 'wpsc-support-tickets') , ':</strong> ' , __('Set this to true if you want to turn off all emails from wpsc Support Tickets.', 'wpsc-support-tickets') , '  <br />
                <select name="disable_all_emails">
                 ';

                $pagesY[0] = 'true';
                $pagesY[1] = 'false';
                foreach ($pagesY as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg === $devOptions['disable_all_emails']) {
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
                ';
                
                if ($devOptions['show_advanced_options'] == 'true') {
                    
                    echo '
                    <div style="padding:5px;border:1px dotted black;">
                    <p><strong>' , __('CC All New Tickets?', 'wpsc-support-tickets') , ':</strong> ' , __('Set this to true if you want to CC the below email address on all new tickets.', 'wpsc-support-tickets') , '  <br />
                    <select name="cc_all_new_tickets">
                     ';

                    $pagesY[0] = 'true';
                    $pagesY[1] = 'false';
                    foreach ($pagesY as $pagg) {
                        $option = '<option value="' . $pagg . '"';
                        if ($pagg === $devOptions['cc_all_new_tickets']) {
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
                    <strong>' , __('Email Address to CC on New Tickets', 'wpsc-support-tickets') , '</strong> ' , __('When enabled, the email address to CC on new tickets.', 'wpsc-support-tickets') , '<br /><input name="cc_all_new_tickets_to_email" value="' , $devOptions['cc_all_new_tickets_to_email'] , '" style="width:95%;" />
                    <br /><br />                    
                    ';                    
                    
                    
                    echo '
                    <p><strong>' , __('CC All User Replies?', 'wpsc-support-tickets') , ':</strong> ' , __('Set this to true if you want to CC the below email address on all user replies to tickets.', 'wpsc-support-tickets') , '  <br />
                    <select name="cc_all_user_replies">
                     ';

                    $pagesY[0] = 'true';
                    $pagesY[1] = 'false';
                    foreach ($pagesY as $pagg) {
                        $option = '<option value="' . $pagg . '"';
                        if ($pagg === $devOptions['cc_all_user_replies']) {
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
                    <strong>' , __('Email Address to CC on All User Replies', 'wpsc-support-tickets') , '</strong> ' , __('When enabled, the email address to CC on new user replies.', 'wpsc-support-tickets') , '<br /><input name="cc_all_user_replies_to_email" value="' , $devOptions['cc_all_user_replies_to_email'] , '" style="width:95%;" />
                    <br /><br />                    
                    ';                       
                    
                    
                    
                    echo '
                    <p><strong>' , __('CC All Admin Replies?', 'wpsc-support-tickets') , ':</strong> ' , __('Set this to true if you want to CC the below email address on all admin replies to tickets.', 'wpsc-support-tickets') , '  <br />
                    <select name="cc_all_admin_replies">
                     ';

                    $pagesY[0] = 'true';
                    $pagesY[1] = 'false';
                    foreach ($pagesY as $pagg) {
                        $option = '<option value="' . $pagg . '"';
                        if ($pagg === $devOptions['cc_all_admin_replies']) {
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
                    <strong>' , __('Email Address to CC on All Admin Replies', 'wpsc-support-tickets') , '</strong> ' , __('When enabled, the email address to CC on new admin replies.', 'wpsc-support-tickets') , '<br /><input name="cc_all_admin_replies_to_email" value="' , $devOptions['cc_all_admin_replies_to_email'] , '" style="width:95%;" />
                    <br /><br />   
                    </div>
                    <br /><br />  
                    ';                      
                    
                    
                    
                                        echo ' 
                                        <p style="padding:5px;border:1px dotted black;">
                        <img src="' , plugins_url() , '/wpsc-support-tickets/images/bug_report.png" alt="' , __('Warning', 'wpsc-support-tickets') , '" style="float:left;" /> <strong style="font-size:1.2em;">' , __('Warning', 'wpsc-support-tickets') , ' - ' , __('Activating these overrides can cause registration emails to fail in some circumstances.  Please double check that new users receive their registration emails after enabling this override.', 'wpsc-support-tickets') , '</strong><br style="clear:both;"  /><br />
                        <strong>' , __('Override Wordpress Email Sent "Name" &amp; "From"', 'wpsc-support-tickets') , ':</strong> ' , __('Set this to true if you want to make emails come from your wpsc Support Ticket admin email below, and to change your sent from name to your Blog\'s name.', 'wpsc-support-tickets') , '  <br />
                                        <select name="override_wordpress_email">
                                         ';

                                        $pagesY[0] = 'true';
                                        $pagesY[1] = 'false';
                                        foreach ($pagesY as $pagg) {
                                            $option = '<option value="' . $pagg . '"';
                                            if ($pagg === $devOptions['override_wordpress_email']) {
                                                $option .= ' selected="selected"';
                                            }
                                            $option .='>';
                                            $option .= $pagg;
                                            $option .= '</option>';
                                            echo $option;
                                        }

                                        echo '
                                        </select>
                                        <br />
                                        <strong>' , __('Override Name Sent From', 'wpsc-support-tickets') ,'</strong> ', __('The name of the admin email sender, such as "Business Name Support Team", or whatever is appropriate for your situation.', 'wpsc-support-tickets') ,'<br /><input name="email_name" value="' , $devOptions['email_name'] , '" style="width:95%;" /><br /><br />
                                        <strong>' , __('Override Email Sent From', 'wpsc-support-tickets') ,'</strong> ', __('The email address that all outgoing emails will appear to be sent from.', 'wpsc-support-tickets') ,'<br /><input name="overrides_email" value="' , $devOptions['overrides_email'] , '" style="width:95%;" /><br /><br />
                                            </p>';
                }
                
                echo ' 
            </td></tr></table>
            <br /><br /><br />
            </div>
            <div id="wst_tabs-4">            
            <h1>' , __('Styling', 'wpsc-support-tickets') , '</h1>
            <table class="widefat" style="background:#FFF;"><tr><td> 

                <p><strong>' , __('Disable inline styles', 'wpsc-support-tickets') , ':</strong> ' , __('Set this to true if you want to disable the inline CSS styles.', 'wpsc-support-tickets') , '  <br />
                <select name="disable_inline_styles">
                 ';

                $pagesX[0] = 'true';
                $pagesX[1] = 'false';
                foreach ($pagesX as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg === $devOptions['disable_inline_styles']) {
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
            </div>
            <div id="wst_tabs-5">            
            <h1>' , __('Guests', 'wpsc-support-tickets') , '</h1>
            <table class="widefat" style="background:#FFF;"><tr><td> 

                <p><strong>' , __('Allow Guests', 'wpsc-support-tickets') , ':</strong> ' , __('Set this to true if you want Guests to be able to use the support ticket system.', 'wpsc-support-tickets') , '  <br />
                <select name="allow_guests">
                 ';

                $pagesY[0] = 'true';
                $pagesY[1] = 'false';
                foreach ($pagesY as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg === $devOptions['allow_guests']) {
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
                
                <p><strong>' , __('Show guest email address in front end list', 'wpsc-support-tickets') , ':</strong> ' , __('Set this to true and a guest\'s email address will be displayed in the ticket list.', 'wpsc-support-tickets') , '  <br />
                <select name="hide_email_on_frontend_list">
                 ';

                $pagesXn[0] = 'true';
                $pagesXn[1] = 'false';
                foreach ($pagesXn as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg === $devOptions['hide_email_on_frontend_list']) {
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
                

                <p><strong>' , __('Show guest email address on support tickets', 'wpsc-support-tickets') , ':</strong> ' , __('Set this to true and a guest\'s email address will be displayed in support tickets.', 'wpsc-support-tickets') , '  <br />
                <select name="hide_email_on_support_tickets">
                 ';

                $pagesXnr[0] = 'true';
                $pagesXnr[1] = 'false';
                foreach ($pagesXnr as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg === $devOptions['hide_email_on_support_tickets']) {
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

                

                <p><strong>' , __('Show login/register links?', 'wpsc-support-tickets') , ':</strong> ' , __('Set this to false if you do not want a link to register or login to appear when a user is not logged in.', 'wpsc-support-tickets') , '  <br />
                <select name="show_login_text">
                 ';

                $pagesXnr[0] = 'true';
                $pagesXnr[1] = 'false';
                foreach ($pagesXnr as $pagg) {
                    $option = '<option value="' . $pagg . '"';
                    if ($pagg === $devOptions['show_login_text']) {
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
            </div>
            <div id="wst_tabs-6">            
            <h1>' , __('Custom Fields', 'wpsc-support-tickets') , '</h1>
            <table class="widefat" style="background:#FFF;"><tr><td> 

                <p><strong>' , __('Place custom form fields', 'wpsc-support-tickets') , ':</strong> ' , __('When creating a ticket, this determines where your custom fields are placed on the ticket submission form.', 'wpsc-support-tickets') , '  <br />
                <select name="custom_field_position">
                 ';

                $pagesXX[0]['valname'] = 'before everything';$pagesXX[0]['displayname'] = __('before everything', 'wpsc-support-tickets');
                $pagesXX[1]['valname'] = 'before message';$pagesXX[1]['displayname'] = __('before message', 'wpsc-support-tickets');
                $pagesXX[2]['valname'] = 'after message';$pagesXX[2]['displayname'] = __('after message', 'wpsc-support-tickets');
                $pagesXX[3]['valname'] = 'after everything';$pagesXX[3]['displayname'] = __('after everything', 'wpsc-support-tickets');

                foreach ($pagesXX as $pagg) {
                    $option = '<option value="' . $pagg['valname'] . '"';
                    if ($pagg['valname'] === $devOptions['custom_field_position']) {
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

                <p><strong>' , __('Display custom fields', 'wpsc-support-tickets') , ':</strong> ' , __('When a ticket creator views a ticket they created, this setting determines where the custom fields are placed on the page.', 'wpsc-support-tickets') , '  <br />
                <select name="custom_field_frontend_position">
                 ';

                $pagesXY[0]['valname'] = 'before everything';$pagesXY[0]['displayname'] = __('before everything', 'wpsc-support-tickets');
                $pagesXY[1]['valname'] = 'before message';$pagesXY[1]['displayname'] = __('before message', 'wpsc-support-tickets');
                $pagesXY[2]['valname'] = 'after message';$pagesXY[2]['displayname'] = __('after message &amp; replies', 'wpsc-support-tickets');

                foreach ($pagesXY as $pagg) {
                    $option = '<option value="' . $pagg['valname'] . '"';
                    if ($pagg['valname'] === $devOptions['custom_field_frontend_position']) {
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
                
                <strong>' , __('Change TITLE to', 'wpsc-support-tickets') , ':</strong> ' , __('By default, a user must fill out a title when submitting a ticket.  This setting lets you easily change the name of the word TITLE so that you word this appropriately for your situation.', 'wpsc-support-tickets') , '<br /><input name="custom_title" value="' . $devOptions['custom_title'] . '" style="width:95%;" /><br /><br />
                
                <strong>' , __('Change MESSAGE to', 'wpsc-support-tickets') , ':</strong> ' , __('By default, a user must fill out a message when submitting a ticket.  This setting lets you easily change the name of the words YOUR MESSAGE so that you word this appropriately for your situation.', 'wpsc-support-tickets') , '<br /><input name="custom_message" value="' . $devOptions['custom_message'] . '" style="width:95%;" /><br /><br />
                ';
                
                echo '<strong>' , __('Change New Ticket button text to', 'wpsc-support-tickets') , ':</strong> ' , __('By default, this button says Create a New Ticket, but here you can change it to whatever you want.', 'wpsc-support-tickets') , '<br /><input name="custom_new_ticket_button_text" value="' . $devOptions['custom_new_ticket_button_text'] . '" style="width:95%;" /><br /><br />';
                
                echo ' 

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


        }

        //Prints out the admin page ================================================================================
        function printAdminPageStats() {

            echo '<div class="wrap">';
            
            $this->adminHeader();
                        
            
            
            if (@!function_exists('wpscSupportTicketsPRO') ) {
                echo '           <div id="wst_tabs" style="padding:5px 5px 0px 5px;font-size:1.1em;border-color:#DDD;border-radius:6px;">
            <ul>
                <li><a href="#wstct_tabs-1">' , __('Statistics', 'wpsc-support-tickets') , '</a></li>
            </ul>        
            <script type="text/javascript">
                jQuery(function() {
                    jQuery( "#wst_tabs" ).tabs();
                    setTimeout(function(){ jQuery(".updated").fadeOut(); },3000);
                });
            </script>

            <div id="wstct_tabs-1">  <table class="widefat" style="width:98%;"><tr><td>';

                    echo '

            <div id="idb_bt_wrap">
                <iframe class="idb_bt_site" src="http://indiedevbundle.com/app/idb-ultimate-wordpress-bundle/#idbsupporttickets"></iframe>
            </div>

            <style type="text/css">

            #idb_bt_wrap {
                width: 100%;
                padding-bottom: 55%;
                background: orange;
            }
            .idb_bt_site{
                position: absolute;
                top: 48px; 
                left: 0;
                width: 100%;
                height: 87%;
            }
            </style>
                    ';                  
                
                echo '</td></tr></table></div></div>';                    
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
                                <li><a href="#wst_tabs-1">' , __('Open', 'wpsc-support-tickets') , '</a></li>
                                <li><a href="#wst_tabs-2">' , __('Closed', 'wpsc-support-tickets') , '</a></li>';

            wpscSupportTickets_extraTabsIndex();
            echo '
                            
                        </ul>                             

                        ';

            $resolution = 'Open';
            $output .= '               
            <div id="wst_tabs-1">';
            
            $table_name = $wpdb->prefix . "wpscst_tickets";
            $sql = "SELECT * FROM `{$table_name}` WHERE `resolution`='{$resolution}' ORDER BY `last_updated` DESC;";
            $results = $wpdb->get_results($sql, ARRAY_A);
            if (isset($results) && isset($results[0]['primkey'])) {
                if ($resolution === 'Open') {
                    $output .= '<h3>' . __('View Open Tickets:', 'wpsc-support-tickets') . '</h3>';
                } elseif ($resolution === 'Closed') {
                    $output .= '<h3>' . __('View Closed Tickets:', 'wpsc-support-tickets') . '</h3>';
                }
                $output .= '<table class="widefat" style="width:100%"><thead><tr><th>' . __('ID', 'wpsc-support-tickets') . '</th><th>' . __('Ticket', 'wpsc-support-tickets') . '</th><th>' . __('Status', 'wpsc-support-tickets') . '</th><th>' . __('User', 'wpsc-support-tickets') . '</th><th>' . __('Last Reply', 'wpsc-support-tickets') . '</th></tr></thead><tbody>';
                foreach ($results as $result) {
                    if ($result['user_id'] != 0) {
                        @$user = get_userdata($result['user_id']);
                        $theusersname = $user->user_nicename;
                    } else {
                        $user = false; // Guest
                        $theusersname = __('Guest', 'wpsc-support-tickets');
                    }
                    if (trim($result['last_staff_reply']) === '') {
                        $last_staff_reply = __('ticket creator', 'wpsc-support-tickets');
                    } else {
                        if ($result['last_updated'] > $result['last_staff_reply']) {
                            $last_staff_reply = __('ticket creator', 'wpsc-support-tickets');
                        } else {
                            $last_staff_reply = '<strong>' . __('Staff Member', 'wpsc-support-tickets') . '</strong>';
                        }
                    }
                    $output .= '<tr><td>' . $result['primkey'] . '</td><td><a href="admin.php?page=wpscSupportTickets-edit&primkey=' . $result['primkey'] . '" style="border:none;text-decoration:none;"><img style="float:left;border:none;margin-right:5px;" src="' . plugins_url('/images/page_edit.png', __FILE__) . '" alt="' . __('View', 'wpsc-support-tickets') . '"  /> ' . base64_decode($result['title']) . '</a></td><td>' . $result['resolution'] . '</td><td><a href="' . get_admin_url() . 'user-edit.php?user_id=' . $result['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $theusersname . '</a></td><td>' . date_i18n( get_option( 'date_format' ), $result['last_updated']) . ' ' . __('by', 'wpsc-support-tickets') . ' ' . $last_staff_reply . '</td></tr>';
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
                $output .= '<table class="widefat" style="width:100%"><thead><tr><th>' . __('ID', 'wpsc-support-tickets') . '</th><th>' . __('Ticket', 'wpsc-support-tickets') . '</th><th>' . __('Status', 'wpsc-support-tickets') . '</th><th>' . __('User', 'wpsc-support-tickets') . '</th><th>' . __('Last Reply', 'wpsc-support-tickets') . '</th></tr></thead><tbody>';
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
                    $output .= '<tr><td>' . $result['primkey'] . '</td><td><a href="admin.php?page=wpscSupportTickets-edit&primkey=' . $result['primkey'] . '" style="border:none;text-decoration:none;"><img style="float:left;border:none;margin-right:5px;" src="' . plugins_url('/images/page_edit.png', __FILE__) . '" alt="' . __('View', 'wpsc-support-tickets') . '"  /> ' . base64_decode($result['title']) . '</a></td><td>' . $result['resolution'] . '</td><td><a href="' . get_admin_url() . 'user-edit.php?user_id=' . $result['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $theusersname . '</a></td><td>' . date_i18n( get_option( 'date_format' ), $result['last_updated']) . ' ' . __('by', 'wpsc-support-tickets') . ' ' . $last_staff_reply . '</td></tr>';
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

            echo '<div class="wrap">';

            $this->adminHeader();
            
        echo '<div id="wst_tabs" style="padding:5px 5px 0px 5px;font-size:1.1em;border-color:#DDD;border-radius:6px;">
            <ul>
                <li><a href="#wstcf_tabs-1">' , __('User Fields', 'wpsc-support-tickets') , '</a></li>
            </ul>        
            <script type="text/javascript">
                jQuery(function() {
                    jQuery( "#wst_tabs" ).tabs();
                    setTimeout(function(){ jQuery(".updated").fadeOut(); },3000);
                });
            </script>

            <div id="wstcf_tabs-1">    ';         
            
            if (@isset($_POST['required_info_key']) && @isset($_POST['required_info_name']) && @isset($_POST['required_info_type'])) {
                $arrayCounter = 0;
                $table_name777 = $wpdb->prefix . "wpstorecart_meta";
                foreach ($_POST['required_info_key'] as $currentKey) {
                    $updateSQL = "UPDATE  `{$table_name777}` SET  `value` =  '{$_POST['required_info_name'][$arrayCounter]}||{$_POST['required_info_required_'.$currentKey]}||{$_POST['required_info_type'][$arrayCounter]}' WHERE  `primkey` ={$currentKey};";
                    $wpdb->query($updateSQL);
                    $arrayCounter++;
                }
            }             
            

            echo '<br style="clear:both;" /><br />
            
            <h2> </h2>

             <script type="text/javascript">
                /* <![CDATA[ */

                function addwpscfield() {
                
                    temp_mc_var = "";
                    
                    jQuery(".custom_field_mc").each(function() {
                        temp_mc_var = temp_mc_var + jQuery(this).val() + ",";
                    });
                    
                    jQuery.ajax({ url: ajaxurl, type:"POST", data:"action=wpsct_add_field&createnewfieldname="+jQuery("#createnewfieldname").val()+"&createnewfieldtype="+jQuery("#createnewfieldtype").val()+"&createnewfieldrequired="+jQuery("input:radio[name=createnewfieldrequired]:checked").val()+"&customfieldmc="+temp_mc_var.toString(), success: function(txt){
                        jQuery("#requiredul").prepend("<li style=\'font-size:90%;cursor:move;background-color: #EDEDED; border: 1px solid #DDDDDD;padding:4px 0 4px 30px;margin:8px;\' id=\'requiredinfo_"+txt+"\'><img onclick=\'delwpscfield("+txt+");\' style=\'cursor:pointer;position:relative;top:4px;\' src=\''.plugins_url().'/wpsc-support-tickets/images/delete.png\' /><input type=\'text\' value=\'"+jQuery("#createnewfieldname").val()+"\' name=\'required_info_name[]\' /><input type=\'hidden\' name=\'required_info_key[]\' value=\'"+txt+"\' /><select name=\'required_info_type[]\' id=\'ri_"+txt+"\'><option value=\'firstname\'>',__('First name', 'wpsc-support-tickets'),'</option><option value=\'lastname\'>',__('Last name', 'wpsc-support-tickets'),'</option><option value=\'shippingaddress\'>',__('Address', 'wpsc-support-tickets'),'</option><option value=\'shippingcity\'>',__('City', 'wpsc-support-tickets'),'</option><option value=\'taxstates\'>',__('US States', 'wpsc-support-tickets'),'</option><option value=\'taxcountries\'>',__('Countries', 'wpsc-support-tickets'),'</option><option value=\'zipcode\'>',__('Zipcode', 'wpsc-support-tickets'),'</option><option value=\'email\'>',__('Email Address', 'wpsc-support-tickets'),'</option><option value=\'input (text)\'>',__('Input (text)', 'wpsc-support-tickets'),'</option><option value=\'input (numeric)\'>',__('Input (numeric)', 'wpsc-support-tickets'),'</option><option value=\'textarea\'>',__('Input textarea', 'wpsc-support-tickets'),'</option><option value=\'dropdown\'>',__('Input Dropdown list', 'wpsc-support-tickets'),'</option><!--<option value=\'checkbox\'>',__('Input Checkbox', 'wpsc-support-tickets'),'</option>--><option value=\'radio\'>',__('Input Radio button', 'wpsc-support-tickets'),'</option><option value=\'separator\'>--- ',__('Separator', 'wpsc-support-tickets'),' ---</option><option value=\'header\'>',__('Header', 'wpsc-support-tickets'),' &lt;h2&gt;&lt;/h2&gt;</option><option value=\'text\'>',__('Text', 'wpsc-support-tickets'),' &lt;p&gt;&lt;/p&gt;</option></select><label for=\'required_info_required_"+txt+"\'><input type=\'radio\' id=\'required_info_required_"+txt+"_yes\' name=\'required_info_required_"+txt+"\' value=\'required\' /> ',__('Required', 'wpsc-support-tickets'),'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for=\'required_info_required_"+txt+"_no\'><input type=\'radio\' id=\'required_info_required_"+txt+"_no\' name=\'required_info_required_"+txt+"\' value=\'optional\' /> ',__('Optional', 'wpsc-support-tickets'),'</label></li>");
                        //jQuery("#requiredul").prepend("<li style=\'font-size:90%;cursor:move;background-color: #EDEDED; border: 1px solid #DDDDDD;padding:4px 0 4px 30px;margin:8px;\' id=\'requiredinfo_"+txt+"\'><img onclick=\'delwpscfield("+txt+");\' style=\'cursor:pointer;position:relative;top:4px;\' src=\''.plugins_url().'/wpsc-support-tickets/images/delete.png\' /><input type=\'text\' value=\'"+jQuery("#createnewfieldname").val()+"\' name=\'required_info_name[]\' /><input type=\'hidden\' name=\'required_info_key[]\' value=\'"+txt+"\' /><select name=\'required_info_type[]\' id=\'ri_"+txt+"\'><option value=\'firstname\'>',__('First name', 'wpsc-support-tickets'),'</option><option value=\'lastname\'>',__('Last name', 'wpsc-support-tickets'),'</option><option value=\'shippingaddress\'>',__('Address', 'wpsc-support-tickets'),'</option><option value=\'shippingcity\'>',__('City', 'wpsc-support-tickets'),'</option><option value=\'taxstates\'>',__('US States', 'wpsc-support-tickets'),'</option><option value=\'taxcountries\'>',__('Countries', 'wpsc-support-tickets'),'</option><option value=\'zipcode\'>',__('Zipcode', 'wpsc-support-tickets'),'</option><option value=\'email\'>',__('Email Address', 'wpsc-support-tickets'),'</option><option value=\'input (text)\'>',__('Input (text)', 'wpsc-support-tickets'),'</option><option value=\'input (numeric)\'>',__('Input (numeric)', 'wpsc-support-tickets'),'</option><option value=\'textarea\'>',__('Input textarea', 'wpsc-support-tickets'),'</option><option value=\'dropdown\'>',__('Input Dropdown list', 'wpsc-support-tickets'),'</option><option value=\'checkbox\'>',__('Input Checkbox', 'wpsc-support-tickets'),'</option><option value=\'radio\'>',__('Input Radio button', 'wpsc-support-tickets'),'</option><option value=\'separator\'>--- ',__('Separator', 'wpsc-support-tickets'),' ---</option><option value=\'header\'>',__('Header', 'wpsc-support-tickets'),' &lt;h2&gt;&lt;/h2&gt;</option><option value=\'text\'>',__('Text', 'wpsc-support-tickets'),' &lt;p&gt;&lt;/p&gt;</option></select><label for=\'required_info_required_"+txt+"\'><input type=\'radio\' id=\'required_info_required_"+txt+"_yes\' name=\'required_info_required_"+txt+"\' value=\'required\' /> ',__('Required', 'wpsc-support-tickets'),'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for=\'required_info_required_"+txt+"_no\'><input type=\'radio\' id=\'required_info_required_"+txt+"_no\' name=\'required_info_required_"+txt+"\' value=\'optional\' /> ',__('Optional', 'wpsc-support-tickets'),'</label></li>");
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
                    jQuery.ajax({ url: ajaxurl, type:"POST", data:"action=wpsct_del_field&delete="+keytodel, success: function(){
                        jQuery("#requiredinfo_"+keytodel).remove();
                    }});
                }
                
                function wpstCheckFieldType() {
                    if ( jQuery("#createnewfieldtype").val()=="dropdown" ||  jQuery("#createnewfieldtype").val()=="checkbox" ||  jQuery("#createnewfieldtype").val()=="radio" ) {
                        jQuery(".wpstAddNewAdditionalOptionButton").show();
                        jQuery("#wpsc-support-tickets-custom-field").show();
                    } else {
                        jQuery(".wpstAddNewAdditionalOptionButton").hide();
                        jQuery("#wpsc-support-tickets-custom-field").hide();
                    }
                    return false;
                }

                function wpstCreateNewOption() {
                    jQuery("#wpsc-support-tickets-custom-field").append(\'<div><br />|_ <input name="" class="custom_field_mc" /> <img style="cursor:pointer;position:relative;top:4px;" src="',plugins_url(),'/wpsc-support-tickets/images/delete.png" onclick="jQuery(this).parent().remove();" /></div>\');
                }

                jQuery(document).ready(function(){

                        jQuery(function() {

                                jQuery("#requiredsort ul").sortable({ opacity: 0.6, cursor: \'move\', update: function() {
                                        var order = jQuery(this).sortable("serialize") + "&action=wpsct_sort_fields";
                                        jQuery.post(ajaxurl, order, function(theResponse){
                                                jQuery("#requiredsort ul").sortable(\'refresh\');
                                        });
                                }
                                });

                        wpstCheckFieldType();

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
            
            $theOptionszz[11] = 'dropdown';$theOptionszzName[11] = __('Input Dropdown list', 'wpsc-support-tickets');
            //$theOptionszz[12] = 'checkbox';$theOptionszzName[12] = __('Input Checkbox', 'wpsc-support-tickets');            
            $theOptionszz[12] = 'radio';$theOptionszzName[12] = __('Input Radio button', 'wpsc-support-tickets');            
            
            $theOptionszz[13] = 'separator';$theOptionszzName[13] = __('--- Separator ---', 'wpsc-support-tickets');
            $theOptionszz[14] = 'header';$theOptionszzName[14] = __('Header &lt;h2&gt;&lt;/h2&gt;', 'wpsc-support-tickets');
            $theOptionszz[15] = 'text';$theOptionszzName[15] = __('Text &lt;p&gt;&lt;/p&gt;', 'wpsc-support-tickets');


            echo'
                <form action="#" method="post">
            <table class="widefat">
            <thead><tr><th><h2>',__('Add New Field', 'wpsc-support-tickets'),'</h2><br /><strong>',__('Name', 'wpsc-support-tickets'),': </strong><input type="text" name="createnewfieldname" id="createnewfieldname" value="" /> <br /><strong>',__('Type', 'wpsc-support-tickets'),': </strong><select name="createnewfieldtype" id="createnewfieldtype" onchange="wpstCheckFieldType();">';

            $icounter = 0;
            foreach ($theOptionszz as $theOption) {

                    $option = '<option value="'.$theOption.'"';
                    $option .='>';
                    $option .= $theOptionszzName[$icounter];
                    $option .= '</option>';
                    echo $option;
                    $icounter++;
            }

            echo '</select>  <button style="display:none;" id="wpstAddNewAdditionalOptionButton" class="button-secondary wpstAddNewAdditionalOptionButton" onclick="wpstCreateNewOption();return false;">',__('Add additional option', 'wpsc-support-tickets'),'</button>
            
            <div id="wpsc-support-tickets-custom-field" style="margin-left:40px;display:none;">
                |_ <input name="" class="custom_field_mc" /> 
            </div>
            

            <br /><label for="createnewfieldrequired_yes"><input type="radio" id="createnewfieldrequired_yes" name="createnewfieldrequired" value="required" checked="checked" /> ',__('Required', 'wpsc-support-tickets'),'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="createnewfieldrequired_no"><input type="radio" id="createnewfieldrequired_no" name="createnewfieldrequired" value="optional" /> ',__('Optional', 'wpsc-support-tickets'),'</label> <br /> <a href="#" style="margin-left:35%;" onclick="addwpscfield();return false;"> &nbsp;  &nbsp; <button class="button-primary"><img style="cursor:pointer;" src="',plugins_url(),'/wpsc-support-tickets/images/Add.png" /> ',__('Save New Field', 'wpsc-support-tickets'),'</button></a></th></tr></thead>
                
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
                            echo '<li style="font-size:90%;cursor:move;background-color: #EDEDED; border: 1px solid #DDDDDD;padding:4px 0 4px 30px;margin:8px;" id="requiredinfo_',$theKey,'"><img onclick="delwpscfield(',$theKey,');" style="cursor:pointer;position:relative;top:4px;" src="',plugins_url(),'/wpsc-support-tickets/images/delete.png" /><input type="text" value="',$exploder[0], '" name="required_info_name[]" /><input type="hidden" name="required_info_key[]" value="',$theKey,'" /><select name="required_info_type[]">';

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
                            
                            echo '</select>';
                            
                            echo '<label for="required_info_required_',$theKey,'"><input type="radio" id="required_info_required_',$theKey,'_yes" name="required_info_required_',$theKey,'" value="required" '; if ($exploder[1]=='required') { echo 'checked="checked"'; }; echo '/> ',__('Required', 'wpsc-support-tickets'),'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="required_info_required_',$theKey,'_no"><input type="radio" id="required_info_required_',$theKey,'_no" name="required_info_required_',$theKey,'" value="optional" '; if ($exploder[1]=='optional') { echo 'checked="checked"'; }; echo '/> ',__('Optional', 'wpsc-support-tickets'),'</label>'; 
                            
                            // If we're dealing with a dropdown, checkbox, or radio
                            //if ($exploder[2] == 'dropdown' || $exploder[2] == 'radio' || $exploder[2] == 'checkbox' ) {
                            //    echo '<br />';
                                
                            //    echo '|_ <input name="" class="custom_field_mc" /> <button class="button-secondary wpstAddNewAdditionalOptionButtonInEdit" onclick="return false;">'.__('Add additional option', 'wpsc-support-tickets').'</button>';
                            //}                            
                            
                            echo '</li>
                                ';
                        }
                }

                echo '
                </ul><br />
            </div>
            <br style="clear:both;" />
            <button class="button-primary">',__('Save All Edits', 'wpsc-support-tickets'),'</button>

            </td></tr></tbody></table>

            </form></div></div>
            <br style="clear:both;" /><br />';            
            
            
            echo '</div>';
        }
        
        //END Prints out the admin page ================================================================================		

        function printAdminPageCreateTicket() {
            global $wpdb;
            $devOptions = $this->getAdminOptions();
            $devOptions['disable_inline_styles'] = 'false';
            
            echo '<div class="wrap"> ';
            $this->adminHeader();
            echo ' 
                
        <div id="wst_tabs" style="padding:5px 5px 0px 5px;font-size:1.1em;border-color:#DDD;border-radius:6px;">
            <ul>
                <li><a href="#wstct_tabs-1">' , __('Create Ticket', 'wpsc-support-tickets') , '</a></li>
                    
            </ul>        
            <script type="text/javascript">
                jQuery(function() {
                    jQuery( "#wst_tabs" ).tabs();
                    setTimeout(function(){ jQuery(".updated").fadeOut(); },3000);
                });
            </script>



            <div id="wstct_tabs-1">                
            ';

            

            echo  '<br style="clear:both;" /><br />';
            
            echo  '<form action="' , get_admin_url(),'admin-post.php" method="post" enctype="multipart/form-data">';
            echo "<input type='hidden' name='action' value='submit-new-support-ticket' />";            

            
            echo  '<input type="hidden" name="admin_created_ticket" value="true" />';
            if (@isset($_POST['guest_email'])) {
                echo  '<input type="hidden" name="guest_email" value="' , esc_sql($_POST['guest_email']) , '" />';
            }
            echo  '<table class="widefat" ';

            echo 'style="width:100%;margin:12px;"';

            echo '><tr><th><img src="' , plugins_url('/images/Chat.png', __FILE__) , '" alt="' , __('Create a New Ticket', 'wpsc-support-tickets') , '" /> ' , __('Create a New Ticket', 'wpsc-support-tickets') , '</th></tr>';

            echo  '<tr><td><h3>' , __('Create ticket on behalf of user', 'wpsc-support-tickets') , ':</h3>';
            echo '<select name="wpscst_ticket_creator_assign" id="wpscst_ticket_creator_assign">';
            global $blog_id; 
            $wpscBlogUsers = get_users("blog_id={$blog_id}&orderby=nicename");
            if(isset($wpscBlogUsers[0])) {
                foreach ($wpscBlogUsers as $wpscTempUser) {
                    echo  "<option value=\"{$wpscTempUser->ID}\">", htmlentities($wpscTempUser->display_name),' (', htmlentities($wpscTempUser->user_email),')</option> ';
                }         
            }
            echo '</select>';            
            echo '</td></tr>';                   
            
            if($devOptions['custom_field_position'] == 'before everything') {
                echo  wpsctPromptForCustomFields();
            }                            

            echo  '<tr><td><h3>' , $devOptions['custom_title'] , '</h3><input type="text" name="wpscst_title" id="wpscst_title" value=""  ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="width:100%;margin:12px;"';
            } echo ' /></td></tr>';

            if($devOptions['custom_field_position'] == 'before message') {
                echo  wpsctPromptForCustomFields();
            }                            

            echo  '<tr><td><h3>' , $devOptions['custom_message'] , '</h3><div id="wpscst_nic_panel" ';
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
                echo  '<tr><td><h3>' , __('Attach a file', 'wpsc-support-tickets') , '</h3> <input type="file" name="wpscst_file" id="wpscst_file"></td></tr>';
            }
            //$exploder = explode('||', $devOptions['departments']);

            if($devOptions['custom_field_position'] == 'after everything') {
                echo  wpsctPromptForCustomFields();
            }                            

            echo  '<tr><td><h3>' , __('Department', 'wpsc-support-tickets') , '</h3><select name="wpscst_department" id="wpscst_department">';
            //if (isset($exploder[0])) {
            //    foreach ($exploder as $exploded) {
            //        echo  '<option value="' , $exploded , '">' , $exploded , '</option>';
            //    }
            //}
            $dep_results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpscst_departments` WHERE `enabled`=1;", ARRAY_A);
            foreach ($dep_results as $dep_result) {
                echo '<option value="'.$dep_result['primkey'].'">'.base64_decode($dep_result['name']).'</option>';
            }
            
            
            echo  '</select><button class="wpscst-button" id="wpscst_cancel" onclick="cancelAdd();return false;"  ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="float:right;"';
            } echo ' ><img ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="float:left;border:none;margin-right:5px;"';
            } echo ' src="' , plugins_url('/images/stop.png', __FILE__) , '" alt="' , __('Cancel', 'wpsc-support-tickets') , '" /> ' , __('Cancel', 'wpsc-support-tickets') , '</button><button class="wpscst-button" type="submit" name="wpscst_submit" id="wpscst_submit" ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="float:right;"';
            }echo '><img ';
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="float:left;border:none;margin-right:5px;"';
            } echo ' src="' , plugins_url('/images/page_white_text.png', __FILE__) , '" alt="' , __('Submit Ticket', 'wpsc-support-tickets') , '" /> ' , __('Submit Ticket', 'wpsc-support-tickets') , '</button></td></tr>';


            echo  '</table></form>';
            echo '</div></div></div></div></div></div>';
            echo  '<div style="clear:both;min-height:45px;height:45px;display:block;width:100%;" />&nbsp;</div> <style>#wpfooter {position:relative;top:-45px;}</style>';

        }
        
        
        function wpsctMakePDFText($string, $maxLength, $start = 75) {
            $string = preg_replace('/^\s+|\n|\r|\t|\s+$/m', ' ', $string);
            $output = array();
            while (strlen($string) > $maxLength) {
                $index = strpos($string, ' ', $maxLength);
                $output[] = trim(substr($string, 0, $index), " \t\n\r\0\x0B");
                $string = substr($string, $index);
            }
            $output[] = trim($string, " \t\n\r\0\x0B");

            foreach ($output as $out) {
                echo 'doc.text(20, '.$start.', "'. strip_tags(trim($out), " \t\n\r\0\x0B") .'"); ';
                $start = $start + 5;
                if (strpos($out,'<br') !== false || strpos($out,'<p') !== false)  {
                    $start = $start + 5;
                }
                if($start > 232) {
                    echo 'doc.addPage();';
                    $start = 20;
                }
            }
           
        }
        
        
        function wpsctMakePDFReplyText($string, $maxLength, $start = 75) {
            $output = array();
            while (strlen($string) > $maxLength) {
                $index = strpos($string, ' ', $maxLength);
                $output[] = trim(substr($string, 0, $index), " \t\n\r\0\x0B");
                $string = substr($string, $index);
            }
            $output[] = trim($string, " \t\n\r\0\x0B");

            foreach ($output as $out) {
                echo 'doc.text(20, '.$start.', "'. strip_tags(trim($out), " \t\n\r\0\x0B") .'"); ';
                $start = $start + 5;

                if($start > 232) {
                    echo 'doc.addPage();';
                    $start = 20;
                }
            }
           
        }        
        
        
        function printAdminPageEdit() {
            global $wpdb;

            $output = '';
            $devOptions = $this->getAdminOptions();

            echo '<div class="wrap">';

            $this->adminHeader();

            $primkey = intval($_GET['primkey']);
            
            $blog_title = htmlentities(get_bloginfo('name'));
            $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$primkey}' LIMIT 0, 1;";
            $results = $wpdb->get_results($sql, ARRAY_A);    
            if (isset($results[0])) { // First processing here
                if ($results[0]['user_id'] != 0) {
                    @$user = get_userdata($results[0]['user_id']);
                    $theusersname = '<a href="' . get_admin_url() . 'user-edit.php?user_id=' . $results[0]['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $user->user_nicename . ' </a>';
                } else {
                    $user = false; // Guest
                    $theusersname = __('Guest', 'wpsc-support-tickets') . ' - <strong>' . $results[0]['email'] . '</strong>';
                }
                
                $messageData = strip_tags(base64_decode($results[0]['initial_message']), '<p><br><a><br><strong><b><u><ul><li><strike><sub><sup><img><font>');
                $messageData = explode('\\', $messageData);
                $messageWhole = '';
                foreach ($messageData as $messagePart) {
                    $messageWhole .= $messagePart;
                }                
            }
            
            // Custom fields
            $table_name33 = $wpdb->prefix . "wpstorecart_meta";
            $grabrecord = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-requiredinfo' ORDER BY `foreignkey` ASC;";
            $resultscf = $wpdb->get_results( $grabrecord , ARRAY_A );            

            // Replies
            $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_replies` WHERE `ticket_id`='{$primkey}' ORDER BY `timestamp` ASC;";
            $result2 = $wpdb->get_results($sql, ARRAY_A);            
            
            echo '        <div id="wst_tabs" style="padding:5px 5px 0px 5px;font-size:1.1em;border-color:#DDD;border-radius:6px;">
            <ul>
                <li><a href="#wstct_tabs-1">' , __('Edit Ticket', 'wpsc-support-tickets') , '</a></li>
            </ul>        
            <script type="text/javascript">
                jQuery(function() {
                    jQuery( "#wst_tabs" ).tabs();
                    setTimeout(function(){ jQuery(".updated").fadeOut(); },3000);
                });';
            
            if ($devOptions['enable_beta_testing']=='true') {
                echo ' 

                    function wpscSaveToPDF() {
                        var doc = new jsPDF();

                        doc.setDrawColor(193, 193, 193);
                        doc.setFillColor(240,240,240);
                        doc.rect(10, 5, 190, 50, "F"); 

                        var imgData = \'data:image/png;base64,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\';
                        doc.addImage(imgData, "JPEG", 20, 10, 10, 10);
                        doc.setFontSize(20);
                        doc.text(32, 18, "'.$blog_title.'");
                        doc.setFontSize(14);
                        ';

                if (isset($results[0])) {
                    echo ' 
                        doc.setFontSize(20);
                        doc.text(20, 30, "'.__('Ticket', 'wpsc-support-tickets').' '.htmlentities($results[0]['primkey'].' : '.base64_decode($results[0]['title'])).'"); 
                        doc.setFontSize(12);
                        doc.text(20, 40, "'.__('Department', 'wpsc-support-tickets').': '.htmlentities(wpscSupportTicketsGetDepartmentName($results[0]['type'])).'"); 
                        doc.text(150, 40, "'.__('Resolution', 'wpsc-support-tickets').': '.htmlentities($results[0]['resolution']).'"); 
                        doc.text(20, 48, "'.__('Posted by', 'wpsc-support-tickets').' '.htmlentities(strip_tags( $theusersname .' ('. date_i18n( get_option( 'date_format' ), $results[0]['time_posted']) )).')"); 
                        doc.text(150, 48, "'.__('Severity', 'wpsc-support-tickets').': '.htmlentities($results[0]['severity']).'"); 

                        doc.setFontSize(20);
                        doc.text(20, 68, "'.__('Initial Message', 'wpsc-support-tickets').':"); 
                        doc.setFontSize(12);

                        ';
                        $this->wpsctMakePDFText($messageWhole, 80);

                        // Replies
                        //if (isset($result2) && @$result2[0]['user_id']!=null) {
                        //    $replyWhole = '';
                        //    echo 'doc.addPage();';
                        //    echo 'doc.setFontSize(20);';
                        //    echo 'doc.text(20, 30, "'.__('Replies', 'wpsc-support-tickets').'"); ';
                        //    echo 'doc.setFontSize(12);';                             
                        //    foreach ($result2 as $resultsX) {
                        //        $styleModifier1 = NULL;
                        //        $styleModifier2 = NULL;
                        //        if ($resultsX['user_id'] != 0) {
                        //            @$user = get_userdata($resultsX['user_id']);
                        //            @$userdata = new WP_User($resultsX['user_id']);
                        //            $theusersname = $user->user_nicename;
                        //        } else {
                        //            $user = false; // Guest
                        //            $theusersname = __('Guest', 'wpsc-support-tickets');
                        //        }
                        //
                        //        $replyWhole .= '                                                                                                                                                                                              ';
                        //        $replyWhole .= __('Posted by', 'wpsc-support-tickets') . ' ' . $theusersname . ' (' . date_i18n( get_option( 'date_format' , $resultsX['timestamp'])) . ') :';
                        //        
                        //        $replyData = strip_tags(base64_decode($resultsX['message']));
                        //        $replyData = explode('\\', $replyData);
                        //        
                        //        foreach ($replyData as $replyPart) {
                        //            $replyWhole .= $replyPart;
                        //        }
                        //        
                        //        
                        //
                        //   }
                        //    $this->wpsctMakePDFReplyText($replyWhole, 80, 40);
                        //}


                        //----------
                        if(isset($resultscf) && @$resultscf[0]['primkey']!=null) { // Custom fields
                            $thereIsAValueSet = false;
                            foreach ($resultscf as $field) {
                                $specific_items = explode("||", $field['value']);
                                $res = $wpdb->get_results("SELECT * FROM `{$table_name33}` WHERE `type`='wpsct_custom_{$field['primkey']}' AND `foreignkey`='{$primkey}';", ARRAY_A);
                                if(@isset($res[0]['primkey'])) {
                                    $thereIsAValueSet = true;
                                }
                            }                        
                            if($thereIsAValueSet) {
                                echo 'doc.addPage();';
                                echo 'doc.setFontSize(24);';
                                echo 'doc.text(20, 30, "'.__('User Fields', 'wpsc-support-tickets').'"); ';
                                echo 'doc.setFontSize(12);';
                                $theCollectedUserFields = '';
                                foreach ($resultscf as $field) {
                                    $specific_items = explode("||", $field['value']);
                                    $res = $wpdb->get_results("SELECT * FROM `{$table_name33}` WHERE `type`='wpsct_custom_{$field['primkey']}' AND `foreignkey`='{$primkey}';", ARRAY_A);
                                    if(@isset($res[0]['primkey'])) {
                                        $theCollectedUserFields .= $specific_items[0].': '.strip_tags(base64_decode($res[0]['value'])).'<br />';

                                    }
                                }
                                $this->wpsctMakePDFText($theCollectedUserFields, 80, 40);
                            }
                        }                      
                }

                echo ' 
                        doc.save("ticket-'.$primkey.'.pdf");

                        return false;
                    } 
                ';
            } // End beta testing
            echo ' 
            </script>

            <div id="wstct_tabs-1">  <br style="clear:both;" /><br />';

            


            if (isset($results[0])) {
                echo '<table class="widefat"><tr><td>';

                
                echo '<div id="wpscst_meta"><h1>' , base64_decode($results[0]['title']) , '</h1> (' , $results[0]['resolution'] , ' - ' , wpscSupportTicketsGetDepartmentName($results[0]['type']) , ')</div>';
                echo '<table class="widefat" style="width:100%;">';
                echo '<thead><tr><th id="wpscst_results_posted_by">' , __('Posted by', 'wpsc-support-tickets') , ' ' , $theusersname , ' (<span id="wpscst_results_time_posted">' , date_i18n( get_option( 'date_format' ), $results[0]['time_posted']) , '</span>)</th></tr></thead>';


                echo '<tbody><tr><td id="wpscst_results_initial_message"><br />' . $messageWhole;
                
                echo '</tbody></table>';

                // Custom fields
                if(isset($resultscf)) {
                        echo '<table class="widefat"><tbody>';
                        foreach ($resultscf as $field) {
                            $specific_items = explode("||", $field['value']);
                            $res = $wpdb->get_results("SELECT * FROM `{$table_name33}` WHERE `type`='wpsct_custom_{$field['primkey']}' AND `foreignkey`='{$primkey}';", ARRAY_A);
                            if(@isset($res[0]['primkey'])) {
                                echo '<tr><td><h4 style="display:inline;">',$specific_items[0],':</h4> ',strip_tags(base64_decode($res[0]['value'])),'</td></tr>';

                            }
                        }
                        echo '</tbody></table>';                        
                }                
                


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

                        echo '<br /><table class="widefat" style="width:100%;' , $styleModifier1 , '">';
                        echo '<thead><tr><th class="wpscst_results_posted_by" style="' , $styleModifier2 , '">' , __('Posted by', 'wpsc-support-tickets') , ' <a href="' , get_admin_url() , 'user-edit.php?user_id=' , $resultsX['user_id'] , '&wp_http_referer=' , urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') , '">' , $theusersname , '</a> (<span class="wpscst_results_timestamp">' , date_i18n( get_option( 'date_format' ), $resultsX['timestamp']) , '</span>)<div style="float:right;">';

                        echo '<form action="' , get_admin_url(),'admin-post.php" method="post" enctype="multipart/form-data">';
                        echo "<input type='hidden' name='action' value='delete-support-ticket' /><input type='hidden' name='replyid' value='{$resultsX['primkey']}' /><input type='hidden' name='ticketid' value='{$primkey}' />";  
                        echo '<button type="submit" onclick="if(confirm(\'' , __('Are you sure you want to delete this reply?', 'wpsc-support-tickets') , '\')){return true;}return false;"><img src="' , plugins_url('/images/delete.png', __FILE__) , '" alt="delete" /> ' , __('Delete Reply', 'wpsc-support-tickets') , '</button></form></div></th></tr></thead>';

                        $replyData = strip_tags(base64_decode($resultsX['message']), '<p><br><a><br><strong><b><u><ul><li><strike><sub><sup><img><font>');
                        $replyData = explode('\\', $replyData);
                        $replyWhole = '';
                        foreach ($replyData as $replyPart) {
                            $replyWhole .= $replyPart;
                        }
                        echo '<tbody><tr><td class="wpscst_results_message"><br />' , $replyWhole , '</td></tr>';
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
            $output .= '<form action="' . get_admin_url().'admin-post.php" method="post" enctype="multipart/form-data">';
            $output .= "<input type='hidden' name='action' value='reply-support-ticket' id='wpscst_hidden_action_field' />";
            
            $output .='<input type="hidden" name="wpscst_is_staff_reply" value="yes" /><input type="hidden" name="wpscst_edit_primkey" value="' . $primkey . '" /><input type="hidden" name="wpscst_goback" value="yes" /> ';
            $output .= '<table class="wpscst-table" style="width:100%;display:none;">';
            $output .= '<tr><td><h3>' . __('Your message', 'wpsc-support-tickets') . '</h3><div id="wpscst_nic_panel2" style="display:block;width:100%;"></div> <textarea name="wpscst_reply" id="wpscst_reply" style="display:block;width:100%;margin:0 auto 0 auto;background-color:#FFF;" rows="5" columns="6"></textarea>';
            $output .= '</td></tr>';
            

            $output .= '<tr><td><div style="float:left;"><h3>' . __('Department', 'wpsc-support-tickets') . '</h3><select name="wpscst_department" id="wpscst_department">';

            $output.= wpscSupportTicketsListDepartments($results[0]['type']);
            
            
            $output .= '</select></div>
                <div style="float:left;margin-left:20px;"><h3>' . __('Severity', 'wpsc-support-tickets') . '</h3>
                    <select name="wpscst_severity">
                        <option value="Low"';
                            if ($results[0]['severity'] == 'Low') {
                                $output.= ' selected="selected" ';
                            } $output.='>            
                            ' . __('Low', 'wpsc-support-tickets') . '
                        </option>                    
                        <option value="Normal"';
                            if ($results[0]['severity'] == 'Normal') {
                                $output.= ' selected="selected" ';
                            } $output.='>            
                            ' . __('Normal', 'wpsc-support-tickets') . '
                        </option>
                        <option value="High"';
                            if ($results[0]['severity'] == 'High') {
                                $output.= ' selected="selected" ';
                            } $output.='>            
                            ' . __('High', 'wpsc-support-tickets') . '
                        </option>       
                        <option value="Urgent"';
                            if ($results[0]['severity'] == 'Urgent') {
                                $output.= ' selected="selected" ';
                            } $output.='>            
                            ' . __('Urgent', 'wpsc-support-tickets') . '
                        </option> 
                        <option value="Critical"';
                            if ($results[0]['severity'] == 'Critical') {
                                $output.= ' selected="selected" ';
                            } $output.='>            
                            ' . __('Critical', 'wpsc-support-tickets') . '
                        </option>                        
                    </select>
                </div>
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
                        <div style="float:left;margin-left:20px;"><h3>' . __('Actions', 'wpsc-support-tickets') . '</h3>';


                        $output.= '<button type="submit" onclick="if(confirm(\'' . __('Are you sure you want to delete this reply?', 'wpsc-support-tickets') . '\')){jQuery(\'#wpscst_hidden_action_field\').val(\'delete-support-ticket\');return true;}return false;"><img src="' . plugins_url('/images/delete.png', __FILE__) . '" alt="' . __('Delete Reply', 'wpsc-support-tickets') . '" /> ' . __('Delete Reply', 'wpsc-support-tickets') . '</button> ';
                        if ($devOptions['enable_beta_testing']=='true') { $output.= '<button onclick="wpscSaveToPDF();return false;"><img src="' . plugins_url('/images/page_white_text.png', __FILE__) . '" alt="' . __('Save to PDF', 'wpsc-support-tickets') . '" /> ' . __('Save to PDF', 'wpsc-support-tickets') . '</button><br />'; }
            
                            $output .= '<input type="checkbox" name="wpsctnoemail" id="wpsctnoemail" checked="checked" value="on" /> ' . __('Send email to ticket creator on reply.', 'wpsc-support-tickets') . '
                        </div>';
            if ($devOptions['allow_uploads'] == 'true' && @function_exists('wpscSupportTicketsPRO')) {
                $output .= '<div style="float:left;margin-left:20px;"><h3>' . __('Attach a file', 'wpsc-support-tickets') . '</h3> <input type="file" name="wpscst_file" id="wpscst_file"></div>';
            }
            $output .='   
                        <input type="hidden" name="ticketid" value="'.$primkey.'" />
                        <button class="button-secondary" onclick="if(confirm(\'' . __('Are you sure you want to cancel?', 'wpsc-support-tickets') . '\')){window.location = \'' . get_admin_url() . 'admin.php?page=wpscSupportTickets-admin\';}return false;"  style="float:right;" ><img style="float:left;border:none;margin-right:5px;" src="' . plugins_url('/images/stop.png', __FILE__) . '" alt="' . __('Cancel', 'wpsc-support-tickets') . '" /> ' . __('Cancel', 'wpsc-support-tickets') . '</button> <button class="button-primary" type="submit" name="wpscst_submit" id="wpscst_submit" style="float:right;margin:0 5px 0 5px;"><img style="float:left;border:none;margin-right:5px;" src="' . plugins_url('/images/page_white_text.png', __FILE__) . '" alt="' . __('Update Ticket', 'wpsc-support-tickets') . '" /> ' . __('Update Ticket', 'wpsc-support-tickets') . '</button></td></tr>';


            $output .= '</table></form>';
            echo $output;

            echo '
			</div></div></div>';
        }

        // Dashboard widget code=======================================================================
        function wpscSupportTickets_main_dashboard_widget_function() {
            global $wpdb;

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
                    $style="";
                    if (trim($result['last_staff_reply']) == '') {
                        $last_staff_reply = __('ticket creator', 'wpsc-support-tickets') . ' <a href="' . get_admin_url() . 'user-edit.php?user_id=' . $result['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $theusersname . '</a>';
                        $style = "background-color:#ffdddd;font-weight: bold;";
                    } else {
                        if ($result['last_updated'] > $result['last_staff_reply']) {
                            $last_staff_reply = __('ticket creator', 'wpsc-support-tickets') . ' <a href="' . get_admin_url() . 'user-edit.php?user_id=' . $result['user_id'] . '&wp_http_referer=' . urlencode(get_admin_url() . 'admin.php?page=wpscSupportTickets-admin') . '">' . $theusersname . '</a>';
                            $style = "background-color:#ffdddd;font-weight: bold;";
                        } else {
                            $last_staff_reply = '<strong>' . __('Staff Member', 'wpsc-support-tickets') . '</strong>';
                            $style = "opacity:0.7;";
                        }
                    }

                    $output .= '<tr style="'.$style.'"><td><a href="admin.php?page=wpscSupportTickets-edit&primkey=' . $result['primkey'] . '" style="border:none;text-decoration:none;"><img style="float:left;border:none;margin-right:5px;" src="' . plugins_url('/images/page_edit.png', __FILE__) . '" alt="' . __('View', 'wpsc-support-tickets') . '"  /> ' . base64_decode($result['title']) . '</a></td><td>' . $result['resolution'] . '</td><td>' . $last_staff_reply . '</td></tr>';
                }
                $output .= '</tbody></table>';
            } else {
                $output .= '<tr><td><i>' . __('No open tickets!', 'wpsc-support-tickets') . '</i></td><td></td><td></td></tr>';
            }
            echo $output;
        }

        // Create the function use in the action hook
        function wpscSupportTickets_main_add_dashboard_widgets() {
            $this->checkPermissions();
            wp_add_dashboard_widget('wpscSupportTickets_main_dashboard_widgets', __('IDB Support Tickets Overview', 'wpsc-support-tickets'), array(&$this, 'wpscSupportTickets_main_dashboard_widget_function'));
            
        }

        function addHeaderCode() {
            if(@$_GET['page']=='wpscSupportTickets-admin' || @$_GET['page']=='wpscSupportTickets-newticket' || @$_GET['page']=='wpscSupportTickets-settings' || @$_GET['page']=='wpscSupportTickets-edit' || @$_GET['page']=='wpscSupportTickets-departments' || @$_GET['page']=='wpscSupportTickets-stats') {
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-tabs');
                if (@!class_exists('AGCA')) {
                    wp_enqueue_script('wpscstniceditor', plugins_url('/js/nicedit/nicEdit.js', __FILE__), array('jquery'), '1.3.2');
                }
                wp_enqueue_script('wpsc-jeditable', plugins_url() . '/wpsc-support-tickets/js/jquery.jeditable.mini.js');
                wp_enqueue_script('jspdf', plugins_url() . '/wpsc-support-tickets/js/jspdf.min.js');
                wp_enqueue_style('wpsc-support-tickets-admin-ui-css', plugins_url('/css/custom-theme/jquery-ui-1.10.3.custom.css', __FILE__), false, 2, false);
            }
        }
        
        function addFieldsHeaderCode() {
            if(@$_GET['page']=='wpscSupportTickets-fields') {
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-tabs');
                wp_enqueue_script('jquery-ui-sortable');
                if (@!class_exists('AGCA')) {
                    wp_enqueue_script('wpscstniceditor', plugins_url('/js/nicedit/nicEdit.js', __FILE__), array('jquery'), '1.3.2');
                }
                wp_enqueue_style('wpsc-support-tickets-admin-ui-css', plugins_url('/css/custom-theme/jquery-ui-1.10.3.custom.css', __FILE__), false, 2, false);
            }
        }        
        
        function addStatsHeaderCode() {
            if(@$_GET['page']=='wpscSupportTickets-stats') {
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-tabs');
                if (@!class_exists('AGCA')) {
                    wp_enqueue_script('wpscstniceditor', plugins_url('/js/nicedit/nicEdit.js', __FILE__), array('jquery'), '1.3.2');
                }
                wp_enqueue_style('wpsc-support-tickets-admin-ui-css', plugins_url('/css/custom-theme/jquery-ui-1.10.3.custom.css', __FILE__), false, 2, false);

                wp_enqueue_script('wpscstraphael', plugins_url().'/wpsc-support-tickets-pro/js/tufte-graph/raphael.js', array('jquery'), '1.3.2');
                wp_enqueue_script('wpscstenumerable', plugins_url().'/wpsc-support-tickets-pro/js/tufte-graph/jquery.enumerable.js', array('jquery'), '1.3.2');
                wp_enqueue_script('wpscsttufte', plugins_url().'/wpsc-support-tickets-pro/js/tufte-graph/jquery.tufte-graph.js', array('jquery'), '1.3.2');
                wp_enqueue_style('tufte-admin-ui-css', plugins_url().'/wpsc-support-tickets-pro/js/tufte-graph/tufte-graph.css', false, 2, false);
            }
        }        

        /**
         * Method to return a string full of search results
         * 
         * @global object $wpdb
         * @global type $current_user
         * @param type $mode
         * @return string
         */
        function search($mode='search') {
            global $wpdb, $current_user;
            $devOptions = $this->getAdminOptions();
            $mode = strtolower($mode);
            if($devOptions['allow_search']=='true' && $mode=='form') { // If search is on and we need the form
                $form = '<form method="post" action="'.get_permalink($devOptions['mainpage']).'" id="wpscst-search-form"><br /><br />'.__('Search Tickets', 'wpsc-support-tickets').': <input type="text" value="" name="wpsc-support-tickets-search" id="wpsc-support-tickets-search" /> <input type="submit" value="'.__('Search', 'wpsc-support-tickets').'" name="wpsc-support-tickets-search-button" id="wpsc-support-tickets-search-button" class="wpscst-button" /></form> ';
                return $form;
            } else {
                if($mode=='search' && $devOptions['allow_search']=='true') {
                    if (session_id() == '') {
                        @session_start();
                    }                    
                    if($devOptions['allow_search']=='true' && @isset($_POST['wpsc-support-tickets-search'])) {
                        //is_user_logged_in() || @isset($_SESSION['wpsct_email'])
                        if ($devOptions['allow_all_tickets_to_be_viewed']=='true') {
                            if ($devOptions['allow_guests']=='true') { // Everyone can search all tickets
                                    $sql_tickets = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets`;";
                                    $sql_replies = null;
                            } else { // Registered users can search all tickets
                                if(is_user_logged_in()) {
                                    $sql_tickets = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets`;";
                                    $sql_replies = null;                                 
                                } else { // Guest are not allowed to search
                                    $sql_tickets = null;
                                    $sql_replies = null;
                                }
                            }                    
                        } else {
                            if ($devOptions['allow_guests']=='true') { // Guest and registered users, their own tickets only
                                if(is_user_logged_in()) {
                                    $sql_tickets = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `user_id`='{$current_user->ID}';";
                                    $sql_replies = null;  
                                } else {
                                    if(@isset($_SESSION['wpsct_email'])) {
                                        $sql_tickets = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `email`='".esc_sql($_SESSION['wpsct_email'])."' AND `user_id`='0';";
                                        $sql_replies = null; // Replies SQL has to be calculated later for guests                                   
                                    } else { // The guest wasn't logged in as a guest
                                        $sql_tickets = null;
                                        $sql_replies = null;                                        
                                    }
                                }
                            } else { // Registered users only, their own tickets only
                                if(is_user_logged_in()) {
                                    $sql_tickets = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `user_id`='{$current_user->ID}';";
                                    $sql_replies = null;                                      
                                } else { // Guest are not allowed
                                    $sql_tickets = null;
                                    $sql_replies = null;                                     
                                }
                            }                    
                        }
                        
                        // Actual searching done here down here
                        if($sql_tickets==null && $sql_replies==null) {
                            return ''; // return an empty string if someone was trying to search in a way they weren't supposed to
                        } else {
                            $result_tickets = $wpdb->get_results($sql_tickets, ARRAY_A);
                            if($sql_replies!=null) {
                                $result_replies = $wpdb->get_results($sql_replies, ARRAY_A);
                            } else { // In this block of code, we're dealing with a guest who may or may not have any replies. Now that we know the tickets they have access to, we can build a query for replies
                                if(@isset($result_tickets[0]['primkey'])) {
                                    $sql_replies = "SELECT * FROM `{$wpdb->prefix}wpscst_replies` WHERE ";
                                    foreach($result_tickets as $result_ticket) {
                                        $sql_replies .= '`ticket_id`="'.$result_ticket['primkey'].'" OR ';
                                    }  
                                    $sql_replies = substr($sql_replies, 0, -3); // Takes out the last OR
                                    $sql_replies .= ';';
                                    $result_replies = $wpdb->get_results($sql_replies, ARRAY_A);
                                } else { // Down here it means the guest had no access to any tickets, so therefore has no access to any replies
                                    $result_replies = null;
                                }
                            }
                        }
                        
                        // Time for searching through the tickets we have access to:
                        $actualticketsresult = 0;
                        if( @isset($result_tickets[0]['primkey']) || @isset($result_tickets[0]['primkey']) ) {
                            
                                $output = '<table class="widefat wpscst_search_results_table" ';
                                if ($devOptions['disable_inline_styles'] == 'false') {
                                    $output.='style="width:100%"';
                                }$output.='><tr><th>' . __('Ticket', 'wpsc-support-tickets') . '</th><th>' . __('Status', 'wpsc-support-tickets') . '</th><th>' . __('Last Reply', 'wpsc-support-tickets') . '</th></tr>';
                                
                                // Search Initial Tickets
                                if( @isset($result_tickets[0]['primkey']) ) {
                                    
                                        foreach ($result_tickets as $result) {

                                            //$_POST['wpsc-support-tickets-search']
                                            if (strripos(base64_decode($result['title']),$_POST['wpsc-support-tickets-search']) !== false) {
                                                // we're good to go
                                                $actualticketsresult = $actualticketsresult + 1;
                                            } else {
                                                if (strripos(strip_tags(base64_decode($result['initial_message'])),$_POST['wpsc-support-tickets-search']) !== false) {
                                                    // we're good to go
                                                    $actualticketsresult = $actualticketsresult + 1;
                                                } else {
                                                    continue; // we didn't find an occurance in the ticket or the message
                                                }
                                            }
                                            $final_result[] = $result; // add it the array
                                        }
                                }
                                
                                // Search Replies and display as Tickets
                                if( @isset($result_replies[0]['primkey']) ) {
                                    foreach ($result_replies as $result) {

                                            if (strripos(strip_tags(base64_decode($result['message'])),$_POST['wpsc-support-tickets-search']) !== false) {
                                                // we're good to go
                                                $actualticketsresult = $actualticketsresult + 1;
                                                $new_result = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$result['ticket_id']}';", ARRAY_A);
                                                $final_result[] = $new_result[0]; // add it the array                                                                                        
                                            } else {
                                                continue; // we didn't find an occurance in the reply
                                            }
                                    }
                                }
                                
                                
                                if($actualticketsresult > 0) {
                                    $final_result = array_map("unserialize", array_unique(array_map("serialize", $final_result))); // Gives us only unique results by removing duplicate arrays
                                    foreach($final_result as $result) {
                                        if (trim($result['last_staff_reply']) == '') {
                                            if ($devOptions['allow_all_tickets_to_be_viewed'] == 'false') {
                                                $last_staff_reply = __('you', 'wpsc-support-tickets');
                                            } else {
                                                if ($devOptions['hide_email_on_frontend_list']=='true') {
                                                    $last_staff_reply = __('Guest', 'wpsc-support-tickets') . ' (' . $wpscst_email . ')';
                                                } else {
                                                    $last_staff_reply = __('Guest', 'wpsc-support-tickets');
                                                }                                            

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
                                        if ($result['resolution'] == strtolower('open') ) {
                                            $resresolution = __('Open', 'wpsc-support-tickets');
                                        } elseif ($result['resolution'] == strtolower('closed') ) {
                                            $resresolution = __('Closed', 'wpsc-support-tickets');
                                        } else {
                                            $resresolution = $result['resolution'];
                                        }
                                        if ($devOptions['disable_inline_styles'] == 'false') {
                                            $output.='style="border:none;text-decoration:none;"';
                                        }$output.='><img';
                                        if ($devOptions['disable_inline_styles'] == 'false') {
                                            $output.=' style="float:left;border:none;margin-right:5px;"';
                                        }$output.=' src="' . plugins_url('/images/page_edit.png', __FILE__) . '" alt="' . __('View', 'wpsc-support-tickets') . '"  /> ' . base64_decode($result['title']) . '</a></td><td>' . $resresolution . '</td><td>' . date_i18n( get_option( 'date_format' ), $result['last_updated']) . ' ' . __('by', 'wpsc-support-tickets') . ' ' . $last_staff_reply . '</td></tr>';                                    

                                    }
                                    $output .= '</table>';
                                    return $output;                                    
                                } else {
                                    return '';
                                }                                
                                
                        } else {
                            return '';
                        }                       
                        
                        
                    } else { // return an empty string if search is off or the search was not set
                        return '';
                    }
                } else { // Mode should only equal search here, so if not, return an empty string
                    return '';
                }
            }
        }
        
        function wpscSupportTickets_install($network) {   
            global $wpdb;
            if (function_exists('is_multisite') && is_multisite()) {
                if ($network) {
                    $old_blog = $wpdb->blogid;
                    $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                    foreach ($blogids as $blog_id) {
                        switch_to_blog($blog_id);
                        $this->addPermissions();
                        $this->wpscSupportTickets_activate();
                    }
                    switch_to_blog($old_blog);
                    return;
                }  
            }
            $this->wpscSupportTickets_activate();   
        }
        
        // Installation ==============================================================================================		
        function wpscSupportTickets_activate() {
            global $wpdb;
            global $wpscSupportTickets_db_version;

            $table_name = $wpdb->prefix . "wpscst_departments";
            if ($wpdb->get_var("show tables like '$table_name'") != $table_name) { // Create 

                $sql = "
                CREATE TABLE `{$table_name}` (
                    `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
                    `name` VARCHAR(512) NOT NULL, 
                    `description` TEXT NOT NULL, 
                    `admin_user_id` INT NOT NULL, 
                    `enabled` TINYINT(1) NOT NULL DEFAULT '1', 
                    `group_name_slug` VARCHAR(256) NOT NULL, 
                    `parent_department` INT NOT NULL, 
                    `forward_all_department_emails` TINYINT(1) NOT NULL DEFAULT '0',  
                    `main_department_email` VARCHAR(512) NOT NULL,
                    `display_list_order` INT NOT NULL, 
                    `target_response_time` VARCHAR(128) NOT NULL
                );				
                ";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            } else {               
                // Departments already exist, let's see if we were running 4.9.43 previously
                $devOptions = $this->getAdminOptions();
                if($devOptions['converted_departments_phase2']==null || @!isset($devOptions['converted_departments_phase2']) || $devOptions['converted_departments_phase2']=='false' ) { // In here we haven't converted yet
                    $resulter = $wpdb->get_results("SELECT * FROM `{$table_name}`;", ARRAY_A);
                    if(@isset($resulter[0]['primkey'])) {
                        foreach($resulter as $resultee) {
                            $wpdb->query("UPDATE `{$table_name}` SET `name`='".base64_encode($resultee['name'])."',  `description`='".base64_encode($resultee['description'])."', `group_name_slug`='".base64_encode($resultee['group_name_slug'])."' WHERE `primkey`='{$resultee['primkey']}';");                           
                        }
                    }
                    $devOptions['converted_departments_phase2'] = 'done';
                    update_option($this->adminOptionsName, $devOptions);
                }
            }
            
            
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

            if (session_id() == '') {
                @session_start();
            }

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

                            $searchstuff = $this::search('form');
                            $searchresults = $this::search('search');
                            
                            $output .= '<div id="wpscst_top_page" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="display:inline;"';
                            } $output.='></div><button class="wpscst-button" id="wpscst-new" onclick="jQuery(\'.wpscst-table\').fadeIn(\'fast\');jQuery(\'#wpscst-new\').fadeOut(\'fast\');jQuery(\'#wpscst_edit_div\').fadeOut(\'fast\');jQuery(\'html, body\').animate({scrollTop: jQuery(\'html\').offset().top}, 100);try { jQuery(\'#wpscst-search-form\').fadeOut(\'fast\'); } catch (e) {};try {jQuery(\'#wpscst_search_results\').fadeOut(\'fast\');} catch (e) {};return false;"><img ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:left;border:none;margin-right:5px;"';
                            } $output.=' src="' . plugins_url('/images/Add.png', __FILE__) . '" alt="' . $devOptions['custom_new_ticket_button_text'] . '" /> ' . $devOptions['custom_new_ticket_button_text'] . '</button> '.$searchstuff.'<br /><br />';
                            $output.=  '<form action="' . get_admin_url().'admin-post.php" method="post" enctype="multipart/form-data">';
                            $output.= "<input type='hidden' name='action' value='submit-new-support-ticket' />";                                 
                            if (@isset($_POST['guest_email'])) {
                                $output .= '<input type="hidden" name="guest_email" value="' . esc_sql($_POST['guest_email']) . '" />';
                            }
                            $output .= '<table class="wpscst-table" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="width:100%"';
                            } 
                            $output .='><tr><th><img src="' . plugins_url('/images/Chat.png', __FILE__) . '" alt="' .  $devOptions['custom_new_ticket_button_text']  . '" /> ' .  $devOptions['custom_new_ticket_button_text']  . '</th></tr>';

                            if($devOptions['custom_field_position'] == 'before everything') {
                                $output .= wpsctPromptForCustomFields();
                            }                            
                            
                            $output .= '<tr><td><h3>' . $devOptions['custom_title'] . '</h3><input type="text" name="wpscst_title" id="wpscst_title" value=""  ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="width:100%"';
                            } $output .=' /></td></tr>';
                            
                            if($devOptions['custom_field_position'] == 'before message') {
                                $output .= wpsctPromptForCustomFields();
                            }                            
                            
                            $output .= '<tr><td><h3>' . $devOptions['custom_message'] . '</h3><div id="wpscst_nic_panel" ';
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
                            //$exploder = explode('||', $devOptions['departments']);

                            if($devOptions['custom_field_position'] == 'after everything') {
                                $output .= wpsctPromptForCustomFields();
                            }                            
                            
                            $output .= '<tr><td><h3>' . __('Department', 'wpsc-support-tickets') . '</h3><select name="wpscst_department" id="wpscst_department">';
                            $dep_results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpscst_departments` WHERE `enabled`=1;", ARRAY_A);
                            foreach ($dep_results as $dep_result) {
                                $output .=  '<option value="'.$dep_result['primkey'].'">'.base64_decode($dep_result['name']).'</option>';
                            }                            
                            $output .= '</select> 
                            <h3';
                            if ($devOptions['display_severity_on_create'] == 'false') {
                               $output .=' style="display:none;" '; 
                            }                            
                            $output .='>' . __('Severity', 'wpsc-support-tickets') . '</h3><select name="wpscst_severity" id="wpscst_severity"';
                            if ($devOptions['display_severity_on_create'] == 'false') {
                               $output .=' style="display:none;" '; 
                            }
                            $output .='>';
                            $output .= '<option value="Low">'. __('Low', 'wpsc-support-tickets') . '</option>                    
                            <option value="Normal">' . __('Normal', 'wpsc-support-tickets') . '
                            </option>
                            <option value="High">' . __('High', 'wpsc-support-tickets') . '
                            </option>       
                            <option value="Urgent">' . __('Urgent', 'wpsc-support-tickets') . '
                            </option> 
                            <option value="Critical">' . __('Critical', 'wpsc-support-tickets') . '
                            </option>';  
                            $output .= '</select>                                 

                                    <button class="wpscst-button" id="wpscst_cancel" onclick="cancelAdd();return false;"  ';
                            
                            
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:right;"';
                            } $output.=' ><img ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:left;border:none;margin-right:5px;"';
                            } $output.=' src="' . plugins_url('/images/stop.png', __FILE__) . '" alt="' . __('Cancel', 'wpsc-support-tickets') . '" /> ' . __('Cancel', 'wpsc-support-tickets') . '</button><button onclick="if(jQuery(\'#wpscst_title\').val().length === 0 || jQuery(\'#wpscst_initial_message\').val().length === 0) {alert(\'' . __('You cannot leave the description or title of your ticket empty!', 'wpsc-support-tickets') . '\');return false;} " class="wpscst-button" type="submit" name="wpscst_submit" id="wpscst_submit" ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:right;"';
                            }$output.='><img ';
                            if ($devOptions['disable_inline_styles'] == 'false') {
                                $output.='style="float:left;border:none;margin-right:5px;"';
                            } $output.=' src="' . plugins_url('/images/page_white_text.png', __FILE__) . '" alt="' . __('Submit Ticket', 'wpsc-support-tickets') . '" /> ' . __('Submit Ticket', 'wpsc-support-tickets') . '</button></td></tr>';


                            $output .= '</table></form>';

                            $output .= '<form action="' . get_admin_url().'admin-post.php" method="post" enctype="multipart/form-data">';
                            $output .= "<input type='hidden' name='action' value='reply-support-ticket' />";
            
                            $output .= '<input type="hidden" value="0" id="wpscst_edit_primkey" name="wpscst_edit_primkey" />';
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
                                if ($devOptions['hide_email_on_frontend_list']=='true') {
                                    $wpscst_username = __('Guest', 'wpsc-support-tickets') . ' (' . $wpscst_email . ')';
                                } else {
                                    $wpscst_username = __('Guest', 'wpsc-support-tickets');
                                }
                            }

                            if($searchresults!='') {
                                $output .= '<div id="wpscst_search_results">';
                                $output .= '<h3>' . __('Search Results', 'wpsc-support-tickets') . ':</h3>';
                                $output.= $searchresults;
                                $output .= '</div>';
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
                                            if ($devOptions['hide_email_on_frontend_list']=='true') {
                                                $last_staff_reply = __('Guest', 'wpsc-support-tickets') . ' (' . $wpscst_email . ')';
                                            } else {
                                                $last_staff_reply = __('Guest', 'wpsc-support-tickets');
                                            }                                            
                                            
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
                                    if ($result['resolution'] == strtolower('open') ) {
                                        $resresolution = __('Open', 'wpsc-support-tickets');
                                    } elseif ($result['resolution'] == strtolower('closed') ) {
                                        $resresolution = __('Closed', 'wpsc-support-tickets');
                                    } else {
                                        $resresolution = $result['resolution'];
                                    }
                                    if ($devOptions['disable_inline_styles'] == 'false') {
                                        $output.='style="border:none;text-decoration:none;"';
                                    }$output.='><img';
                                    if ($devOptions['disable_inline_styles'] == 'false') {
                                        $output.=' style="float:left;border:none;margin-right:5px;"';
                                    }$output.=' src="' . plugins_url('/images/page_edit.png', __FILE__) . '" alt="' . __('View', 'wpsc-support-tickets') . '"  /> ' . base64_decode($result['title']) . '</a></td><td>' . $resresolution . '</td><td>' . date_i18n( get_option( 'date_format' ), $result['last_updated']) . ' ' . __('by', 'wpsc-support-tickets') . ' ' . $last_staff_reply . '</td></tr>';
                                }
                                $output .= '</table>';
                            }
                            $output .= '</div>';
                        }
                    } else {
                        if ($devOptions['show_login_text']=='true') {
                            $output .='<div id="wpscSupportTicketsRegisterMessage">'. __('Please', 'wpsc-support-tickets') . ' <a href="' . wp_login_url(get_permalink()) . '">' . __('log in', 'wpsc-support-tickets') . '</a> ' . __('or', 'wpsc-support-tickets') . ' <a href="' . site_url('/wp-login.php?action=register&redirect_to=' . get_permalink()) . '">' . __('register', 'wpsc-support-tickets') . '</a>.</div>';
                        }
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
            add_menu_page(__('wpsc Support Tickets', 'wpsc-support-tickets'), __('Support Tickets', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-admin', array(&$wpscSupportTickets, 'printAdminPage'), plugins_url() . '/wpsc-support-tickets/images/controller.png', '94.7');
            $newTicketPage = add_submenu_page('wpscSupportTickets-admin', __('Create Ticket', 'wpsc-support-tickets'), __('Create Ticket', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-newticket', array(&$wpscSupportTickets, 'printAdminPageCreateTicket'));
            $settingsPage = add_submenu_page('wpscSupportTickets-admin', __('Settings', 'wpsc-support-tickets'), __('Settings', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-settings', array(&$wpscSupportTickets, 'printAdminPageSettings'));
            $editPage = add_submenu_page(NULL, __('Reply to Support Ticket', 'wpsc-support-tickets'), __('Reply to Support Tickets', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-edit', array(&$wpscSupportTickets, 'printAdminPageEdit'));
            if(@function_exists('wpscSupportTicketDepartments')) { // For wpsc Support Tickets v5.0+
                $departmentsPage = add_submenu_page('wpscSupportTickets-admin', __('Departments', 'wpsc-support-tickets'), __('Departments', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-departments', array(&$wpscSupportTickets, 'printAdminPageDepartments'));
            }
            $statsPage = add_submenu_page('wpscSupportTickets-admin', __('Statistics', 'wpsc-support-tickets'), __('Statistics', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-stats', array(&$wpscSupportTickets, 'printAdminPageStats'));
            if(@function_exists('wstPROStats')) {
                $statsHeaderCode = 'addStatsHeaderCode';
            } else {
                $statsHeaderCode = 'addHeaderCode';
            }
            $fieldsPage = add_submenu_page('wpscSupportTickets-admin', __('Edit User Fields Collected', 'wpsc-support-tickets'), __('User Fields', 'wpsc-support-tickets'), 'manage_wpsct_support_tickets', 'wpscSupportTickets-fields', array(&$wpscSupportTickets, 'printAdminPageFields'));
            add_action("admin_print_scripts-$newTicketPage", array(&$wpscSupportTickets, 'addHeaderCode'));
            add_action("admin_print_scripts-$editPage", array(&$wpscSupportTickets, 'addHeaderCode'));
            if(@function_exists('wpscSupportTicketDepartments')) { // For wpsc Support Tickets v5.0+
                add_action("admin_print_scripts-$departmentsPage", array(&$wpscSupportTickets, 'addHeaderCode')); 
            }
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
        'wpscstAjaxUrl' => admin_url('admin-ajax.php'),
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
    
    add_action('wp_dashboard_setup', array(&$wpscSupportTickets, 'wpscSupportTickets_main_add_dashboard_widgets')); // Dashboard widget
    //add_action('wp_head', array(&$wpscSupportTickets, 'addHeaderCode')); // Place wpscSupportTickets comment into header
    add_shortcode('wpscSupportTickets', array(&$wpscSupportTickets, 'wpscSupportTickets_mainshortcode'));
    add_shortcode('wpscsupporttickets', array(&$wpscSupportTickets, 'wpscSupportTickets_mainshortcode'));
    add_shortcode('IDBSupportTickets', array(&$wpscSupportTickets, 'wpscSupportTickets_mainshortcode'));
    add_shortcode('idbsupporttickets', array(&$wpscSupportTickets, 'wpscSupportTickets_mainshortcode'));
    
    
    add_action("wp_print_scripts", array(&$wpscSupportTickets, "addHeaderCode"));
    add_action('init', 'wpscLoadInit'); // Load other languages, and javascript
    
    add_action('admin_menu', 'wpscSupportTicketsAdminPanel'); // Create admin panel
    $devOptions = get_option('wpscSupportTicketsAdminOptions');
    if($devOptions['override_wordpress_email']=='true') {
        add_filter( 'wp_mail_from', array(&$wpscSupportTickets, 'change_mail_from') );
        add_filter( 'wp_mail_from_name', array(&$wpscSupportTickets, 'change_mail_name') );
    }
}
/**
 * ===============================================================================================================
 * Call everything
 */
if (!function_exists('wpscSupportTicketsPRO')) {

    function wstPROSettingsFakeForm() {
        echo '<div>';
        
                    echo '

            <div id="idb_bt_wrap">
                <iframe class="idb_bt_site" src="http://indiedevbundle.com/app/idb-ultimate-wordpress-bundle/#idbsupporttickets"></iframe>
            </div>

            <style type="text/css">

            #idb_bt_wrap {
                width: 100%;
                padding-bottom: 55%;
                background: orange;
            }
            .idb_bt_site{
                position: absolute;
                top: 48px; 
                left: 0;
                width: 100%;
                height: 87%;
            }
            </style>
                    ';          
        
     echo '</div>       
        

        ';
    }

    add_action('wpscSupportTickets_settings', 'wstPROSettingsFakeForm');
}

/**
 * 
 * Submits a new ticket
 * 
 * @global type $current_user
 * @global object $wpdb
 * @global type $user_info
 */
function wpscstSubmitTicket() {

    global $current_user, $wpdb;

    $devOptions = get_option("wpscSupportTicketsAdminOptions");
    if(!isset($devOptions['mainpage']) || $devOptions['mainpage']=='') {
        $devOptions['mainpage'] = home_url();
    }
    $direct_to = get_permalink($devOptions['mainpage']);

    if (session_id() == '') {@session_start();};
    if(is_user_logged_in() || @isset($_SESSION['wpsct_email']) || @isset($_POST['guest_email']) ) {

        if(!is_user_logged_in()) {
            if(@trim($_SESSION['wpsct_email'])=='' || @!isset($_SESSION['wpsct_email'])) {
                $_SESSION['wpsct_email'] = $_POST['guest_email'];
            }
        }
        
        if(is_user_logged_in() && @isset($_POST['admin_created_ticket']) && function_exists('current_user_can') && current_user_can('manage_wpsct_support_tickets') ) {
            // redirect back to admin
            $direct_to = get_admin_url().'admin.php?page=wpscSupportTickets-admin';
        } else {
            // redirect back to ticket page
            $direct_to = get_permalink($devOptions['mainpage']);
        }

        if(trim($_POST['wpscst_initial_message'])=='' || trim($_POST['wpscst_title'])=='') {// No blank messages/titles allowed
                if(!headers_sent()) {
                    header("HTTP/1.1 301 Moved Permanently");
                    header ('Location: '.$direct_to);
                    exit();
                } else {
                    echo '<script type="text/javascript">
                            <!--
                            window.location = "'.$direct_to.'"
                            //-->
                            </script>';
                }
        } 




        // Guest additions here
        if(is_user_logged_in()) {
            if (@isset($_POST['wpscst_ticket_creator_assign']) && current_user_can('manage_wpsct_support_tickets') && @isset($_POST['admin_created_ticket'])  ) {
                // Save from the admin panel
                $wpscst_userid = intval($_POST['wpscst_ticket_creator_assign']);
                global $user_info;
                $user_info = get_userdata($wpscst_userid);      
                $wpscst_email = $user_info->user_email;
            } else {
                // Save from the front end
                $wpscst_userid = $current_user->ID;
                $wpscst_email = $current_user->user_email;
            }
        } else {
            $wpscst_userid = 0;
            $wpscst_email = esc_sql($_SESSION['wpsct_email']);     
            if(trim($wpscst_email)=='') {
                $wpscst_email = @esc_sql($_POST['guest_email']);
            }
        }

        $wpscst_initial_message = '';

        // Code for Session Cookie workaround
        if (@isset($_POST["PHPSESSID"])) {
                session_id($_POST["PHPSESSID"]);
        } else if (@isset($_GET["PHPSESSID"])) {
                session_id($_GET["PHPSESSID"]);
        }

        session_start();


        // Custom form fields first checked here (Added in 4.0)
        $custom_field_problem = false;
        $custom_field_problem_text = __('There was a problem with your form.  Please resubmit after you add the required information for the following:', 'wpsc-support-tickets');

        $table_name33 = $wpdb->prefix . "wpstorecart_meta";
        $grabrecord = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-requiredinfo' ORDER BY `foreignkey` ASC;";

        $resultscf = $wpdb->get_results( $grabrecord , ARRAY_A );
        if(isset($resultscf)) {
                foreach ($resultscf as $field) {
                    $specific_items = explode("||", $field['value']);
                    if($specific_items[1]=='required' && $specific_items[2]!='checkbox') { // Required field, let's verify it has a value:
                        if( @isset($_POST['wpsct_custom_'.$field['primkey']]) && @trim($_POST['wpsct_custom_'.$field['primkey']]) != '' ) {
                            $_SESSION['wpsct_custom_'.$field['primkey']] = $_POST['wpsct_custom_'.$field['primkey']];
                        } else {
                            // The required field had no value
                            $custom_field_problem = true;
                            $custom_field_problem_text .= ' '. $specific_items[0];
                        }
                    } 
                }
        }

        if($custom_field_problem==true) {
                echo '<script type="text/javascript">
                        <!--
                        alert("'.$custom_field_problem_text.'");
                        window.history.back()
                        //-->
                        </script>';
                exit();

        }
        // End custom field check (we'll revisit the custom form fields and save them after writing our initial ticket to the DB)



        if($devOptions['allow_uploads']=='true' && function_exists('wpscSupportTicketsPRO') && @isset($_FILES["wpscst_file"]) && @$_FILES["wpscst_file"]["error"] != 4 ) {
            /* Handles the error output. This error message will be sent to the uploadSuccess event handler.  The event handler
            will have to check for any error messages and react as needed. */
            function HandleError($message) {
                    echo '<script type="text/javascript">alert("'.$message.'");</script>'.$message.'';
            }


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
                    $file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename(substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1).substr(md5(time()),1) . $_FILES[$upload_name]['name']));
                    
                    if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH) {
                            HandleError(__("Invalid file name", 'wpsc-support-tickets'));
                    }


                    if (!@move_uploaded_file($_FILES[$upload_name]["tmp_name"], $save_path.$file_name)) {
                            HandleError(__("File could not be saved.", 'wpsc-support-tickets'));
                    } else {
                        // SUCCESS
                        $wpscst_initial_message .= '<br /><p class="wpsc-support-ticket-attachment"';
                        if($devOptions['disable_inline_styles']=='false'){
                            $wpscst_initial_message .=  ' style="border: 1px solid #DDD;padding:8px;" ';
                        }
                        $wpscst_initial_message .= '>';
                        $wpscst_initial_message .= '<img src="'.plugins_url().'/wpsc-support-tickets-pro/images/attachment.png" alt="" /> <strong>'.__('ATTACHMENT','wpsc-support-tickets').'</strong>: <a href="'.$wpsc_wordpress_upload_dir['baseurl'].'/wpsc-support-tickets/'.$file_name.'" target="_blank">'.$wpsc_wordpress_upload_dir['baseurl'].'/wpsc-support-tickets/'.$file_name.'</a></p>';
                    }       
        }    


        $wpscst_title = base64_encode(strip_tags($_POST['wpscst_title']));
        
        //$wpscst_initial_message = base64_encode(nl2br($_POST['wpscst_initial_message'] . $wpscst_initial_message));
        // Alternative new method for dealing with line breaks adding in 4.9.23
        $wpscst_initial_message = base64_encode( '<p>'. preg_replace('/[\r\n]+/', '</p><p>', $_POST['wpscst_initial_message'] . $wpscst_initial_message) . '</p>' );
        
        $wpscst_department = intval($_POST['wpscst_department']);    
        $wpscst_severity = esc_sql($_POST['wpscst_severity']);

        $sql = "
        INSERT INTO `{$wpdb->prefix}wpscst_tickets` (
            `primkey`, `title`, `initial_message`, `user_id`, `email`, `assigned_to`, `severity`, `resolution`, `time_posted`, `last_updated`, `last_staff_reply`, `target_response_time`, `type`) VALUES (
                NULL,
                '{$wpscst_title}',
                '{$wpscst_initial_message}',
                '{$wpscst_userid}',
                '{$wpscst_email}',
                '0',
                '{$wpscst_severity}',
                'Open',
                '".current_time( 'timestamp' )."',
                '".current_time( 'timestamp' )."',
                '',
                '2 days',
                '{$wpscst_department}'
            );
        ";

        $wpdb->query($sql);
        $lastID = $wpdb->insert_id;

        // Save custom fields
        if(isset($resultscf)) {
                foreach ($resultscf as $field) {
                    $specific_items = explode("||", $field['value']);

                    if( @isset($_POST['wpsct_custom_'.$field['primkey']]) && @trim($_POST['wpsct_custom_'.$field['primkey']]) != '' ) {
                        $val = base64_encode(strip_tags($_POST['wpsct_custom_'.$field['primkey']]));
                        $insertw = "
                        INSERT INTO `{$table_name33}` (
                        `primkey`, `value`, `type`, `foreignkey`)
                        VALUES (
                                NULL,
                                '{$val}',
                                'wpsct_custom_{$field['primkey']}',
                                '{$lastID}'
                        );
                        ";
                        $wpdb->query($insertw);
                        $_SESSION['wpsct_custom_'.$field['primkey']] = $_POST['wpsct_custom_'.$field['primkey']];
                    }

                }
        }
        // End custom fields 

        if($devOptions['disable_all_emails']=='false') { // If we're sending out emails
            $cc = null;
            if($devOptions['cc_all_new_tickets'] == 'true') {
               $cc = $devOptions['cc_all_new_tickets_to_email'];
            }	            
            
            $to      = $wpscst_email; // Send this to the ticket creator
            if($devOptions['allow_html']=='true') {
                $subject = $devOptions['email_new_ticket_subject'] .' "'. htmlspecialchars_decode( htmlentities( strip_tags($_POST['wpscst_title']), ENT_NOQUOTES, strtoupper($devOptions['email_encoding']), false ), ENT_NOQUOTES ).'"';
            } else {
                $subject = $devOptions['email_new_ticket_subject'] .' "'. strip_tags($_POST['wpscst_title']).'"';
            }
            $message = $devOptions['email_new_ticket_body'];
            if($devOptions['use_ticket_in_email']=='true') {
                $message .= "\r\n";
                $message .= "\r\n";
                if($devOptions['allow_html']=='true') {
                    $cleaned_message = __("The content of the ticket is: ", 'wpsc-support-tickets'). '"'. htmlspecialchars_decode( htmlentities( strip_tags($_POST['wpscst_initial_message']), ENT_NOQUOTES, strtoupper($devOptions['email_encoding']), false ), ENT_NOQUOTES ) .'"';
                } else {
                    $cleaned_message = __("The content of the ticket is: ", 'wpsc-support-tickets'). '"'. strip_tags($_POST['wpscst_initial_message']) .'"';
                }
                $message .= $cleaned_message;
            }    


            wpscSupportTickets_mail($to, $subject, $message, null, $cc);


            $to      = $devOptions['email']; // Send this to the admin
            if($devOptions['allow_html']=='true') {
                $subject = __("A new support ticket was received.", 'wpsc-support-tickets').' "'. htmlspecialchars_decode( htmlentities( strip_tags($_POST['wpscst_title']), ENT_NOQUOTES, strtoupper($devOptions['email_encoding']), false ), ENT_NOQUOTES ) .'"';
            } else {
                $subject = __("A new support ticket was received.", 'wpsc-support-tickets').' "'. strip_tags($_POST['wpscst_title']).'"';
            }
            $message = __('There is a new support ticket: ','wpsc-support-tickets').get_admin_url().'admin.php?page=wpscSupportTickets-edit&primkey='.$lastID;
            if($devOptions['use_ticket_in_email']=='true') {
                $message .= "\r\n";
                $message .= "\r\n";
                if($devOptions['allow_html']=='true') {
                    $cleaned_message = __("The content of the ticket is: ", 'wpsc-support-tickets'). '"'. htmlspecialchars_decode( htmlentities( strip_tags($_POST['wpscst_initial_message']), ENT_NOQUOTES, strtoupper($devOptions['email_encoding']), false ), ENT_NOQUOTES )  .'"';
                } else {
                    $cleaned_message = __("The content of the ticket is: ", 'wpsc-support-tickets'). '"'. strip_tags($_POST['wpscst_initial_message']) .'"';
                }
                $message .= $cleaned_message;
            }    
            $headers = '';

            wpscSupportTickets_mail($to, $subject, $message, $headers);
            
            // New department emails
            $dep_results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpscst_departments` WHERE `primkey`='{$wpscst_department}' ;", ARRAY_A);
            if (@isset($dep_results[0]['name']) ) {
                
                $email_department_admin = null;
                if($dep_results[0]['admin_user_id']!=0 && $dep_results[0]['admin_user_id']!=null) {
                    global $user_info;
                    $user_info = get_userdata($dep_results[0]['admin_user_id']);    
                    $email_department_admin = $user_info->user_email;                 

                    if ($devOptions['email'] != $email_department_admin) { // If the Department head is different from the admin, send the Department head an email as well.
                        wpscSupportTickets_mail($email_department_admin, $subject, $message, $headers); 
                    }
                
                }
                if ($dep_results[0]['forward_all_department_emails'] == 1) {
                    if($dep_results[0]['main_department_email'] != $devOptions['email']) {
                        if($dep_results[0]['main_department_email'] != $email_department_admin) { // If the main department email hasn't gotten an email, but department forwarding is on, lets send it
                            wpscSupportTickets_mail($dep_results[0]['main_department_email'], $subject, $message, $headers); 
                        }                        
                    }
                }
            }
            // End new department code
            
        }
    }

    if(!headers_sent()) {
        header("HTTP/1.1 301 Moved Permanently");
        header ('Location: '.$direct_to);

    } else {
        echo '<script type="text/javascript">
                <!--
                window.location = "'.$direct_to.'"
                //-->
                </script>';
    }

    exit();    
}
add_action('admin_post_submit-new-support-ticket', 'wpscstSubmitTicket'); // If the user is logged in
add_action('admin_post_nopriv_submit-new-support-ticket', 'wpscstSubmitTicket'); // If the user in not logged in

/**
 * 
 * @global type $current_user
 * @global object $wpdb
 * @global type $wpscSupportTickets
 */
function wpscstReplyTicket() {
    global $current_user, $wpdb, $wpscSupportTickets;

    $devOptions = get_option("wpscSupportTicketsAdminOptions");

    if (session_id() == "") {@session_start();};
    
    $is_an_admin_reply = false;

    if ( current_user_can('manage_wpsct_support_tickets')) { // admin edits such as closing tickets should happen here first:
        $is_an_admin_reply = true;
        if(@isset($_POST['wpscst_status']) && @isset($_POST['wpscst_department']) && is_numeric($_POST['wpscst_edit_primkey'])) {
            $wpscst_department = intval($_POST['wpscst_department']);
            $wpscst_status = esc_sql($_POST['wpscst_status']);
            $wpscst_severity = esc_sql($_POST['wpscst_severity']);
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
            $wpscst_email = esc_sql($_SESSION['wpsct_email']);  
            if(trim($wpscst_email)=='') {
                $wpscst_email = @esc_sql($_POST['guest_email']);
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

                            $file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename(substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1).substr(md5(time()),1) . $_FILES[$upload_name]['name']));

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
                                $wpscst_message .= '<img src="'.plugins_url().'/wpsc-support-tickets-pro/images/attachment.png" alt="'.__('ATTACHMENT','wpsc-support-tickets').'" /> <strong>'.__('ATTACHMENT','wpsc-support-tickets').'</strong>: <a href="'.$wpsc_wordpress_upload_dir['baseurl'].'/wpsc-support-tickets/'.$file_name.'" target="_blank">'.$wpsc_wordpress_upload_dir['baseurl'].'/wpsc-support-tickets/'.$file_name.'</a></p>';
                            }       
                }        



                //$wpscst_message = base64_encode(nl2br($_POST['wpscst_reply'] . $wpscst_message));
                // Alternative new method for dealing with line breaks adding in 4.9.23
                $wpscst_message = base64_encode( '<p>'. preg_replace('/[\r\n]+/', '</p><p>', $_POST['wpscst_reply'] . $wpscst_message) . '</p>' );
                
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
                        $updateSQL = "UPDATE `{$wpdb->prefix}wpscst_tickets` SET `last_updated` = '".current_time( 'timestamp' )."', `last_staff_reply` = '".current_time( 'timestamp' )."' WHERE `primkey` ='{$primkey}';";
                } else {
                        // This is a reply from the front end
                        $updateSQL = "UPDATE `{$wpdb->prefix}wpscst_tickets` SET `last_updated` = '".current_time( 'timestamp' )."' WHERE `primkey` ='{$primkey}';";
                }
                $wpdb->query($updateSQL);

                if($devOptions['disable_all_emails']=='false') { // If emails are turned on
                    if (@isset($_POST['wpsctnoemail']) && $_POST['wpsctnoemail'] == 'on' && $results[0]['email'] != $wpscst_email) {
                        $cc = null;
                        if($devOptions['cc_all_user_replies'] == 'true' && !$is_an_admin_reply) {
                           $cc = $devOptions['cc_all_user_replies_to_email'];
                        }                        
                        
                        $to      = $results[0]['email']; // Send this to the original ticket creator
                        if($devOptions['allow_html']=='true') {
                            $subject = $devOptions['email_new_reply_subject'].' "' . htmlspecialchars_decode( htmlentities( base64_decode($results[0]['title']), ENT_NOQUOTES, strtoupper($devOptions['email_encoding']), false ), ENT_NOQUOTES ) .'"';
                        } else {
                            $subject = $devOptions['email_new_reply_subject'].' "' .  base64_decode($results[0]['title']) .'"';
                        }                
                        $message = $devOptions['email_new_reply_body'];
                        if($devOptions['use_reply_in_email']=='true') {
                            $message .= "\r\n";
                            $message .= "\r\n";
                            if($devOptions['allow_html']=='true') {
                                $cleaned_message = __("The content of the ticket is: ", 'wpsc-support-tickets'). '"'. htmlspecialchars_decode( htmlentities( strip_tags($_POST['wpscst_reply']), ENT_NOQUOTES, strtoupper($devOptions['email_encoding']), false ), ENT_NOQUOTES )  .'"';
                            } else {
                                $cleaned_message = __("The content of the ticket is: ", 'wpsc-support-tickets'). '"'. strip_tags($_POST['wpscst_reply']) .'"';
                            }
                            $message .= $cleaned_message;
                        }            

                        wpscSupportTickets_mail($to, $subject, $message, null, $cc);
                    }

                    if( $devOptions['email']!=$results[0]['email']) { 
                        $cc = null;
                        if($devOptions['cc_all_admin_replies'] == 'true' && $is_an_admin_reply) {
                           $cc = $devOptions['cc_all_admin_replies'];
                        }                                  
                        
                        $to      = $devOptions['email']; // Send this to the admin
                        if($devOptions['allow_html']=='true') {
                            $subject = __("Reply to a support ticket was received.", 'wpsc-support-tickets').' "' . htmlspecialchars_decode( htmlentities( base64_decode($results[0]['title']), ENT_NOQUOTES, strtoupper($devOptions['email_encoding']), false ), ENT_NOQUOTES ) .'"';
                        } else {
                            $subject = __("Reply to a support ticket was received.", 'wpsc-support-tickets').' "' .  base64_decode($results[0]['title']) .'"';
                        }
                        $message = __('There is a new reply on support ticket: ','wpsc-support-tickets').get_admin_url().'admin.php?page=wpscSupportTickets-edit&primkey='.$primkey.'';
                        if($devOptions['use_reply_in_email']=='true') {
                            $message .= "\r\n";
                            $message .= "\r\n";

                            if($devOptions['allow_html']=='true') {
                                $cleaned_message = __("The content of the ticket is: ", 'wpsc-support-tickets'). '"'. htmlspecialchars_decode( htmlentities( strip_tags($_POST['wpscst_reply']), ENT_NOQUOTES, strtoupper($devOptions['email_encoding']), false ), ENT_NOQUOTES )  .'"';
                            } else {
                                $cleaned_message = __("The content of the ticket is: ", 'wpsc-support-tickets'). '"'. strip_tags($_POST['wpscst_reply']) .'"';
                            }


                            $message .= $cleaned_message;
                        }

                        wpscSupportTickets_mail($to, $subject, $message, null, $cc);
                    }
                    
                    
                    // New department emails
                    $dep_results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpscst_departments` WHERE `primkey`='{$wpscst_department}' ;", ARRAY_A);
                    if (@isset($dep_results[0]['name']) ) {

                        $email_department_admin = null;
                        if($dep_results[0]['admin_user_id']!=0 && $dep_results[0]['admin_user_id']!=null) {
                            global $user_info;
                            $user_info = get_userdata($dep_results[0]['admin_user_id']);    
                            $email_department_admin = $user_info->user_email;                 

                            if ($devOptions['email'] != $email_department_admin) { // If the Department head is different from the admin, send the Department head an email as well.
                                if ($results[0]['email'] != $email_department_admin) {
                                    wpscSupportTickets_mail($email_department_admin, $subject, $message); 
                                }
                            }

                        }
                        if ($dep_results[0]['forward_all_department_emails'] == 1) {
                            if($dep_results[0]['main_department_email'] != $devOptions['email']) {
                                if($dep_results[0]['main_department_email'] != $email_department_admin) { // If the main department email hasn't gotten an email, but department forwarding is on, lets send it
                                    if ($results[0]['email'] != $dep_results[0]['main_department_email']) {
                                        wpscSupportTickets_mail($dep_results[0]['main_department_email'], $subject, $message); 
                                    }
                                }                        
                            }
                        }
                    }
                    // End new department code                    
                    
                    
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
}
add_action('admin_post_reply-support-ticket', 'wpscstReplyTicket'); // If the user is logged in
add_action('admin_post_nopriv_reply-support-ticket', 'wpscstReplyTicket'); // If the user in not logged in

/**
 * 
 * Deletes a ticket
 * 
 * @global type $current_user
 * @global object $wpdb
 * @global type $wpscSupportTickets
 */
function  wpscstDeleteTicket() {
    global $wpdb;

    if(is_user_logged_in()) {
        wpscSupportTickets::checkPermissions();

        if(@isset($_POST['ticketid']) && @is_numeric($_POST['ticketid']) && @!isset($_POST['replyid'])) {
            $primkey = intval($_POST['ticketid']);

            $wpdb->query("DELETE FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$primkey}';");
            $wpdb->query("DELETE FROM `{$wpdb->prefix}wpscst_replies` WHERE `ticket_id`='{$primkey}';");
            header("HTTP/1.1 301 Moved Permanently");
            header ('Location: '.get_admin_url().'admin.php?page=wpscSupportTickets-admin');
            exit();
        }
         if(@isset($_POST['replyid']) && @is_numeric($_POST['replyid']) && @isset($_POST['ticketid']) && @is_numeric($_POST['ticketid'])) {
            $primkey = intval($_POST['replyid']);
            $ticketprimkey = intval($_POST['ticketid']);

            $wpdb->query("DELETE FROM `{$wpdb->prefix}wpscst_replies` WHERE `primkey`='{$primkey}';");
            header("HTTP/1.1 301 Moved Permanently");
            header ('Location: '.get_admin_url().'admin.php?page=wpscSupportTickets-edit&primkey='.$ticketprimkey);
            exit();
        }

    }    
}
add_action('admin_post_delete-support-ticket', 'wpscstDeleteTicket'); // If the user is logged in

if (!function_exists('wstPROBulkTabIndex')) {
    function wstInformPROBulkTabIndex() {
        echo '<li><a href="#wst_tabs-all">'.__('Advanced (PRO Only)','wpsc-support-tickets').'</a></li>';
    }

    add_action( 'wpscSupportTickets_extraTabsIndex', 'wstInformPROBulkTabIndex' );
    
    function wstInformPROBulkTabContents() {
                echo '<div id="wst_tabs-all"><table class="widefat" style="width:98%;"><tr><td>';

                    echo '

            <div id="idb_bt_wrap">
                <iframe class="idb_bt_site" src="http://indiedevbundle.com/app/idb-ultimate-wordpress-bundle/#idbsupporttickets"></iframe>
            </div>

            <style type="text/css">

            #idb_bt_wrap {
                width: 100%;
                padding-bottom: 55%;
                background: orange;
            }
            .idb_bt_site{
                position: absolute;
                top: 48px; 
                left: 0;
                width: 100%;
                height: 87%;
            }
            </style>
                    ';                
                
                echo '</td></tr></table></div>  ';          
        
    }
    add_action( 'wpscSupportTickets_extraTabsContents', 'wstInformPROBulkTabContents' );
}


if(!function_exists('wpscSupportTicketsReturnValidManagers')) {
    /**
     * Returns an array of IDs of users who can manage issues
     * 
     * @global object $wpdb
     * @return array 
     */
    function wpscSupportTicketsReturnValidManagers() {
        global $wpdb;
        
        $valid_managers = array();

        $search = $wpdb->get_results("SELECT `ID` FROM `{$wpdb->prefix}users` ORDER BY `ID`;", ARRAY_A);

        foreach ($search as $userid) {
            if (user_can($userid['ID'],  'manage_wpsct_support_tickets')) {
                $valid_managers[] = $userid['ID'];
            }
        } 

        return $valid_managers;
    }
}


    function wpscSupportTickets_installDepartments() {
        global $wpdb;

        $devOptions = get_option('wpscSupportTicketsAdminOptions');

        /**
         * Install the department system if the table isn't present
         */
        $table_name = $wpdb->prefix . "wpscst_departments";
        if ($wpdb->get_var("show tables like '$table_name'") != $table_name) { // Create 

            $sql = "
            CREATE TABLE `{$table_name}` (
                `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
                `name` VARCHAR(512) NOT NULL, 
                `description` TEXT NOT NULL, 
                `admin_user_id` INT NOT NULL, 
                `enabled` TINYINT(1) NOT NULL DEFAULT '1', 
                `group_name_slug` VARCHAR(256) NOT NULL, 
                `parent_department` INT NOT NULL, 
                `forward_all_department_emails` TINYINT(1) NOT NULL DEFAULT '0',  
                `main_department_email` VARCHAR(512) NOT NULL,
                `display_list_order` INT NOT NULL, 
                `target_response_time` VARCHAR(128) NOT NULL
            );				
            ";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            $retrieve_user = get_user_by( 'email', get_option('admin_email'));
            $admin_user_id = intval($retrieve_user->ID);
            $exploder = explode('||', $devOptions['departments']);
            $count = 0;
            if (isset($exploder[0])) {
                foreach ($exploder as $exploded) {

                    $exploded = $exploded;

                    $insert_sql = "INSERT INTO `{$table_name}` (`primkey`, `name`, `description`, `admin_user_id`, `enabled`, `group_name_slug`, `parent_department`, `forward_all_department_emails`, `main_department_email`, `display_list_order`, `target_response_time`) VALUES (NULL, '".esc_sql(trim(base64_encode($exploded)))."', '', '".$admin_user_id."', '1', '".esc_sql(base64_encode(wpsctSlug(trim($exploded))))."', '0', '0', '".esc_sql(get_bloginfo('admin_email'))."', '".$count."', '2 days');";
                    $wpdb->query($insert_sql);
                    $lastID = $wpdb->insert_id;

                    $update_sql = "UPDATE `{$wpdb->prefix}wpscst_tickets` SET `type`='{$lastID}' WHERE `type`='".base64_encode($exploded)."' ;";
                    $wpdb->query($update_sql);

                    $count++;
                }
            } else {
                $insert_sql = "INSERT INTO `{$table_name}` (`primkey`, `name`, `description`, `admin_user_id`, `enabled`, `group_name_slug`, `parent_department`, `forward_all_department_emails`, `main_department_email`, `display_list_order`, `target_response_time`) VALUES (NULL, 'General', '', '".$admin_user_id."', '1', 'general-support', '0', '0', '".esc_sql(get_bloginfo('admin_email'))."', '0', '2 days');";
                $wpdb->query($insert_sql);
            }
            $devOptions['departments'] = '';
            update_option( 'wpscSupportTicketsAdminOptions', $devOptions ); // Remove the old departments
        }    
    }


    function wpscSupportTicketsGetDepartments() {
        global $wpdb;
        $table_name = $wpdb->prefix . "wpscst_departments";  
        $dep_results = $wpdb->get_results("SELECT * FROM `{$table_name}` ;", ARRAY_A);    
        return $dep_results;
    }

    
    function wpscSupportTicketsGetDepartmentName($key) {
        global $wpdb;
        $key = intval($key);
        $departmentName = null;
        $table_name = $wpdb->prefix . "wpscst_departments"; 
        $new_results = $wpdb->get_results("SELECT `name` FROM `{$table_name}` WHERE `primkey`='{$key}';", ARRAY_A);  
        if(@isset($new_results[0]['name'])) {
            $departmentName = base64_decode($new_results[0]['name']);
        }        
        return $departmentName;
    }
    

    /**
     * 
     * A function to display the department on tickets
     * 
     * @param type $result
     * @param type $resresolution
     */
    function wpscSupportTicketsDepartments($result, $resresolution) {

        $final_cat_name = '';

        $new_results = wpscSupportTicketsGetDepartmentName(intval($result['type']));                
        if(@isset($new_results) && $new_results!=null) {
            $final_cat_name = $new_results;
        }
        
        echo '<strong>'.base64_decode($result['title']).'</strong> ('.$resresolution.' - '.$final_cat_name.')</div>';


    }

    function wpscSupportTicketsListDepartments($selected=null) {
        $results = wpscSupportTicketsGetDepartments();
        $string = '';
        if (@isset($results[0])) {
            foreach ($results as $result) {
                $string .= '<option value="'.$result['primkey'].'"'; 
                if($selected == $result['primkey']) {
                    $string .= ' selected="selected" ';
                }
                $string .= '>'.base64_decode($result['name']).'</option>';
            }
        }
        return $string;
    }


    /**
     * New Departments system in 
     * @global object $wpdb
     */
    function wpscSupportTicketDepartments() {
        global $wpdb;

        if (function_exists('current_user_can') && !current_user_can('manage_wpsct_support_tickets')) {
            die(__('Unable to Authenticate', 'wpsc-support-tickets'));
        }     

        wpscSupportTickets_installDepartments();

        $table_name = $wpdb->prefix . "wpscst_departments";  
        
        
        if(@isset($_POST['dep_description'])) {
            $sql = " 
                INSERT INTO `{$table_name}` (
                `primkey` ,
                `name` ,
                `description` ,
                `admin_user_id` ,
                `enabled` ,
                `group_name_slug` ,
                `parent_department` ,
                `forward_all_department_emails` ,
                `main_department_email` ,
                `display_list_order` ,
                `target_response_time`
                )
                VALUES (
                    NULL , '".base64_encode($_POST['dep_name'])."', '".base64_encode($_POST['dep_description'])."', '".intval($_POST['dep_lead_admin'])."', '".intval($_POST['dep_enabled'])."', '".base64_encode($_POST['dep_slug'])."', '".intval($_POST['dep_parent'])."', '".intval($_POST['dep_forward_all_emails'])."', '".esc_sql($_POST['dep_email'])."', '0', '2 days'
                );
                ";
            $wpdb->query($sql);
        }
        
        
        $dep_results = $wpdb->get_results("SELECT * FROM `{$table_name}` ;", ARRAY_A);
                
        
        echo '<div class="wrap">';


            
        echo '<div id="wst_tabs" style="padding:5px 5px 0px 5px;font-size:1.1em;border-color:#DDD;border-radius:6px;">
            <ul>
                <li><a href="#wstcf_tabs-1">' , __('Departments', 'wpsc-support-tickets') , '</a></li>
            </ul>        
            <script type="text/javascript">
                jQuery(function() {
                    jQuery( "#wpsct-new-department-form" ).hide();
                    jQuery( "#wpsct-hide-nf" ).hide();
                    jQuery( "#wst_tabs" ).tabs();
                    setTimeout(function(){ jQuery(".updated").fadeOut(); },3000);
                });
                ';
        echo " 
                jQuery(document).ready(function() {
                    jQuery('.wpsct-edit').editable(ajaxurl+'?action=wpsct_save_department_edit', { 
                        type      : 'text',
                            width     : '180px',
                            height    : '20px',                        
                        cancel    : '".__('Cancel', 'wpsc-support-tickets')."',
                        submit    : '".__('Save', 'wpsc-support-tickets')."',
                        tooltip   : '".__('Click to Edit', 'wpsc-support-tickets')."'
                    });
                    

                    jQuery('.wpsct-edit-enabled').editable(ajaxurl+'?action=wpsct_save_department_edit', {
                        submit : \"".__('Save', 'wpsc-support-tickets')."\",
                        cancel : \"".__('Cancel', 'wpsc-support-tickets')."\",
                        type   : \"select\",
                        data   :  { '1' : '".__('Enabled', 'wpsc-support-tickets')."',                
                            '0' : '".__('Disabled', 'wpsc-support-tickets')."' }

                    });


                    jQuery('.wpsct-edit-user').editable(ajaxurl+'?action=wpsct_save_department_edit', {
                        submit : \"".__('Save', 'wpsc-support-tickets')."\",
                        cancel : \"".__('Cancel', 'wpsc-support-tickets')."\",
                        type   : \"select\",
                        data   : \" {'0':'".__('Unassigned', 'wpsc-support-tickets')."'"; 
                        $wpscBlogUsers = wpscSupportTicketsReturnValidManagers();
                        foreach ($wpscBlogUsers as $wpbt_manager) {
                            global $user_info;
                            $user_info = get_userdata($wpbt_manager);                            
                            echo  ",'{$user_info->ID}' : '".htmlentities($user_info->display_name)."' ";
                        }       
                        echo '}"

                    });';
                    
                    echo " 
                    jQuery('.wpsct-edit-parent').editable(ajaxurl+'?action=wpsct_save_department_edit', {
                        submit : \"".__('Save', 'wpsc-support-tickets')."\",
                        cancel : \"".__('Cancel', 'wpsc-support-tickets')."\",
                        type   : \"select\",
                        data   : \" {'0':'".__('Unassigned', 'wpsc-support-tickets')."'"; 
                        if(@isset($dep_results[0])) {
                          foreach ($dep_results as $dep_result) {
                              echo  ",'{$dep_result['primkey']}' : '".htmlentities(base64_decode($dep_result['name']))."' ";
                          }
                        }                   

                        echo '}"

                    });';    
                        
            echo '
                    
                 });

            </script>

            <div id="wstcf_tabs-1">    ';         

        echo '<table style="width:500px;max-width:500px;">';
        echo '<tr><td>';
        echo '<button onclick="jQuery(\'#wpsct-new-department-form\').show();jQuery(\'#wpsct-hide-nf\').show();jQuery(\'#wpsct-make-nf\').hide();return false;" class="button-primary" id="wpsct-make-nf">',__('Create new Department', 'wpsc-support-tickets'),'</button>'; 
        echo '<button onclick="jQuery(\'#wpsct-new-department-form\').hide();jQuery(\'#wpsct-make-nf\').show();jQuery(\'#wpsct-hide-nf\').hide();return false;" class="button-secondary" id="wpsct-hide-nf">',__('Close new Department form', 'wpsc-support-tickets'),'</button><br /><br />'; 
        
        echo '<form action="#" method="post">';
        echo '<table class="widefat" id="wpsct-new-department-form">';
        echo '<tr><td><h3> ',__('Add New Department', 'wpsc-support-tickets'),'</h3></td><td></td></tr>';
        echo '<tr><td>'.__('Name', 'wpsc-support-tickets') , '<br /><input type="text" name="dep_name" /> </td>';
        echo '<td>'.__('Description', 'wpsc-support-tickets') , '<br /><input type="text" name="dep_description" /></td></tr>';

        echo '<tr><td>'.__('Lead Admin', 'wpsc-support-tickets'); 
        echo '<br /><select name="dep_lead_admin" id="dep_lead_admin">';
        $wpbt_managers = wpscSupportTicketsReturnValidManagers();
        foreach ($wpbt_managers as $wpbt_manager) {
            global $user_info;
            $user_info = get_userdata($wpbt_manager);
            echo '<option value="'.$wpbt_manager.'">'.$user_info->user_login.'</option>';
        }
        echo '</select>';
        echo '</td>';
        echo '<td>',__('Enabled?', 'wpsc-support-tickets') , '<br /><select name="dep_enabled"><option value="1">'.__('Enabled', 'wpsc-support-tickets').'</option><option value="0">'.__('Disabled', 'wpsc-support-tickets').'</option></select></td></tr>';        

        echo '<tr><td>',__('Slug', 'wpsc-support-tickets') , '<br /><input type="text" name="dep_slug" /> </td>';
        echo '<td>',__('Parent Department', 'wpsc-support-tickets');
        
        if(@isset($dep_results[0])) {
        echo '<br /><select name="dep_parent" id="dep_parent">';
            echo '<option value="0"> </option>';
            foreach ($dep_results as $dep_result) {
                echo '<option value="'.$dep_result['primkey'].'">'.base64_decode($dep_result['name']).'</option>';
            }
        echo '</select>';            
        }
        
        echo '</td></tr>';
        
        echo '<tr><td>',__('Forward All Emails?', 'wpsc-support-tickets') , '<br /><select name="dep_forward_all_emails"><option value="1">'.__('Enabled', 'wpsc-support-tickets').'</option><option value="0">'.__('Disabled', 'wpsc-support-tickets').'</option></select></td>';
        echo '<td>',__('Department Email', 'wpsc-support-tickets') , '<br /><input type="text" name="dep_email" /></td></tr>';    
        
        echo '<tr><td></td><td><input type="submit" value="',__('Save This Department', 'wpsc-support-tickets') , '" class="button-primary"></td></tr>';
        
        echo '</table></form></td></tr>';
        
        echo '</table><br />';
        
        if(@isset($dep_results[0])) {
            echo '<table class="widefat">';
            echo '<thead><tr><th>',__('ID', 'wpsc-support-tickets'),'</th><th>',__('Name', 'wpsc-support-tickets'),'</th><th>',__('Description', 'wpsc-support-tickets'),'</th><th>',__('Lead Admin', 'wpsc-support-tickets'),'</th><th>',__('Enabled?', 'wpsc-support-tickets'),'</th><th>',__('Slug', 'wpsc-support-tickets'),'</th><th>',__('Parent Department', 'wpsc-support-tickets'),'</th><th>',__('Forward All Emails?', 'wpsc-support-tickets'),'</th><th>',__('Department Email', 'wpsc-support-tickets'),'</th></tr></thead><tbody>';
            foreach ($dep_results as $dep_result) {
                $user_info = get_userdata($dep_result['admin_user_id']);
                $username = $user_info->user_login;                
                $parent = '';
                if($dep_result['parent_department'] > 0) { // If our parent department is set
                    $parent_id = intval($dep_result['parent_department']);
                    $parent_results = $wpdb->get_results("SELECT `name` FROM `{$table_name}` WHERE `primkey`='{$parent_id}' ;", ARRAY_A);
                    if(@isset($parent_results[0]['name'])) {
                        $parent = base64_decode($parent_results[0]['name']);
                    }
                } 
                echo '<tr id="wpsct_department_'.$dep_result['primkey'] .'"><td><img src="'.plugins_url().'/wpsc-support-tickets/images/delete.png" style="cursor:pointer;" onclick="if ( confirm(\''.__('Are you sure you wish to delete this department?', 'wpsc-support-tickets').'\') ) { jQuery.post(ajaxurl+\'?action=wpsct_delete_department\', { wpsct_primkey: '.$dep_result['primkey'] .'}, function(data) { jQuery(\'#wpsct_department_'.$dep_result['primkey'] .'\').remove(); });  }" />'.$dep_result['primkey'].'</td><td class="wpsct-edit" id="wpsctDepEditName_'.$dep_result['primkey'].'">'.base64_decode($dep_result['name']).'</td><td class="wpsct-edit" id="wpsctDepEditDesc_'.$dep_result['primkey'].'">'.base64_decode($dep_result['description']).'</td><td class="wpsct-edit-user" id="wpsctDepEditLeadUser_'.$dep_result['primkey'].'">'.$username.'</td><td id="wpsctDepEditEnabled_'.$dep_result['primkey'].'" class="wpsct-edit-enabled">';
                if ($dep_result['enabled']==1) {echo __('Enabled', 'wpsc-support-tickets');} else {echo __('Disabled', 'wpsc-support-tickets');}
                echo '</td><td class="wpsct-edit" id="wpsctDepEditSlug_'.$dep_result['primkey'].'">'.base64_decode($dep_result['group_name_slug']).'</td><td id="wpsctDepEditParent_'.$dep_result['primkey'].'" class="wpsct-edit-parent">'.$parent.'</td><td id="wpsctDepEditForward_'.$dep_result['primkey'].'" class="wpsct-edit-enabled">';
                if ($dep_result['forward_all_department_emails']==1) {echo __('Enabled', 'wpsc-support-tickets');} else {echo __('Disabled', 'wpsc-support-tickets');}
                echo '</td><td class="wpsct-edit" id="wpsctDepEditEmail_'.$dep_result['primkey'].'">'.$dep_result['main_department_email'].'</td></tr>';
            }
            echo '</tbody></table>';
        }

        echo '</div></div></div>';

    }

    add_action('wpscSupportTickets_departmentsHook', 'wpscSupportTicketDepartments');

function wpsctAjaxSaveDepartmentEdit() {
        global $wpdb;

        if(is_user_logged_in()) {
            wpscSupportTickets::checkPermissions();        

            $id_raw = $_POST['id'];
            $value = $_POST['value'];

            $sql = null;
            $to_be_replaced = array("wpsctDepEditName_", "wpsctDepEditDesc_", "wpsctDepEditLeadUser_", "wpsctDepEditEnabled_", "wpsctDepEditSlug_", "wpsctDepEditParent_", "wpsctDepEditForward_", "wpsctDepEditEmail_");
            $id = intval(str_replace($to_be_replaced, "", $id_raw));

            if (strpos($id_raw,'wpsctDepEditName') !== false) {
                $newvalue = base64_encode($value);
                $sql = "UPDATE `{$wpdb->prefix}wpscst_departments` SET `name`='$newvalue' WHERE `primkey`='{$id}'; ";
            }  

            if (strpos($id_raw,'wpsctDepEditDesc') !== false) {
                $newvalue = base64_encode($value);
                $sql = "UPDATE `{$wpdb->prefix}wpscst_departments` SET `description`='$newvalue' WHERE `primkey`='{$id}'; ";
            }  

            if (strpos($id_raw,'wpsctDepEditLeadUser') !== false) {
                $value = intval($value);
                $sql = "UPDATE `{$wpdb->prefix}wpscst_departments` SET `admin_user_id`='$value' WHERE `primkey`='{$id}'; ";
                global $user_info;
                $user_info = get_userdata($value);    
                $value = $user_info->user_login;                
            }      

            if (strpos($id_raw,'wpsctDepEditEnabled') !== false) {
                $value = intval($value);
                $sql = "UPDATE `{$wpdb->prefix}wpscst_departments` SET `enabled`='$value' WHERE `primkey`='{$id}'; ";
                if ($value==1) {$value = __('Enabled', 'wpsc-support-tickets');} else {$value = __('Disabled', 'wpsc-support-tickets');}
            }   

            if (strpos($id_raw,'wpsctDepEditSlug') !== false) {
                $newvalue = base64_encode($value);
                $sql = "UPDATE `{$wpdb->prefix}wpscst_departments` SET `group_name_slug`='$newvalue' WHERE `primkey`='{$id}'; ";
            }              
            
            if (strpos($id_raw,'wpsctDepEditParent') !== false) {
                $value = intval($value);
                $sql = "UPDATE `{$wpdb->prefix}wpscst_departments` SET `parent_department`='$value' WHERE `primkey`='{$id}'; ";
                $parent_results = $wpdb->get_results("SELECT `name` FROM `{$wpdb->prefix}wpscst_departments` WHERE `primkey`='{$value}' ;", ARRAY_A);
                if(@isset($parent_results[0]['name'])) {
                    $value = $parent_results[0]['name'];
                }                
            } 

            if (strpos($id_raw,'wpsctDepEditForward') !== false) {
                $value = intval($value);
                $sql = "UPDATE `{$wpdb->prefix}wpscst_departments` SET `forward_all_department_emails`='$value' WHERE `primkey`='{$id}'; ";
                if ($value==1) {$value = __('Enabled', 'wpsc-support-tickets');} else {$value = __('Disabled', 'wpsc-support-tickets');}
            }  
            
            if (strpos($id_raw,'wpsctDepEditEmail') !== false) {
                $sql = "UPDATE `{$wpdb->prefix}wpscst_departments` SET `main_department_email`='$value' WHERE `primkey`='{$id}'; ";
            }              

            if($sql!=null) {
                $wpdb->query($sql);
            }


            echo stripslashes($value);
        }
        die();     
}
add_action( 'wp_ajax_wpsct_save_department_edit', 'wpsctAjaxSaveDepartmentEdit' );

if(!function_exists('wpsctAjaxDeleteDepartment')) {
    function wpsctAjaxDeleteDepartment() {
        global $wpdb;
        if(is_user_logged_in()) {
            wpscSupportTickets::checkPermissions();

            $id = intval($_POST['wpsct_primkey']);
            
            $wpdb->query( "DELETE FROM `{$wpdb->prefix}wpscst_departments` WHERE `primkey`='{$id}'; ");
            
        
        }
        die();
    }
}
add_action( 'wp_ajax_wpsct_delete_department', 'wpsctAjaxDeleteDepartment' );

?>