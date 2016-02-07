<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author slyhne
 * @copyright slyhne 2010-2011
 * @link www.zurf.dk/elgg
  */

$voucher_guid = $vars['voucher_guid'];
$size =  $vars['size'];
$class = $vars['class'];
$tu = $vars['tu'];
$direct = $vars['direct'];
$is_code_qr = $vars['is_code_qr'];

if ($is_code_qr) {
	echo '<img src="' . elgg_get_site_url() . 'mod/vouchers/thumbnail.php?is_code_qr=1&voucher_guid='.$voucher_guid.'&size='.$size.'&ut='.$tu.'" class="elgg-photo '.$class.'">';
}
else  {
	if ($direct) {
		echo elgg_get_site_url() . 'mod/vouchers/thumbnail.php?voucher_guid='.$voucher_guid.'&size='.$size.'&ut='.$tu;
	}
	else {
		echo '<img src="' . elgg_get_site_url() . 'mod/vouchers/thumbnail.php?voucher_guid='.$voucher_guid.'&size='.$size.'&ut='.$tu.'" class="elgg-photo '.$class.'">';
	}
}
