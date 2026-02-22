<?php
/**
 * Uninstall Copy Delete Posts Premium
 * @since 1.5.1
 */
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}


$g = get_option('_cdp_globals', array());
if (isset($g['others'])) {
    $g = $g['others'];
} else {
    $g = cdp_default_global_options();
}
if (!isset($g['cdp-delete-on-uninstall']) || $g['cdp-delete-on-uninstall'] != 'true') {
    return;
}

global $wpdb;
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_cdp_%' OR option_name LIKE '__cdp_%' OR option_name LIKE '_cdpp_%'");
