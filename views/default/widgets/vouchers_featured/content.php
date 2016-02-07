<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

elgg_load_library('elgg:vouchers');

// set the default timezone to use
date_default_timezone_set(voucher_get_default_timezone());

//the page owner
$owner = get_user($vars['entity']->owner_guid);

//the number of files to display
$num = (int) $vars['entity']->num_display;
if (!$num) {
	$num = 5;
}		
		
$posts = elgg_get_entities_from_metadata(array(
	'type'=>'object',
	'subtype'=>'vouchers', 
	'limit'=>$num,
	'metadata_name_value_pairs' => array(
		array('name' => 'featured','value' => true,'operand' => '='), 
	), 	
));

echo '<ul class="elgg-list">';		
// display the posts, if there are any
if (is_array($posts) && sizeof($posts) > 0) {

	if (!$size || $size == 1){
		foreach($posts as $post) {
			echo "<li class=\"pvs\">";
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
			echo elgg_view_image_block($voucher_img, $list_body);
			echo "</li>";
		}
			
	}
	echo "</ul>";

}

