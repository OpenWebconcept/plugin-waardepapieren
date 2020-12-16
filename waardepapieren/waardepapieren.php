<?php

/**
 * Waardepapieren
 *
 * @package           WaardenpapierenPlugin
 * @author            Conduction
 * @copyright         2020 Conduction
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Waardepapieren
 * Plugin URI:        https://conduction.nl/waardepapieren
 * Description:       De waardepapieren plugin
 * Version:           1.0.6
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Conduction
 * Author URI:        https://conduction.nl
 * Text Domain:       plugin-slug
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

/*
 * Lets define the basic settings page
 */
function waardepapieren_options_page_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "wporg_options"
            settings_fields('waardepapieren_options' );
            // output setting sections and their fields
            // (sections are registered for "wporg", each field is registered to a specific section)
            do_settings_sections( 'waardepapieren_api' );
            // output save settings button
            submit_button( __( 'Save Settings', 'textdomain' ) );
            ?>
        </form>
    </div>
    <?php
}

/*
 * The settings menu item
 */
function waardepapieren_options_page() {

    add_submenu_page(
        'options-general.php',
        'Waardepapieren',
        'Waardepapieren',
        'manage_options',
        'waardepapieren',
        'waardepapieren_options_page_html'
    );
}

// Adding the menu item to the menu
add_action( 'admin_menu', 'waardepapieren_options_page' );

/*
 * Lets define some settings
 */
function wporg_settings_init() {
    // register a new setting for "reading" page
    register_setting('waardepapieren_options', 'waardepapieren_api_endpoint');
    register_setting('waardepapieren_options', 'waardepapieren_api_key');
    register_setting('waardepapieren_options', 'waardepapieren_organization');

    // register a new section in the "reading" page
    add_settings_section(
        'default', // id
        'API  Configuration', // title
        'wporg_settings_section_callback', // callback
        'waardepapieren_api' // page
    );

    // register a new field in the "wporg_settings_section" section, inside the "reading" page
    add_settings_field(
        'waardepapieren_api_endpoint_field', // id
        'API Endpoint',  // title
        'waardepapieren_api_endpoint_field_callback', //callback
        'waardepapieren_api',
        'default'
    );

    // register a new field in the "wporg_settings_section" section, inside the "reading" page
    add_settings_field(
        'waardepapieren_api_key_field',
        'API  KEY',
        'waardepapieren_api_key_field_callback',
        'waardepapieren_api',
        'default'
    );

    // register a new field in the "wporg_settings_section" section, inside the "reading" page

    add_settings_field(
        'waardepapieren_organization_field',
        'Organization',
        'waardepapieren_organization_field_callback',
        'waardepapieren_api',
        'default'
    );

}

/**
 * register wporg_settings_init to the admin_init action hook
 */
add_action('admin_init', 'wporg_settings_init');

/**
 * callback functions
 */

// section content cb
function wporg_settings_section_callback() {
    echo '<p>In order to use the waardenpapieren api you wil need to provide api credentials.</p>';
}

// field content cb
function waardepapieren_api_endpoint_field_callback() {
    // get the value of the setting we've registered with register_setting()
    $setting = get_option('waardepapieren_api_endpoint');
    // output the field
    ?>
    <input type="text" name="waardepapieren_api_endpoint" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
    <?php
}

function waardepapieren_api_key_field_callback() {
    // get the value of the setting we've registered with register_setting()
    $setting = get_option('waardepapieren_api_key');
    // output the field
    ?>
    <input type="text" name="waardepapieren_api_key" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
    <?php
}

function waardepapieren_organization_field_callback() {
    // get the value of the setting we've registered with register_setting()
    $setting = get_option('waardepapieren_organization');
    // output the field
    ?>
    <input type="text" name="waardepapieren_organization" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
    <?php
}

/**
 * The Waardepapieren short code (for example purposes)
 */
function waardepapieren_form_shortcode() {
    // do something to $content
    // always return
    return file_get_contents (plugin_dir_url(__FILE__) . 'public/form.php');
}
add_shortcode('waardepapieren-form', 'waardepapieren_form_shortcode');

function waardepapieren_list_shortcode() {
    // do something to $content
    // always return
    $url = esc_url( admin_url('admin-post.php'));
    $formtag = "<form action=\"".$url."\" method=\"post\">";
    return $formtag.file_get_contents (plugin_dir_url(__FILE__) . 'public/list.php');
}
add_shortcode('waardepapieren-list', 'waardepapieren_list_shortcode');


/**
 * Catching the custom post
 */

function waardepapieren_post() {
    $organization = get_option('waardepapieren_organization');
    $key = get_option('waardepapieren_api_key');
    $endpoint = get_option('waardepapieren_api_endpoint');
    //var_dump($organization);
    //var_dump($key);
    //var_dump($endpoint);
    //var_dump($_POST);

    $post = ["person"=>$_POST["bsn"],"type"=>$_POST["type"],"organization"=>$organization];

    $data = wp_remote_post($endpoint, array(
        'headers'     => array('Content-Type' => 'application/json; charset=utf-8', 'Authorization' => $key),
        'body'        => json_encode($post),
        'method'      => 'POST',
        'data_format' => 'body',
    ));

    $body     = json_decode(wp_remote_retrieve_body( $data ), true) ;

    if($_POST["format"]=="png"){
        header("Cache-Control: public"); // needed for internet explorer
        header("Content-Type: image/png");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Disposition: attachment; filename=claim_".$body["id"].".png");
        $image = explode(",",$body['image']);
        echo base64_decode ($image[1]);
        die;
    }
    else{
        header("Cache-Control: public"); // needed for internet explorer
        header("Content-Type: application/pdf");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Disposition: attachment; filename=claim_".$body["id"].".pdf");
        $document = explode(",",$body['document']);
        echo base64_decode ($document[1]);
        die;
    }
    var_dump($body);
    die;
}

add_action( 'admin_post_nopriv_waardepapieren_form', 'waardepapieren_post' );
add_action( 'admin_post_waardepapieren_form', 'waardepapieren_post' );