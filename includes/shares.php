<?php

/**
 * Created by PhpStorm.
 * User: dvasilevskiy
 * Date: 08.12.17
 * Time: 12:37
 */

require 'shares/shares-table.php';

class S3_Share_Bar
{
    /** @var  S3_AdminNotice */
    private $notice;

    /** @var  S3_Connector */
    private $connector;

    private $screen;

    public function __construct($connector, $notice)
    {
        global $S3_TEMPLATES_PATH;

        $this->notice        = $notice;
        $this->connector     = $connector;
        $this->template_path = $S3_TEMPLATES_PATH;

        add_action( 's3_add_admin_menu', array($this, 'add_options_page') );
        add_filter( 'set-screen-option', array($this, 'shares_table_set_option'), 10, 3);
        add_action( 'init', array($this, 'handle_share_bar_save') );

//        add_action( 'wp_ajax_delete_share_account', array($this, 'ajax_delete_share_account') );
        add_action( 'wp_ajax_status_share_account', array($this, 'ajax_status_share_account') );
    }

    public function add_options_page($main_slug)
    {
        $this->screen = add_submenu_page( $main_slug, __('Share Accounts', 'social3'), __('Share Accounts', 'social3'), 'manage_options', $main_slug, array($this, 'render_share_bar_list_page') );
        add_submenu_page('any', 'Share Account', 'Share Account', 'manage_options', 's3_menu_share_bar', array($this, 'render_share_bar_page'));
        add_action( "load-$this->screen", array($this, 'add_options') );
    }

    public function shares_table_set_option($status, $option, $value)
    {
        update_user_option(get_current_user_id(), $option, $value);
        return $value;
    }

    public function add_options()
    {
        $option = 'per_page';
        $args = array(
            'label' => __('Number of items per page:', 'social3'),
            'default' => 10,
            'option' => 's3_share_bars_per_page'
        );
        add_screen_option( $option, $args );
    }

    public function render_share_bar_list_page()
    {
        //Create an instance of our package class...
        $sharesListTable = new Share_Bars_Table( $this->connector, $this->screen, $this->notice );
        //Fetch, prepare, sort, and filter our data...
        $sharesListTable->prepare_items();

        require $this->template_path . 'shares/table-page.php';
    }

    public function render_share_bar_page()
    {
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['account_id']) && $_REQUEST['account_id'] > 0) {
            $result = $this->connector->do_request('/share/account/get/'. $_REQUEST['account_id']);
            $account = $result->account;
            $script  = $result->script;
        } elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'new') {
            $account = isset($_POST['share_bar']) ? (object)$_POST['share_bar'] : new stdClass();
            $script  = false;
            $brand   = $this->connector->get_brand();

            $account->site_id  = $brand->site_id;
            $account->brand_id = $brand->brand_id;

            foreach ($account->share_types as $index=>$type) {
                $account->share_types[$index] = (object)array('id' => $type);
            }
        } else {
            $redirect = admin_url('/admin.php?page=s3_main_slug');
        }

        $auto_integration = $this->connector->is_enabled_auto_integration();
        $types = $this->connector->do_request('/share/types');
        require $this->template_path . 'shares/share-bar-page.php';
    }

    public function is_checked_type($current_type_id, $types)
    {
        foreach ($types as $type) {
            if ($type->id == $current_type_id) {
                return true;
            }
        }

        return false;
    }

    public function handle_share_bar_save()
    {
        if (!(isset($_POST['s3_share_bar_action_save_nonce']) && wp_verify_nonce($_POST['s3_share_bar_action_save_nonce'], 's3_share_bar_action_save'))) {
            return;
        }

        $data = isset($_POST['share_bar']) ? $_POST['share_bar'] : array();

        if (empty($data['share_types'])) {
            $this->notice->displayError(__('The social networks is required. Check at least one.', 'social3'));

            return;
        }

        $result = $this->connector->do_request(
            '/share/account/save',
            json_encode($data),
            S3_Connector::METHOD_POST,
            S3_Connector::DATA_TYPE_JSON
        );

        if ($result->success) {
            $_REQUEST['action']     = 'edit';
            $_REQUEST['account_id'] = $result->account->id;
            $this->notice->displaySuccess(__('Share Account success saved.', 'social3'));
        }
    }

    public function ajax_delete_share_account()
    {
        $account_id = isset($_POST['account_id']) ? $_POST['account_id'] : false;

        if (empty($account_id)) {
            $this->notice->displayError(__('Internal error. Invalid ID.', 'social3'));

            return;
        }

        $result = $this->connector->do_request(
            '/share/account/delete/' . $account_id,
            '',
            S3_Connector::METHOD_POST,
            S3_Connector::DATA_TYPE_JSON
        );

        if ($result->success) {
            $this->notice->displaySuccess(__('Share Account success deleted.', 'social3'));
        }
    }

    public function ajax_status_share_account()
    {
        $account_id = isset($_POST['account_id']) ? $_POST['account_id'] : false;
        $action     = isset($_POST['s3_action']) ? $_POST['s3_action'] : false;

        if (empty($account_id)) {
            $this->notice->displayError(__('Internal error. Invalid ID.', 'social3'));

            return;
        }

        if (empty($action)) {
            $this->notice->displayError(__('Internal error. Invalid action.', 'social3'));

            return;
        }

        $result = $this->connector->do_request(
            '/share/account/status/' . $account_id . '/' . $action,
            '',
            S3_Connector::METHOD_POST,
            S3_Connector::DATA_TYPE_JSON
        );

        if ($result->success) {
            $this->notice->displaySuccess(__('Share Account success changed status.', 'social3'));
        }
    }
}