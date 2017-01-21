<?php
// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
if (!defined('SLICKREMIX_STORE_URL')) {
    define('SLICKREMIX_STORE_URL', 'http://www.slickremix.com/'); // you should use your own CONSTANT name, and be sure to replace it throughout this file.
}
// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
//New Updater
include(dirname(__FILE__) . '/namespaced_updater_overrides.php');
/**
 * Feed Them Social Premium Plugin Updater
 *
 * Licensing and update code
 *
 * @since 1.5.6
 */
function feed_them_social_combined_streams_plugin_updater() {
    $plugin_identifier = 'feed_them_social_combined_streams';
    $item_name = 'Feed Them Social Combined Streams';
    $current_version = FTS_COMBINED_STREAMS_CURRENT_VERSION;
    $author = 'slickremix';
    // retrieve our license key from the DB
    $license_key = trim(get_option($plugin_identifier . '_license_key'));
    $store_url = SLICKREMIX_STORE_URL;
    //Build updater Array
    $plugin_details = array(
        'version' => $current_version,        // current version number
        'license' => $license_key,    // license key (used get_option above to retrieve from DB)
        'item_name' => $item_name,        // name of this plugin
        'author' => $author    // author of this plugin
    );
    // make sure FREE version is active.

    // setup the updater
    $edd_updater = new feed_them_social_combined_streams\SlickRemix_updater_overrides($store_url, __FILE__, $plugin_details, $plugin_identifier, $item_name);
    //Setup the activator
    $edd_update = new feed_them_social_combined_streams\EDD_SL_Plugin_Licence_Manager($plugin_identifier, $item_name, $store_url);
}

add_action('plugins_loaded', 'feed_them_social_combined_streams_plugin_updater');
?>