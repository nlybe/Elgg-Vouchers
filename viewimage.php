<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author slyhne
 * @copyright slyhne 2010-2011
 * @link www.zurf.dk/elgg
  */

// Get engine
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

// Get the specified voucher post
$voucher_guid = (int) get_input('voucher_guid');

$voucher = get_entity($voucher_guid);
if (!$voucher || $voucher->getSubtype() != "vouchers") {
	exit;
}

$voucher_img = elgg_view('output/url', array(
    'href' => "vouchers/view/{$voucher->guid}/" . elgg_get_friendly_title($voucher->title),
    'text' => elgg_view('vouchers/thumbnail', array(
        'voucher_guid' => $voucher->guid,
        'size' => 'master',
        'class' => 'voucher-image-popup',
    )),
));
			
echo "<div style='min-width: 600px;'>";
echo "<h3>{$voucher->title}</h3>";
echo $voucher_img;
echo "</div>";

