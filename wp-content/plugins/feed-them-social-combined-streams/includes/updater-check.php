<?php
/**
 * Feed Them Social Premium Plugin Updater
 *
 * Licensing and update code.
 *
 * @since 1.5.6
 */
function feed_them_social_premium_plugin_updater()
{
    $plugin_identifier = 'feed_them_social_premium';
    $item_name = 'Feed Them Social Premium';
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
    $freeVersionActive = is_plugin_active('feed-them-social/feed-them.php');

    if ($freeVersionActive) {
        if (class_exists('feedthemsocial\EDD_SL_Plugin_Updater') && class_exists('feedthemsocial\EDD_SL_Plugin_Licence_Manager')) {
            // setup the updater
            $edd_updater = new feedthemsocial\EDD_SL_Plugin_Updater($store_url, __FILE__, $plugin_details);
            //Setup the activator
            $edd_update = new feedthemsocial\EDD_SL_Plugin_Licence_Manager($plugin_identifier, $item_name, $store_url);
        } else {
            // setup the updater
            $edd_updater = new EDD_SL_Plugin_Updater($store_url, __FILE__, $plugin_details);
            //Setup the activator
            $edd_update = new EDD_SL_Plugin_Licence_Manager($plugin_identifier, $item_name, $store_url);
        }
    } else {


        // Adds Admin notice because no EDD_SL_Plugin_Updater or EDD_SL_Plugin_Licence_Manager classes were found because Main Plugin is not current version or is not active
        $main_plugin_class_failed = function () {
            // Deactivate Plugin
            deactivate_plugins(plugin_basename(__FILE__));
            echo '<div class="error"><p>' . __('Warning: The <strong>Feed Them Social Premium</strong> extension has been deactivated because it needs the <a href="plugin-install.php?tab=search&s=Feed+Them+Social&plugin-search-input=Search+Plugins"><strong>Feed Them Social</strong></a> (Free version) plugin to be <strong>INSTALLED</strong> and <strong>ACTIVATED</strong> to function properly.', 'feed-them-social') . '</p></div>';
        };
        add_action('admin_notices', $main_plugin_class_failed);
    }
}

add_action('plugins_loaded', 'feed_them_social_premium_plugin_updater');
?>