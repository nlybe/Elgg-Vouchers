<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

elgg_load_library('elgg:vouchers');

// set the default timezone to use
date_default_timezone_set(voucher_get_default_timezone());

$subject = strip_tags(get_input('subject'));
$body = get_input('body');
$recipient_guid = get_input('recipient_guid');
$entity_guid = get_input('entity_guid');
$entity = get_entity($entity_guid);

elgg_make_sticky_form('messages');

if (elgg_instanceof($entity, 'object', 'vouchers')) {

	if (!$recipient_guid) {
		register_error(elgg_echo("messages:user:blank"));
		forward("messages/compose");
	}

	$user = get_user($recipient_guid);
	if (!$user) {
		register_error(elgg_echo("messages:user:nonexist"));
		forward("messages/compose");
	}

	// Make sure the message field, send to field and title are not blank
	if (!$body || !$subject) {
		register_error(elgg_echo("messages:blank"));
		forward(REFERER);
	}

	// Otherwise, 'send' the message 
	$body = elgg_echo("vouchers:be_interested:adtitle", array($entity->getURL(),$entity->title)).'<br /><br />'.$body;
	$result = messages_send($subject, $body, $recipient_guid, 0, $reply);

	// Save 'send' the message
	if (!$result) {
		register_error(elgg_echo("messages:error"));
		forward(REFERER);
	}
	else {
		system_message(elgg_echo("vouchers:be_interested:success"));
		system_message(elgg_echo("vouchers:be_interested:success_message"));		
		elgg_clear_sticky_form('messages');
	}
}
else {
	register_error(elgg_echo("vouchers:be_interested:failed"));
}

forward(REFERER);


