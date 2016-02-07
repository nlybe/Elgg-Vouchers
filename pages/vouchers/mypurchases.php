<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

elgg_load_library('elgg:vouchers');

$logged_in_user = elgg_get_logged_in_user_entity();
if (!$logged_in_user) {
	forward('vouchers/all');
}

elgg_push_breadcrumb(elgg_echo('vouchers:mypurchases'));

// check if user can post vouchers
if (check_if_user_can_post())   {
    elgg_register_title_button();
}

// check if this user has bought this voucher
$options = array(
        'type' => 'object',
        'subtype' => 'vsales',
        'limit' => 10,
        'metadata_name_value_pairs' => array(
            array('name' => 'txn_buyer_guid', 'value' => $logged_in_user->guid, 'operand' => '='),
        ),
        'metadata_name_value_pairs_operator' => 'AND',
);

$content = elgg_list_entities_from_metadata($options);

if (!$content) {
	$content = elgg_echo('vouchers:none');
}

$title = elgg_echo('vouchers:mypurchases');

$filter_context = 'mypurchases';

$vars = array(
	'filter_context' => $filter_context,
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('vouchers/sidebar'),
	'filter_override' => elgg_view('vouchers/nav', array('selected' => $vars['page'])),
);

$body = elgg_view_layout('content', $vars);

echo elgg_view_page($title, $body);
