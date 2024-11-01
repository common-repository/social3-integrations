<?php
/*
Plugin Name: Social3: Free Website and Social Media Marketing Tools
Version: 0.5.3
Description: Tools to automate integration your site with Social3.io
Author: Social3
Author URI: https://social3.io/
*/

$S3_BASE_PATH       = plugin_dir_path(__FILE__);
$S3_TEMPLATES_PATH  = $S3_BASE_PATH . 'templates/';
$S3_ASSETS_PATH     = plugin_dir_url( __FILE__ ) . 'assets/';

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( ! function_exists('write_log')) {
	function write_log ( $log )  {
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}
}

require 'includes/notices.php';
require 'includes/connector.php';

require 'includes/shares.php';
require 'includes/list-builder.php';

class S3_Integration
{
    /** @var  S3_Connector */
    protected $connector;

    /** @var  S3_AdminNotice */
    protected $notice;

    public function __construct()
    {
        $this->notice = S3_AdminNotice::getInstance();

        $this->connector = new S3_Connector($this->notice);

        new S3_Share_Bar($this->connector, $this->notice);
        new S3_List_Builder($this->connector, $this->notice);

        add_action( 'admin_notices', array($this->notice, 'displayAdminNotice') );
        add_action( 'admin_menu', array($this, 'add_menu_page') );
        add_action( 'plugins_loaded', array($this, 'load_domain') );

        if ($this->connector->is_enabled_auto_integration() && !is_admin()) {
            if ((boolean)get_option('s3_integration_in_footer')) {
                add_action( 'wp_footer', array($this, 'integration_inline_script'), 999 );
            } else {
                add_action( 'wp_head', array($this, 'integration_inline_script'), 999 );
            }
        }

        $plugin = plugin_basename(__FILE__);
        add_filter('plugin_action_links_'.$plugin, array($this, 'plugin_settings_link'));
    }

    public function plugin_settings_link($links)
    {
        $settings_link = '<a href="admin.php?page=s3-connector-options">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function add_menu_page()
    {
        add_menu_page(__('Social3', 'social3'), __('Social3', 'social3'), 'manage_options', 's3_main_slug');

        do_action('s3_add_admin_menu', 's3_main_slug');
    }

    public function load_domain()
    {
        load_plugin_textdomain( 'social3', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    public function integration_inline_script()
    {
        if ($script = get_option('s3_integration_script')) {
            echo $script;
        }
    }
}

$S3_Integration = new S3_Integration();
