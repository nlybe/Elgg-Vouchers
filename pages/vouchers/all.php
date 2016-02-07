<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

elgg_load_library('elgg:vouchers');

elgg_pop_breadcrumb();
elgg_push_breadcrumb(elgg_echo('vouchers'));

// check if user can post vouchers
if (check_if_user_can_post())   {
    elgg_register_title_button();
}

$content = elgg_list_entities(array(
	'type' => 'object',
	'subtype' => 'vouchers',
	'limit' => 10,
	'full_view' => false,
	'view_toggle_type' => false 
));


if (!$content) {
	$content = elgg_echo('vouchers:none');
} 
 
$title = elgg_echo('vouchers');

$body = elgg_view_layout('content', array(
	'filter_context' => 'all',
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('vouchers/sidebar'),
	'filter_override' => elgg_view('vouchers/nav', array('selected' => $vars['page'])),
));

echo elgg_view_page($title, $body);










