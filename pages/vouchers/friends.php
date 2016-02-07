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

elgg_push_breadcrumb($page_owner->name, "vouchers/owner/$page_owner->username");
elgg_push_breadcrumb(elgg_echo('friends'));

// check if user can post vouchers
if (check_if_user_can_post())   {
    elgg_register_title_button();
}

$title = elgg_echo('vouchers:friends');

// get current elgg version
$release = get_version(true);
if ($release < 1.9)  // version 1.8
	$content = list_user_friends_objects($page_owner->guid, 'vouchers', 10, false);
else { // use this since Elgg 1.9
	$content = elgg_list_entities_from_relationship(array(
		'type' => 'object',
		'subtype' => 'vouchers',
		'full_view' => false,
		'limit' => 10,
		'relationship' => 'friend',
		'relationship_guid' => $page_owner->guid,
		'relationship_join_on' => 'container_guid',
	));
}


if (!$content) {
	$content = elgg_echo('vouchers:none');
}

$params = array(
	'filter_context' => 'friends',
	'content' => $content,
	'title' => $title,
	'filter_override' => elgg_view('vouchers/nav', array('selected' => $vars['page'])),
);

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);
