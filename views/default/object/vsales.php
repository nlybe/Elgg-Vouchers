<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

elgg_load_library('elgg:vouchers');

global $CONFIG;

//$full = elgg_extract('full_view', $vars, FALSE);
$vsale = elgg_extract('entity', $vars, FALSE);

if (!$vsale) { 
    return;
}

$voucher = get_entity($vsale->txn_vguid);
$buyer = get_user($vsale->txn_buyer_guid);
$seller = get_entity($voucher->container_guid);

// don't show filter if out of filter context
if (elgg_instanceof($seller, 'group')) {
	$seller = get_user($voucher->owner_guid);
}
           
if (elgg_instanceof($voucher, 'object', 'vouchers'))	{
	$voucher_img = elgg_view('output/url', array(
		'href' => "vouchers/view/{$voucher->guid}/" . elgg_get_friendly_title($voucher->title),
		'text' => elgg_view('vouchers/thumbnail', array('voucher_guid' => $voucher->guid, 'size' => 'small', 'tu' => $tu)),
	));    
	$voucher_title = elgg_view('output/url', array(
		'href' => "vouchers/view/{$voucher->guid}/" . elgg_get_friendly_title($voucher->title),
		'text' => $voucher->title,
	));    
}
else {
	$voucher_img = '';
	$voucher_title = elgg_echo('vouchers:voucher:isdeleted');
}  

$content = '<div>';
//$buyer = get_user($voucher->owner_guid);
$content .= '<h3>'.$voucher_title.'</h3>';
$content .= elgg_view_friendly_time($vsale->time_created).':';
$content .= '&nbsp;&nbsp;<strong>'.elgg_echo('vouchers:settings:transactions:seller').'</strong>: <a href="'.elgg_get_site_url().'profile/'.$seller->username.'">'.$seller->username.'</a>';
$content .= '&nbsp;&nbsp;<strong>'.elgg_echo('vouchers:settings:transactions:buyer').'</strong>: <a href="'.elgg_get_site_url().'profile/'.$buyer->username.'">'.$buyer->username.'</a>';
$content .= '<br/><span  style="font-weight:bold;">'.elgg_echo('vouchers:transactionid').'</span>: '.$vsale->txn_id;
$content .= ', <span  style="font-weight:bold;">'.elgg_echo('vouchers:addvoucher:code').': </span> '.get_buyer_code($vsale->txn_code, $voucher);
if ($vsale->txn_method)
	$content .= '&nbsp;&nbsp;<strong>'.elgg_echo('vouchers:settings:transactions:method').'</strong>: '.$vsale->txn_method;
$content .= '</div>';

$params = array(
		'entity' => $vsale,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'content' => $content,
);
$params = $params + $vars;
$body = elgg_view('object/elements/summary', $params);

echo elgg_view_image_block($voucher_img, $body);


