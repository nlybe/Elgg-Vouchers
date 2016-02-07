<?php
/**
 * Elgg vouchers plugin
 *
 * @package Vouchers
 */

elgg_load_library('elgg:vouchers');

$voucher_guid = get_input('guid');
$voucher = get_entity($voucher_guid);

if (!elgg_instanceof($voucher, 'object', 'vouchers') || !$voucher->canEdit()) {
	register_error(elgg_echo('vouchers:unknown_voucher'));
	forward(REFERRER);
}

$page_owner = elgg_get_page_owner_entity();

$title = elgg_echo('vouchers:edit');
elgg_push_breadcrumb($title);

$form_vars = array('name' => 'voucherpost', 'enctype' => 'multipart/form-data');
$vars = vouchers_prepare_form_vars($voucher);
$content = elgg_view_form('vouchers/addvoucher', $form_vars, $vars);

$body = elgg_view_layout('content', array(
	'filter' => '',
	'content' => $content,
	'title' => $title,
));

echo elgg_view_page($title, $body);
