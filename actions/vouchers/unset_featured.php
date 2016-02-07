<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

elgg_load_library('elgg:vouchers');

$voucher_guid = get_input('voucher_guid');
if (!$voucher_guid) {	// if not voucher guid
	$errmsg = elgg_echo('vouchers:set_featured:voucher_guid_missing');
}

$voucher = get_entity($voucher_guid);
if (!elgg_instanceof($voucher, 'object', 'vouchers')) {	// if not voucher entity
	$errmsg = elgg_echo('vouchers:set_featured:voucher_entity_missing');
}

if ($errmsg)	{
	register_error($errmsg);
}
//else if (check_if_group_has_apply($group->guid, $league->guid)) {
//	register_error(elgg_echo('leaguemanager:league:participate:alreadyapplied',array($group->name)));
//}
else
{
	$voucher->featured = false;
	
	if ($voucher->save()) {
		system_message(elgg_echo("vouchers:unset_featured:success"));
	}
	else {
		register_error(elgg_echo("vouchers:unset_featured:failed"));
	}	
}

forward(REFERER);
