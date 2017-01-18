<?php

/**
 * Do not edit anything in this file unless you know what you're doing
 */

/**
 * Helper function for prettying up errors
 * @param string $message
 * @param string $subtitle
 * @param string $title
 */
$sage_error = function ($message, $subtitle = '', $title = '') {
    $title = $title ?: __('Sage &rsaquo; Error', 'sage');
    $footer = '<a href="https://roots.io/sage/docs/">roots.io/sage/docs/</a>';
    $message = "<h1>{$title}<br><small>{$subtitle}</small></h1><p>{$message}</p><p>{$footer}</p>";
    wp_die($message, $title);
};

/**
 * Ensure compatible version of PHP is used
 */
if (version_compare('5.6.4', phpversion(), '>=')) {
    $sage_error(__('You must be using PHP 5.6.4 or greater.', 'sage'), __('Invalid PHP version', 'sage'));
}

/**
 * Ensure dependencies are loaded
 */
if (!file_exists($composer = __DIR__.'/vendor/autoload.php') && !class_exists('Roots\\Sage\\Container')) {
    $sage_error(
        __('You must run <code>composer install</code> from the Sage directory.', 'sage'),
        __('Autoloader not found.', 'sage')
    );
}
require_once $composer;

/**
 * Sage required files
 *
 * The mapped array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 */
array_map(function ($file) use ($sage_error) {
    $file = "src/{$file}.php";
    if (!locate_template($file, true, true)) {
        $sage_error(sprintf(__('Error locating <code>%s</code> for inclusion.', 'sage'), $file), 'File not found');
    }
}, ['helpers', 'setup', 'filters', 'admin']);

/**
 * Here's what's happening with these hooks:
 * 1. WordPress initially detects theme in themes/sage
 * 2. Upon activation, we tell WordPress that the theme is actually in themes/sage/templates
 * 3. When we call get_template_directory() or get_template_directory_uri(), we point it back to themes/sage
 *
 * We do this so that the Template Hierarchy will look in themes/sage/templates for core WordPress themes
 * But functions.php, style.css, and index.php are all still located in themes/sage
 *
 * This is not compatible with the WordPress Customizer theme preview prior to theme activation
 *
 * get_template_directory()   -> /srv/www/example.com/current/web/app/themes/sage
 * get_stylesheet_directory() -> /srv/www/example.com/current/web/app/themes/sage
 * locate_template()
 * ├── STYLESHEETPATH         -> /srv/www/example.com/current/web/app/themes/sage
 * └── TEMPLATEPATH           -> /srv/www/example.com/current/web/app/themes/sage/templates
 */
if (is_customize_preview() && isset($_GET['theme'])) {
    $sage_error(__('Theme must be activated prior to using the customizer.', 'sage'));
}
add_filter('template', function ($stylesheet) {
    $this_theme = wp_get_theme();
    return $this_theme['Template'];
    // return dirname($stylesheet);
});
if (basename($stylesheet = get_option('template')) !== 'templates') {
    $theme_dir = get_option('stylesheet');
    update_option('template', "{$theme_dir}/templates");
    // update_option('template', "{$stylesheet}/templates");
    wp_redirect($_SERVER['REQUEST_URI']);
    exit();
}


/**
 * Activate ET Divi Magic
 */
require_once(get_template_directory() . '/functions.php');


/**
 * Let's make the pages better
 */
add_action( 'init', 'cac_extend_page_functionality' );
function cac_extend_page_functionality() {

    //Add Excerpt
    add_post_type_support( 'page', 'excerpt' );

    //Add Categories and Tags
    register_taxonomy_for_object_type( 'post_tag', 'page' );
    register_taxonomy_for_object_type( 'category', 'page' );

    //Hook
    add_action( 'save_post', 'cac_post_page', 10, 2 );
}


/**
 * Donate Button Shortcode
 */
add_shortcode( 'donate_button', 'donate_button_shortcode' );
function donate_button_shortcode() {
    return <<<HTML
<a href="#donate" class="btn btn-special">
    Donate
</a>
HTML;
}

/**
 * Inline SVG Shortcode
 */
add_shortcode( 'inline_svg', 'inline_svg_shortcode' );
function inline_svg_shortcode($attr) {

    if( empty( $attr['name'] ) )
        return null;

    $image_asset    = get_template_directory() . '/assets/images/svg/';
    $svg_path       = $image_asset . $attr['name'] . '.svg';
    $svg            = file_exists( $svg_path ) ? file_get_contents( $svg_path ) : null;

    return $svg;
}

/**
 * MailChimp Newsletter Form Shortcode
 */
add_shortcode( 'newsletter_signup', 'newsletter_signup_shortcode' );
function newsletter_signup_shortcode() {

    return <<<HTML
<!-- Begin MailChimp Signup Form -->
<div id="mc_embed_signup" class="mailchimp-signup">
    <form action="//acodesmith.us13.list-manage.com/subscribe/post?u=f771a5bf552cd475fc41b7d14&amp;id=XXXXXXX" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
        <div id="mc_embed_signup_scroll">
            <div class="mc-field-group">
                <input type="email" value="" placeholder="Email Address" name="EMAIL" class="required email" id="mce-EMAIL"><input type="submit" value="Sign Up" name="subscribe" id="mc-embedded-subscribe" class="button">
            </div>
            <div style="position: absolute; left: -5000px;" aria-hidden="true">
                <input type="text" name="b_f771a5bf552cd475fc41b7d14_c193ce9772" tabindex="-1" value="">
            </div>
        </div>
    </form>
</div>
<!--End mc_embed_signup-->
HTML;

}

/**
 * Enqueue scripts and styles based on certain pages or other queries.
 */
function scripts_per_page() {

    if ( is_page('home') ) {

    }
}
add_action( 'wp_enqueue_scripts', 'scripts_per_page' );

/**
 * Hook into the save action for pages.
 * Save the Special Title
 *
 * @param $post_id
 * @param $post
 */
function cac_post_page( $post_id, $post ) {

    if ( wp_is_post_revision( $post_id ) || $post->post_type != 'page' || ! current_user_can( 'edit_post', $post_id ) )
        return;

    if ( isset( $_POST['cac_special_title'] ) && wp_verify_nonce( $_POST['cac_special_title_meta'], 'cac_special_title_nonce' ) ) {

        $special_title = sanitize_text_field( $_POST['cac_special_title'] );

        if ( ! $special_title )
            delete_post_meta( $post_id, 'cac_special_title' );
        else
            update_post_meta( $post_id, 'cac_special_title', $special_title );
    }
}



/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 */
$sage_includes = [
    'src/helpers.php',
    'src/setup.php',
    'src/filters.php',
    'src/admin.php'
];
array_walk($sage_includes, function ($file) {
    if (!locate_template($file, true, true)) {
        trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
    }
});

/**
 * Enqueue scripts for Divi builder on init
 *
 */
// if (function_exists('\et_builder_load_modules_styles')) {
  // add_action( 'init', '\et_builder_load_modules_styles', 11 );
// }
