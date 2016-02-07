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

// Get file GUID
$voucher_guid = (int) get_input('voucher_guid', 0);
$is_code_qr = (int) get_input('is_code_qr', 0);

$voucher = get_entity($voucher_guid);
if (!$voucher || $voucher->getSubtype() != "vouchers") {
	exit;
}

// Get owner
$owner = $voucher->getOwnerEntity();

// Get the size
$size = strtolower(get_input('size'));
if (!in_array($size,array('large','medium','small','tiny','master'))) {
    $size = "medium";
}

// Use master if we need the full size
if ($size == "master") {
    $size = "";
}

// Try and get the icon
$filehandler = new ElggFile();
$filehandler->owner_guid = $owner->guid;

if ($is_code_qr) {
	// check if this user has bought this voucher
	$options = array(
			'type' => 'object',
			'subtype' => 'vsales',
			'limit' => 1,
			'metadata_name_value_pairs' => array(
				array('name' => 'txn_vguid','value' => $voucher_guid, 'operand' => '='),
				array('name' => 'txn_buyer_guid', 'value' => elgg_get_logged_in_user_guid(), 'operand' => '='),
			),
			'metadata_name_value_pairs_operator' => 'AND',
	);

	$getbuyers = elgg_get_entities_from_metadata($options);
	$isbuyer = false;
	if (is_array($getbuyers)) {
		foreach ($getbuyers as $b) {
			$isbuyer = true; 
		}
	}	
	
	if ($isbuyer)
		$filehandler->setFilename("voucher-qrcode/" . $voucher->guid . $size . ".jpg");
	else
		$filehandler->setFilename("noqrcode");
}
else  {
	$filehandler->setFilename("voucher/" . $voucher->guid . $size . ".jpg");
}
		
$success = false;
if ($filehandler->open("read")) {
    if ($contents = $filehandler->read($filehandler->size())) {
        $success = true;
    } 
}

if (!$success) {
	$path = elgg_get_site_url() . "mod/vouchers/graphics/noimage{$size}.png";
	header("Location: $path");
	exit;
}

header("Content-type: image/jpeg");
header('Expires: ' . date('r',time() + 864000));
header("Pragma: public");
header("Cache-Control: public");
header("Content-Length: " . strlen($contents));

$splitString = str_split($contents, 1024);

foreach($splitString as $chunk) {
	echo $chunk;
}

