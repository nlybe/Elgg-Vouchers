<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

elgg_load_js('vouchersjs');
elgg_load_css('vouchers_tooltip_css');

// get current elgg version
$release = get_version(true);
if ($release < 1.9)	{  // version 1.8
	elgg_load_js('vouchers_tooltip_js');
}

$title = elgg_extract('title', $vars, '');
$code = elgg_extract('code', $vars, '');
$code_type = elgg_extract('code_type', $vars, '');
$code_end = elgg_extract('code_end', $vars, '');
$desc = elgg_extract('description', $vars, '');
$amount = elgg_extract('amount', $vars, '');
$amount_type = elgg_extract('amount_type', $vars, '');
$valid_from = elgg_extract('valid_from', $vars, '');
$valid_until = elgg_extract('valid_until', $vars, '');
$price = elgg_extract('price', $vars, 0);
$points = elgg_extract('points', $vars, 0);
$howmany = elgg_extract('howmany', $vars, 0);
$currency = elgg_extract('currency', $vars, 0);
$weburl = elgg_extract('weburl', $vars, 0);
$zone = elgg_extract('zone', $vars, '');
$terms = elgg_extract('terms', $vars, '');
$excerpt = elgg_extract('excerpt', $vars, '');
$tags = elgg_extract('tags', $vars, '');
$access_id = elgg_extract('access_id', $vars, ACCESS_DEFAULT);
$container_guid = elgg_extract('container_guid', $vars);
if (!$container_guid) {
	$container_guid = elgg_get_logged_in_user_guid();
}
$guid = elgg_extract('guid', $vars, null);
$answers_yesno = array('Yes', 'No');
$answers_amount_type = array(
    elgg_echo('vouchers:addvoucher:total') => elgg_echo('vouchers:addvoucher:total'),
    elgg_echo('vouchers:addvoucher:percentage') => elgg_echo('vouchers:addvoucher:percentage')
);

if (empty($currency))   {
    $currency = trim(elgg_get_plugin_setting('default_currency', 'vouchers'));
}

// get currency list
$CurrOptions = get_currency_list();

$comments_input = elgg_view('input/dropdown', array(
	'name' => 'comments_on',
	'id' => 'vouchers_comments_on',
	'value' => elgg_extract('comments_on', $vars, ''),
	'options_values' => array('On' => elgg_echo('on'), 'Off' => elgg_echo('off'))
));


if (empty($code_type)) {
	$code_type = VOUCHERS_CODE_TYPE_SINGLE;	
}
?>

<script type="text/javascript">
$(function() {
	$( document ).tooltip();
});
</script>

<p><?php echo elgg_echo('vouchers:addvoucher:requiredfields'); ?></p>
<div style="display:block; clear:both;">
    <label><?php echo elgg_echo('vouchers:addvoucher:title'); ?></label> <span style="color:red;">(*)</span>
    <span class='v_custom_fields_more_info' id='more_info_title' title='<?php echo elgg_echo('vouchers:addvoucher:title:note'); ?>'></span>
    <br /><?php echo elgg_view('input/text', array('name' => 'title', 'value' => $title)); ?>
</div>

<div class="code_options"> 
    <label><?php echo elgg_echo('vouchers:addvoucher:code:type'); ?></label> <span style="color:red;">(*)</span>
    <span class='v_custom_fields_more_info' id='more_info_code' title='<?php echo elgg_echo('vouchers:addvoucher:code:type:note'); ?>'></span>
    <?php
		echo elgg_view('input/radio', array('id' => 'code_type', 'name' => 'code_type', 'value' => $code_type, 'options' => array(elgg_echo('vouchers:addvoucher:code:code_single')=>VOUCHERS_CODE_TYPE_SINGLE,elgg_echo('vouchers:addvoucher:code:code_series')=>VOUCHERS_CODE_TYPE_SERIES,elgg_echo('vouchers:addvoucher:code:code_qr')=>VOUCHERS_CODE_TYPE_QR), 'onchange' => 't_show("'.elgg_echo('vouchers:addvoucher:code').'","'.elgg_echo('vouchers:addvoucher:code_series').'","'.elgg_echo('vouchers:addvoucher:qr_image').'");'));
	?>	
</div>

<div style="display:block; clear:both;">
    <label id="code_message"><?php echo elgg_echo('vouchers:addvoucher:code'); ?></label> <span style="color:red;">(*)</span>
    <br /><?php echo elgg_view('input/text', array('id' => 'code', 'name' => 'code', 'value' => $code, 'class' => 'medium')); ?>

    <?php echo elgg_view('input/text', array('id' => 'code_end', 'name' => 'code_end', 'value' => $code_end, 'class' => 'medium')); ?>
    <span class='v_custom_fields_more_info' id='more_info_code_end' title='<?php echo elgg_echo('vouchers:addvoucher:code_end:note'); ?>'></span>
    
	<div id="code_qr_image">
		<span class='v_custom_fields_more_info' id='more_info_image' title='<?php echo elgg_echo('vouchers:addvoucher:qr_image:note'); ?>'></span>
		<?php echo elgg_view('input/file', array('name' => 'qr_image_upload')); ?>
	</div>
</div>

<div id="howmany">
    <label><?php echo elgg_echo('vouchers:addvoucher:howmany'); ?>:</label>
    <span class='v_custom_fields_more_info' id='more_info_howmany' title='<?php echo elgg_echo('vouchers:addvoucher:howmany:note'); ?>'></span>
    <?php echo elgg_view('input/text', array('name' => 'howmany', 'value' => $howmany, 'class' => 'short')); ?>
</div>

<div style="display:block; clear:both;">
    <label><?php echo elgg_echo('vouchers:addvoucher:amount'); ?></label> <span style="color:red;">(*)</span>
    <span class='v_custom_fields_more_info' id='more_info_amount' title='<?php echo elgg_echo('vouchers:addvoucher:amount:note'); ?>'></span>
    <?php echo elgg_view('input/text', array('name' => 'amount', 'value' => $amount, 'class' => 'short')); ?>

    <label><?php echo elgg_echo('vouchers:addvoucher:amount_type'); ?></label>:
    <span class='v_custom_fields_more_info' id='more_info_amount_type' title='<?php echo elgg_echo('vouchers:addvoucher:amount_type:note'); ?>'></span>
    <?php echo elgg_view('input/dropdown', array('name' => 'amount_type','value' => elgg_extract('amount_type', $vars, ''),'options_values' => $answers_amount_type)); ?>
</div>

<div style="display:block; clear:both;margin-top:20px;">
    <label><?php echo elgg_echo('vouchers:addvoucher:price'); ?></label>
    <span class='v_custom_fields_more_info' id='more_info_price' title='<?php echo elgg_echo('vouchers:addvoucher:price:note'); ?>'></span>
    <br /><?php echo elgg_view('input/text', array('name' => 'price', 'value' => $price, 'class' => 'short')); ?>
    <?php echo $paypal_tip; ?>
    
	<div style="margin-top:20px;">
		<label><?php echo elgg_echo('vouchers:addvoucher:currency'); ?></label>:
		<?php echo elgg_view('input/dropdown', array('name' => 'currency', 'value' => $currency, 'options_values' => $CurrOptions)); ?> 
	</div>     
</div>

<?php if (check_if_use_userpoints_is_enabled()) { ?>
<div style="display:block; clear:both;">
    <label><?php echo elgg_echo('vouchers:addvoucher:points'); ?></label>:
    <span class='v_custom_fields_more_info' id='more_info_points' title='<?php echo elgg_echo('vouchers:addvoucher:points:note'); ?>'></span>
    <?php echo elgg_view('input/text', array('name' => 'points', 'value' => $points, 'class' => 'short')); ?>
</div>
<?php } ?>

<div style="display:block; clear:both;">
    <label><?php echo elgg_echo('vouchers:addvoucher:validfrom'); ?></label>: 
    <?php echo elgg_view('input/date', array('name' => 'valid_from', 'value' => $valid_from, 'class' => 'mediumshort')); ?> 
</div> 

<div style="display:block; clear:both;">
    <label><?php echo elgg_echo('vouchers:addvoucher:validuntil'); ?></label> <span style="color:red;">(*)</span>: 
    <?php echo elgg_view('input/date', array('name' => 'valid_until', 'value' => $valid_until, 'class' => 'mediumshort')); ?> 
</div>

<div style="display:block; clear:both;">
    <label><?php echo elgg_echo('vouchers:addvoucher:weburl'); ?></label> <span style="color:red;">(*)</span>
    <span class='v_custom_fields_more_info' id='more_info_weburl' title='<?php echo elgg_echo('vouchers:addvoucher:weburl:note'); ?>'></span>
    <br /><?php echo elgg_view('input/text', array('name' => 'weburl', 'value' => $weburl)); ?>
</div>

<div style="display:block; clear:both;">
    <label><?php echo elgg_echo('vouchers:addvoucher:zone'); ?></label> 
    <span class='v_custom_fields_more_info' id='more_info_zone' title='<?php echo elgg_echo('vouchers:addvoucher:zone:note'); ?>'></span>
    <br /><?php echo elgg_view('input/text', array('name' => 'zone', 'value' => $zone)); ?>
</div>

<div style="display:block; clear:both;"> 
    <label><?php echo elgg_echo('vouchers:addvoucher:excerpt'); ?></label> 
    <span class='v_custom_fields_more_info' id='more_info_excerpt' title='<?php echo elgg_echo('vouchers:addvoucher:excerpt:note'); ?>'></span>
    <br /><?php echo elgg_view('input/text', array('name' => 'excerpt', 'value' => $excerpt)); ?>
</div>

<div style="display:block; clear:both;">
    <label><?php echo elgg_echo('vouchers:addvoucher:description'); ?></label>
    <span class='v_custom_fields_more_info' id='more_info_description' title='<?php echo elgg_echo('vouchers:addvoucher:description:note'); ?>'></span>
    <?php echo elgg_view('input/longtext', array('name' => 'description', 'value' => $desc)); ?>
</div>

<div style="display:block; clear:both;">
    <label><?php echo elgg_echo('vouchers:addvoucher:terms'); ?></label>
    <span class='v_custom_fields_more_info' id='more_info_terms' title='<?php echo elgg_echo('vouchers:addvoucher:terms:note'); ?>'></span>
    <?php echo elgg_view('input/longtext', array('name' => 'terms', 'value' => $terms)); ?>
</div>

<div style="display:block; clear:both;">
    <label><?php echo elgg_echo('vouchers:addvoucher:image'); ?></label>
    <span class='v_custom_fields_more_info' id='more_info_image' title='<?php echo elgg_echo('vouchers:addvoucher:image:note'); ?>'></span><br />
    <?php echo elgg_view('input/file', array('name' => 'upload', 'class' => 'medium')); ?>
	<?php
	if ($guid) {
		echo '<div style="float:right;margin-top: 8px;">'.elgg_view('vouchers/thumbnail', array('voucher_guid' => $guid, 'size' => 'medium', 'tu' => time())).'</div>';
	}
	?>     
</div>

<div style="display:block; clear:both;">
    <label><?php echo elgg_echo('vouchers:addvoucher:tags'); ?></label>
    <?php echo elgg_view('input/tags', array('name' => 'tags', 'value' => $tags)); ?>
</div>

<div style="display:block; clear:both;">
    <label for="vouchers_comments_on"><?php echo elgg_echo('comments'); ?></label>
    <?php echo $comments_input; ?>
</div>

<div style="display:block; clear:both;">
    <label><?php echo elgg_echo('access'); ?></label><br />
    <?php echo elgg_view('input/access', array('name' => 'access_id', 'value' => $access_id)); ?>
</div>

<div class="elgg-foot">
<?php

    if ($guid) {
            echo elgg_view('input/hidden', array('name' => 'voucher_guid', 'value' => $guid));
    }
    echo elgg_view('input/hidden', array('name' => 'container_guid', 'value' => $container_guid));
    echo elgg_view('input/submit', array('value' => elgg_echo('vouchers:addvoucher:submit')));
?>
</div>
