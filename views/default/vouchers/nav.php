<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

if (elgg_is_logged_in()) {
	$user = elgg_get_logged_in_user_entity();
	
	$filter_context = '';
	if ($vars['page_owner_guid'] == elgg_get_logged_in_user_guid()) {
		$selected = 'owner';
	}	
		
    $tabs = array(
            'newest' => array(
                    'title' => elgg_echo('all'),
                    'url' => 'vouchers/all',
                    'selected' => $vars['selected'] == 'all',
            ),
            'owner' => array(
                    'title' => elgg_echo('mine'),
                    'url' => 'vouchers/owner/'.$user->username,
                    'selected' => $selected,
            ),
            'friends' => array(
                    'title' => elgg_echo('friends'),
                    'url' => 'vouchers/friends/'.$user->username,
                    'selected' => $vars['selected'] == 'friends',
            ),    
          
    );

    echo elgg_view('navigation/tabs', array('tabs' => $tabs));
}
