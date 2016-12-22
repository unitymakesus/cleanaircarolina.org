<?php

/**
 * Do not edit anything in this file unless you know what you're doing
 */

/**
 * Require Composer autoloader if installed on it's own
 */
if (file_exists($composer = __DIR__ . '/vendor/autoload.php')) {
    require_once $composer;
}

/**
 * Here's what's happening with these hooks:
 * 1. WordPress detects theme in themes/sage
 * 2. When we activate, we tell WordPress that the theme is actually in themes/sage/templates
 * 3. When we call get_template_directory() or get_template_directory_uri(), we point it back to themes/sage
 *
 * We do this so that the Template Hierarchy will look in themes/sage/templates for core WordPress themes
 * But functions.php, style.css, and index.php are all still located in themes/sage
 *
 * get_template_directory()   -> /srv/www/example.com/current/web/app/themes/sage
 * get_stylesheet_directory() -> /srv/www/example.com/current/web/app/themes/sage
 * locate_template()
 * ├── STYLESHEETPATH         -> /srv/www/example.com/current/web/app/themes/sage
 * └── TEMPLATEPATH           -> /srv/www/example.com/current/web/app/themes/sage/templates
 */
add_filter('template', function ($stylesheet) {
    return dirname($stylesheet);
});
add_action('after_switch_theme', function () {
    $stylesheet = get_option('template');
    if (basename($stylesheet) !== 'templates') {
        update_option('template', $stylesheet . '/templates');
    }
});

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
 * Special Titles Meta Box
 */
add_action( 'add_meta_boxes', 'cac_meta_boxes' );
function cac_meta_boxes() {
    add_meta_box( 'speaker-title',   __( 'Special Title',   'cleanaircarolina' ), 'cac_special_title', 'page', 'advanced', 'high' );
}

/**
 * Add Special Title to Pages
 */
function cac_special_title() {
    global $post;

    $special_title  = get_post_meta( $post->ID, 'cac_special_title', true );
    ?>

    <?php wp_nonce_field( 'cac_special_title_nonce', 'cac_special_title_meta' ); ?>
    <p>Used for homepage and other callout areas</p>
    <p>
        <input type="text" class="widefat" id="cac_special_title" name="cac_special_title" value="<?php echo esc_attr( $special_title ); ?>" />
    </p>

<?php
}

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