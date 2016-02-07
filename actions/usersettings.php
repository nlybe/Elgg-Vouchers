<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */ 

$user_guid = (int) get_input("user_guid", elgg_get_logged_in_user_guid());
$voucher_paypal_account = get_input("voucher_paypal_account");

if(!empty($user_guid)){
	if(($user = get_user($user_guid)) && $user->canEdit()){
		$error_count = 0;
		if(!empty($voucher_paypal_account)){
			if(!($user->setPrivateSetting("voucher_paypal_account", $voucher_paypal_account))){
				$error_count++;
			}				
		} 
		else {
			$user->removePrivateSetting("voucher_paypal_account");
		}	
		
		if($error_count == 0){
			system_message(elgg_echo("vouchers:usersettings:update:success"));
		} else {
			register_error(elgg_echo("vouchers:usersettings:update:error"));
		}
	} else {
		register_error(elgg_echo("InvalidClassException:NotValidElggStar", array($user_guid, "ElggUser")));
	}
} else {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
}	

forward(REFERER);
	
