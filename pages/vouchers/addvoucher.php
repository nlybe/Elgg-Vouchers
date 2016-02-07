<?php
/**
 * Elgg vouchers plugin
 *
 * @package Vouchers
 */

elgg_load_library('elgg:vouchers');

$user = elgg_get_logged_in_user_entity();
if (!check_if_user_reached_max_no_of_vouchers($user)) { 
	forward(REFERER); 
}

// check if user can post vouchers
if (check_if_user_can_post()) { 

    $title = elgg_echo('vouchers:addvoucher');
    elgg_push_breadcrumb($title);

    // build sidebar 
    $sidebar = '';

    // create form
    $form_vars = array('name' => 'voucherpost', 'enctype' => 'multipart/form-data');
    $vars = vouchers_prepare_form_vars();
    $content = elgg_view_form('vouchers/addvoucher', $form_vars, $vars);

    $body = elgg_view_layout('content', array(
        'content' => $content,
        'title' => $title,
        'sidebar' => $sidebar,
        'filter' => '',
    ));

    echo elgg_view_page($title, $body);
} 
else    {  
    register_error(elgg_echo('vouchers:addvoucher:noaccessforpost'));  
    forward(REFERER);    
}



