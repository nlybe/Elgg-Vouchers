<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

function vouchers_manager_run_once_subtypes()	{
    add_subtype('object', Voucher::SUBTYPE, "vouchers");
    add_subtype('object', Vsales::SUBTYPE, "vsales");
}
