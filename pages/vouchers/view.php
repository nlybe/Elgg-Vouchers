<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

elgg_load_library('elgg:vouchers');

// Load fancybox
elgg_load_js('lightbox');
elgg_load_css('lightbox');

//get entity
$voucher = get_entity(get_input('guid'));
if (!$voucher) {
	register_error(elgg_echo('noaccess'));
	$_SESSION['last_forward_from'] = current_page_url();
	forward('');
}

$page_owner = elgg_get_page_owner_entity();
$crumbs_title = $page_owner->name;
if (elgg_instanceof($page_owner, 'group')) {
	elgg_push_breadcrumb($crumbs_title, "vouchers/group/$page_owner->guid/all");
} else {
	elgg_push_breadcrumb($crumbs_title, "vouchers/owner/$page_owner->username");
}

$title = $voucher->title; 
elgg_push_breadcrumb($title);

$content = elgg_view_entity($voucher, array('full_view' => true));
if ($voucher->comments_on != 'Off') {
	$content .= elgg_view_comments($voucher);
}     

$sidebar = '';

// show voucher sales on sidebar if any only for voucher owner
if (elgg_is_logged_in()) {
	$user = elgg_get_logged_in_user_entity();
	if ($user && $user->guid==$page_owner->guid) {
		// set ignore access for loading all sales entries
		$ia = elgg_get_ignore_access();
		elgg_set_ignore_access(true);	
			
		// load list buyers
		$options = array(
			'type' => 'object',
			'subtype' => 'vsales',
			'limit' => 0,
			'metadata_name_value_pairs' => array(
				array('name' => 'txn_vguid','value' => $voucher->guid,'operand' => '='), 
			),   
		);

		$buyerslist = elgg_get_entities_from_metadata($options); 
		$sidebar .= '<div style="font-size:90%;">';
		$sidebar .= '<h3>'.elgg_echo('vouchers:sales').'</h3>';

		if (is_array($buyerslist))	{
			foreach ($buyerslist as $b) {
				//$sidebar .= $b->voucher_guid.' - '.$b->user_guid.' - '.$b->txn_date.'<br/>';
				$buyer = get_user($b->txn_buyer_guid);
				$sidebar .= '<p><a href="'.elgg_get_site_url().'profile/'.$buyer->username.'">'.$buyer->username.'</a> - '.elgg_view_friendly_time($b->time_created);
				$sidebar .= '<br/>'.elgg_echo('vouchers:transactionid').': '.$b->txn_id;
				//$sidebar .= '<br/>'.elgg_echo('vouchers:addvoucher:code').': '.get_buyer_code($b->txn_code, $voucher).'</p>';
			}
		}
		$sidebar .= '</div>';      
		
		// restore ignore access
		elgg_set_ignore_access($ia);		
		
	}
}

$body = elgg_view_layout('content', array(
	'content' => $content,
	'title' => $title,
	'filter' => '',
	'sidebar' => $sidebar,
));
echo elgg_view_page($title, $body);



