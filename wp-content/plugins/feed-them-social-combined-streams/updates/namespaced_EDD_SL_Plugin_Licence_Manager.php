<?php
namespace feed_them_social_combined_streams;
/***
 * Plugins Activation Manager
 *
 * @author SlickRemix
 * @version 1.6.5
 */
class EDD_SL_Plugin_Licence_Manager	{
    // static variables
    private static	$instance	= false;

    /**
    * EDD_SL_Plugin_Licence_Manager constructor.
    * @param $plugin_identifier
    * @param $item_name
    * @param $store_url
    *
    * @since 1.5.6
    */
    public function __construct($plugin_identifier, $item_name, $store_url){
        $this->plugin_identifier   = $plugin_identifier;
        $this->item_name		   = $item_name;
        $this->store_url		   = $store_url;
        $this->main_menu_slug = 'feed-them-settings-page';
        $this->license_page_slug = 'fts-license-page';

        $this->install();

    }
    /**
    * Install Updater
    *
    * @since 1.5.6
    */
    function install() {
        if ( !self::$instance ) {
            self::$instance = true;
              add_action('admin_menu', array($this, 'license_menu'));
        }
        add_action('admin_init', array($this, 'register_option'));
        add_action('admin_init', array($this, 'activate_license'));
        add_action('admin_init', array($this, 'deactivate_license'));
    }
    /**
    * Add Plugin License Menu
    *
    * @since 1.5.6
    */
    function license_menu() {
        global $submenu;
            if (isset($submenu[$this->main_menu_slug] ) && !in_array($this->license_page_slug, wp_list_pluck($submenu[$this->main_menu_slug], 2))) {
                add_submenu_page($this->main_menu_slug, __('Plugin License', 'feed-them-social'),  __('Plugin License', 'feed-them-social'), 'manage_options', $this->license_page_slug, array($this, 'license_page') );
            }
    }
    /**
    * Add Plugin License Page
    *
    * @since 1.5.6
    */
    function license_page() {
    ?>
        <div class="wrap">
                <h2><?php _e('Plugin License Options'); ?></h2><div class="license-note"> <?php _e("If you need more licenses or your key has expired, please go to the <a href='https://www.slickremix.com/my-account/' target='_blank'>MY ACCOUNT</a> page on our website to upgrade or renew your license.<br/>To get started follow the instructions below.", "feed-them-social") ?> </div>

        <div class="fts-activation-msg"><ol><li><?php _e('Enter your License Key and Click the Save Changes button.', 'feed-them-social') ?></li>
       <li><?php _e('Now Click the Activate License button.', 'feed-them-social') ?></li></ol></div>
                      <form method="post" action="options.php" class="fts-license-master-form">
                    <?php settings_fields($this->license_page_slug.'_license_manager_page'); ?>

                    <table class="form-table">
                        <tbody>
                            <?php  do_settings_fields($this->license_page_slug,'main_section');?>
                        </tbody>
                    </table>
                    <?php submit_button(); ?>
                </form>
    <?php
    }
    /**
    * Register Options for Plugin License page
    *
    * @since 1.5.6
    */
    function register_option() {
        // creates our settings in the options table
        register_setting($this->license_page_slug.'_license_manager_page',$this->plugin_identifier.'_license_key', array($this,'edd_sanitize_license') );
        add_settings_section('main_section', '', null, $this->license_page_slug);
        add_settings_field($this->plugin_identifier.'_license_key', '',  array($this,'registered_option'), $this->license_page_slug, 'main_section');
    }
    /**
     * Registered Options for Plugin License page
     *
     * @since 1.5.6
     */
    function registered_option()  {
                $license 	= get_option($this->plugin_identifier.'_license_key');
                $status 	= get_option($this->plugin_identifier.'_license_status');
                ?>
                <tr valign="top" class="fts-license-wrap">
                    <th scope="row" valign="top">
                        <?php _e($this->item_name); ?>
                    </th>
                    <td>
                        <input id="<?php echo $this->plugin_identifier?>_license_key" name="<?php echo $this->plugin_identifier?>_license_key" type="text" placeholder="<?php _e('Enter your license key'); ?>" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
                        <label class="description" for="<?php print $this->plugin_identifier?>_license_key"><?php if( $status !== false && $status == 'valid' ) { ?>
                              <?php wp_nonce_field('license_page_nonce','license_page_nonce'); ?>
                              <input type="submit" class="button-secondary" name="<?php echo $this->plugin_identifier?>_edd_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>

                              <div class="edd-license-data"><p><?php _e('License Key Active.'); ?></p></div>

                          <?php
                                } else {
                              wp_nonce_field('license_page_nonce','license_page_nonce'); ?>
                              <input type="submit" class="button-secondary" name="<?php echo $this->plugin_identifier?>_edd_license_activate"  value="<?php _e('Activate License'); ?>"/>
                              <div class="edd-license-data edd-license-msg-error"><p><?php $this->update_admin_notices(); _e('To receive updates notifications, please enter your valid license key.'); ?></p></div>
                          <?php } ?></label>
                    </td>
                </tr>
            <?php
    }
    /**
    * Sanatize License Keys
    *
    * @param $new
    * @return mixed
    * @since 1.5.6
    */
    function edd_sanitize_license( $new ) {
        $old = get_option( $this->plugin_identifier.'_license_key' );
        if( $old && $old != $new ) {
            delete_option( $this->plugin_identifier.'_license_status' ); // new license has been entered, so must reactivate
        }
        return $new;
    }
    /**
    * Activate License Key
    *
    * @return bool
    * @since 1.5.6
    */
    function activate_license() {
        // listen for our activate button to be clicked
        if( isset( $_POST[$this->plugin_identifier.'_edd_license_activate'] ) ) {


		// retrieve the license from the database
		$license = trim( get_option($this->plugin_identifier.'_license_key') );


            // data to send in our API request
            $api_params = array(
                'edd_action'=> 'activate_license',
                'license' 	=> $license,
                'item_name' => urlencode($this->item_name), // the name of our product in EDD
                'url'       => home_url(),
            );

            // Call the custom API.
            $response = wp_remote_post( $this->store_url, array( 'timeout' => 60, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :

						$message = sprintf(
							__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'revoked' :

						$message = __( 'Your license key has been disabled.' );
						break;

					case 'missing' :

						$message = __( 'Invalid license.' );
						break;

					case 'invalid' :
					case 'site_inactive' :

						$message = __( 'Your license is not active for this URL.' );
						break;

					case 'item_name_mismatch' :

						$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), $this->item_name );
						break;

					case 'no_activations_left':

						$message = __( 'Your license key has reached its activation limit.' );
						break;

					default :

						$message = __( 'An error occurred, please try again.' );
						break;
				}

			}

		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'admin.php?page=' . $this->license_page_slug );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		update_option( $this->plugin_identifier.'_license_status', $license_data->license );
		//CHANGED to admin.php
		wp_redirect( admin_url( 'admin.php?page=' . $this->license_page_slug ) );
		exit();
	}
}


/***********************************************
* Illustrates how to deactivate a license key.
* This will decrease the site count
***********************************************/

function deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST[$this->plugin_identifier.'_edd_license_deactivate'] ) ) {


		// retrieve the license from the database
		$license = trim( get_option( $this->plugin_identifier.'_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( $this->store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

			//CHANGED to admin.php
			$base_url = admin_url( 'admin.php?page=' . $this->license_page_slug );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' ) {
			delete_option( $this->plugin_identifier.'_license_status');
		}

		//CHANGED to admin.php
		wp_redirect( admin_url( 'admin.php?page=' . $this->license_page_slug ) );
		exit();

        }
    }
    /**
     * This is a means of catching errors from the activation method above and displaying it to the customer
     */
    function update_admin_notices() {
        if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

            switch( $_GET['sl_activation'] ) {

                case 'false':
                    $message = urldecode( $_GET['message'] );
                    ?>

                    <?php echo $message; ?>

                    <?php
                    break;

                case 'true':
                default:
                    // Developers can put a custom success message here for when activation is successful if they want.
                    break;

            }
        }
    }

}//END Slick_Licence_Manager
?>