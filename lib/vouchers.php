<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

//add voucher form parameters
function vouchers_prepare_form_vars($voucher = null) {

	// input names => defaults
        $values = array(
            'title' => '',
            'code' => '',
            'code_type' => '',
            'code_end' => '',
            'description' => '',
            'amount' => '',
            'amount_type' => '',
            'access_id' => ACCESS_DEFAULT,
            'tags' => '',
            'container_guid' => elgg_get_page_owner_guid(),
            'entity' => $voucher,
            'valid_from' => '',
            'valid_until' => '',
            'price' => 0,
            'points' => 0,
            'howmany' => '',
            'currency' => '',
            'weburl' => '',
            'vimage' => '',
            'qrcode' => '',
            'zone' => '',
            'terms' => '',
            'excerpt' => '',
            'guid' => null,
            'comments_on' => NULL,
        ); 
        
    	if ($voucher) {
            foreach (array_keys($values) as $field) {
                if (isset($voucher->$field)) {
                        $values[$field] = $voucher->$field;
                }
            }
	}

	if (elgg_is_sticky_form('vouchers')) {
            $sticky_values = elgg_get_sticky_values('vouchers');
            foreach ($sticky_values as $key => $value) {
                $values[$key] = $value;
            }
	}

	elgg_clear_sticky_form('vouchers');

	return $values;
}

function vouchers_get_form_pulldown_hours($name = '', $value = '', $h = 23) {
        $time_hours_options = range(0, $h);

        array_walk($time_hours_options, 'vouchers_manager_time_pad');

        return elgg_view('input/dropdown', array('name' => $name, 'value' => $value, 'options' => $time_hours_options));
}

function vouchers_get_form_pulldown_minutes($name = '', $value = '') {
        $time_minutes_options = range(0, 59, 5);

        array_walk($time_minutes_options, 'event_manager_time_pad');

        return elgg_view('input/dropdown', array('name' => $name, 'value' => $value, 'options' => $time_minutes_options));
}

function vouchers_manager_time_pad(&$value) {
    $value = str_pad($value, 2, "0", STR_PAD_LEFT);;
}

function get_currency_list() {
    // Currencies list according paypal api
    $CurrOptions = array(
        'AUD'=>'Australian Dollar',
        'BRL'=>'Brazilian Real',
        'CAD'=>'Canadian Dollar',
        'CZK'=>'Czech Koruna',
        'DKK'=>'Danish Krone',
        'EUR'=>'Euro',
        'HKD'=>'Hong Kong Dollar',
        'HUF'=>'Hungarian Forint',
        'ILS'=>'Israeli New Sheqel',
        'JPY'=>'Japanese Yen',
        'MYR'=>'Malaysian Ringgit',
        'MXN'=>'Mexican Peso',
        'NOK'=>'Norwegian Krone',
        'NZD'=>'New Zealand Dollar',
        'PHP'=>'Philippine Peso',
        'PLN'=>'Polish Zloty',
        'GBP'=>'Pound Sterling',
        'SGD'=>'Singapore Dollar',
        'SEK'=>'Swedish Krona',
        'CHF'=>'Swiss Franc',
        'TWD'=>'Taiwan New Dollar',
        'THB'=>'Thai Baht',
        'TRY'=>'Turkish Lira',
        'USD'=>'U.S. Dollar',
    );
    
    return $CurrOptions;
}

function get_currency_sign($currency_code) {
    switch ($currency_code) {
        case "AUD":
            return "$";
            break;  
        case "BRL":
            return "R$";
            break;  
        case "CAD":
            return "$";
            break;  
        case "CZK":
            return "Kč";
            break;  
        case "DKK":
            return "kr";
            break;  
        case "EUR":
            return "€";
            break;  
        case "HKD":
            return "$";
            break;  
        case "HUF":
            return "Ft";
            break;  
        case "ILS":
            return "₪";
            break; 
        case "JPY":
            return "¥";
            break; 
        case "MYR":
            return "RM";
            break; 
        case "MXN":
            return "$";
            break; 
        case "NOK":
            return "kr";
            break;         
        case "NZD":
            return "$";
            break;   
        case "PHP":
            return "₱";
            break;   
        case "PLN":
            return "zł";
            break;   
        case "GBP":
            return "£";
            break;   
        case "SGD":
            return "$";
            break;   
        case "SEK":
            return "kr";
            break;   
        case "CHF":
            return "CHF";
            break;   
        case "TWD":
            return "NT$";
            break;   
        case "THB":
            return "฿";
            break;   
        case "TRY":
            return "TRY";
            break;   
        case "USD":
            return "$";
            break;           
        default:
            return "$";
    }   
}

// check if user can post vouchers
function check_if_user_can_post($user = null) {
    $whocanpost = trim(elgg_get_plugin_setting('voucher_uploaders', 'vouchers'));
	
    if (elgg_is_logged_in())    {
        if ($whocanpost === 'allmembers')   {
            return true;
        }
        else if ($whocanpost === 'admins')   {
            if (!$user) $user = elgg_get_logged_in_user_entity();
            if ($user->isAdmin()) {
                return true;
            } 
        }
    }
    
    return false;
}

// check if user reached maximum number of allowed active vouchers
function check_if_user_reached_max_no_of_vouchers($user = null) {
	if (!$user) $user = elgg_get_logged_in_user_entity();
	$max_no_active_vouchers = trim(elgg_get_plugin_setting('no_active_vouchers', 'vouchers'));
	
	if (($max_no_active_vouchers == 0) || (get_no_of_active_vouchers($user) < $max_no_active_vouchers))	{
		return true;
	}
	else {
		register_error(elgg_echo('vouchers:addvoucher:reachedmaxno0fvouchers', array($max_no_active_vouchers)));  
		return false;		
	}		
	
    return false;
}

// get number of active vouchers for a given or current user
function get_no_of_active_vouchers($user = null) {
	if (!$user) $user = elgg_get_logged_in_user_entity();
	
	$active = elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'vouchers',
		'container_guid' => $user->guid,
		'limit' => 0,
		'full_view' => false,
		'metadata_name_value_pairs' => array(
			//array('name' => 'howmany','value' => 0,'operand' => '>'), 
			array('name' => 'valid_until','value' => time(),'operand' => '>'), 
		),		
		//'metadata_name_value_pairs_operator' => 'OR',
	));	
	
	$i = 0;
	foreach ($active as $act)	{
		$voucher_howmany = get_voucher_howmany($act);
		if (trim($voucher_howmany)=='' || $voucher_howmany > 0) $i++;	// empty howmany means that number is unlimited, so it's active
	}
	
    return $i;
}

// check if members can send private message to seller
function vouchers_check_if_members_can_send_private_message() {
    $send_message = trim(elgg_get_plugin_setting('send_message', 'vouchers'));
    
    if ($send_message === 'yes')   {
		return true;
	}
	
    return false;
}

// check if Paypal gateway is enabled
function check_if_use_paypal_is_enabled() {
    $use_paypal = trim(elgg_get_plugin_setting('use_paypal', 'vouchers'));
    
    if ($use_paypal === 'yes')   {
		return true;
	}
	
    return false;
}

// check if Elggx Userpoints gateway is enabled
function check_if_use_userpoints_is_enabled() {
	if (elgg_is_active_plugin("elggx_userpoints")) {
		$use_userpoints = trim(elgg_get_plugin_setting('use_userpoints', 'vouchers'));
		
		if ($use_userpoints === 'yes')   {
			return true;
		}
	}
	
    return false;
}

// returns print button
function getPrintButton($vouchers) {
	$printbuttton = '<img src="'.elgg_get_site_url() . 'mod/vouchers/graphics/voucher_print.png" alt="'.elgg_echo('vouchers:print').'" class="printbutton" onclick="window.print()" />';
    return $printbuttton;
}

// check if use has enough points to buy this voucher
function hasUserPointsRequired($voucher_points) {
	if (elgg_is_active_plugin("elggx_userpoints")) {
		$userpoints = userpoints_get(elgg_get_logged_in_user_guid());
	
		if($userpoints[approved]>=$voucher_points)
			return true;
	}
	
    return false;
}

// this function returns a voucher code
function get_voucher_code($voucher) {
	if (elgg_instanceof($voucher, 'object', 'vouchers'))	{
		if ($voucher->code_type == VOUCHERS_CODE_TYPE_SINGLE) {
			return $voucher->code;
		}
		else if ($voucher->code_type == VOUCHERS_CODE_TYPE_QR) {
			return VOUCHERS_CODE_TYPE_QR;
		}		
		else if ($voucher->code_type == VOUCHERS_CODE_TYPE_SERIES) {
			if (!is_numeric($voucher->code) || !is_numeric($voucher->code_end)) {
				return false;
			}
			else if ($voucher->code >= $voucher->code_end) {
				return false;
			}	
			else  {
				// set ignore access for loading all sales entries
				$ia = elgg_get_ignore_access();
				elgg_set_ignore_access(true);
				
				// search the codes which have already be given
				$options = array(
					'type' => 'object',
					'subtype' => 'vsales',
					'limit' => 0,
					'metadata_name_value_pairs' => array(
						array('name' => 'txn_vguid','value' => $voucher->guid,'operand' => '='), 
					),   
				);
				$sales_list = elgg_get_entities_from_metadata($options); 

				// make an array with codes which have be given
				if (is_array($sales_list))	{
					$codes_sold = array();
					foreach ($sales_list as $cs) {
						array_push($codes_sold, $cs->txn_code);
					}
				}
				
				// restore ignore access
				elgg_set_ignore_access($ia);
				
				$codes_list = array();
				for ($x=$voucher->code; $x<=$voucher->code_end; $x++) {
					$key = in_array($x, $codes_sold);
					
					if (!$key)
						return $x;  // return the 1st available code
				} 
			}
		}	
	}
	
	return false;
}

// this function returns how many vouchers codes left for a specific voucher
function get_voucher_howmany($voucher) {
	if (elgg_instanceof($voucher, 'object', 'vouchers'))	{

		if ($voucher->code_type != VOUCHERS_CODE_TYPE_SERIES) {
			return $voucher->howmany;
		}
		else if (!is_numeric($voucher->code) || !is_numeric($voucher->code_end)) {
			return 0;
		}
		else if ($voucher->code >= $voucher->code_end) {
			return 0;
		}	
		else  {
			// set ignore access for loading all sales entries
			$ia = elgg_get_ignore_access();
			elgg_set_ignore_access(true);
			
			// search the codes which have already be given
            $options = array(
                'type' => 'object',
                'subtype' => 'vsales',
                'limit' => 0,
                'count' => true,
                'metadata_name_value_pairs' => array(
                    array('name' => 'txn_vguid','value' => $voucher->guid,'operand' => '='), 
                ),   
            );
            $sales_no = elgg_get_entities_from_metadata($options); 

			// restore ignore access
            elgg_set_ignore_access($ia);

			// initial no of code vouchers
			$init_no =  $voucher->code_end - $voucher->code +1;
			// final howmany no
			$howmany = $init_no - $sales_no;

			return $howmany;
		}	
	}
	
	return 0;
}

// this function returns the voucher code of a specific transaction
function get_buyer_code($txn_code, $voucher) {
	if (elgg_instanceof($voucher, 'object', 'vouchers'))	{
		if ($txn_code == VOUCHERS_CODE_TYPE_QR) {
			$code_image = elgg_view('vouchers/thumbnail', array('voucher_guid' => $voucher->guid, 'is_code_qr' => true, 'size' => 'medium', 'tu' => $tu));  
			
			return $code_image;
		}
		else {
			return $txn_code;
		}
	}
	
	return false;
}

// get a list of all timezones
function vouchers_get_all_times_zones() {
	$zones_array = array();
	$timestamp = time();

	foreach(timezone_identifiers_list() as $key => $zone) {
		$zones_array[$zone] = $zone;
	}

	return $zones_array;
}

// get timezone from settings
function voucher_get_default_timezone() {
	$timezone = trim(elgg_get_plugin_setting('default_timezone', 'vouchers'));
	if (empty($timezone))	{
		$timezone = 'UTC';
	}

	return $timezone;
}

// get paypal account, depending on settings
function vouchers_get_paypal_account($vouchers_owner_guid) {
	$paypal_acount = '';
	$whocanpost = trim(elgg_get_plugin_setting('voucher_uploaders', 'vouchers'));
	
	if ($whocanpost === 'allmembers')   {
		$vowner = get_user($vouchers_owner_guid);
		if (elgg_instanceof($vowner, 'user')) {   
			$paypal_acount = trim($vowner->getPrivateSetting("voucher_paypal_account"));
		}
	}
	else if ($whocanpost === 'admins')   {
		$paypal_acount = trim(elgg_get_plugin_setting('paypal_account', 'vouchers'));
	}

	return $paypal_acount;
}

// check if use sandbox paypal account
function vouchers_use_sandbox_paypal($method) {
	$usesandbox = trim(elgg_get_plugin_setting('usesandbox', 'vouchers'));
	if ($usesandbox === 'yes' && $method == VOUCHERS_PAYPAL_METHOD_SIMPLE)   {
		return 'data-env="sandbox"';
	}
	if ($usesandbox === 'yes' && $method == VOUCHERS_PAYPAL_METHOD_ADAPTIVE)   {
		return true;
	}	
	
	return '';
}

/*
 * Check if adaptive payments is enabled. 
 * 
 * Returns true if all options below are true:
 * 1. amap_paypal_api is enabled
 * 2. adaptive payment option is enabled on agora settings
 * 3. all fields on amap_paypal_api settings are not empty
 * 4. the commission is numeric and between 0 and 100
 * 
 */ 
function vouchers_check_if_paypal_adaptive_payments_is_enabled() {
	if (elgg_is_active_plugin("amap_paypal_api"))	{
		$vouchers_adaptive_payments = trim(elgg_get_plugin_setting('vouchers_adaptive_payments', 'vouchers'));
		
		if(empty($vouchers_adaptive_payments) || $vouchers_adaptive_payments == AGORA_GENERAL_NO) {
			return false;
		}  
		else {
			$API_caller_username = trim(elgg_get_plugin_setting('paypal_API_caller_username', 'amap_paypal_api'));
			$API_caller_passwd = trim(elgg_get_plugin_setting('paypal_API_caller_passwd', 'amap_paypal_api'));
			$API_caller_signature = trim(elgg_get_plugin_setting('paypal_API_signature', 'amap_paypal_api'));
			$API_app_id = trim(elgg_get_plugin_setting('paypal_API_app_id', 'amap_paypal_api'));
			$commission = trim(elgg_get_plugin_setting('vouchers_adaptive_payments_commission', 'vouchers'));
			
			if (!empty($API_caller_username) && !empty($API_caller_passwd) && !empty($API_caller_signature) && !empty($API_app_id) 
				&& (is_numeric($commission) && ($commission > 0)  && ($commission < 100) ))	{
				return true;
			}
		}
		
	}
	
	return false;
}	

/*
 * Get the owner commission amount for adaptive payments for a given price
 * 
 * Returns the commission
 */
function vouchers_get_adaptive_payment_owner_commission($voucher_price) {
	$site_owner_commission = trim(elgg_get_plugin_setting('vouchers_adaptive_payments_commission', 'vouchers')); 
	
	$commission = 0;
	if (is_numeric($site_owner_commission))
		$commission = $voucher_price * $site_owner_commission / 100;
		
	return $commission;
}

// Notify buyer for transaction he just made
function vouchers_notify_buyer_for_transaction($buyer_profil_guid, $entity_unit) {
	$subject = elgg_echo('vouchers:paypal:buyersubject', array($entity_unit->title));
	$message = '';
	$message .= '<p>'.elgg_echo('vouchers:paypal:buyerbody').'</p>';
	if ($entity_unit->points && elgg_is_active_plugin("elggx_userpoints")) {
		$message .= '<p>'.elgg_echo('vouchers:get_with_points:pointsused', array($entity_unit->points)).'</p>';
	} 	
	$message .= '<p>'.elgg_echo('vouchers:paypal:title').': <a href="'.elgg_get_site_url().'vouchers/view/'.$entity_unit->guid.'">'.$entity_unit->title.'</a></p>';
	notify_user($buyer_profil_guid, $entity_unit->owner_guid, $subject, $message); 
	
	return true;
}


// Notify users defined in settings for each transaction
function vouchers_notify_users_for_transaction($entity_owner_guid, $entity_unit) {
	$users_to_notify = elgg_get_plugin_setting('users_to_notify','vouchers');
	$fields = explode(",", $users_to_notify);
	
	$subject = elgg_echo('vouchers:paypal:buyersubject', array($entity_unit->title));
	$message = '';
	$message .= '<p>'.elgg_echo('vouchers:paypal:buyerbody').'</p>';
	$message .= '<p>'.elgg_echo('vouchers:paypal:title').': <a href="'.elgg_get_site_url().'vouchers/view/'.$entity_unit->guid.'">'.$entity_unit->title.'</a></p>';	
	
	foreach ($fields as $val){
		$user_to_notify = get_user_by_username(trim($val));
		
		if($user_to_notify){
			$res = notify_user($user_to_notify->guid, $entity_owner_guid, $subject, $message);  
		}
	}
	
	return true;
}
