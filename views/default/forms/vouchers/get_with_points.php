<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

$voucher = elgg_extract('entity', $vars, FALSE);
$buyer_guid = elgg_extract('buyer_guid', $vars, FALSE);
?>

<div class="elgg-foot">
<?php
	echo elgg_view('input/hidden', array('name' => 'buyer_guid', 'value' => $buyer_guid));
	echo elgg_view('input/hidden', array('name' => 'voucher_guid', 'value' => $voucher->guid));
    echo elgg_view('input/submit', array('value' => elgg_echo('vouchers:get_with_points')));
?>
</div>
