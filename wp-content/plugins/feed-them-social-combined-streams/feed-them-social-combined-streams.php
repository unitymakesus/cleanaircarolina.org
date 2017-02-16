<?php 
/*
Plugin Name: Feed Them Social Combined Streams
Plugin URI: http://slickremix.com/
Description: This is the Combined Streams Extension for the Free version of Feed Them Social.
Version: 1.0.3
Author: SlickRemix
Author URI: http://slickremix.com/
Requires at least: wordpress 4.5
Requires at Feed Them Social: 2.1.0
Tested up to: wordpress 4.7.2
Stable tag: 1.0.3

 * @package    	Feed Them Social Combined Streams
 * @category   	Core 
 * @author		SlickRemix
 * @copyright  	Copyright (c) 2012-2017 SlickRemix

Need Support? http://www.slickremix.com/support-forum/
*/

define('FTS_COMBINED_STREAMS_CURRENT_VERSION', '1.0.3'); // Affects updater files.

if (!function_exists('is_plugin_active'))
    require_once(ABSPATH . '/wp-admin/includes/plugin.php');
// Makes sure the plugin is defined before trying to use it

// Make sure php version is greater than 5.3
if (function_exists('phpversion'))

    $phpversion = phpversion();
$phpcheck = '5.2.9';

/**
 * FTS Combined Streams Version
 *
 * @return mixed
 * @since 1.5.6
 */
function fts_combined_streams_version() {
    $plugin = 'feed-them-social/feed-them.php';
    if (is_plugin_active($plugin)) {
        $plugins = get_plugins();
        $plugin_data = $plugins[$plugin];
        // print_r($plugin_data);
        $plugin_version = $plugin_data['Version'];
        return $plugin_version;
    }
}

$ftsFreeVersion = fts_combined_streams_version();
$ftsRequiredVersion = '1.8.3';


// First check the free version is active
if (!is_plugin_active('feed-them-social/feed-them.php') && !isset($single_feed_active)) {
    deactivate_plugins('feed-them-social-combined-streams/feed-them-social-combined-streams.php');
    add_action('admin_notices', 'fts_required_php_check2');

    /**
     * FTS Required php check 2
     *
     * @since 1.5.6
     */
    function fts_required_php_check2() {
        echo '<div class="error"><p>' . __('Warning: <strong>Feed Them Social Combined Streams</strong> has been deactivated because it needs one of the following plugins <a href="plugin-install.php?tab=search&s=Feed+Them+Social&plugin-search-input=Search+Plugins"><strong>Feed Them Social</strong></a> (Free version) to be <strong>INSTALLED</strong> and <strong>ACTIVATED</strong> to function properly.', 'feed-them-social-combined-streams') . '</p></div>';
    }
} // Second check the free version is not less than 1.8.3
elseif ($ftsFreeVersion < $ftsRequiredVersion && !isset($single_feed_active)) {

    deactivate_plugins('feed-them-social-combined-streams/feed-them-social-combined-streams.php');
    add_action('admin_notices', 'fts_required_php_check1');
    /**
     * FTS Required php check 1
     *
     * @since 1.5.6
     */
    function fts_required_php_check1() {
        echo '<div class="error"><p>' . __('Warning: You will need to upgrade the FREE Version of FTS before you can activate this New Premium Version.', 'feed-them-social-combined-streams') . '</p></div>';
    }
} // Third check the php version is not less than 5.2.9
elseif ($phpversion < $phpcheck) {

    deactivate_plugins('feed-them-social-combined-streams/feed-them-social-combined-streams.php');
    add_action('admin_notices', 'fts_required_php_check1');
    /**
     * FTS Required php check1
     *
     * @since 1.5.6
     */
    function fts_required_php_check1() {
        echo '<div class="error"><p>' . __('Warning: Your version of php must be 5.3 or greater to use this plugin. Please upgrade the php by contacting your host provider. Some host providers will allow you to change this yourself in the hosting control panel too.', 'feed-them-social-combined-streams') . '</p></div>';
    }
} else {

    add_action('plugins_loaded', 'fts_combined_streams_includes');
    /**
     * FTS Combined Streams Includes
     *
     * @since 1.5.6
     */
    function fts_combined_streams_includes() {
        $fts_plugin_rel_url = plugin_dir_path(__FILE__);
								include_once($fts_plugin_rel_url . 'feeds/mashup/mashup-feed.php');
    }

    $fts_plugin_rel_url = plugin_dir_path(__FILE__);
    include_once($fts_plugin_rel_url . 'updates/namespaced_EDD_SL_Plugin_Licence_Manager.php');
    include_once($fts_plugin_rel_url . 'updates/updater-check.php');

    /**
     * FTS Combined Streams Action Init
     *
     * @since 1.5.6
     */
    function fts_combined_streams_action_init() {
        // Localization
        load_plugin_textdomain('feed-them-social-combined-streams', false, basename(dirname(__FILE__)) . '/languages');
    }

    // Add actions
    add_action('init', 'fts_combined_streams_action_init');
    // Include our own Settings link to plugin activation and update page.
    add_filter("plugin_action_links_" . plugin_basename(__FILE__), "fts_plugin_actions_combined_feeds", 10, 4);
    /**
     * FTS Plugin Actions
     *
     * @param $actions
     * @param $plugin_file
     * @param $plugin_data
     * @param $context
     * @return mixed
     * @since 1.5.6
     */
    function fts_plugin_actions_combined_feeds($actions, $plugin_file, $plugin_data, $context) {
        array_unshift(
            $actions, "<a href=\"" . __('http://www.slickremix.com/support-forum/forum/feed-them-social-2/') . "\">" . __("Support") . "</a> | <a href=\"" . menu_page_url('feed-them-settings-page', false) . "\">" . __("Settings") . "</a>"

        );
        return $actions;
    }
}
// Include Leave feedback, Get support and Plugin info links to plugin activation and update page.
add_filter("plugin_row_meta", "fts_add_leave_feedback_link_combined_feeds", 10, 2);

/**
 * FTS Add Leave Feedback Link
 *
 * @param $links
 * @param $file
 * @return mixed
 * @since 1.5.6
 */
function fts_add_leave_feedback_link_combined_feeds($links, $file) {
    if ($file === plugin_basename(__FILE__)) {
        $links['feedback'] = '<a href="http://wordpress.org/support/view/plugin-reviews/feed-them-social" target="_blank">' . __('Rate Plugin', 'feed-them-social-combined-streams') . '</a>';
        // $links['support'] = '<a href="http://www.slickremix.com/support-forum/forum/feed-them-social-2/" target="_blank">' . __('Get support', 'feed-them-social-combined-streams') . '</a>';
        $links['plugininfo'] = '<a href="plugin-install.php?tab=plugin-information&plugin=feed-them-social-combined-streams&section=changelog&TB_iframe=true&width=640&height=423" class="thickbox">' . __('Plugin info', 'gd_quicksetup') . '</a>';
    }
    return $links;
}

?>