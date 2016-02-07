<?php
/**
 * Elgg vouchers plugin
 *
 * @package Vouchers
 */

elgg_load_library('elgg:vouchers');

// check if user can post vouchers
if (check_if_user_can_post()) { 
    
    // Get variables
    $title = get_input("title");
    $code = get_input("code");
    $code_type = get_input("code_type");
    $code_end = get_input("code_end");
    $amount = get_input("amount");
    $amount_type = get_input("amount_type");
    $desc = get_input("description");
    $valid_from = get_input("valid_from");
    $valid_until = get_input("valid_until");
    $price = get_input("price");
    $points = get_input("points");
    $howmany = get_input("howmany");
    $currency = get_input("currency");
    $weburl = get_input("weburl");
    $zone = get_input("zone");
    $terms = get_input("terms");
    $excerpt = get_input("excerpt");
    $tags = get_input("tags");
    $access_id = (int) get_input("access_id");
    $guid = (int) get_input('voucher_guid');
    $container_guid = get_input('container_guid', elgg_get_logged_in_user_guid());
    $comments_on = get_input("comments_on");

    elgg_make_sticky_form('vouchers');

    if (!$title) {
        register_error(elgg_echo('vouchers:save:missing_title'));
        forward(REFERER);
    }
    
    if ($code_type == VOUCHERS_CODE_TYPE_SINGLE && !$code) {
        register_error(elgg_echo('vouchers:save:missing_code'));
        forward(REFERER);
    } 
    
	if ($code_type == VOUCHERS_CODE_TYPE_SERIES && !is_numeric($code)) {
		register_error(elgg_echo('vouchers:save:code_not_numeric'));
		forward(REFERER);
    }   
        
	if ($code_type == VOUCHERS_CODE_TYPE_SERIES && !is_numeric($code_end)) {
		register_error(elgg_echo('vouchers:save:code_end_not_numeric'));
		forward(REFERER);
    }    
    
	if ($code_type == VOUCHERS_CODE_TYPE_SERIES && ($code >= $code_end)) {
		register_error(elgg_echo('vouchers:save:code_less_codend'));
		forward(REFERER);
    } 
    
    if (!$amount) {
        register_error(elgg_echo('vouchers:save:missing_amount'));
        forward(REFERER);
    }  
    
    if (!is_numeric($amount)) {
        register_error(elgg_echo('vouchers:save:amount_not_numeric'));
        forward(REFERER);
    }   
    
    if ($price && !is_numeric($price)) {
        register_error(elgg_echo('vouchers:save:price_not_numeric'));
        forward(REFERER);
    } 
    
    if ($points && !is_numeric($points)) {
        register_error(elgg_echo('vouchers:save:points_not_numeric'));
        forward(REFERER);
    } 
    
    if(!$valid_until) {
        register_error(elgg_echo('vouchers:save:missing_until_date'));
        forward(REFERER);
    } 
    
	if ($howmany && !is_numeric($howmany)) {
        register_error(elgg_echo('vouchers:save:howmany_not_numeric'));
        forward(REFERER);
    }      
    
    // don't use elgg_normalize_url() because we don't want relative links resolved to this site.
    if ($weburl && !preg_match("#^((ht|f)tps?:)?//#i", $weburl)) {
            $weburl = "http://$weburl";
    }  
    
    $validated = false;
    if ($php_5_2_13_and_below || $php_5_3_0_to_5_3_2) {
            $tmp_address = str_replace("-", "", $weburl);
            $validated = filter_var($tmp_address, FILTER_VALIDATE_URL);
    } else {
            $validated = filter_var($weburl, FILTER_VALIDATE_URL);
    }
    if (!$validated) {
            register_error(elgg_echo('vouchers:save:novalid_weburl'));
            forward(REFERER);
    }    
    
    if(!empty($valid_until)) {
        $date_until = explode('-',$valid_until);
        //$valid_until = mktime(0,$end_time_minutes,$end_time_hours,$date_until[1],$date_until[2],$date_until[0]);
        $valid_until = mktime(0,0,0,$date_until[1],$date_until[2]+1,$date_until[0]);
    }     
    
    if(!empty($valid_from)) {
        $date_from = explode('-',$valid_from);
        $valid_from = mktime(0,0,0,$date_from[1],$date_from[2]+1,$date_from[0]);

        if ($valid_until < $valid_from) {
            register_error(elgg_echo('vouchers:save:until_date_lessthan_valid_from'));
            forward(REFERER);
        }
    }  

    // check whether this is a new object or an edit
    $new_voucher = true;
    if ($guid > 0) {
            $new_voucher = false;
    }
    
	if ($code_type == VOUCHERS_CODE_TYPE_QR && $_FILES["qr_image_upload"]["error"] == 4 && $new_voucher) {
		register_error(elgg_echo('vouchers:save:code_not_qr_image'));
		forward(REFERER);
    }     

    if ($guid == 0) {
        $voucher = new ElggObject;
        $voucher->subtype = "vouchers";
        //$voucher->container_guid = (int)get_input('container_guid', $_SESSION['user']->getGUID());
        $voucher->container_guid = $container_guid;
        $new = true;
        // if no title on new upload, grab filename
        if (empty($title)) {
                $title = elgg_echo('vouchers:addvoucher:missing_title');
        }        
    } else {
        $voucher = get_entity($guid);
        if (!$voucher->canEdit()) {
            system_message(elgg_echo('vouchers:save:failed'));
            forward(REFERRER);
        }
        if (!$title) {
                // user blanked title, but we need one
                $title = $voucher->title;
        }    
    }

	// allowed file types for uploads
	$allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");

	// Check if image uploaded
	if ($_FILES["upload"]["error"] != 4) {
		$temp = explode(".", $_FILES["upload"]["name"]);
		$extension = end($temp);
		if (((	$_FILES["upload"]["type"] == "image/gif") 
			|| ($_FILES["upload"]["type"] == "image/jpeg") 
			|| ($_FILES["upload"]["type"] == "image/jpg")
			|| ($_FILES["upload"]["type"] == "image/pjpeg") 
			|| ($_FILES["upload"]["type"] == "image/x-png") 
			|| ($_FILES["upload"]["type"] == "image/png"))
			&& (in_array($extension, $allowedExts))	)	 {
		}
		else
		{
			register_error(elgg_echo('vouchers:addvoucher:image:invalidfiletype'));  
			forward(REFERER); 
		} 
	}
	
	// Check if qr code mage uploaded
	if ($_FILES["qr_image_upload"]["error"] != 4) {
		$temp_qr = explode(".", $_FILES["qr_image_upload"]["name"]);
		$extension_qr = end($temp_qr);
		if (((	$_FILES["qr_image_upload"]["type"] == "image/gif") 
			|| ($_FILES["qr_image_upload"]["type"] == "image/jpeg") 
			|| ($_FILES["qr_image_upload"]["type"] == "image/jpg")
			|| ($_FILES["qr_image_upload"]["type"] == "image/pjpeg") 
			|| ($_FILES["qr_image_upload"]["type"] == "image/x-png") 
			|| ($_FILES["qr_image_upload"]["type"] == "image/png"))
			&& (in_array($extension_qr, $allowedExts))	)	 {
		}
		else
		{
			register_error(elgg_echo('vouchers:addvoucher:image:invalidfiletype:qrcode'));  
			forward(REFERER); 
		} 
	}	
	
    $tagarray = string_to_tag_array($tags);

    $voucher->title = $title;
    $voucher->code = $code;
    $voucher->code_type = $code_type;
    $voucher->code_end = $code_end;
    $voucher->description = $desc;
    $voucher->amount = $amount;
    $voucher->amount_type = $amount_type;
    $voucher->access_id = $access_id;
    $voucher->valid_from = $valid_from;
    $voucher->valid_until = $valid_until;
    $voucher->howmany = $howmany;
    $voucher->price = $price;
    $voucher->points = $points;
    $voucher->currency = $currency;
    $voucher->weburl = $weburl;
    $voucher->zone = $zone;
    $voucher->terms = $terms;
    $voucher->excerpt = $excerpt;
    $voucher->tags = $tagarray;
    //$voucher->container_guid = $container_guid;
    $voucher->comments_on = $comments_on;

    if ($voucher->save()) {
        elgg_clear_sticky_form('vouchers');
        
		// Check if image uploaded
        if ((isset($_FILES['upload']['name'])) && (substr_count($_FILES['upload']['type'],'image/'))) {
            $prefix = "voucher/".$voucher->guid;

            $filehandler = new ElggFile();
            $filehandler->owner_guid = $voucher->owner_guid;
            $filehandler->setFilename($prefix . ".jpg");
            $filehandler->open("write");
            $filehandler->write(get_uploaded_file('upload'));
            $filehandler->close();

            $thumbtiny = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),25,25, true);
            $thumbsmall = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),40,40, true);
            $thumbmedium = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),153,153, true);
            $thumblarge = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),200,200, false);

            if ($thumbtiny) {
                $thumb = new ElggFile();
                $thumb->owner_guid = $voucher->owner_guid;
                $thumb->setMimeType('image/jpeg');

                $thumb->setFilename($prefix."tiny.jpg");
                $thumb->open("write");
                $thumb->write($thumbtiny);
                $thumb->close();

                $thumb->setFilename($prefix."small.jpg");
                $thumb->open("write");
                $thumb->write($thumbsmall);
                $thumb->close();

                $thumb->setFilename($prefix."medium.jpg");
                $thumb->open("write");
                $thumb->write($thumbmedium);
                $thumb->close();

                $thumb->setFilename($prefix."large.jpg");
                $thumb->open("write");
                $thumb->write($thumblarge);
                $thumb->close();
            }
        }     
        
		// Check if qr code image uploaded
        if ((isset($_FILES['qr_image_upload']['name'])) && (substr_count($_FILES['qr_image_upload']['type'],'image/'))) {
            $prefix = "voucher-qrcode/".$voucher->guid;

            $filehandler = new ElggFile();
            $filehandler->owner_guid = $voucher->owner_guid;
            $filehandler->setFilename($prefix . ".jpg");
            $filehandler->open("write");
            $filehandler->write(get_uploaded_file('qr_image_upload'));
            $filehandler->close();

            $thumbtiny = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),25,25, true);
            $thumbsmall = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),40,40, true);
            $thumbmedium = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),153,153, true);
            $thumblarge = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),200,200, false);

            if ($thumbtiny) {
                $thumb = new ElggFile();
                $thumb->owner_guid = $voucher->owner_guid;
                $thumb->setMimeType('image/jpeg');

                $thumb->setFilename($prefix."tiny.jpg");
                $thumb->open("write");
                $thumb->write($thumbtiny);
                $thumb->close();

                $thumb->setFilename($prefix."small.jpg");
                $thumb->open("write");
                $thumb->write($thumbsmall);
                $thumb->close();

                $thumb->setFilename($prefix."medium.jpg");
                $thumb->open("write");
                $thumb->write($thumbmedium);
                $thumb->close();

                $thumb->setFilename($prefix."large.jpg");
                $thumb->open("write");
                $thumb->write($thumblarge);
                $thumb->close();
            }
        }

        system_message(elgg_echo('vouchers:save:success'));

        //add to river only if new
        if ($new) {
			// add purchase to river
			$release = get_version(true);
			if ($release < 1.9)  // version 1.8
				add_to_river('river/object/vouchers/create','create', elgg_get_logged_in_user_guid(), $voucher->getGUID());
			else { // use this since Elgg 1.9
				elgg_create_river_item(array(
					'view' => 'river/object/vouchers/create',
					'action_type' => 'create',
					'subject_guid' => elgg_get_logged_in_user_guid(),
					'object_guid' => $voucher->getGUID(),
				));
			}            
        }

        forward($voucher->getURL());
    } else {
        register_error(elgg_echo('vouchers:save:failed'));
        forward("vouchers");
    }

} 
else    {  
    register_error(elgg_echo('vouchers:addvoucher:noaccessforpost'));  
    forward(REFERER);    
}
