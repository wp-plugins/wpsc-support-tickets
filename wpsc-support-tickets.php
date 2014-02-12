<?php
/*
  Plugin Name: wpsc Support Tickets
  Plugin URI: http://wpscsupporttickets.com/wordpress-support-ticket-plugin/
  Description: An open source help desk and support ticket system for Wordpress using jQuery. Easy to use for both users & admins.
  Version: 4.3.1
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
$wpscSupportTickets_version = 4.3;
$wpscSupportTickets_db_version = 4.3;
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


function wpsctPromptForCustomFields() {
    global $wpdb;
    
    $devOptions = get_option('wpscSupportTicketsAdminOptions');
    $output = '';
    if (session_id() == "") {@session_start();};
    
    // Custom form fields here
    $table_name33 = $wpdb->prefix . "wpstorecart_meta";
    $grabrecord = "SELECT * FROM `{$table_name33}` WHERE `type`='wpst-requiredinfo' ORDER BY `foreignkey` ASC;";

    $resultscf = $wpdb->get_results( $grabrecord , ARRAY_A );
    if(isset($resultscf)) {
            foreach ($resultscf as $field) {
                $specific_items = explode("||", $field['value']);
                $prev_val = $_SESSION['wpsct_custom_'.$field['primkey']];
                if($specific_items[2]=='input (text)') {
                    $output .= '<tr><td><h3>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins><div class="wpst-required-symbol" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="display:inline;"'; }  $output .='>* </div></ins>';}$output.='</h3><input  id="wpsct_custom_'.$field['primkey'].'" type="text"  value="'.$_SESSION['wpsct_custom_'.$field['primkey']].'" name="wpsct_custom_'.$field['primkey'].'" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="width:100%"'; }  $output .='  /></td></tr>';
                }
                if($specific_items[2]=='shippingcity') {
                    $output .= '<tr><td><h3>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins><div class="wpst-required-symbol" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="display:inline;"'; }  $output .='>* </div></ins>';}$output.='</h3><input  id="wpsct_custom_'.$field['primkey'].'" type="text"  value="'.$_SESSION['wpsct_custom_'.$field['primkey']].'" name="wpsct_custom_'.$field['primkey'].'" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="width:100%"'; }  $output .=' /></td></tr>';
                }                
                if($specific_items[2]=='firstname') {
                    $output .= '<tr><td><h3>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins><div class="wpst-required-symbol" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="display:inline;"'; }  $output .='>* </div></ins>';}$output.='</h3><input  id="wpsct_custom_'.$field['primkey'].'" type="text"  value="'.$_SESSION['wpsct_custom_'.$field['primkey']].'" name="wpsct_custom_'.$field['primkey'].'" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="width:100%"'; }  $output .=' /></td></tr>';
                }
                if($specific_items[2]=='lastname') {
                    $output .= '<tr><td><h3>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins><div class="wpst-required-symbol" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="display:inline;"'; }  $output .='>* </div></ins>';}$output.='</h3><input  id="wpsct_custom_'.$field['primkey'].'" type="text"  value="'.$_SESSION['wpsct_custom_'.$field['primkey']].'" name="wpsct_custom_'.$field['primkey'].'" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="width:100%"'; }  $output .=' /></td></tr>';
                }                
                if($specific_items[2]=='shippingaddress') {
                    $output .= '<tr><td><h3>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins><div class="wpst-required-symbol" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="display:inline;"'; }  $output .='>* </div></ins>';}$output.='</h3><input  id="wpsct_custom_'.$field['primkey'].'" type="text"  value="'.$_SESSION['wpsct_custom_'.$field['primkey']].'" name="wpsct_custom_'.$field['primkey'].'" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="width:100%"'; }  $output .=' /></td></tr>';
                }                
                if($specific_items[2]=='input (numeric)') {
                    $output .= '<tr><td><h3>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins><div class="wpst-required-symbol" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="display:inline;"'; }  $output .='>* </div></ins>';}$output.='</h3><input  id="wpsct_custom_'.$field['primkey'].'" type="text"  value="'.$_SESSION['wpsct_custom_'.$field['primkey']].'" name="wpsct_custom_'.$field['primkey'].'" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="width:100%"'; }  $output .=' /></td></tr>';
                }
                if($specific_items[2]=='zipcode') {
                    $output .= '<tr><td><h3>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins><div class="wpst-required-symbol" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="display:inline;"'; }  $output .='>* </div></ins>';}$output.='</h3><input  id="wpsct_custom_'.$field['primkey'].'" type="text" size="22" value="'.$_SESSION['wpsct_custom_'.$field['primkey']].'" name="wpsct_custom_'.$field['primkey'].'" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="width:100%"'; }  $output .=' /></td></tr>';
                }                
                if($specific_items[2]=='textarea') {
                    $output .= '<tr><td><h3>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins><div class="wpst-required-symbol" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="display:inline;"'; }  $output .='>* </div></ins>';}$output.='</h3><textarea  id="wpsct_custom_'.$field['primkey'].'" name="wpsct_custom_'.$field['primkey'].'" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="width:100%"'; }  $output .='>'.$_SESSION['wpsct_custom_'.$field['primkey']].'</textarea></td></tr>';
                }
                if($specific_items[2]=='states' || $specific_items[2]=='taxstates') {
                    $output .= '<tr><td><h3>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins><div class="wpst-required-symbol" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="display:inline;"'; }  $output .='>* </div></ins>';}$output.='</h3><select  name="wpsct_custom_'.$field['primkey'].'" class="wpsct-states" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="width:100%"'; }  $output .='>
                    <option value="not applicable"'; if($prev_val==''){$output.=' selected="selected" ';}; $output.='>'.__('Other (Non-US)', 'wpsc-support-tickets').'</option>
                    <option value="AL"'; if($prev_val=='AL'){$output.=' selected="selected" ';}; $output.='>'.__('Alabama', 'wpsc-support-tickets').'</option>
                    <option value="AK"'; if($prev_val=='AK'){$output.=' selected="selected" ';}; $output.='>'.__('Alaska', 'wpsc-support-tickets').'</option>
                    <option value="AZ"'; if($prev_val=='AZ'){$output.=' selected="selected" ';}; $output.='>'.__('Arizona', 'wpsc-support-tickets').'</option>
                    <option value="AR"'; if($prev_val=='AR'){$output.=' selected="selected" ';}; $output.='>'.__('Arkansas', 'wpsc-support-tickets').'</option>
                    <option value="CA"'; if($prev_val=='CA'){$output.=' selected="selected" ';}; $output.='>'.__('California', 'wpsc-support-tickets').'</option>
                    <option value="CO"'; if($prev_val=='CO'){$output.=' selected="selected" ';}; $output.='>'.__('Colorado', 'wpsc-support-tickets').'</option>
                    <option value="CT"'; if($prev_val=='CT'){$output.=' selected="selected" ';}; $output.='>'.__('Connecticut', 'wpsc-support-tickets').'</option>
                    <option value="DE"'; if($prev_val=='DE'){$output.=' selected="selected" ';}; $output.='>'.__('Delaware', 'wpsc-support-tickets').'</option>
                    <option value="DC"'; if($prev_val=='DC'){$output.=' selected="selected" ';}; $output.='>'.__('District Of Columbia', 'wpsc-support-tickets').'</option>
                    <option value="FL"'; if($prev_val=='FL'){$output.=' selected="selected" ';}; $output.='>'.__('Florida', 'wpsc-support-tickets').'</option>
                    <option value="GA"'; if($prev_val=='GA'){$output.=' selected="selected" ';}; $output.='>'.__('Georgia', 'wpsc-support-tickets').'</option>
                    <option value="HI"'; if($prev_val=='HI'){$output.=' selected="selected" ';}; $output.='>'.__('Hawaii', 'wpsc-support-tickets').'</option>
                    <option value="ID"'; if($prev_val=='ID'){$output.=' selected="selected" ';}; $output.='>'.__('Idaho', 'wpsc-support-tickets').'</option>
                    <option value="IL"'; if($prev_val=='IL'){$output.=' selected="selected" ';}; $output.='>'.__('Illinois', 'wpsc-support-tickets').'</option>
                    <option value="IN"'; if($prev_val=='IN'){$output.=' selected="selected" ';}; $output.='>'.__('Indiana', 'wpsc-support-tickets').'</option>
                    <option value="IA"'; if($prev_val=='IA'){$output.=' selected="selected" ';}; $output.='>'.__('Iowa', 'wpsc-support-tickets').'</option>
                    <option value="KS"'; if($prev_val=='KS'){$output.=' selected="selected" ';}; $output.='>'.__('Kansas', 'wpsc-support-tickets').'</option>
                    <option value="KY"'; if($prev_val=='KY'){$output.=' selected="selected" ';}; $output.='>'.__('Kentucky', 'wpsc-support-tickets').'</option>
                    <option value="LA"'; if($prev_val=='LA'){$output.=' selected="selected" ';}; $output.='>'.__('Louisiana', 'wpsc-support-tickets').'</option>
                    <option value="ME"'; if($prev_val=='ME'){$output.=' selected="selected" ';}; $output.='>'.__('Maine', 'wpsc-support-tickets').'</option>
                    <option value="MD"'; if($prev_val=='MD'){$output.=' selected="selected" ';}; $output.='>'.__('Maryland', 'wpsc-support-tickets').'</option>
                    <option value="MA"'; if($prev_val=='MA'){$output.=' selected="selected" ';}; $output.='>'.__('Massachusetts', 'wpsc-support-tickets').'</option>
                    <option value="MI"'; if($prev_val=='MI'){$output.=' selected="selected" ';}; $output.='>'.__('Michigan', 'wpsc-support-tickets').'</option>
                    <option value="MN"'; if($prev_val=='MN'){$output.=' selected="selected" ';}; $output.='>'.__('Minnesota', 'wpsc-support-tickets').'</option>
                    <option value="MS"'; if($prev_val=='MS'){$output.=' selected="selected" ';}; $output.='>'.__('Mississippi', 'wpsc-support-tickets').'</option>
                    <option value="MO"'; if($prev_val=='MO'){$output.=' selected="selected" ';}; $output.='>'.__('Missouri', 'wpsc-support-tickets').'</option>
                    <option value="MT"'; if($prev_val=='MT'){$output.=' selected="selected" ';}; $output.='>'.__('Montana', 'wpsc-support-tickets').'</option>
                    <option value="NE"'; if($prev_val=='NE'){$output.=' selected="selected" ';}; $output.='>'.__('Nebraska', 'wpsc-support-tickets').'</option>
                    <option value="NV"'; if($prev_val=='NV'){$output.=' selected="selected" ';}; $output.='>'.__('Nevada', 'wpsc-support-tickets').'</option>
                    <option value="NH"'; if($prev_val=='NH'){$output.=' selected="selected" ';}; $output.='>'.__('New Hampshire', 'wpsc-support-tickets').'</option>
                    <option value="NJ"'; if($prev_val=='NJ'){$output.=' selected="selected" ';}; $output.='>'.__('New Jersey', 'wpsc-support-tickets').'</option>
                    <option value="NM"'; if($prev_val=='NM'){$output.=' selected="selected" ';}; $output.='>'.__('New Mexico', 'wpsc-support-tickets').'</option>
                    <option value="NY"'; if($prev_val=='NY'){$output.=' selected="selected" ';}; $output.='>'.__('New York', 'wpsc-support-tickets').'</option>
                    <option value="NC"'; if($prev_val=='NC'){$output.=' selected="selected" ';}; $output.='>'.__('North Carolina', 'wpsc-support-tickets').'</option>
                    <option value="ND"'; if($prev_val=='ND'){$output.=' selected="selected" ';}; $output.='>'.__('North Dakota', 'wpsc-support-tickets').'</option>
                    <option value="OH"'; if($prev_val=='OH'){$output.=' selected="selected" ';}; $output.='>'.__('Ohio', 'wpsc-support-tickets').'</option>
                    <option value="OK"'; if($prev_val=='OK'){$output.=' selected="selected" ';}; $output.='>'.__('Oklahoma', 'wpsc-support-tickets').'</option>
                    <option value="OR"'; if($prev_val=='OR'){$output.=' selected="selected" ';}; $output.='>'.__('Oregon', 'wpsc-support-tickets').'</option>
                    <option value="PA"'; if($prev_val=='PA'){$output.=' selected="selected" ';}; $output.='>'.__('Pennsylvania', 'wpsc-support-tickets').'</option>
                    <option value="RI"'; if($prev_val=='RI'){$output.=' selected="selected" ';}; $output.='>'.__('Rhode Island', 'wpsc-support-tickets').'</option>
                    <option value="SC"'; if($prev_val=='SC'){$output.=' selected="selected" ';}; $output.='>'.__('South Carolina', 'wpsc-support-tickets').'</option>
                    <option value="SD"'; if($prev_val=='SD'){$output.=' selected="selected" ';}; $output.='>'.__('South Dakota', 'wpsc-support-tickets').'</option>
                    <option value="TN"'; if($prev_val=='TN'){$output.=' selected="selected" ';}; $output.='>'.__('Tennessee', 'wpsc-support-tickets').'</option>
                    <option value="TX"'; if($prev_val=='TX'){$output.=' selected="selected" ';}; $output.='>'.__('Texas', 'wpsc-support-tickets').'</option>
                    <option value="UT"'; if($prev_val=='UT'){$output.=' selected="selected" ';}; $output.='>'.__('Utah', 'wpsc-support-tickets').'</option>
                    <option value="VT"'; if($prev_val=='VT'){$output.=' selected="selected" ';}; $output.='>'.__('Vermont', 'wpsc-support-tickets').'</option>
                    <option value="VA"'; if($prev_val=='VA'){$output.=' selected="selected" ';}; $output.='>'.__('Virginia', 'wpsc-support-tickets').'</option>
                    <option value="WA"'; if($prev_val=='WA'){$output.=' selected="selected" ';}; $output.='>'.__('Washington', 'wpsc-support-tickets').'</option>
                    <option value="WV"'; if($prev_val=='WV'){$output.=' selected="selected" ';}; $output.='>'.__('West Virginia', 'wpsc-support-tickets').'</option>
                    <option value="WI"'; if($prev_val=='WI'){$output.=' selected="selected" ';}; $output.='>'.__('Wisconsin', 'wpsc-support-tickets').'</option>
                    <option value="WY"'; if($prev_val=='WY'){$output.=' selected="selected" ';}; $output.='>'.__('Wyoming', 'wpsc-support-tickets').'</option>
                    </select></td></tr>';
                }
                if($specific_items[2]=='countries' || $specific_items[2]=='taxcountries') {
                    $output .= '<tr><td><h3>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins><div class="wpst-required-symbol" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="display:inline;"'; }  $output .='>* </div></ins>';}$output.='</h3><select  name="wpsct_custom_'.$field['primkey'].'" class="wpsct-countries" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="width:100%"'; }  $output .='>
                    <option value="United States"'; if($prev_val=='United States'){$output.=' selected="selected" ';}; $output.='>'.__('United States', 'wpsc-support-tickets').'</option>
                    <option value="Canada"'; if($prev_val=='Canada'){$output.=' selected="selected" ';}; $output.='>'.__('Canada', 'wpsc-support-tickets').'</option>
                    <option value="United Kingdom"'; if($prev_val=='United Kingdom'){$output.=' selected="selected" ';}; $output.=' >'.__('United Kingdom', 'wpsc-support-tickets').'</option>
                    <option value="Ireland"'; if($prev_val=='Ireland'){$output.=' selected="selected" ';}; $output.=' >'.__('Ireland', 'wpsc-support-tickets').'</option>
                    <option value="Australia"'; if($prev_val=='Australia'){$output.=' selected="selected" ';}; $output.=' >'.__('Australia', 'wpsc-support-tickets').'</option>
                    <option value="New Zealand"'; if($prev_val=='New Zealand'){$output.=' selected="selected" ';}; $output.=' >'.__('New Zealand', 'wpsc-support-tickets').'</option>
                    <option value="Afghanistan"'; if($prev_val=='Afghanistan'){$output.=' selected="selected" ';}; $output.='>'.__('Afghanistan', 'wpsc-support-tickets').'</option>
                    <option value="Albania"'; if($prev_val=='Albania'){$output.=' selected="selected" ';}; $output.='>'.__('Albania', 'wpsc-support-tickets').'</option>
                    <option value="Algeria"'; if($prev_val=='Algeria'){$output.=' selected="selected" ';}; $output.='>'.__('Algeria', 'wpsc-support-tickets').'</option>
                    <option value="American Samoa"'; if($prev_val=='American Samoa'){$output.=' selected="selected" ';}; $output.='>'.__('American Samoa', 'wpsc-support-tickets').'</option>
                    <option value="Andorra"'; if($prev_val=='Andorra'){$output.=' selected="selected" ';}; $output.='>'.__('Andorra', 'wpsc-support-tickets').'</option>
                    <option value="Angola"'; if($prev_val=='Angola'){$output.=' selected="selected" ';}; $output.='>'.__('Angola', 'wpsc-support-tickets').'</option>
                    <option value="Anguilla"'; if($prev_val=='Anguilla'){$output.=' selected="selected" ';}; $output.='>'.__('Anguilla', 'wpsc-support-tickets').'</option>
                    <option value="Antarctica"'; if($prev_val=='Antarctica'){$output.=' selected="selected" ';}; $output.='>'.__('Antarctica', 'wpsc-support-tickets').'</option>
                    <option value="Antigua and Barbuda"'; if($prev_val=='Antigua and Barbuda'){$output.=' selected="selected" ';}; $output.='>'.__('Antigua and Barbuda', 'wpsc-support-tickets').'</option>
                    <option value="Argentina"'; if($prev_val=='Argentina'){$output.=' selected="selected" ';}; $output.='>'.__('Argentina', 'wpsc-support-tickets').'</option>
                    <option value="Armenia"'; if($prev_val=='Armenia'){$output.=' selected="selected" ';}; $output.='>'.__('Armenia', 'wpsc-support-tickets').'</option>
                    <option value="Aruba"'; if($prev_val=='Aruba'){$output.=' selected="selected" ';}; $output.='>'.__('Aruba', 'wpsc-support-tickets').'</option>
                    <option value="Australia"'; if($prev_val=='Australia'){$output.=' selected="selected" ';}; $output.='>'.__('Australia', 'wpsc-support-tickets').'</option>
                    <option value="Austria"'; if($prev_val=='Austria'){$output.=' selected="selected" ';}; $output.='>'.__('Austria', 'wpsc-support-tickets').'</option>
                    <option value="Azerbaijan"'; if($prev_val=='Azerbaijan'){$output.=' selected="selected" ';}; $output.='>'.__('Azerbaijan', 'wpsc-support-tickets').'</option>
                    <option value="Bahamas"'; if($prev_val=='Bahamas'){$output.=' selected="selected" ';}; $output.='>'.__('Bahamas', 'wpsc-support-tickets').'</option>
                    <option value="Bahrain"'; if($prev_val=='Bahrain'){$output.=' selected="selected" ';}; $output.='>'.__('Bahrain', 'wpsc-support-tickets').'</option>
                    <option value="Bangladesh"'; if($prev_val=='Bangladesh'){$output.=' selected="selected" ';}; $output.='>'.__('Bangladesh', 'wpsc-support-tickets').'</option>
                    <option value="Barbados"'; if($prev_val=='Barbados'){$output.=' selected="selected" ';}; $output.='>'.__('Barbados', 'wpsc-support-tickets').'</option>
                    <option value="Belarus"'; if($prev_val=='Belarus'){$output.=' selected="selected" ';}; $output.='>'.__('Belarus', 'wpsc-support-tickets').'</option>
                    <option value="Belgium"'; if($prev_val=='Belgium'){$output.=' selected="selected" ';}; $output.='>'.__('Belgium', 'wpsc-support-tickets').'</option>
                    <option value="Belize"'; if($prev_val=='Belize'){$output.=' selected="selected" ';}; $output.='>'.__('Belize', 'wpsc-support-tickets').'</option>
                    <option value="Benin"'; if($prev_val=='Benin'){$output.=' selected="selected" ';}; $output.='>'.__('Benin', 'wpsc-support-tickets').'</option>
                    <option value="Bermuda"'; if($prev_val=='Bermuda'){$output.=' selected="selected" ';}; $output.='>'.__('Bermuda', 'wpsc-support-tickets').'</option>
                    <option value="Bhutan"'; if($prev_val=='Bhutan'){$output.=' selected="selected" ';}; $output.='>'.__('Bhutan', 'wpsc-support-tickets').'</option>
                    <option value="Bolivia"'; if($prev_val=='Bolivia'){$output.=' selected="selected" ';}; $output.='>'.__('Bolivia', 'wpsc-support-tickets').'</option>
                    <option value="Bosnia and Herzegovina"'; if($prev_val=='Bosnia and Herzegovina'){$output.=' selected="selected" ';}; $output.='>'.__('Bosnia and Herzegovina', 'wpsc-support-tickets').'</option>
                    <option value="Botswana"'; if($prev_val=='Botswana'){$output.=' selected="selected" ';}; $output.='>'.__('Botswana', 'wpsc-support-tickets').'</option>
                    <option value="Bouvet Island"'; if($prev_val=='Bouvet Island'){$output.=' selected="selected" ';}; $output.='>'.__('Bouvet Island', 'wpsc-support-tickets').'</option>
                    <option value="Brazil"'; if($prev_val=='Brazil'){$output.=' selected="selected" ';}; $output.='>'.__('Brazil', 'wpsc-support-tickets').'</option>
                    <option value="British Indian Ocean Territory"'; if($prev_val=='British Indian Ocean Territory'){$output.=' selected="selected" ';}; $output.='>'.__('British Indian Ocean Territory', 'wpsc-support-tickets').'</option>
                    <option value="Brunei Darussalam"'; if($prev_val=='Brunei Darussalam'){$output.=' selected="selected" ';}; $output.='>'.__('Brunei Darussalam', 'wpsc-support-tickets').'</option>
                    <option value="Bulgaria"'; if($prev_val=='Bulgaria'){$output.=' selected="selected" ';}; $output.='>'.__('Bulgaria', 'wpsc-support-tickets').'</option>
                    <option value="Burkina Faso"'; if($prev_val=='Burkina Faso'){$output.=' selected="selected" ';}; $output.='>'.__('Burkina Faso', 'wpsc-support-tickets').'</option>
                    <option value="Burundi"'; if($prev_val=='Burundi'){$output.=' selected="selected" ';}; $output.='>'.__('Burundi', 'wpsc-support-tickets').'</option>
                    <option value="Cambodia"'; if($prev_val=='Cambodia'){$output.=' selected="selected" ';}; $output.='>'.__('Cambodia', 'wpsc-support-tickets').'</option>
                    <option value="Cameroon"'; if($prev_val=='Cameroon'){$output.=' selected="selected" ';}; $output.='>'.__('Cameroon', 'wpsc-support-tickets').'</option>
                    <option value="Canada"'; if($prev_val=='Canada'){$output.=' selected="selected" ';}; $output.='>'.__('Canada', 'wpsc-support-tickets').'</option>
                    <option value="Cape Verde"'; if($prev_val=='Cape Verde'){$output.=' selected="selected" ';}; $output.='>'.__('Cape Verde', 'wpsc-support-tickets').'</option>
                    <option value="Cayman Islands"'; if($prev_val=='Cayman Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Cayman Islands', 'wpsc-support-tickets').'</option>
                    <option value="Central African Republic"'; if($prev_val=='Central African Republic'){$output.=' selected="selected" ';}; $output.='>'.__('Central African Republic', 'wpsc-support-tickets').'</option>
                    <option value="Chad"'; if($prev_val=='Chad'){$output.=' selected="selected" ';}; $output.='>'.__('Chad', 'wpsc-support-tickets').'</option>
                    <option value="Chile"'; if($prev_val=='Chile'){$output.=' selected="selected" ';}; $output.='>'.__('Chile', 'wpsc-support-tickets').'</option>
                    <option value="China"'; if($prev_val=='China'){$output.=' selected="selected" ';}; $output.='>'.__('China', 'wpsc-support-tickets').'</option>
                    <option value="Christmas Island"'; if($prev_val=='Christmas Island'){$output.=' selected="selected" ';}; $output.='>'.__('Christmas Island', 'wpsc-support-tickets').'</option>
                    <option value="Cocos (Keeling) Islands"'; if($prev_val=='Cocos (Keeling) Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Cocos (Keeling) Islands', 'wpsc-support-tickets').'</option>
                    <option value="Colombia"'; if($prev_val=='Colombia'){$output.=' selected="selected" ';}; $output.='>'.__('Colombia', 'wpsc-support-tickets').'</option>
                    <option value="Comoros"'; if($prev_val=='Comoros'){$output.=' selected="selected" ';}; $output.='>'.__('Comoros', 'wpsc-support-tickets').'</option>
                    <option value="Congo"'; if($prev_val=='Congo'){$output.=' selected="selected" ';}; $output.='>'.__('Congo', 'wpsc-support-tickets').'</option>
                    <option value="Congo, The Democratic Republic of The"'; if($prev_val=='Congo, The Democratic Republic of The'){$output.=' selected="selected" ';}; $output.='>'.__('Congo, The Democratic Republic of The', 'wpsc-support-tickets').'</option>
                    <option value="Cook Islands"'; if($prev_val=='Cook Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Cook Islands', 'wpsc-support-tickets').'</option>
                    <option value="Costa Rica"'; if($prev_val=='Costa Rica'){$output.=' selected="selected" ';}; $output.='>'.__('Costa Rica', 'wpsc-support-tickets').'</option>
                    <option value="Cote D\'ivoire"'; if($prev_val=='Cote D\'ivoire'){$output.=' selected="selected" ';}; $output.='>'.__('Cote D\'ivoire', 'wpsc-support-tickets').'</option>
                    <option value="Croatia"'; if($prev_val=='Croatia'){$output.=' selected="selected" ';}; $output.='>'.__('Croatia', 'wpsc-support-tickets').'</option>
                    <option value="Cuba"'; if($prev_val=='Cuba'){$output.=' selected="selected" ';}; $output.='>'.__('Cuba', 'wpsc-support-tickets').'</option>
                    <option value="Cyprus"'; if($prev_val=='Cyprus'){$output.=' selected="selected" ';}; $output.='>'.__('Cyprus', 'wpsc-support-tickets').'</option>
                    <option value="Czech Republic"'; if($prev_val=='Czech Republic'){$output.=' selected="selected" ';}; $output.='>'.__('Czech Republic', 'wpsc-support-tickets').'</option>
                    <option value="Denmark"'; if($prev_val=='Denmark'){$output.=' selected="selected" ';}; $output.='>'.__('Denmark', 'wpsc-support-tickets').'</option>
                    <option value="Djibouti"'; if($prev_val=='Djibouti'){$output.=' selected="selected" ';}; $output.='>'.__('Djibouti', 'wpsc-support-tickets').'</option>
                    <option value="Dominica"'; if($prev_val=='Dominica'){$output.=' selected="selected" ';}; $output.='>'.__('Dominica', 'wpsc-support-tickets').'</option>
                    <option value="Dominican Republic"'; if($prev_val=='Dominican Republic'){$output.=' selected="selected" ';}; $output.='>'.__('Dominican Republic', 'wpsc-support-tickets').'</option>
                    <option value="Ecuador"'; if($prev_val=='Ecuador'){$output.=' selected="selected" ';}; $output.='>'.__('Ecuador', 'wpsc-support-tickets').'</option>
                    <option value="Egypt"'; if($prev_val=='Egypt'){$output.=' selected="selected" ';}; $output.='>'.__('Egypt', 'wpsc-support-tickets').'</option>
                    <option value="El Salvador"'; if($prev_val=='El Salvador'){$output.=' selected="selected" ';}; $output.='>'.__('El Salvador', 'wpsc-support-tickets').'</option>
                    <option value="Equatorial Guinea"'; if($prev_val=='Equatorial Guinea'){$output.=' selected="selected" ';}; $output.='>'.__('Equatorial Guinea', 'wpsc-support-tickets').'</option>
                    <option value="Eritrea"'; if($prev_val=='Eritrea'){$output.=' selected="selected" ';}; $output.='>'.__('Eritrea', 'wpsc-support-tickets').'</option>
                    <option value="Estonia"'; if($prev_val=='Estonia'){$output.=' selected="selected" ';}; $output.='>'.__('Estonia', 'wpsc-support-tickets').'</option>
                    <option value="Ethiopia"'; if($prev_val=='Ethiopia'){$output.=' selected="selected" ';}; $output.='>'.__('Ethiopia', 'wpsc-support-tickets').'</option>
                    <option value="Falkland Islands (Malvinas)"'; if($prev_val=='Falkland Islands (Malvinas)'){$output.=' selected="selected" ';}; $output.='>'.__('Falkland Islands (Malvinas)', 'wpsc-support-tickets').'</option>
                    <option value="Faroe Islands"'; if($prev_val=='Faroe Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Faroe Islands', 'wpsc-support-tickets').'</option>
                    <option value="Fiji"'; if($prev_val=='Fiji'){$output.=' selected="selected" ';}; $output.='>'.__('Fiji', 'wpsc-support-tickets').'</option>
                    <option value="Finland"'; if($prev_val=='Finland'){$output.=' selected="selected" ';}; $output.='>'.__('Finland', 'wpsc-support-tickets').'</option>
                    <option value="France"'; if($prev_val=='France'){$output.=' selected="selected" ';}; $output.='>'.__('France', 'wpsc-support-tickets').'</option>
                    <option value="French Guiana"'; if($prev_val=='French Guiana'){$output.=' selected="selected" ';}; $output.='>'.__('French Guiana', 'wpsc-support-tickets').'</option>
                    <option value="French Polynesia"'; if($prev_val=='French Polynesia'){$output.=' selected="selected" ';}; $output.='>'.__('French Polynesia', 'wpsc-support-tickets').'</option>
                    <option value="French Southern Territories"'; if($prev_val=='French Southern Territories'){$output.=' selected="selected" ';}; $output.='>'.__('French Southern Territories', 'wpsc-support-tickets').'</option>
                    <option value="Gabon"'; if($prev_val=='Gabon'){$output.=' selected="selected" ';}; $output.='>'.__('Gabon', 'wpsc-support-tickets').'</option>
                    <option value="Gambia"'; if($prev_val=='Gambia'){$output.=' selected="selected" ';}; $output.='>'.__('Gambia', 'wpsc-support-tickets').'</option>
                    <option value="Georgia"'; if($prev_val=='Georgia'){$output.=' selected="selected" ';}; $output.='>'.__('Georgia', 'wpsc-support-tickets').'</option>
                    <option value="Germany"'; if($prev_val=='Germany'){$output.=' selected="selected" ';}; $output.='>'.__('Germany', 'wpsc-support-tickets').'</option>
                    <option value="Ghana"'; if($prev_val=='Ghana'){$output.=' selected="selected" ';}; $output.='>'.__('Ghana', 'wpsc-support-tickets').'</option>
                    <option value="Gibraltar"'; if($prev_val=='Gibraltar'){$output.=' selected="selected" ';}; $output.='>'.__('Gibraltar', 'wpsc-support-tickets').'</option>
                    <option value="Greece"'; if($prev_val=='Greece'){$output.=' selected="selected" ';}; $output.='>'.__('Greece', 'wpsc-support-tickets').'</option>
                    <option value="Greenland"'; if($prev_val=='Greenland'){$output.=' selected="selected" ';}; $output.='>'.__('Greenland', 'wpsc-support-tickets').'</option>
                    <option value="Grenada"'; if($prev_val=='Grenada'){$output.=' selected="selected" ';}; $output.='>'.__('Grenada', 'wpsc-support-tickets').'</option>
                    <option value="Guadeloupe"'; if($prev_val=='Guadeloupe'){$output.=' selected="selected" ';}; $output.='>'.__('Guadeloupe', 'wpsc-support-tickets').'</option>
                    <option value="Guam"'; if($prev_val=='Guam'){$output.=' selected="selected" ';}; $output.='>'.__('Guam', 'wpsc-support-tickets').'</option>
                    <option value="Guatemala"'; if($prev_val=='Guatemala'){$output.=' selected="selected" ';}; $output.='>'.__('Guatemala', 'wpsc-support-tickets').'</option>
                    <option value="Guinea"'; if($prev_val=='Guinea'){$output.=' selected="selected" ';}; $output.='>'.__('Guinea', 'wpsc-support-tickets').'</option>
                    <option value="Guinea-bissau"'; if($prev_val=='Guinea-bissau'){$output.=' selected="selected" ';}; $output.='>'.__('Guinea-bissau', 'wpsc-support-tickets').'</option>
                    <option value="Guyana"'; if($prev_val=='Guyana'){$output.=' selected="selected" ';}; $output.='>'.__('Guyana', 'wpsc-support-tickets').'</option>
                    <option value="Haiti"'; if($prev_val=='Haiti'){$output.=' selected="selected" ';}; $output.='>'.__('Haiti', 'wpsc-support-tickets').'</option>
                    <option value="Heard Island and Mcdonald Islands"'; if($prev_val=='Heard Island and Mcdonald Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Heard Island and Mcdonald Islands', 'wpsc-support-tickets').'</option>
                    <option value="Holy See (Vatican City State)"'; if($prev_val=='Holy See (Vatican City State)'){$output.=' selected="selected" ';}; $output.='>'.__('Holy See (Vatican City State)', 'wpsc-support-tickets').'</option>
                    <option value="Honduras"'; if($prev_val=='Honduras'){$output.=' selected="selected" ';}; $output.='>'.__('Honduras', 'wpsc-support-tickets').'</option>
                    <option value="Hong Kong"'; if($prev_val=='Hong Kong'){$output.=' selected="selected" ';}; $output.='>'.__('Hong Kong', 'wpsc-support-tickets').'</option>
                    <option value="Hungary"'; if($prev_val=='Hungary'){$output.=' selected="selected" ';}; $output.='>'.__('Hungary', 'wpsc-support-tickets').'</option>
                    <option value="Iceland"'; if($prev_val=='Iceland'){$output.=' selected="selected" ';}; $output.='>'.__('Iceland', 'wpsc-support-tickets').'</option>
                    <option value="India"'; if($prev_val=='India'){$output.=' selected="selected" ';}; $output.='>'.__('India', 'wpsc-support-tickets').'</option>
                    <option value="Indonesia"'; if($prev_val=='Indonesia'){$output.=' selected="selected" ';}; $output.='>'.__('Indonesia', 'wpsc-support-tickets').'</option>
                    <option value="Iran, Islamic Republic of"'; if($prev_val=='Iran, Islamic Republic of'){$output.=' selected="selected" ';}; $output.='>'.__('Iran, Islamic Republic of', 'wpsc-support-tickets').'</option>
                    <option value="Iraq"'; if($prev_val=='Iraq'){$output.=' selected="selected" ';}; $output.='>'.__('Iraq', 'wpsc-support-tickets').'</option>
                    <option value="Ireland"'; if($prev_val=='Ireland'){$output.=' selected="selected" ';}; $output.='>'.__('Ireland', 'wpsc-support-tickets').'</option>
                    <option value="Israel"'; if($prev_val=='Israel'){$output.=' selected="selected" ';}; $output.='>'.__('Israel', 'wpsc-support-tickets').'</option>
                    <option value="Italy"'; if($prev_val=='Italy'){$output.=' selected="selected" ';}; $output.='>'.__('Italy', 'wpsc-support-tickets').'</option>
                    <option value="Jamaica"'; if($prev_val=='Jamaica'){$output.=' selected="selected" ';}; $output.='>'.__('Jamaica', 'wpsc-support-tickets').'</option>
                    <option value="Japan"'; if($prev_val=='Japan'){$output.=' selected="selected" ';}; $output.='>'.__('Japan', 'wpsc-support-tickets').'</option>
                    <option value="Jordan"'; if($prev_val=='Jordan'){$output.=' selected="selected" ';}; $output.='>'.__('Jordan', 'wpsc-support-tickets').'</option>
                    <option value="Kazakhstan"'; if($prev_val=='Kazakhstan'){$output.=' selected="selected" ';}; $output.='>'.__('Kazakhstan', 'wpsc-support-tickets').'</option>
                    <option value="Kenya"'; if($prev_val=='Kenya'){$output.=' selected="selected" ';}; $output.='>'.__('Kenya', 'wpsc-support-tickets').'</option>
                    <option value="Kiribati"'; if($prev_val=='Kiribati'){$output.=' selected="selected" ';}; $output.='>'.__('Kiribati', 'wpsc-support-tickets').'</option>
                    <option value="Korea, Democratic People\'s Republic of"'; if($prev_val=='Korea, Democratic People\'s Republic of'){$output.=' selected="selected" ';}; $output.='>'.__('Korea, Democratic People\'s Republic of', 'wpsc-support-tickets').'</option>
                    <option value="Korea, Republic of"'; if($prev_val=='Korea, Republic of'){$output.=' selected="selected" ';}; $output.='>'.__('Korea, Republic of', 'wpsc-support-tickets').'</option>
                    <option value="Kuwait"'; if($prev_val=='Kuwait'){$output.=' selected="selected" ';}; $output.='>'.__('Kuwait', 'wpsc-support-tickets').'</option>
                    <option value="Kyrgyzstan"'; if($prev_val=='Kyrgyzstan'){$output.=' selected="selected" ';}; $output.='>'.__('Kyrgyzstan', 'wpsc-support-tickets').'</option>
                    <option value="Lao People\'s Democratic Republic"'; if($prev_val=='Lao People\'s Democratic Republic'){$output.=' selected="selected" ';}; $output.='>'.__('Lao People\'s Democratic Republic', 'wpsc-support-tickets').'</option>
                    <option value="Latvia"'; if($prev_val=='Latvia'){$output.=' selected="selected" ';}; $output.='>'.__('Latvia', 'wpsc-support-tickets').'</option>
                    <option value="Lebanon"'; if($prev_val=='Lebanon'){$output.=' selected="selected" ';}; $output.='>'.__('Lebanon', 'wpsc-support-tickets').'</option>
                    <option value="Lesotho"'; if($prev_val=='Lesotho'){$output.=' selected="selected" ';}; $output.='>'.__('Lesotho', 'wpsc-support-tickets').'</option>
                    <option value="Liberia"'; if($prev_val=='Liberia'){$output.=' selected="selected" ';}; $output.='>'.__('Liberia', 'wpsc-support-tickets').'</option>
                    <option value="Libyan Arab Jamahiriya"'; if($prev_val=='Libyan Arab Jamahiriya'){$output.=' selected="selected" ';}; $output.='>'.__('Libyan Arab Jamahiriya', 'wpsc-support-tickets').'</option>
                    <option value="Liechtenstein"'; if($prev_val=='Liechtenstein'){$output.=' selected="selected" ';}; $output.='>'.__('Liechtenstein', 'wpsc-support-tickets').'</option>
                    <option value="Lithuania"'; if($prev_val=='Lithuania'){$output.=' selected="selected" ';}; $output.='>'.__('Lithuania', 'wpsc-support-tickets').'</option>
                    <option value="Luxembourg"'; if($prev_val=='Luxembourg'){$output.=' selected="selected" ';}; $output.='>'.__('Luxembourg', 'wpsc-support-tickets').'</option>
                    <option value="Macao"'; if($prev_val=='Macao'){$output.=' selected="selected" ';}; $output.='>'.__('Macao', 'wpsc-support-tickets').'</option>
                    <option value="Macedonia, The Former Yugoslav Republic of"'; if($prev_val=='Macedonia, The Former Yugoslav Republic of'){$output.=' selected="selected" ';}; $output.='>'.__('Macedonia, The Former Yugoslav Republic of', 'wpsc-support-tickets').'</option>
                    <option value="Madagascar"'; if($prev_val=='Madagascar'){$output.=' selected="selected" ';}; $output.='>'.__('Madagascar', 'wpsc-support-tickets').'</option>
                    <option value="Malawi"'; if($prev_val=='Malawi'){$output.=' selected="selected" ';}; $output.='>'.__('Malawi', 'wpsc-support-tickets').'</option>
                    <option value="Malaysia"'; if($prev_val=='Malaysia'){$output.=' selected="selected" ';}; $output.='>'.__('Malaysia', 'wpsc-support-tickets').'</option>
                    <option value="Maldives"'; if($prev_val=='Maldives'){$output.=' selected="selected" ';}; $output.='>'.__('Maldives', 'wpsc-support-tickets').'</option>
                    <option value="Mali"'; if($prev_val=='Mali'){$output.=' selected="selected" ';}; $output.='>'.__('Mali', 'wpsc-support-tickets').'</option>
                    <option value="Malta"'; if($prev_val=='Malta'){$output.=' selected="selected" ';}; $output.='>'.__('Malta', 'wpsc-support-tickets').'</option>
                    <option value="Marshall Islands"'; if($prev_val=='Marshall Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Marshall Islands', 'wpsc-support-tickets').'</option>
                    <option value="Martinique"'; if($prev_val=='Martinique'){$output.=' selected="selected" ';}; $output.='>'.__('Martinique', 'wpsc-support-tickets').'</option>
                    <option value="Mauritania"'; if($prev_val=='Mauritania'){$output.=' selected="selected" ';}; $output.='>'.__('Mauritania', 'wpsc-support-tickets').'</option>
                    <option value="Mauritius"'; if($prev_val=='Mauritius'){$output.=' selected="selected" ';}; $output.='>'.__('Mauritius', 'wpsc-support-tickets').'</option>
                    <option value="Mayotte"'; if($prev_val=='Mayotte'){$output.=' selected="selected" ';}; $output.='>'.__('Mayotte', 'wpsc-support-tickets').'</option>
                    <option value="Mexico"'; if($prev_val=='Mexico'){$output.=' selected="selected" ';}; $output.='>'.__('Mexico', 'wpsc-support-tickets').'</option>
                    <option value="Micronesia, Federated States of"'; if($prev_val=='Micronesia, Federated States of'){$output.=' selected="selected" ';}; $output.='>'.__('Micronesia, Federated States of', 'wpsc-support-tickets').'</option>
                    <option value="Moldova, Republic of"'; if($prev_val=='Moldova, Republic of'){$output.=' selected="selected" ';}; $output.='>'.__('Moldova, Republic of', 'wpsc-support-tickets').'</option>
                    <option value="Monaco"'; if($prev_val=='Monaco'){$output.=' selected="selected" ';}; $output.='>'.__('Monaco', 'wpsc-support-tickets').'</option>
                    <option value="Mongolia"'; if($prev_val=='Mongolia'){$output.=' selected="selected" ';}; $output.='>'.__('Mongolia', 'wpsc-support-tickets').'</option>
                    <option value="Montserrat"'; if($prev_val=='Montserrat'){$output.=' selected="selected" ';}; $output.='>'.__('Montserrat', 'wpsc-support-tickets').'</option>
                    <option value="Morocco"'; if($prev_val=='Morocco'){$output.=' selected="selected" ';}; $output.='>'.__('Morocco', 'wpsc-support-tickets').'</option>
                    <option value="Mozambique"'; if($prev_val=='Mozambique'){$output.=' selected="selected" ';}; $output.='>'.__('Mozambique', 'wpsc-support-tickets').'</option>
                    <option value="Myanmar"'; if($prev_val=='Myanmar'){$output.=' selected="selected" ';}; $output.='>'.__('Myanmar', 'wpsc-support-tickets').'</option>
                    <option value="Namibia"'; if($prev_val=='Namibia'){$output.=' selected="selected" ';}; $output.='>'.__('Namibia', 'wpsc-support-tickets').'</option>
                    <option value="Nauru"'; if($prev_val=='Nauru'){$output.=' selected="selected" ';}; $output.='>'.__('Nauru', 'wpsc-support-tickets').'</option>
                    <option value="Nepal"'; if($prev_val=='Nepal'){$output.=' selected="selected" ';}; $output.='>'.__('Nepal', 'wpsc-support-tickets').'</option>
                    <option value="Netherlands"'; if($prev_val=='Netherlands'){$output.=' selected="selected" ';}; $output.='>'.__('Netherlands', 'wpsc-support-tickets').'</option>
                    <option value="Netherlands Antilles"'; if($prev_val=='Netherlands Antilles'){$output.=' selected="selected" ';}; $output.='>'.__('Netherlands Antilles', 'wpsc-support-tickets').'</option>
                    <option value="New Caledonia"'; if($prev_val=='New Caledonia'){$output.=' selected="selected" ';}; $output.='>'.__('New Caledonia', 'wpsc-support-tickets').'</option>
                    <option value="New Zealand"'; if($prev_val=='New Zealand'){$output.=' selected="selected" ';}; $output.='>'.__('New Zealand', 'wpsc-support-tickets').'</option>
                    <option value="Nicaragua"'; if($prev_val=='Nicaragua'){$output.=' selected="selected" ';}; $output.='>'.__('Nicaragua', 'wpsc-support-tickets').'</option>
                    <option value="Niger"'; if($prev_val=='Niger'){$output.=' selected="selected" ';}; $output.='>'.__('Niger', 'wpsc-support-tickets').'</option>
                    <option value="Nigeria"'; if($prev_val=='Nigeria'){$output.=' selected="selected" ';}; $output.='>'.__('Nigeria', 'wpsc-support-tickets').'</option>
                    <option value="Niue"'; if($prev_val=='Niue'){$output.=' selected="selected" ';}; $output.='>'.__('Niue', 'wpsc-support-tickets').'</option>
                    <option value="Norfolk Island"'; if($prev_val=='Norfolk Island'){$output.=' selected="selected" ';}; $output.='>'.__('Norfolk Island', 'wpsc-support-tickets').'</option>
                    <option value="Northern Mariana Islands"'; if($prev_val=='Northern Mariana Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Northern Mariana Islands', 'wpsc-support-tickets').'</option>
                    <option value="Norway"'; if($prev_val=='Norway'){$output.=' selected="selected" ';}; $output.='>'.__('Norway', 'wpsc-support-tickets').'</option>
                    <option value="Oman"'; if($prev_val=='Oman'){$output.=' selected="selected" ';}; $output.='>'.__('Oman', 'wpsc-support-tickets').'</option>
                    <option value="Pakistan"'; if($prev_val=='Pakistan'){$output.=' selected="selected" ';}; $output.='>'.__('Pakistan', 'wpsc-support-tickets').'</option>
                    <option value="Palau"'; if($prev_val=='Palau'){$output.=' selected="selected" ';}; $output.='>'.__('Palau', 'wpsc-support-tickets').'</option>
                    <option value="Palestinian Territory, Occupied"'; if($prev_val=='Palestinian Territory, Occupied'){$output.=' selected="selected" ';}; $output.='>'.__('Palestinian Territory, Occupied', 'wpsc-support-tickets').'</option>
                    <option value="Panama"'; if($prev_val=='Panama'){$output.=' selected="selected" ';}; $output.='>'.__('Panama', 'wpsc-support-tickets').'</option>
                    <option value="Papua New Guinea"'; if($prev_val=='Papua New Guinea'){$output.=' selected="selected" ';}; $output.='>'.__('Papua New Guinea', 'wpsc-support-tickets').'</option>
                    <option value="Paraguay"'; if($prev_val=='Paraguay'){$output.=' selected="selected" ';}; $output.='>'.__('Paraguay', 'wpsc-support-tickets').'</option>
                    <option value="Peru"'; if($prev_val=='Peru'){$output.=' selected="selected" ';}; $output.='>'.__('Peru', 'wpsc-support-tickets').'</option>
                    <option value="Philippines"'; if($prev_val=='Philippines'){$output.=' selected="selected" ';}; $output.='>'.__('Philippines', 'wpsc-support-tickets').'</option>
                    <option value="Pitcairn"'; if($prev_val=='Pitcairn'){$output.=' selected="selected" ';}; $output.='>'.__('Pitcairn', 'wpsc-support-tickets').'</option>
                    <option value="Poland"'; if($prev_val=='Poland'){$output.=' selected="selected" ';}; $output.='>'.__('Poland', 'wpsc-support-tickets').'</option>
                    <option value="Portugal"'; if($prev_val=='Portugal'){$output.=' selected="selected" ';}; $output.='>'.__('Portugal', 'wpsc-support-tickets').'</option>
                    <option value="Puerto Rico"'; if($prev_val=='Puerto Rico'){$output.=' selected="selected" ';}; $output.='>'.__('Puerto Rico', 'wpsc-support-tickets').'</option>
                    <option value="Qatar"'; if($prev_val=='Qatar'){$output.=' selected="selected" ';}; $output.='>'.__('Qatar', 'wpsc-support-tickets').'</option>
                    <option value="Reunion"'; if($prev_val=='Reunion'){$output.=' selected="selected" ';}; $output.='>'.__('Reunion', 'wpsc-support-tickets').'</option>
                    <option value="Romania"'; if($prev_val=='Romania'){$output.=' selected="selected" ';}; $output.='>'.__('Romania', 'wpsc-support-tickets').'</option>
                    <option value="Russian Federation"'; if($prev_val=='Russian Federation'){$output.=' selected="selected" ';}; $output.='>'.__('Russian Federation', 'wpsc-support-tickets').'</option>
                    <option value="Rwanda"'; if($prev_val=='Rwanda'){$output.=' selected="selected" ';}; $output.='>'.__('Rwanda', 'wpsc-support-tickets').'</option>
                    <option value="Saint Helena"'; if($prev_val=='Saint Helena'){$output.=' selected="selected" ';}; $output.='>'.__('Saint Helena', 'wpsc-support-tickets').'</option>
                    <option value="Saint Kitts and Nevis"'; if($prev_val=='Saint Kitts and Nevis'){$output.=' selected="selected" ';}; $output.='>'.__('Saint Kitts and Nevis', 'wpsc-support-tickets').'</option>
                    <option value="Saint Lucia"'; if($prev_val=='Saint Lucia'){$output.=' selected="selected" ';}; $output.='>'.__('Saint Lucia', 'wpsc-support-tickets').'</option>
                    <option value="Saint Pierre and Miquelon"'; if($prev_val=='Saint Pierre and Miquelon'){$output.=' selected="selected" ';}; $output.='>'.__('Saint Pierre and Miquelon', 'wpsc-support-tickets').'</option>
                    <option value="Saint Vincent and The Grenadines"'; if($prev_val=='Saint Vincent and The Grenadines'){$output.=' selected="selected" ';}; $output.='>'.__('Saint Vincent and The Grenadines', 'wpsc-support-tickets').'</option>
                    <option value="Samoa"'; if($prev_val=='Samoa'){$output.=' selected="selected" ';}; $output.='>'.__('Samoa', 'wpsc-support-tickets').'</option>
                    <option value="San Marino"'; if($prev_val=='San Marino'){$output.=' selected="selected" ';}; $output.='>'.__('San Marino', 'wpsc-support-tickets').'</option>
                    <option value="Sao Tome and Principe"'; if($prev_val=='Sao Tome and Principe'){$output.=' selected="selected" ';}; $output.='>'.__('Sao Tome and Principe', 'wpsc-support-tickets').'</option>
                    <option value="Saudi Arabia"'; if($prev_val=='Saudi Arabia'){$output.=' selected="selected" ';}; $output.='>'.__('Saudi Arabia', 'wpsc-support-tickets').'</option>
                    <option value="Senegal"'; if($prev_val=='Senegal'){$output.=' selected="selected" ';}; $output.='>'.__('Senegal', 'wpsc-support-tickets').'</option>
                    <option value="Serbia and Montenegro"'; if($prev_val=='Serbia and Montenegro'){$output.=' selected="selected" ';}; $output.='>'.__('Serbia and Montenegro', 'wpsc-support-tickets').'</option>
                    <option value="Seychelles"'; if($prev_val=='Seychelles'){$output.=' selected="selected" ';}; $output.='>'.__('Seychelles', 'wpsc-support-tickets').'</option>
                    <option value="Sierra Leone"'; if($prev_val=='Sierra Leone'){$output.=' selected="selected" ';}; $output.='>'.__('Sierra Leone', 'wpsc-support-tickets').'</option>
                    <option value="Singapore"'; if($prev_val=='Singapore'){$output.=' selected="selected" ';}; $output.='>'.__('Singapore', 'wpsc-support-tickets').'</option>
                    <option value="Slovakia"'; if($prev_val=='Slovakia'){$output.=' selected="selected" ';}; $output.='>'.__('Slovakia', 'wpsc-support-tickets').'</option>
                    <option value="Slovenia"'; if($prev_val=='Slovenia'){$output.=' selected="selected" ';}; $output.='>'.__('Slovenia', 'wpsc-support-tickets').'</option>
                    <option value="Solomon Islands"'; if($prev_val=='Solomon Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Solomon Islands', 'wpsc-support-tickets').'</option>
                    <option value="Somalia"'; if($prev_val=='Somalia'){$output.=' selected="selected" ';}; $output.='>'.__('Somalia', 'wpsc-support-tickets').'</option>
                    <option value="South Africa"'; if($prev_val=='South Africa'){$output.=' selected="selected" ';}; $output.='>'.__('South Africa', 'wpsc-support-tickets').'</option>
                    <option value="South Georgia and The South Sandwich Islands"'; if($prev_val=='South Georgia and The South Sandwich Islands'){$output.=' selected="selected" ';}; $output.='>'.__('South Georgia and The South Sandwich Islands', 'wpsc-support-tickets').'</option>
                    <option value="Spain"'; if($prev_val=='Spain'){$output.=' selected="selected" ';}; $output.='>'.__('Spain', 'wpsc-support-tickets').'</option>
                    <option value="Sri Lanka"'; if($prev_val=='Sri Lanka'){$output.=' selected="selected" ';}; $output.='>'.__('Sri Lanka', 'wpsc-support-tickets').'</option>
                    <option value="Sudan"'; if($prev_val=='Sudan'){$output.=' selected="selected" ';}; $output.='>'.__('Sudan', 'wpsc-support-tickets').'</option>
                    <option value="Suriname"'; if($prev_val=='Suriname'){$output.=' selected="selected" ';}; $output.='>'.__('Suriname', 'wpsc-support-tickets').'</option>
                    <option value="Svalbard and Jan Mayen"'; if($prev_val=='Svalbard and Jan Mayen'){$output.=' selected="selected" ';}; $output.='>'.__('Svalbard and Jan Mayen', 'wpsc-support-tickets').'</option>
                    <option value="Swaziland"'; if($prev_val=='Swaziland'){$output.=' selected="selected" ';}; $output.='>'.__('Swaziland', 'wpsc-support-tickets').'</option>
                    <option value="Sweden"'; if($prev_val=='Sweden'){$output.=' selected="selected" ';}; $output.='>'.__('Sweden', 'wpsc-support-tickets').'</option>
                    <option value="Switzerland"'; if($prev_val=='Switzerland'){$output.=' selected="selected" ';}; $output.='>'.__('Switzerland', 'wpsc-support-tickets').'</option>
                    <option value="Syrian Arab Republic"'; if($prev_val=='Syrian Arab Republic'){$output.=' selected="selected" ';}; $output.='>'.__('Syrian Arab Republic', 'wpsc-support-tickets').'</option>
                    <option value="Taiwan, Province of China"'; if($prev_val=='Taiwan, Province of China'){$output.=' selected="selected" ';}; $output.='>'.__('Taiwan, Province of China', 'wpsc-support-tickets').'</option>
                    <option value="Tajikistan"'; if($prev_val=='Tajikistan'){$output.=' selected="selected" ';}; $output.='>'.__('Tajikistan', 'wpsc-support-tickets').'</option>
                    <option value="Tanzania, United Republic of"'; if($prev_val=='Tanzania, United Republic of'){$output.=' selected="selected" ';}; $output.='>'.__('Tanzania, United Republic of', 'wpsc-support-tickets').'</option>
                    <option value="Thailand"'; if($prev_val=='Thailand'){$output.=' selected="selected" ';}; $output.='>'.__('Thailand', 'wpsc-support-tickets').'</option>
                    <option value="Timor-leste"'; if($prev_val=='Timor-leste'){$output.=' selected="selected" ';}; $output.='>'.__('Timor-leste', 'wpsc-support-tickets').'</option>
                    <option value="Togo"'; if($prev_val=='Togo'){$output.=' selected="selected" ';}; $output.='>'.__('Togo', 'wpsc-support-tickets').'</option>
                    <option value="Tokelau"'; if($prev_val=='Tokelau'){$output.=' selected="selected" ';}; $output.='>'.__('Tokelau', 'wpsc-support-tickets').'</option>
                    <option value="Tonga"'; if($prev_val=='Tonga'){$output.=' selected="selected" ';}; $output.='>'.__('Tonga', 'wpsc-support-tickets').'</option>
                    <option value="Trinidad and Tobago"'; if($prev_val=='Trinidad and Tobago'){$output.=' selected="selected" ';}; $output.='>'.__('Trinidad and Tobago', 'wpsc-support-tickets').'</option>
                    <option value="Tunisia"'; if($prev_val=='Tunisia'){$output.=' selected="selected" ';}; $output.='>'.__('Tunisia', 'wpsc-support-tickets').'</option>
                    <option value="Turkey"'; if($prev_val=='Turkey'){$output.=' selected="selected" ';}; $output.='>'.__('Turkey', 'wpsc-support-tickets').'</option>
                    <option value="Turkmenistan"'; if($prev_val=='Turkmenistan'){$output.=' selected="selected" ';}; $output.='>'.__('Turkmenistan', 'wpsc-support-tickets').'</option>
                    <option value="Turks and Caicos Islands"'; if($prev_val=='Turks and Caicos Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Turks and Caicos Islands', 'wpsc-support-tickets').'</option>
                    <option value="Tuvalu"'; if($prev_val=='Tuvalu'){$output.=' selected="selected" ';}; $output.='>'.__('Tuvalu', 'wpsc-support-tickets').'</option>
                    <option value="Uganda"'; if($prev_val=='Uganda'){$output.=' selected="selected" ';}; $output.='>'.__('Uganda', 'wpsc-support-tickets').'</option>
                    <option value="Ukraine"'; if($prev_val=='Ukraine'){$output.=' selected="selected" ';}; $output.='>'.__('Ukraine', 'wpsc-support-tickets').'</option>
                    <option value="United Arab Emirates"'; if($prev_val=='United Arab Emirates'){$output.=' selected="selected" ';}; $output.='>'.__('United Arab Emirates', 'wpsc-support-tickets').'</option>
                    <option value="United States Minor Outlying Islands"'; if($prev_val=='United States Minor Outlying Islands'){$output.=' selected="selected" ';}; $output.='>'.__('United States Minor Outlying Islands', 'wpsc-support-tickets').'</option>
                    <option value="Uruguay"'; if($prev_val=='Uruguay'){$output.=' selected="selected" ';}; $output.='>'.__('Uruguay', 'wpsc-support-tickets').'</option>
                    <option value="Uzbekistan"'; if($prev_val=='Uzbekistan'){$output.=' selected="selected" ';}; $output.='>'.__('Uzbekistan', 'wpsc-support-tickets').'</option>
                    <option value="Vanuatu"'; if($prev_val=='Vanuatu'){$output.=' selected="selected" ';}; $output.='>'.__('Vanuatu', 'wpsc-support-tickets').'</option>
                    <option value="Venezuela"'; if($prev_val=='Venezuela'){$output.=' selected="selected" ';}; $output.='>'.__('Venezuela', 'wpsc-support-tickets').'</option>
                    <option value="Viet Nam"'; if($prev_val=='Viet Nam'){$output.=' selected="selected" ';}; $output.='>'.__('Viet Nam', 'wpsc-support-tickets').'</option>
                    <option value="Virgin Islands, British"'; if($prev_val=='Virgin Islands, British'){$output.=' selected="selected" ';}; $output.='>'.__('Virgin Islands, British', 'wpsc-support-tickets').'</option>
                    <option value="Virgin Islands, U.S."'; if($prev_val=='Virgin Islands, U.S.'){$output.=' selected="selected" ';}; $output.='>'.__('Virgin Islands, U.S.', 'wpsc-support-tickets').'</option>
                    <option value="Wallis and Futuna"'; if($prev_val=='Wallis and Futuna'){$output.=' selected="selected" ';}; $output.='>'.__('Wallis and Futuna', 'wpsc-support-tickets').'</option>
                    <option value="Western Sahara"'; if($prev_val=='Western Sahara'){$output.=' selected="selected" ';}; $output.='>'.__('Western Sahara', 'wpsc-support-tickets').'</option>
                    <option value="Yemen"'; if($prev_val=='Yemen'){$output.=' selected="selected" ';}; $output.='>'.__('Yemen', 'wpsc-support-tickets').'</option>
                    <option value="Zambia"'; if($prev_val=='Zambia'){$output.=' selected="selected" ';}; $output.='>'.__('Zambia', 'wpsc-support-tickets').'</option>
                    <option value="Zimbabwe"'; if($prev_val=='Zimbabwe'){$output.=' selected="selected" ';}; $output.='>'.__('Zimbabwe', 'wpsc-support-tickets').'</option>
                    </select></td></tr>';
                }
                if($specific_items[2]=='email') {
                    $output .= '<tr><td><h3>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins><div class="wpst-required-symbol" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="display:inline;"'; }  $output .='>* </div></ins>';}$output.='</h3><input  id="wpsct_custom_'.$field['primkey'].'" type="text"  value="'.$_SESSION['wpsct_custom_'.$field['primkey']].'" name="wpsct_custom_'.$field['primkey'].'" '; if ($devOptions['disable_inline_styles'] == 'false') { $output.='style="width:100%"'; }  $output .=' /></td></tr>';
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
                'custom_field_frontend_position' => 'after message'
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

                <strong>' . __('Email', 'wpsc-support-tickets') . ':</strong> ' . __('The admin email where all new ticket &amp; reply notification emails will be sent', 'wpsc-support-tickets') . '<br /><input name="email" value="' . $devOptions['email'] . '" style="width:95%;" /><br /><br />

                <strong>' . __('New Ticket Email', 'wpsc-support-tickets') . '</strong> ' . __('The subject &amp; body of the email sent to the customer when creating a new ticket.', 'wpsc-support-tickets') . '<br /><input name="email_new_ticket_subject" value="' . $devOptions['email_new_ticket_subject'] . '" style="width:95%;" />
                <textarea style="width:95%;" name="email_new_ticket_body">' . $devOptions['email_new_ticket_body'] . '</textarea>
                <br /><br />

                <strong>' . __('New Reply Email', 'wpsc-support-tickets') . '</strong> ' . __('The subject &amp; body of the email sent to the customer when there is a new reply.', 'wpsc-support-tickets') . '<br /><input name="email_new_reply_subject" value="' . $devOptions['email_new_reply_subject'] . '" style="width:95%;" />
                <textarea style="width:95%;" name="email_new_reply_body">' . $devOptions['email_new_reply_body'] . '</textarea>
                <br /><br />

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
            if ($devOptions['disable_inline_styles'] == 'false') {
                echo 'style="width:100%"';
            } 
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
                echo 'style="width:100%"';
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