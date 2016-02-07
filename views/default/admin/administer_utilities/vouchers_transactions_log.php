<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

elgg_load_library('elgg:vouchers');

// load list of bought items
$options = array(
	'type' => 'object',
	'subtype' => 'vsales',
	'limit' => 10,
);

$content = elgg_list_entities($options);

$body = elgg_view_layout('one_column', array(
	'filter_context' => 'all',
	'content' => $content,
	'title' => '',
	'sidebar' => '',
	'filter_override' => '',
));

echo $body;
