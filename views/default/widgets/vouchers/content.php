<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

elgg_load_library('elgg:vouchers');

// set the default timezone to use
date_default_timezone_set(voucher_get_default_timezone());

// modified to be compatible with widget manager
$owner = get_entity($vars['entity']->owner_guid);
if (elgg_instanceof($owner, 'user')) {
	$url = "vouchers/owner/{$owner->username}";
} else {
	$url = "vouchers/group/{$owner->guid}/all";
}

//the number of files to display
$num = (int) $vars['entity']->num_display;
if (!$num) {
	$num = 4;
}		
		
$options = array(
	'type'=>'object',
	'subtype'=>'vouchers', 
	'container_guid' => $owner->guid, 
	'limit'=>$num,
	'full_view' => false,
	'pagination' => false,
	'size' => 'small'
);
		

if (elgg_instanceof($owner, 'user')) {
	$posts = elgg_get_entities($options);	
	if (is_array($posts) && sizeof($posts) > 0) {
		$content =  '<ul class="elgg-list">';	
		
		foreach($posts as $post) {
			$content .=  "<li class=\"pvs\">";
			$comments_count = $post->countComments();
			$text = elgg_echo("comments") . " ($comments_count)";
			$comments_link = elgg_view('output/url', array(
				'href' => $post->getURL() . '#vouchers-comments',
				'text' => $text,
			));
			$voucher_img = elgg_view('output/url', array(
				'href' => "vouchers/view/{$post->guid}/" . elgg_get_friendly_title($post->title),
				'text' => elgg_view('vouchers/thumbnail', array('voucher_guid' => $post->guid, 'size' => 'small')),
			));			
			//$owner_icon = elgg_view_entity_icon($owner, 'tiny');
			$dformat = trim(elgg_get_plugin_setting('default_dateformat', 'vouchers'));
			if (empty($dformat) || $dformat=='')    {
				$dformat = 'F j, Y';
			}
			$date_display_until = date($dformat, $post->valid_until);

			if ($post->amount_type === 'Currency') {
				$currency = get_currency_sign($vouchers->currency).$post->amount;
			}
			else    {
				$currency = $post->amount.'%';
			}
			
			$subtitle = '<strong>'.elgg_echo('vouchers:widget:amount') . ' :</strong>'.$currency;
			if ($post->price) {
				$adprice = get_currency_sign($post->currency).' '.$post->price;
				$subtitle .= ', <strong>'.elgg_echo('vouchers:widget:price') . ":</strong> {$adprice}";
			}
			$subtitle .= '<br/><strong>'.elgg_echo('vouchers:widget:validuntil') . ': </strong>'. $date_display_until;
			$subtitle .= "<br>{$author_text} {$date} {$comments_link}";
			$params = array(
				'entity' => $post,
				'metadata' => $metadata,
				'subtitle' => $subtitle,
				'tags' => $tags,
				'content' => $excerpt,
			);
			$params = $params + $vars;
			$list_body = elgg_view('object/elements/summary', $params);
			$content .=  elgg_view_image_block($voucher_img, $list_body);
			$content .=   "</li>";
		}
				
		$content .= "</ul>";
	}	
} else {
	elgg_push_context('widgets');
	$content = elgg_list_entities($options);
	elgg_pop_context();	
}


if (!$content) {
	$content = '<p>' . elgg_echo('vouchers:none') . '</p>';
}

echo $content;

$more_link = elgg_view('output/url', array(
	'href' => $url,
	'text' => elgg_echo("vouchers:widget:viewall"),
	'is_trusted' => true,
));
echo "<span class=\"elgg-widget-more\">$more_link</span>";



