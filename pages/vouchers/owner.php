<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

elgg_load_library('elgg:vouchers');

$page_owner = elgg_get_page_owner_entity();
if (!$page_owner) {
	forward('vouchers/all');
}

elgg_push_breadcrumb($page_owner->name);

// check if user can post vouchers
if (check_if_user_can_post())   {
    elgg_register_title_button();
}

$content .= elgg_list_entities(array(
	'type' => 'object',
	'subtype' => 'vouchers',
	'container_guid' => $page_owner->guid,
	'limit' => 10,
	'full_view' => false,
	'view_toggle_type' => false
));

if (!$content) {
	$content = elgg_echo('vouchers:none');
}

$title = elgg_echo('vouchers:owner', array($page_owner->name));

$filter_context = '';
if ($page_owner->getGUID() == elgg_get_logged_in_user_guid()) {
	$filter_context = 'mine';
}

$vars = array(
	'filter_context' => $filter_context,
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('vouchers/sidebar'),
	//'filter_override' => elgg_view('vouchers/nav', array('selected' => $vars['page'])),
	'filter_override' => elgg_view('vouchers/nav', array('selected' => $vars['page'], 'page_owner_guid' => $page_owner->getGUID())),
);

// don't show filter if out of filter context
if ($page_owner instanceof ElggGroup) {
	$vars['filter'] = false;
}

$body = elgg_view_layout('content', $vars);

echo elgg_view_page($title, $body);
