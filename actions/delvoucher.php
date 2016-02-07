<?php
/**
 * Elgg vouchers plugin
 *
 * @package Vouchers
 */

// chech if user is loggedin
if (!elgg_is_logged_in()) forward();

$guid = get_input('guid');
$voucher = get_entity($guid);

if (elgg_instanceof($voucher, 'object', 'vouchers') && $voucher->canEdit()) {
    $container = $voucher->getContainerEntity();
    if ($voucher->delete()) {
        system_message(elgg_echo("vouchers:delete:success"));
        if (elgg_instanceof($container, 'group')) {
                forward("vouchers/group/$container->guid/all");
        } else {
                forward("vouchers/owner/$container->username");
        }
    }
}

register_error(elgg_echo("vouchers:delete:failed"));
forward(REFERER);
