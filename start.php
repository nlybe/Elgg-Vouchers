<?php
/**
 * Elgg vouchers plugin
 * @package Vouchers
 */

elgg_register_event_handler('init', 'system', 'vouchers_init');

define('VOUCHERS_NO_ACTIVE_VOUCHERS', 0);	// set default number of active vouchers per user in case that not defined in settings 
define('VOUCHERS_CODE_TYPE_SINGLE', 'code_single');	
define('VOUCHERS_CODE_TYPE_SERIES', 'code_series');	
define('VOUCHERS_CODE_TYPE_QR', 'code_qr');	
define('VOUCHERS_PURCHASE_METHOD_POINTS', 'Points');	// points method of purchase
	
/**
 * Vouchers plugin initialization functions.
 */
function vouchers_init() {
	
	// register a library of helper functions
    elgg_register_library('elgg:vouchers', elgg_get_plugins_path() . 'vouchers/lib/vouchers.php');
    
    // Register subtype
    run_function_once('vouchers_manager_run_once_subtypes');
                
    // Register entity_type for search
    elgg_register_entity_type('object', Voucher::SUBTYPE);
    
    // Site navigation
    $item = new ElggMenuItem('vouchers', elgg_echo('vouchers:menu'), 'vouchers/all');
    elgg_register_menu_item('site', $item); 
    
    // Extend CSS
    elgg_extend_view('css/elgg', 'vouchers/css');
    elgg_register_css('vouchers_tooltip_css', '//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css');
    
    // Extend js
    elgg_register_js('vouchersjs', elgg_get_site_url() . 'mod/vouchers/assets/vouchers.js');
    elgg_register_js('vouchers_tooltip_js', '//code.jquery.com/ui/1.11.1/jquery-ui.js'); 

    // Register a page handler, so we can have nice URLs
    elgg_register_page_handler('vouchers', 'vouchers_page_handler');
    
    // Register a URL handler for voucher
    $release = get_version(true);
	// Register a URL handler for agora
	if ($release < 1.9)  // version 1.8
		elgg_register_entity_url_handler('object', 'vouchers', 'voucher_url');
	else { // use this since Elgg 1.9
		elgg_register_plugin_hook_handler('entity:url', 'object', 'vouchers_set_url');
	}	    
    
    // Register menu item to an ownerblock
    elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'vouchers_owner_block_menu');
    
    // Register admin transaction log menu under "Utilities" 
    elgg_register_admin_menu_item('administer', 'vouchers_transactions_log', 'administer_utilities');
    
	// register plugin hooks
	elgg_register_plugin_hook_handler("public_pages", "walled_garden", "vouchers_walled_garden_hook");      

    // Register actions
    $action_path = elgg_get_plugins_path() . 'vouchers/actions';
    elgg_register_action("vouchers/addvoucher", "$action_path/addvoucher.php");    
    elgg_register_action("vouchers/delete", "$action_path/delvoucher.php");
    elgg_register_action("vouchers/set_featured", "$action_path/vouchers/set_featured.php"); // set a voucher post as featured
    elgg_register_action("vouchers/unset_featured", "$action_path/vouchers/unset_featured.php"); // unset a voucher post from featured
    elgg_register_action("vouchers/get_with_points", "$action_path/vouchers/get_with_points.php"); // voucher purchase with points only
    elgg_register_action('vouchers/be_interested', "$action_path/be_interested.php"); // send interest 
    elgg_register_action("vouchers/usersettings", "$action_path/usersettings.php");	// save user settings
    
    // extend group main page 
    elgg_extend_view('groups/tool_latest', 'vouchers/group_module');
    
    //elgg_extend_view("print", "printpreview/pageshell/pageshell");
    
    // add the group vouchers tool option
    add_group_tool_option('vouchers', elgg_echo('vouchers:group:enablevouchers'), true);     
    
    // Add vouchers widgets
	elgg_register_widget_type('vouchers', elgg_echo('vouchers:widget'), elgg_echo('vouchers:widget:description'), 'profile,groups,dashboard');  // member's voucher posts
	elgg_register_widget_type('vouchers_featured',	elgg_echo('vouchers:widget:featured'),	elgg_echo('vouchers:widget:featured:description'), 'dashboard');  // featured vouchers for dashboard
}

/**
 *  Dispatches vouchers pages.
 *
 * @param array $page
 * @return bool
 */

function vouchers_page_handler($page) {
    elgg_push_breadcrumb(elgg_echo('vouchers'), 'vouchers/all');

    // user usernames
    //$user = get_user_by_username($page[0]);
    //if ($user) {
    //        bookmarks_url_forwarder($page);
   // }
   
	$vars = array();
	$vars['page'] = $page[0];   

    $base = elgg_get_plugins_path() . 'vouchers/pages/vouchers';

    switch ($page[0]) {
       
        case "all":
			vouchers_register_toggle();
            include "$base/all.php";
            break;        

        case "owner":
			vouchers_register_toggle();
            include "$base/owner.php";
            break;

        case "friends":
			vouchers_register_toggle();
            include "$base/friends.php";
            break;
            
        case "mypurchases":
			vouchers_register_toggle();
            include "$base/mypurchases.php";
            break;            

        case "view":
            set_input('guid', $page[1]);
            include "$base/view.php";
            break;

        case "add":
            gatekeeper();
            include "$base/addvoucher.php";
            break;

        case "edit":
            gatekeeper();
            set_input('guid', $page[1]);
            include "$base/editvoucher.php";
            break;

        case "group":
            group_gatekeeper();
            include "$base/owner.php";
            break;
            
        default:
            include "$base/all.php";
            return false;
    }

    elgg_pop_context();
    return true;
}

/**
 * Populates the ->getUrl() method for voucher objects
 */
function voucher_url($entity) {
	global $CONFIG;

	$title = $entity->title;
	$title = elgg_get_friendly_title($title);
	return $CONFIG->url . "vouchers/view/" . $entity->getGUID() . "/" . $title;
}

/**
 * Add a menu item to an ownerblock
 * 
 */
function vouchers_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'user')) {
		$url = "vouchers/owner/{$params['entity']->username}";
		$item = new ElggMenuItem('vouchers', elgg_echo('vouchers'), $url);
		$return[] = $item;
	} else {
		if ($params['entity']->vouchers_enable != 'no') {
			$url = "vouchers/group/{$params['entity']->guid}/all";
			$item = new ElggMenuItem('vouchers', elgg_echo('vouchers:group'), $url);
			$return[] = $item;
		}
	}

	return $return;
}

/**
 * Adds a toggle to extra menu for switching between list and gallery views
 */
function vouchers_register_toggle() {
	$url = elgg_http_remove_url_query_element(current_page_url(), 'list_type');

	if (get_input('list_type', 'list') == 'list') {
		$list_type = "gallery";
		$icon = elgg_view_icon('grid');
	} else {
		$list_type = "list";
		$icon = elgg_view_icon('list');
	}

	if (substr_count($url, '?')) {
		$url .= "&list_type=" . $list_type;
	} else {
		$url .= "?list_type=" . $list_type;
	}


	elgg_register_menu_item('extras', array(
		'name' => 'vouchers_list',
		'text' => $icon,
		'href' => $url,
		'title' => elgg_echo("vouchers:list:$list_type"),
		'priority' => 1000,
	));
}

/**
 * Allow users to view marketplace even in walled garden
 *
 * @param string $hook
 * @param string $type
 * @param array $return_value
 * @param array $params
 * @return array
 */
function vouchers_walled_garden_hook($hook, $type, $return_value, $params){
	$add = array();
		
	if (is_array($return_value))
        $add = array_merge($add, $return_value);	
        
	return $add;
}

/**
 * Format and return the URL for vouchers objects, since 1.9.
 *
 * @param string $hook
 * @param string $type
 * @param string $url
 * @param array  $params
 * @return string URL of voucher object.
 */
function vouchers_set_url($hook, $type, $url, $params) {
	$entity = $params['entity'];
	if (elgg_instanceof($entity, 'object', 'vouchers')) {
		$friendly_title = elgg_get_friendly_title($entity->title);
		return "vouchers/view/{$entity->guid}/$friendly_title";
	}
}
