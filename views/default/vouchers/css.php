<?php
        $plugin_graphics_folder = elgg_get_site_url() . "mod/profile_manager/graphics/";
?>

div.vouchers-gallery-item {
    margin: 5px;
    text-align: center;
    display: table-cell; 
    vertical-align: top;
    padding: 5px 20px 40px;
    border: 0px solid #eaeaea;
}

div.gallery-view {
    margin: 1px 1px 20px 1px;
    height: 40px;
    min-height: 40px;
}

p.gallery-date	{
	margin: 1px;
	font-size:90%;
}

div.vbody {
    margin-top: 0px;
}

div.vbody div {
    margin: 0px 0 10px;
    padding: 4px;
    border: 1px solid #eaeaea;
    width: 98%;
    background-color: #f6f6f6;
}

div.vbody div.desc, .desc-voucher {
    border-top: 1px solid #eaeaea;
    border-left: 0;
    border-right: 0;
    border-bottom: 0;
    width: 100%;
    margin-bottom: 50px;
    display: block;
    float:left;
}

div.star {
    float:left;
    margin: 5px;
}

div.star_right {
    float:right;
    clear: both;
    margin: 5px;
}

div.print {
    float:right;
    margin-top: 10px;
}

a.print {
    padding:7px;
    border: 1px solid gray;
    background: #eeeeee;
    font-size: 110%;
}

a.print:hover {
    padding:7px;
    border: 1px solid #4690d6;
    background: #fafafa;
}

input.short {
    width: 15%;
    margin-right: 20px;
}

input.mediumshort {
    width: 25%;
    margin-right: 20px;
}

input.medium {
    width: 40%;
    margin-right: 20px;
}

.v_custom_fields_more_info {
        width: 16px;
        height: 16px;
        margin: 0 2px 0 5px;
        display: inline-block;
        vertical-align: top;
        background: transparent url(<?php echo elgg_get_site_url(); ?>_graphics/elgg_sprites.png) 0 -486px;
        cursor: pointer;
}

.v_custom_fields_more_info:hover {
        background-position: 0 -468px;
}

.custom_fields_more_info_text {
        display:none;
}

div.voucher_pm	{
	border: 0!important;
	text-align:left;
	margin-top: 10px;
}

div.notenoughpoints {
    border: 1px solid red;
    padding: 3px;
    border-radius: 3px;
    display:block;
    width:60%;
    float:right;
    text-align: center;
    background: #f4f4f4;
}

div.voucherbody {
    margin-top: 10px;
	display: block;
	float:left;
	width:100%;    
}

div.voucherbody div.desc {
    border-top: 1px solid #eaeaea;
    border-left: 0;
    border-right: 0;
    border-bottom: 0;
    width: 100%;
    margin-bottom: 50px;
	clear:both;

}

div.code_options ul 	{
	
}

div.code_options ul li	{
	display: inline;
	margin-right: 10px;
}

@media print { 
     .elgg-page-messages, .elgg-page-topbar, .elgg-page-footer, .set_button, .editbuttons, .elgg-search, .elgg-menu-site, .header_icons, .header_menu, .header_owner_block, .elgg-inner nav, .elgg-sidebar, .elgg-comments, .printbutton, .pm { 
      display:none !important; 
    } 
}

.printbutton {
    cursor: pointer;
}

div.vouchers_view #login-dropdown {
    background: none repeat scroll 0 0 #71b9f7;
    border-radius: 3px;
    position: relative;
    right: 0;
    top: 10px;
    z-index: 100;
    float:right;
    margin: 0 0 10px;
}

.lb-image, .voucher-image-popup{
max-width: inherit!important;
}
