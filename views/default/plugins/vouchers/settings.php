<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */ 

elgg_load_library('elgg:vouchers');
$plugin = $vars["entity"];

$potential_yesno = array(
    "no" => elgg_echo('vouchers:settings:sandbox:no'),
    "yes" => elgg_echo('vouchers:settings:sandbox:yes'),
); 


// set default date format
$defaultdateformat = $plugin->default_dateformat;
if(empty($defaultdateformat)){
        $defaultdateformat = 'F j, Y';
}
// available date formats
$dformat = array(
    "F j, Y" => "January 31, 2013",
    "j F, Y" => "31 January, 2013",
    "j, n, Y" => "31, 1, 2013",
    "m.d.y" => "01.31.13",
    "m.d.Y" => "01.31.2013",            
    "m/d/y" => "01/31/13",
    "m/d/Y" => "01/31/2013",
    "d.m.y" => "31.01.13",
    "d.m.Y" => "31.01.2013",
    "d/m/y" => "31/01/13",
    "d/m/Y" => "31/01/2013",
    "n.d.y" => "1.31.13",
    "n.d.Y" => "1.31.2013",
    "n/d/y" => "1/31/13",
    "n/d/Y" => "1/31/2013",
    "d.n.y" => "31.1.13",
    "d.n.Y" => "31.1.2013",
    "d/n/y" => "31/1/13",
    "d/n/Y" => "31/1/2013",
);

$dateformat = elgg_view('input/dropdown', array('name' => 'params[default_dateformat]', 'value' => $defaultdateformat, 'options_values' => $dformat));
$dateformat .= "<span class='elgg-subtext'>" . elgg_echo('vouchers:settings:defaultdateformat:note') . "</span>";
echo elgg_view_module("inline", elgg_echo('vouchers:settings:defaultdateformat'), $dateformat);        

// set default currency
$defaultcurrency = $plugin->default_currency;
if(empty($defaultcurrency)){
        $defaultdateformat = '€';
} 
$CurrOptions = get_currency_list();  // get currency list
$currency = elgg_view('input/dropdown', array('name' => 'params[default_currency]', 'value' => $defaultcurrency, 'options_values' => $CurrOptions));
$currency .= "<span class='elgg-subtext'>" . elgg_echo('vouchers:settings:defaultcurrency:note') . "</span>";
echo elgg_view_module("inline", elgg_echo('vouchers:settings:defaultcurrency'), $currency);     

// set timezone
$default_timezone = $plugin->default_timezone;
if(empty($default_timezone)){
	$defaulttimezone = 'UTC';
}   

$timezones_list = vouchers_get_all_times_zones();
$timezone = elgg_view('input/dropdown', array('name' => 'params[default_timezone]', 'value' => $default_timezone, 'options_values' => $timezones_list));
$timezone .= "<span class='elgg-subtext'>" . elgg_echo('vouchers:settings:defaulttimezone:note') . "</span>";
echo elgg_view_module("inline", elgg_echo('vouchers:settings:defaulttimezone'), $timezone);     

// set who can post vouchers
$voucher_uploaders = $plugin->voucher_uploaders;
if(empty($voucher_uploaders)){
        $voucher_uploaders = 'allmembers';
}    
$potential_uploaders = array(
    "admins" => elgg_echo('vouchers:settings:uploaders:admins'),
    "allmembers" => elgg_echo('vouchers:settings:uploaders:allmembers'),
);
$uploaders = elgg_view('input/dropdown', array('name' => 'params[voucher_uploaders]', 'value' => $voucher_uploaders, 'options_values' => $potential_uploaders));
$uploaders .= "<span class='elgg-subtext'>" . elgg_echo('vouchers:settings:uploaders:note') . "</span>";
echo elgg_view_module("inline", elgg_echo('vouchers:settings:uploaders'), $uploaders);   

// set number of active vouchers per user
$active_no_active_vouchers = $plugin->no_active_vouchers;
if(empty($defaultdateformat) || !is_numeric($active_no_active_vouchers)){
        $active_no_active_vouchers = VOUCHERS_NO_ACTIVE_VOUCHERS;
}
$no_active_vouchers = elgg_view('input/text', array('name' => 'params[no_active_vouchers]', 'value' => $active_no_active_vouchers, 'style' => "width:50px; margin-right:10px;"));
$no_active_vouchers .= "<span class='elgg-subtext'>" . elgg_echo('vouchers:settings:no_active_vouchers:note') . "</span>";
echo elgg_view_module("inline", elgg_echo('vouchers:settings:no_active_vouchers'), $no_active_vouchers); 

// set if members can send private message to seller
$send_message = $plugin->send_message;
if(empty($send_message)){
        $send_message = 'yes';
}    
$potential_send_message = array(
    "no" => elgg_echo('vouchers:settings:no'),
    "yes" => elgg_echo('vouchers:settings:yes'),
); 

$send_message_output = elgg_view('input/dropdown', array('name' => 'params[send_message]', 'value' => $send_message, 'options_values' => $potential_send_message));
$send_message_output .= "<span class='elgg-subtext'>" . elgg_echo('vouchers:settings:send_message:note') . "</span>";
echo elgg_view_module("inline", elgg_echo('vouchers:settings:send_message'), $send_message_output);  
/////////////////////////////////////////////////////////////////////////////////////////

// only in pro version

if(elgg_is_active_plugin("elggx_userpoints")){
// set if use Elggx Userpoints as payment gateway
$use_userpoints = $plugin->use_userpoints;
if(empty($use_userpoints))	{
	$use_userpoints = 'no';
}    
$use_userpoints_option = elgg_view('input/dropdown', array('name' => 'params[use_userpoints]', 'value' => $use_userpoints, 'options_values' => $potential_yesno));
$use_userpoints_option .= "<span class='elgg-subtext'>" . elgg_echo('vouchers:settings:use_userpoints:note') . "</span>";
echo elgg_view_module("inline", elgg_echo('vouchers:settings:use_userpoints'), $use_userpoints_option); 
}

