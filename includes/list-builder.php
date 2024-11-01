<?php

/**
 * Created by PhpStorm.
 * User: dvasilevskiy
 * Date: 11.12.17
 * Time: 11:25
 */

class S3_List_Builder
{
    /** @var  S3_AdminNotice */
    private $notice;

    /** @var  S3_Connector */
    private $connector;

    private $screen;

    public function __construct($connector, $notice)
    {
        global $S3_TEMPLATES_PATH;
        global $S3_ASSETS_PATH;

        $this->notice        = $notice;
        $this->connector     = $connector;
        $this->template_path = $S3_TEMPLATES_PATH;
        $this->assets_path   = $S3_ASSETS_PATH;

        add_action( 's3_add_admin_menu', array($this, 'add_options_page'), 30 );
        add_filter( 'set-screen-option', array($this, 'list_builder_table_set_option'), 10, 3);
        add_action( 'init', array($this, 'handle_list_builder_save') );
        add_action( 'init', array($this, 'handle_list_builder_test_save') );

        add_action( 'admin_enqueue_scripts', array($this, 'add_admin_script') );

        add_action( 'wp_ajax_get_connection_lists', array($this, 'ajax_get_connection_lists') );
        add_action( 'wp_ajax_delete_list_builder', array($this, 'ajax_delete_list_builder') );
        add_action( 'wp_ajax_save_connection', array($this, 'ajax_save_connection') );
        add_action( 'wp_ajax_status_list_builder', array($this, 'ajax_status_list_builder') );
        add_action( 'wp_ajax_clone_list_builder', array($this, 'ajax_clone_list_builder') );
        add_action( 'wp_ajax_remove_connection', array($this, 'ajax_remove_connection') );
        add_action( 'wp_ajax_list_builder_chart_data', array($this, 'ajax_list_builder_chart_data') );
    }

    public function add_admin_script( $hook )
    {
        if ( 'admin_page_s3_menu_list_builder_test' == $hook || 'social3_page_s3_menu_list_builders' == $hook) {
            wp_enqueue_script('google-charts', 'https://www.gstatic.com/charts/loader.js');
            wp_enqueue_style('list-builder', $this->assets_path . 'css/list-builder.css' );
        }
        if ( 'admin_page_s3_menu_list_builder' == $hook) {
            wp_enqueue_style('list-builder', $this->assets_path . 'css/list-builder.css' );
            wp_enqueue_style('list-builder-datetimepicker', $this->assets_path . 'css/datetimepicker.css' );

            wp_enqueue_script('list-builder-select', $this->assets_path . 'js/select2.full.min.js', array('jquery'), false, true );
            wp_enqueue_script('lb-moment', $this->assets_path . 'js/moment.min.js', array('jquery'), false, true );
            wp_enqueue_script('lb-moment-tz', $this->assets_path . 'js/moment-timezone-with-data.min.js', array('jquery', 'lb-moment'), false, true );
            wp_enqueue_script('list-builder-datetime', $this->assets_path . 'js/bootstrap-datetimepicker.min.js', array('jquery', 'lb-moment'), false, true );
            wp_enqueue_script('list-builder', $this->assets_path . 'js/list-builder.js', array('jquery', 'list-builder-select'), false, true );
            wp_enqueue_script('list-builder-rules', $this->assets_path . 'js/list-builder-rules.js', array('jquery', 'list-builder', 'list-builder-select'), false, true );
        }
    }

    public function add_options_page($main_slug)
    {
        $this->screen = add_submenu_page( $main_slug, __('Audience Builder', 'social3'), __('Audience Builder', 'social3'), 'manage_options', 's3_menu_list_builders', array($this, 'render_list_builder_dashboard_page') );
        $this->screen = add_submenu_page( 'any', __('A/B Test', 'social3'), __('A/B Test', 'social3'), 'manage_options', 's3_menu_list_builder_test', array($this, 'render_list_builder_test_page') );
        add_submenu_page('any', __('Create Form', 'social3'), __('Create Form', 'social3'), 'manage_options', 's3_menu_list_builder', array($this, 'render_list_builder_page'));
        add_action( "load-$this->screen", array($this, 'add_options') );
    }

    public function list_builder_table_set_option($status, $option, $value)
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
            'option' => 's3_list_builders_per_page'
        );
        add_screen_option( $option, $args );
    }

    public function render_list_builder_dashboard_page()
    {
        $brand      = $this->connector->get_brand();
        $info       = $this->connector->do_request('/list-builder/test/info/' . $brand->brand_id);
        $typesConv  = $info->typesConv;

        $result      = $this->connector->do_request( '/list-builder/dashboard/' . $brand->brand_id);
        $lists       = $result->lists;
        $datePeriods = $result->datePeriods;

        require $this->template_path . 'list-builder/list-builder-dashboard-page.php';
    }

    public function render_list_builder_test_page()
    {
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'new') {
            $data = array(
                'form_type' => $_REQUEST['form_type']
            );

            $brand = $this->connector->get_brand();
            $info  = $this->connector->do_request(
                '/list-builder/test/start/'.$brand->brand_id,
                json_encode($data),
                S3_Connector::METHOD_POST,
                S3_Connector::DATA_TYPE_JSON
            );

            $test            = isset($_POST['form']) ? (object)$_POST['form'] : new stdClass();
            $test->status    = 1;
            $test->brand_id  = $brand->brand_id;

            if (empty($test->threshold)) {
                $test->threshold = 5000;
            }

            $lists    = $info->lists;
            $convFrom = $info->convFrom;
            $convTo   = $info->convTo;
        } else {
            $redirect = admin_url('/admin.php?page=s3_menu_list_builders');
        }

        require $this->template_path . 'list-builder/list-builder-test-page.php';
    }

    public function render_list_builder_page()
    {
        $inline_code = '';

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['form_id'])
            && $_REQUEST['form_id'] > 0
        ) {
            $result  = $this->connector->do_request('/list-builder/form/get/'. $_REQUEST['form_id']);
            $list    = $result->list;

            $connections = array();
            foreach($list->connections as $connection) {
                $connections[] = array(
                    "connection_id" => $connection->id,
                    "list_id"       => $connection->service_list_id,
                    "list_name"     => $connection->service_list_name
                );
            }

            $list->connections = json_encode($connections);

            $script  = $result->code;

            if ($base = get_option('s3_integration_script')) {
                $inline_code = str_replace($base, '', $script);
            }
        } elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'new') {
            $list    = isset($_POST['form']) ? (object)$_POST['form'] : new stdClass();
            $script  = false;
            $brand   = $this->connector->get_brand();

            $list->main_site_id = $brand->site_id;
            $list->brand_id     = $brand->brand_id;
        } else {
            $redirect = admin_url('/admin.php?page=s3_menu_list_builders');
        }

        if (!empty($_SESSION['list_builder_all_data'])) {
            parse_str($_SESSION['list_builder_all_data'], $output);

            $list = (object)$output['form'];

            if (!empty($_REQUEST['new_connection_id'])) {
                $list->email_service_connection_id = $_REQUEST['new_connection_id'];
            }

            unset($_SESSION['list_builder_all_data']);
        }

        $auto_integration   = $this->connector->is_enabled_auto_integration();
        $types              = $this->connector->do_request('/list-builder/form/types');
        require $this->template_path . 'list-builder/list-builder-page.php';
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

    public function handle_list_builder_save()
    {
        if (!(isset($_POST['s3_list_builder_action_save_nonce'])
              && wp_verify_nonce($_POST['s3_list_builder_action_save_nonce'], 's3_list_builder_action_save'))
        ) {
            return;
        }

        $data = isset($_POST['form']) ? $_POST['form'] : array();

        if (empty($data['form_title'])) {
            $this->notice->displayError(__('The Title is required field.', 'social3'));

            return;
        }

        if (empty($data['form_text'])) {
            $this->notice->displayError(__('The Form Text is required field.', 'social3'));

            return;
        }

        $result = $this->connector->do_request(
            '/list-builder/form/save/' . $data['id'],
            json_encode($data),
            S3_Connector::METHOD_POST,
            S3_Connector::DATA_TYPE_JSON
        );

        if ($result->list) {
            unset($_REQUEST['action']);
            unset($_REQUEST['form_id']);
            $_REQUEST['page'] = 's3_menu_list_builders';
            $this->notice->displaySuccess(__('Form success saved.', 'social3'));
        }
    }

    public function handle_list_builder_test_save()
    {
        if (!(isset($_POST['s3_list_builder_test_action_save_nonce'])
              && wp_verify_nonce($_POST['s3_list_builder_test_action_save_nonce'], 's3_list_builder_test_action_save'))
        ) {
            return;
        }

        $data = isset($_POST['form']) ? $_POST['form'] : array();

        if (empty($data['brand_id'])) {
            $this->notice->displayError(__('Internal error. Invalid Brand ID.', 'social3'));

            return;
        }

        if (empty($data['threshold']) || $data['threshold'] <= 0) {
            $this->notice->displayError(__('The Number of Visits is required field. And should be greater than 0.', 'social3'));

            return;
        }

        if (empty($data['lists']) || count($data['lists']) < 2) {
            $this->notice->displayError(__('Select at least two forms.', 'social3'));

            return;
        }

        $result = $this->connector->do_request(
            '/list-builder/test/save',
            json_encode($data),
            S3_Connector::METHOD_POST,
            S3_Connector::DATA_TYPE_JSON
        );

        if ($result->success) {
            unset($_REQUEST['action']);
            unset($_REQUEST['form_type']);
            $_REQUEST['page'] = 's3_menu_list_builders';
            $this->notice->displaySuccess(__('A/B Test success saved.', 'social3'));
        }
    }

    public function ajax_delete_list_builder()
    {
        $form_id = isset($_POST['form_id']) ? $_POST['form_id'] : null;

        if (empty($form_id)) {
            $this->notice->displayError(__('Internal error. Invalid ID.', 'social3'));

            return;
        }

        $result = $this->connector->do_request(
            '/list-builder/form/delete/' . $form_id,
            '',
            S3_Connector::METHOD_POST,
            S3_Connector::DATA_TYPE_JSON
        );

        if ($result->success) {
            $this->notice->displaySuccess(__('Form success deleted.', 'social3'));
        }
    }

    public function ajax_remove_connection()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : null;

        if (empty($id)) {
            wp_send_json(array(
                'error'     => true,
                'message'   => __('Internal error. Invalid ID.', 'social3')
            ), 400);
        }

        $result = $this->connector->do_request(
            '/list-builder/connection/delete/' . $id,
            '',
            S3_Connector::METHOD_POST,
            S3_Connector::DATA_TYPE_JSON
        );

        if ($result->success) {
            wp_send_json(array(
                'success'     => true,
                'message'   => __('Connection successfully disconnected.', 'social3')
            ));
        }
    }

    public function ajax_status_list_builder()
    {
        $form_id = isset($_POST['form_id']) ? $_POST['form_id'] : false;
        $action  = isset($_POST['s3_action']) ? $_POST['s3_action'] : false;

        if (empty($form_id)) {
            $this->notice->displayError(__('Internal error. Invalid ID.', 'social3'));

            return;
        }

        if (empty($action)) {
            $this->notice->displayError(__('Internal error. Invalid action.', 'social3'));

            return;
        }

        $result = $this->connector->do_request(
            '/list-builder/status/' . $form_id . '/' . $action,
            '',
            S3_Connector::METHOD_POST,
            S3_Connector::DATA_TYPE_JSON
        );

        if ($result->success) {
            $this->notice->displaySuccess(__('List builder success changed status.', 'social3'));
        }
    }

    public function ajax_clone_list_builder()
    {
        $form_id = isset($_POST['form_id']) ? $_POST['form_id'] : false;
        $name    = isset($_POST['name']) ? $_POST['name'] : false;

        if (empty($form_id)) {
            $this->notice->displayError(__('Internal error. Invalid ID.', 'social3'));

            return;
        }

        if (empty($form_id)) {
            $this->notice->displayError(__('Name is required field.', 'social3'));

            return;
        }

        $data = array(
            'name' => $name
        );

        $result = $this->connector->do_request(
            '/list-builder/clone/' . $form_id,
            json_encode($data),
            S3_Connector::METHOD_POST,
            S3_Connector::DATA_TYPE_JSON
        );

        if ($result->list) {
            $this->notice->displaySuccess(__('List builder success cloned.', 'social3'));
        }
    }

    public function ajax_get_connection_lists()
    {
        $connection_id = isset($_POST['connection_id']) ? $_POST['connection_id'] : false;

        if (empty($connection_id)) {
            wp_send_json(array(
                'error'     => true,
                'message'   => __('Internal error. Invalid Connection ID.', 'social3')
            ));
        }

        $result = $this->connector->do_request('/list-builder/connection/lists', array('connection_id' => $connection_id));

        if (isset($result->lists)) {
            wp_send_json(array(
                'success'   => true,
                'data'      => $result->lists
            ));
        }
    }

    public function ajax_list_builder_chart_data()
    {
        $listId = isset($_POST['list_id']) ? $_POST['list_id'] : false;
        $convFrom = isset($_POST['conv_from']) ? $_POST['conv_from'] : false;
        $convTo = isset($_POST['conv_to']) ? $_POST['conv_to'] : false;

        if (empty($listId)) {
            wp_send_json(array(
                'error'     => true,
                'message'   => __('Internal error. Invalid List Form ID.', 'social3')
            ));
        }

        if (empty($convFrom) || empty($convTo)) {
            wp_send_json(array(
                'error'     => true,
                'message'   => __('Internal error. Missed required fields.', 'social3')
            ));
        }

        $result = $this->connector->do_request(
            '/list-builder/data/' . $listId,
            array(
                'convFrom'  => $convFrom,
                'convTo'    => $convTo
            )
        );

        if (isset($result->chartData)) {
            wp_send_json($result);
        }
    }

    public function ajax_save_connection()
    {
        $connection = isset($_POST) ? $_POST : array();

        if (empty($connection)) {
            wp_send_json(array(
                'error'     => true,
                'message'   => __('Internal error. Missing required fields.', 'social3')
            ));
        }

        $_SESSION['list_builder_all_data'] = $connection['all_data'];

        $connection['redirect_uri'] = $_SERVER['HTTP_REFERER'];

        $result = $this->connector->do_request(
            '/list-builder/connection/save',
            $connection,
            S3_Connector::METHOD_POST,
            S3_Connector::DATA_TYPE_URLENCODED,
            null,
            true
        );

        if (isset($result->connection)) {
            wp_send_json(array(
                'success'    => true,
                'connection' => $result->connection
            ));
        } else if (isset($result->redirect)) {
            wp_send_json(array(
                'success'     => true,
                'redirect'    => $result->redirect
            ));
        } elseif(isset($result->error_code)) {
            wp_send_json($result, 400);
        }
    }
}