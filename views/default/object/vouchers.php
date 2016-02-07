<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

elgg_load_library('elgg:vouchers');

$full = elgg_extract('full_view', $vars, FALSE);
$vouchers = elgg_extract('entity', $vars, FALSE);

// set the default timezone to use
date_default_timezone_set(voucher_get_default_timezone());

if (!$vouchers) { 
    return;
}

$owner = $vouchers->getOwnerEntity();
$owner_icon = elgg_view_entity_icon($owner, 'small');
// $container = $vouchers->getContainerEntity();

// create form for set as featured by admins
$set_featured_button = '';
if (elgg_is_admin_logged_in())	{
	if (!$vouchers->featured)	{
		$form_vars = array('name' => 'set_featured', 'enctype' => 'multipart/form-data');
		$set_featured_form = elgg_view_form('vouchers/set_featured', $form_vars, $vars);		
		$set_featured_button .= '<div style="text-align:center;">'.$set_featured_form.'</div>';
	}
	else	{
		$form_vars = array('name' => 'unset_featured', 'enctype' => 'multipart/form-data');
		$set_featured_form = elgg_view_form('vouchers/unset_featured', $form_vars, $vars);		
		$set_featured_button .= '<div style="text-align:center;">'.$set_featured_form.'</div>';
	}
}

// check if this user has bought this voucher
$options = array(
        'type' => 'object',
        'subtype' => 'vsales',
        'limit' => 1,
        'metadata_name_value_pairs' => array(
            array('name' => 'txn_vguid','value' => $vouchers->guid, 'operand' => '='),
            array('name' => 'txn_buyer_guid', 'value' => elgg_get_logged_in_user_guid(), 'operand' => '='),
        ),
        'metadata_name_value_pairs_operator' => 'AND',
);

$getbuyers = elgg_get_entities_from_metadata($options);
$message_to_buyer = '';
$buyer_code = '';
$isbuyer = false;
if (is_array($getbuyers)) {
	foreach ($getbuyers as $b) {
		$isbuyer = true; 
		//$buyer_code = $b->txn_code;
		$buyer_code = get_buyer_code($b->txn_code, $vouchers);
		$message_to_buyer = elgg_echo('vouchers:messagetobuyer');	
	}
}
            
// retrieve date format from settings
$dformat = trim(elgg_get_plugin_setting('default_dateformat', 'vouchers'));
if (empty($dformat) || $dformat=='')    {
    $dformat = 'F j, Y';
}

if ($vouchers->valid_from)  {
    $date_display_from = date($dformat, $vouchers->valid_from);
}
else {
    $date_display_from = false;
}

// set sold out icon
$status = '';
$vouchers_howmany = get_voucher_howmany($vouchers);
if (is_numeric($vouchers_howmany) && $vouchers_howmany == 0) {
	$status = '<img src="'.elgg_get_site_url() . 'mod/vouchers/graphics/soldout.png" width="100" height="76" alt="" class="soldout" />';
}

$date_display_until = date($dformat, $vouchers->valid_until);

if ($vouchers->valid_until > time()) {
    $vcode = '';
    $print = false;
    if (elgg_is_logged_in())    {
		if ( (!$vouchers->price || empty($vouchers->price)) && (!$vouchers->points || empty($vouchers->points)) )	{
		    $buybuttton = getPrintButton($vouchers); 
		    $print = true;
            $vcode = '<div><strong>'.elgg_echo('vouchers:addvoucher:code') . '</strong>: '.$vouchers->code.'</div>';
            $gallerybutton = $buybuttton;			
		}
		else if ($isbuyer)	{	// current user has already buy this voucher
			$buybuttton = getPrintButton($vouchers); 
			$print = true;
			$vcode = '<div><span  style="float:left;margin-right:5px;"><strong>'.elgg_echo('vouchers:addvoucher:code') . '</strong>:</span> '.$buyer_code.'<br/><span style="color:red;">'.$message_to_buyer.'</span></div>';
			$gallerybutton = $buybuttton;
		}		
		else if (!empty($status))   {   // in case of sold out
			$vcode = ''; 
			$buybuttton = $status;
		}			
		else  {
			if ($vouchers->points && elgg_is_active_plugin("elggx_userpoints"))	{  // condition if userpoints required
				if (!hasUserPointsRequired($vouchers->points)) { // points required but not enough to buy
					$notenoughpoints = elgg_echo('vouchers:userpoints:notenough');
				}
				else {
					$buybuttton = '<div>'.elgg_echo('vouchers:userpoints:pointstobereleased', array($vouchers->points)).'</div>';
				}
			}
			
			if ($notenoughpoints) {
				$vcode = ''; 
				$buybuttton = '<div class="notenoughpoints">'.$notenoughpoints.'</div>';
			}
			else {
				if ($vouchers->points && elgg_is_active_plugin("elggx_userpoints"))	{  // condition if userpoints required
					$form_vars = array('name' => 'get_with_points', 'enctype' => 'multipart/form-data', 'class' => 'elgg-requires-confirmation');
					$vars[buyer_guid] = elgg_get_logged_in_user_guid();
					$get_with_points_form = elgg_view_form('vouchers/get_with_points', $form_vars, $vars);		
					//$get_with_points_form .= '<div style="text-align:right;">'.$get_with_points_form.'</div>';
					$buybuttton .= '<div style="text-align:right;">'.$get_with_points_form.'</div>';
					$vcode = ''; 
					$gallerybutton = $get_with_points_form;
				}
			} 
		}
    }
    else {
		if ($full && !elgg_in_context('gallery')) {		// login to buy button, only to full view
			$buybuttton = '<div id="login-dropdown">
				<a class="elgg-button elgg-button-dropdown" rel="popup" href="http://localhost/elgg/login#login-dropdown-box">'.elgg_echo("vouchers:object:login_to_buy").'</a>
				</div>';
		}	
		else	
			$buybuttton = '';	
			
        $vcode = '<div>'.elgg_echo('vouchers:addvoucher:code:login') . '</div>';
        $gallerybutton = '&nbsp;';
    }
}    
else {
    $statusv = '(<span style="color:red;">'.elgg_echo('vouchers:expired').'</span>)';
    $buybuttton = '';
    $vcode = '';
    $gallerybutton = '&nbsp;';
}

if ($vouchers->amount_type === 'Currency') {
    $currency = get_currency_sign($vouchers->currency).$vouchers->amount;
}
else    {
    $currency = $vouchers->amount.'%';
}

$owner_link = elgg_view('output/url', array(
	'href' => "vouchers/owner/$owner->username",
	'text' => $owner->name,
	'is_trusted' => true,
));
$author_text = elgg_echo('byline', array($owner_link));

$date = elgg_view_friendly_time($vouchers->time_created);

//only display if there are commments
if ($vouchers->comments_on != 'Off') {
    $comments_count = $vouchers->countComments();
    //only display if there are commments
    if ($comments_count != 0) {
        $text = elgg_echo("comments") . " ($comments_count)";
        $comments_link = elgg_view('output/url', array(
            'href' => $vouchers->getURL() . '#vouchers-comments',
            'text' => $text,
            'is_trusted' => true,
        ));
    } else {
        $comments_link = '';
    }
} else {
    $comments_link = '';
}

$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => 'vouchers',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

$subtitle = "$author_text $date $comments_link";

if ($full && !elgg_in_context('gallery')) {
    $params = array(
            'entity' => $vouchers,
            'title' => false,
            'metadata' => $metadata,
            'subtitle' => $subtitle,
    );
    $params = $params + $vars;
    $summary = elgg_view('object/elements/summary', $params);

    $body = '';
    // add star to featured posts
	if ($vouchers->featured)	{
		$body .= '<div class="star"><img src="'.elgg_get_site_url() . 'mod/vouchers/graphics/star.png" width="30" height="30" alt="'.elgg_echo('vouchers:featured').'" /></div>';
	}  
	     
    $body .= '<div class="vouchers_view print">'.$buybuttton.'</div>';
    if ($vouchers->excerpt) {
        $body .= '<div>'.$vouchers->excerpt.'</div>';
    }	   
    $body .= '<div class="voucherbody elgg-image-block clearfix">';
    $body .= '<div class="elgg-image">';
	$body .= elgg_view('output/url', array(
		'href' => elgg_get_site_url() . "mod/vouchers/viewimage.php?voucher_guid={$vouchers->guid}",
		'text' => elgg_view('vouchers/thumbnail', array('voucher_guid' => $vouchers->guid, 'size' => 'medium', 'tu' => $tu)),
		'class' => "elgg-lightbox",
		'rel' => 'prefetch',
	));  
	
	$body .= $set_featured_button;

	/* // obs
    if (vouchers_check_if_members_can_send_private_message())	{
		if (elgg_is_logged_in()) {
			$pmbutton = elgg_view('output/url', array(
				'class' => 'elgg-button elgg-button-action',
				'href' => "messages/compose?send_to={$owner->guid}",
				'text' => elgg_echo('vouchers:send_message'),
				));
				
			
		}		
		$body .= '<div class="voucher_pm">'.$pmbutton.'</div>';
	} */
	
	// be interested form
		
	$body .= '</div>';    
    $body .= '<div class="elgg-body vbody">';
      
    $body .= $vcode;
    $body .= '<div><strong>'.elgg_echo('vouchers:addvoucher:amount') . '</strong>: '.$currency.'</div>';
    if ($vouchers->price) {
        $body .= '<div><strong>'.elgg_echo('vouchers:addvoucher:price') . '</strong>: '.get_currency_sign($vouchers->currency).$vouchers->price.'</div>';
    } 
    if ($vouchers->points) {
		$body .= '<div>';
        $body .= elgg_echo('vouchers:addvoucher:pointsrequired', array($vouchers->points));
        if (elgg_is_logged_in())    { 
			$body .= elgg_echo('vouchers:addvoucher:pointsrequired:subtracted'); 
		}
        $body .= '</div>';
    } 
    if ($date_display_from) {
        $body .= '<div><strong>'.elgg_echo('vouchers:addvoucher:validfrom') . '</strong>: '. $date_display_from.'</div>';
    }
    $body .= '<div><strong>'.elgg_echo('vouchers:addvoucher:validuntil') . '</strong>: '. $date_display_until.'   '.$statusv.'</div>';
	if (is_numeric($vouchers_howmany)) {
		$body .= '<div><strong>'.elgg_echo('vouchers:addvoucher:howmany') . '</strong>: '.$vouchers_howmany.'</div>';
    }     

    if ($vouchers->zone) {
        $body .= '<div><strong>'.elgg_echo('vouchers:addvoucher:zone') . '</strong>: '.$vouchers->zone.'</div>';
    } 
    
    if ($vouchers->weburl) {
        $body .= '<div><strong>'.elgg_echo('vouchers:addvoucher:weburl') . '</strong>: <a href="'.$vouchers->weburl.'" target="blank">'.$vouchers->weburl.'</a></div>';
    }   
     
    if ($vouchers->terms) {
		$body .= '<div><strong>'.elgg_echo('vouchers:addvoucher:terms') . '</strong>: '.$vouchers->terms.'</div>';
    } 
    
	$body .= '</div>';

	// send interest form
    if (elgg_is_logged_in() && elgg_is_active_plugin("messages") && vouchers_check_if_members_can_send_private_message() && !(elgg_get_logged_in_user_guid() == $vouchers->owner_guid))	{
		
		$form_params = array(
			'id' => 'interested-in-form',
			'class' => 'hidden mtl',
		);
		$body_params = array(
			'entity_guid' => $vouchers->guid, 
			'recipient_guid' => $vouchers->owner_guid, 
			'subject' => elgg_echo("vouchers:be_interested:ad_message_subject", array($vouchers->title)),
		);
		$interest_form = elgg_view_form('vouchers/be_interested', $form_params, $body_params);
		// $from_user = get_user($message->fromId); // mallon obs
		
		$pmbutton = elgg_view('output/url', array(
			'name' => 'reply',
			'class' => 'elgg-button elgg-button-action',
			'rel' => 'toggle',
			'href' => '#interested-in-form',
			'text' => elgg_echo('vouchers:be_interested'),
		));		
					
		$body .= '<div class="pm">'.$pmbutton.'</div>';
		$body .= $interest_form;
	}
		    
    if ($vouchers->description) {
        $body .= '<div class="desc-voucher">'.$vouchers->description.'</div>';
    }  
    else {	
		$body .= '<div class="desc">&nbsp;</div>';
    }  
    $body .= '</div>';    

    echo elgg_view('object/elements/full', array(
        'entity' => $vouchers,
        'icon' => $owner_icon,
        'summary' => $summary,
        'body' => $body,
    ));
} 
elseif (elgg_in_context('gallery')) {
	$galleryhref = elgg_get_site_url().'vouchers/view/'.$vouchers->guid.'/'. elgg_get_friendly_title($vouchers->title);
	echo '<div class="vouchers-gallery-item">';
	echo '<a href="'.$galleryhref.'"><h3>'.$vouchers->title.'</h3></a>';
	echo '<a href="'.$galleryhref.'">'.elgg_view('vouchers/thumbnail', array('voucher_guid' => $vouchers->guid, 'size' => 'medium', 'tu' => $tu)).'</a>';
	echo '<p class="gallery-date">'.$owner_link.' '.$date.'</p>';
	echo '<div class="gallery-view">';
	echo '<strong>'.elgg_echo('vouchers:addvoucher:amount') . '</strong>: '.$currency.'<br />';
    if ($date_display_from) {
        echo '<strong>'.elgg_echo('vouchers:addvoucher:validfrom') . '</strong>: '. $date_display_from.'<br />';
    } 	
    echo '<strong>'.elgg_echo('vouchers:addvoucher:validuntil') . '</strong>: '. $date_display_until.'<br />';
    if ($vouchers->points) {
		echo elgg_echo('vouchers:addvoucher:pointsrequired:gallery', array($vouchers->points)).'<br />';
    }     
	if ($vouchers->price) { 
		echo '<strong>'.elgg_echo('vouchers:addvoucher:price') . '</strong>: '.get_currency_sign($vouchers->currency).' '.$vouchers->price.'<br />';
	}
	
	if (($vouchers->price || $vouchers->points) && !$print) {   
		echo $gallerybutton;
	}
	
    echo '</div>';
	echo '</div>';
}
else {
    // brief view
    $display_text = $url;
    
	// we want small thumb on group views
	$page_owner = elgg_get_page_owner_entity();
	if (elgg_instanceof($page_owner, 'group'))  
		$thumbsize = 'small';
	else
		$thumbsize = 'medium';
	
    // brief view
    $voucher_img = elgg_view('output/url', array(
        'href' => "vouchers/view/{$vouchers->guid}/" . elgg_get_friendly_title($vouchers->title),
        'text' => elgg_view('vouchers/thumbnail', array('voucher_guid' => $vouchers->guid, 'size' => $thumbsize, 'tu' => $tu)),
    ));    

	$content = '';
	
	// add star to featured posts
	if ($vouchers->featured)	{
		$content .= '<div class="star_right"><img src="'.elgg_get_site_url() . 'mod/vouchers/graphics/star.png" width="30" height="30" alt="'.elgg_echo('vouchers:featured').'" /></div>';
	} 

    if ($vouchers->excerpt) {
        $content .= ''.$vouchers->excerpt.'<br />';
    }    
    	
    $content .= $vcode;
    $content .= '<strong>'.elgg_echo('vouchers:addvoucher:amount') . '</strong>: '.$currency.'<br />';
	if (($vouchers->price || $vouchers->points) && !$print) { 
		$content .= '<div class="print" style="clear:both;">'.$buybuttton.'</div>';
	}    
    if ($vouchers->price) { 
		$content .=  '<strong>'.elgg_echo('vouchers:addvoucher:price') . '</strong>: '.get_currency_sign($vouchers->currency).' '.$vouchers->price.'<br />';
	}    
    if ($vouchers->points && !$isbuyer) {
		$content .= '<div>';
        $content .= elgg_echo('vouchers:addvoucher:pointsrequired', array($vouchers->points));
        if (elgg_is_logged_in())    { 
			$content .= elgg_echo('vouchers:addvoucher:pointsrequired:subtracted'); 
		}
        $content .= '</div>';		
    }
    
    if ($date_display_from) {
        $content .= '<strong>'.elgg_echo('vouchers:addvoucher:validfrom') . '</strong>: '. $date_display_from.'<br />';
    }    
    $content .= '<strong>'.elgg_echo('vouchers:addvoucher:validuntil') . '</strong>: '. $date_display_until.'<br />';
	if (is_numeric($vouchers_howmany)) {
		$content .= '<div><strong>'.elgg_echo('vouchers:addvoucher:howmany') . '</strong>: '.$vouchers_howmany.'</div><br />';
    }  
    
    $params = array(
            'entity' => $vouchers,
            'metadata' => $metadata,
            'subtitle' => $subtitle,
            'content' => $content,
    );
    $params = $params + $vars;
    $body = elgg_view('object/elements/summary', $params);

    echo elgg_view_image_block($voucher_img, $body);
}
