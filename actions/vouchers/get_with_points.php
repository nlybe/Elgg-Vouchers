<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

elgg_load_library('elgg:vouchers');

// set the default timezone to use
date_default_timezone_set(voucher_get_default_timezone());

$voucher_guid = get_input('voucher_guid');
$buyer_guid = get_input('buyer_guid');

if (!$voucher_guid) {	// if not voucher guid
	$errmsg = elgg_echo('vouchers:get_with_points:voucher_guid_missing');
}

$voucher = get_entity($voucher_guid);
if (!elgg_instanceof($voucher, 'object', 'vouchers')) {	// if not voucher entity
	$errmsg = elgg_echo('vouchers:get_with_points:voucher_entity_missing');
}

$buyer_profil = get_user($buyer_guid);
if (!elgg_instanceof($buyer_profil, 'user')) {	// if not user entity
	$errmsg = elgg_echo('vouchers:get_with_points:user_entity_missing');
}

if ($errmsg)	{
	register_error($errmsg);
}
else
{
	$subtract = false;
	// subtract buyer elggx userpoints if required
	if ($voucher->points && elgg_is_active_plugin("elggx_userpoints")) {
		$subtract = userpoints_subtract($buyer_guid, $voucher->points, elgg_echo('vouchers:get_with_points:description', array($voucher->guid)), 'vouchers', $voucher->guid);
	}	
	
	if ($subtract) {
		$vouchersale = new ElggObject;
		$vouchersale->subtype = "vsales";
		$vouchersale->access_id = 0;
		$vouchersale->save();

		// set object metadata
		$transaction_date = date('Y-m-d H:i:s');
		$vouchersale->container_guid = $voucher->container_guid;
		$vouchersale->owner_guid = $buyer_guid;
		$vouchersale->txn_vguid = $voucher->guid;
		$vouchersale->txn_buyer_guid = $buyer_guid;
		$vouchersale->txn_date = $transaction_date;
		$vouchersale->txn_id = 'VElggx-'.$voucher->guid.'-'.$buyer_guid;
		$vouchersale->txn_code = get_voucher_code($voucher);
		$vouchersale->txn_method = VOUCHERS_PURCHASE_METHOD_POINTS;

		if ($vouchersale->save()) {
			// reduce available inits. It make sense only when there just one code for this voucher
			$available_units = $voucher->howmany;
			if ($available_units && is_numeric($available_units)) {
				$voucher->howmany = $available_units - 1;
				$voucher->save();
			}	
			
			// notify seller
			$subject = elgg_echo('vouchers:get_with_points:sellersubject').' '.$buyer_profil->username;
			$message = '';
			$message .= '<p>'.elgg_echo('vouchers:paypal:paymentdate').': '.$transaction_date.'</p>';
			$message .= '<p>'.elgg_echo('vouchers:addvoucher:title').': <a href="'.elgg_get_site_url().'vouchers/view/'.$voucher->guid.'">'.$voucher->title.'</a></p>';
			$message .= '<p>'.elgg_echo('vouchers:buyerprofil').': <a href="'.elgg_get_site_url().'profile/'.$buyer_profil->username.'">'.$buyer_profil->username.'</a></p>';           
			notify_user($voucher->owner_guid, $buyer_guid, $subject, $message);
			
		   // notify buyer
			$subject = elgg_echo('vouchers:paypal:buyersubject').' '.$voucher->title;
			$message = '';
			$message .= '<p>'.elgg_echo('vouchers:paypal:paymentdate').': '.$transaction_date.'</p>';
			$message .= '<p>'.elgg_echo('vouchers:addvoucher:code').': '.$voucher->code.'</p>';  
			$message .= '<p>'.elgg_echo('vouchers:get_with_points:pointsused', array($voucher->points)).'</p>';
			$message .= '<p>'.elgg_echo('vouchers:paypal:buyerbody').'</p>';
			$message .= '<p>'.elgg_echo('vouchers:paypal:title').': <a href="'.elgg_get_site_url().'vouchers/view/'.$voucher->guid.'">'.$voucher->title.'</a></p>';
			notify_user($buyer_guid, $voucher->owner_guid, $subject, $message);     
			
			// add purchase to river
			$release = get_version(true);
			if ($release < 1.9)  // version 1.8
				add_to_river('river/object/vouchers/purchase','purchase', $buyer_guid, $voucher->guid);
			else { // use this since Elgg 1.9
				elgg_create_river_item(array(
					'view' => 'river/object/vouchers/purchase',
					'action_type' => 'purchase',
					'subject_guid' => $buyer_guid,
					'object_guid' => $voucher->guid,
				));
			}			
			
			forward($voucher->getURL());
			
		} else {
			$errmsg = elgg_echo('vouchers:ipn:error5');
		}		

		system_message(elgg_echo("vouchers:get_with_points:success"));
	}
	else {
		register_error(elgg_echo("vouchers:get_with_points:failed"));
	}	
}

forward(REFERER);
