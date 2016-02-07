<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

class Vsales extends ElggObject {
    const SUBTYPE = "vsales";
    
    protected $meta_defaults = array(
        "txn_vguid" 		=> NULL,
        "txn_buyer_guid"       	=> NULL,
        "txn_date" 		=> NULL,    // date of transaction
        "txn_id" 		=> NULL,    // id of transaction
        "txn_code" 		=> NULL,    // voucher code given
        "txn_method" 		=> NULL,    // method of purchase such as paypal, elggx points etc etc
    );    

    protected function initializeAttributes() {
        parent::initializeAttributes();

        $this->attributes["subtype"] = self::SUBTYPE;
    }
}
