<?php
/**
 * Elgg vouchers plugin
 *
 * @package Vouchers
 */

$title = $vars['entity']->title;
if (empty($title)) {
	$title = strip_tags($vars['entity']->description);
	$title = elgg_get_excerpt($title, 32);
}

$permalink = htmlspecialchars($vars['entity']->getURL(), ENT_NOQUOTES, 'UTF-8');
$pubdate = date('r', $vars['entity']->getTimeCreated());

// retrieve date format from settings
$dformat = trim(elgg_get_plugin_setting('default_dateformat', 'vouchers'));
print_r($dformat);
if (empty($dformat) || $dformat=='')    {
    $dformat = 'F j, Y';
}

$text = elgg_echo('vouchers:addvoucher:code').': '.$vars['entity']->code;
$text .= '<br />'.elgg_echo('vouchers:addvoucher:validuntil').': '.date($dformat, $vars['entity']->valid_until);
if ($vars['entity']->valid_until <= time()) {
    $text .= ' (<span style="color:red;">'.elgg_echo('vouchers:expired').'</span>)';
}
$description = "<p>$text</p>".$vars['entity']->description;

$creator = elgg_view('page/components/creator', $vars);
$georss = elgg_view('page/components/georss', $vars);
$extension = elgg_view('extensions/item');

$item = <<<__HTML
<item>
	<guid isPermaLink="true">$permalink</guid>
	<pubDate>$pubdate</pubDate>
	<link>$permalink</link>
	<title><![CDATA[$title]]></title>
	<description><![CDATA[$description]]></description>
	$creator$georss$extension
</item>

__HTML;

echo $item;
