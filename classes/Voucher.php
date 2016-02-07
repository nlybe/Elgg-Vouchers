<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

class Voucher extends ElggObject {
    const SUBTYPE = "vouchers";
    
    protected $meta_defaults = array(
        "title" 		=> NULL,
        "code"          => NULL,	// code of voucher
        "code_type"  	=> NULL,	// define type of code
        "code_end"      => NULL,	// required to set a range of vouchers
        "description" 	=> NULL,	// description of voucher
        "amount"        => NULL,	// amount reduced for the purchase of product
        "amount_type"   => NULL,    // total or percentage discount
        "valid_from" 	=> NULL,	
        "valid_until" 	=> NULL,
        "price" 		=> NULL,	// price in money required for purchase of this voucher
        "points" 		=> NULL,	// points required for purchase of this voucher
        "howmany" 		=> NULL,	// how many vouchers are availabled. It doesn't affect for a series of codes
        "currency" 		=> NULL,	// currency 
        "weburl" 		=> NULL,	// URL that this voucher can be used
        "tags"          => NULL,	
        "comments_on" 	=> NULL,
        "featured" 		=> NULL,	// set true if voucher is featured, so will be displayed with a star
        "vimage" 		=> NULL,	// image for voucher
        "qrcode" 		=> NULL,	// QR code image
        "zone" 			=> NULL,	// zone that this is valid like US, EU world etc
        "terms" 		=> NULL,	// terms of voucher
        "excerpt" 		=> NULL, 	// short description of voucher
    );    

    protected function initializeAttributes() {
        parent::initializeAttributes();

        $this->attributes["subtype"] = self::SUBTYPE;
    }
    
}
