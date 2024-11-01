<?php
/***
 * Plugin Name: Uwi Homes Widget
 * Description: Plugin for applying home loan via Uwi Homes - Widget
 * Author: UWI DEV TEAM
 * Author URI: https://www.uwi.com.ph/
 * Version: 1.0.0
 * Text Domain: uwi-homes-widget
 * 
 */

if (!defined('ABSPATH')) {
    echo 'Access Denied.';
    exit;
}

class UwiHomeWidget {

    public function __construct()
    {
        add_filter( 'plugin_action_links_uwi-homes-widget/uwihomes-widget.php', array($this, 'uwi_settings_link') );
        add_action('admin_init', array($this, 'uwi_register_settings'));
        add_action('admin_menu', array($this, 'uwi_add_api_settings_page'));
        add_action('wp_footer', array($this, 'uwi_load_widget'));
    }

    public function uwi_settings_link( $links ) {
        $url = esc_url( add_query_arg('page', 'uwi-settings', get_admin_url() . 'options-general.php') );
        $settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
        array_push($links, $settings_link);
        return $links;
    }

    public function uwi_add_api_settings_page()
    {
        add_options_page( 'UWI Homes Widget Settings', 'UWI Settings', 'manage_options', 'uwi-settings', array($this, 'uwi_render_plugin_settings_page'));
    }

    public function uwi_render_plugin_settings_page()
    {
        ?>
        <h2>UWI Plugin Options</h2>
        <form action="options.php" method="post">
            <?php 
                settings_fields( 'uwi_widget_plugin_options' );
                do_settings_sections( 'uwi_widget_plugin' );
            ?>
            <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
        </form>
        <?php
    }

    function uwi_register_settings() {
        register_setting( 'uwi_widget_plugin_options', 'uwi_widget_plugin_options', array($this, 'uwi_widget_plugin_options_validate'));
        add_settings_section( 'api_settings', 'API Settings', array($this, 'uwi_plugin_section_text'), 'uwi_widget_plugin' );
    
        add_settings_field( 'uwi_plugin_setting_api_key', 'API Key', array($this, 'uwi_plugin_setting_api_key'), 'uwi_widget_plugin', 'api_settings' );
    }

    function uwi_plugin_section_text() {
        echo '<p>Here you can set all the options for using the API</p>';
    }

    function uwi_plugin_setting_api_key() {
        $options = get_option( 'uwi_widget_plugin_options' );
        echo '<input id="uwi_plugin_setting_api_key" name="uwi_widget_plugin_options[api_key]" type="text" value="' . esc_attr( $options['api_key'] ?? '' ) . '" style="max-width: 600px; width: 100%;" />';
    }

    function uwi_widget_plugin_options_validate( $input ) {
        $newinput['api_key'] = trim( $input['api_key'] );
        /*
        if ( ! preg_match( '/^[a-z0-9]{100}$/i', $newinput['api_key'] ) ) {
            $newinput['api_key'] = '';
        }
        */
    
        return $newinput;
    }

    public function uwi_load_widget()
    {
        $options = get_option( 'uwi_widget_plugin_options' );
        if ($options['api_key']) {
            ?>
            <!-- Start of Uwi Widget code -->
            <script type="text/javascript">
            window.__UWI_WIDGET_CLIENT_ID__="<?php echo esc_attr( $options['api_key'] ); ?>";
            const script=document.createElement("script"),head=(script.type="text/javascript",script.defer="defer",script.async=!1,script.src="https://widget.uwi.com.ph/loader.js?ver="+Math.random(),script.onload=function(){},document.getElementsByTagName("head")[0]);head&&head.appendChild(script);
            </script>
            <!-- End of Uwi Widget code -->
            <?php 
        }
    }

}

new UwiHomeWidget;