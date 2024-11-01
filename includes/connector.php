<?php

class S3_Connector
{
    const DATA_TYPE_JSON        = 'application/json';
    const DATA_TYPE_URLENCODED  = 'application/x-www-form-urlencoded';

    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';

    private $access_token;
    private $api_endpoint;
    private $template_path;

    /** @var  S3_AdminNotice */
    private $notice;

    private $api_version = 'v1';

    public function __construct($notice)
    {
        global $S3_TEMPLATES_PATH;

        $this->notice        = $notice;
        $this->template_path = $S3_TEMPLATES_PATH;
        $this->load_settings();

        add_action( 'init', array($this, 'start_session'), 1 );
        add_action( 'init', array($this, 'load_settings'), 11 );
        add_action( 's3_add_admin_menu', array($this, 'add_options_page'), 99 );
        add_action( 'init', array($this, 'handle_integration_save') );
        add_action( 'init', array($this, 'handle_login') );
        add_action( 'init', array($this, 'handle_logout') );
        add_action( 'http_api_curl', array($this, 'tls12ct_http_api_curl') );
    }

    public function tls12ct_http_api_curl($handle)
    {
        $version = get_option('s3_connection_ssl', 6);

        if ($version == 'false') {
            curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, false ); // only for developer testing
        } else {
            curl_setopt($handle, CURLOPT_SSLVERSION, $version);
        }
    }

    public function load_settings()
    {
        $this->access_token = get_option('s3_access_token');
        $this->api_endpoint = get_option('s3_api_endpoint', 'https://social3.io/'); // for testing set option s3_api_endpoint
    }

    public function start_session()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function is_connected()
    {
        if (empty($this->access_token) || empty($this->api_endpoint)) {
            return false;
        }

        return true;
    }

    public function add_options_page($main_slug)
    {
        add_submenu_page( $main_slug, __('Settings', 'social3'), __('Settings', 'social3'), 'manage_options', 's3-connector-options', array($this, 'render_options_page') );
    }

    public function render_options_page()
    {
        $access_token = get_option('s3_access_token');

        if ($access_token) {
            $this->get_brand();
        }

        $s3_brand_name              = get_option('s3_brand_name');
        $s3_script                  = get_option('s3_integration_script');
        $s3_integration_status      = get_option('s3_integration_status');
        $s3_integration_in_footer   = get_option('s3_integration_in_footer');

        require $this->template_path . 'options-page.php';
    }

    public function is_enabled_auto_integration()
    {
        $s3_script                  = get_option('s3_integration_script');
        $s3_integration_status      = get_option('s3_integration_status');

        if (!empty($s3_script) && (boolean)$s3_integration_status) {
            return true;
        }

        return false;
    }

    public function handle_integration_save()
    {
        if (!(isset($_POST['s3_app_integration_action_nonce'])
              && wp_verify_nonce($_POST['s3_app_integration_action_nonce'], 's3_app_integration_action'))
        ) {
            return;
        }

        $status     = isset($_POST['s3_integration_status']) ? true : false;
        $position   = isset($_POST['s3_integration_in_footer']) ? true : false;

        update_option('s3_integration_status', $status);
        update_option('s3_integration_in_footer', $position);

        $this->update_flag_on_social($status);
    }

    public function update_flag_on_social($status)
    {
        $data = array(
            'included' => ($status) ? 1 : 0
        );

        $brandId = get_option('s3_brand_id', false);

        try {
            $responce = $this->do_request('/brand/update/checking/'. $brandId, $data, self::METHOD_POST, self::DATA_TYPE_URLENCODED);

            if ($responce && $responce->success) {
                return true;
            }
        } catch (Exception $error) {
            $this->notice->displayError('Social3: ' . $error->getMessage());
        }

        return false;
    }

    public function handle_login()
    {
        if (!(isset($_POST['s3_app_login_action_nonce'])
              && wp_verify_nonce($_POST['s3_app_login_action_nonce'], 's3_app_login_action') && isset($_POST['s3_user']))
        ) {
            return;
        }

        if (isset($_POST['s3_user']['email']) && isset($_POST['s3_user']['password'])) {
            $data = $_POST['s3_user'];

            try {
                $token = $this->get_token($data);

                if ($token) {
                    update_option('s3_access_token', $token);
                    $this->notice->displaySuccess(__('You have successfully connected Wordpress to Social3!', 'social3'));

                    $this->load_settings();
                    $this->update_flag_on_social((bool)get_option('s3_integration_status', false));
                }
            } catch (Exception $error) {
                $this->notice->displayError($error->getMessage());
            }
        } else if (isset($_POST['s3_user']['use_key']) && isset($_POST['s3_user']['api_key'])) {
            $data = $_POST['s3_user'];

            try {
                $this->access_token = $data['api_key'];

                $site = array(
                    'url' => get_home_url('/')
                );
                $responce = $this->do_request('/brand/get-id', $site, self::METHOD_GET, self::DATA_TYPE_URLENCODED);

                if ($responce && $responce->site_id && $responce->brand_id) {
                    update_option('s3_access_token', $data['api_key']);
                    $this->notice->displaySuccess(__('You have successfully connected Wordpress to Social3!', 'social3'));

                    $this->load_settings();
                    $this->update_flag_on_social((bool)get_option('s3_integration_status', false));
                } else {
                    $this->notice->displayError(__('Invalid API key.', 'social3'));
                }
            } catch (Exception $error) {
                $this->notice->displayError(__('Invalid API key.', 'social3'));
            }
        }
    }

    public function handle_logout()
    {
        if (!(isset($_POST['s3_app_logout_action_nonce'])
              && wp_verify_nonce($_POST['s3_app_logout_action_nonce'], 's3_app_logout_action'))
        ) {
            return;
        }

        $this->update_flag_on_social(false);

        delete_option('s3_access_token');
        delete_option('s3_site_id');
        delete_option('s3_integration_script');
        $this->notice->displayInfo(__('You are disconnected!', 'social3'));
    }

    public function get_token($data, $api_version = null)
    {
        if ($api_version === null) {
            $api_version = $this->api_version;
        }

        $url = $this->api_endpoint.'api/'.$api_version.'/login';
        $request_data = array(
            'headers' => array(
                'Content-Type' => self::DATA_TYPE_JSON,
                'Accept'       => 'application/json'
            ),
            'method'  => self::METHOD_POST,
            'timeout' => 20,
            'body'    => json_encode($data),
        );

        $result = wp_remote_request($url, $request_data);

        if ($result instanceof WP_Error) {
            write_log('Request error: ' . $result->get_error_message());
            throw new Exception($result->get_error_message());
        }

        if ($result['response']['code'] >= 300) {
            $body = json_decode($result['body']);
            if ($body) {
                write_log('Failed to make a request: ' . $body->error);

                throw new Exception($body->error);
            }
            write_log('Failed to make a request: ' . $result['response']['code'] . '|' . $result['response']['message']);

            throw new Exception('Failed to make a request: ' . $result['response']['code'] . '|' . $result['response']['message']);
        }

        $body = json_decode($result['body']);

        return $body->token;
    }

    public function get_brand()
    {
        $data = array(
            'url' => get_home_url('/')
        );

        try {
            $responce = $this->do_request('/brand/get-id', $data, self::METHOD_GET, self::DATA_TYPE_URLENCODED);

            if ($responce && $responce->site_id) {
                update_option('s3_site_id', $responce->site_id);
                update_option('s3_brand_id', $responce->brand_id);
                update_option('s3_brand_name', $responce->brand_name);
                update_option('s3_integration_script', $responce->script);

                return $responce;
            }
        } catch (Exception $error) {
            $this->notice->displayError('Social3: ' . $error->getMessage());
            return false;
        }
    }

    public function do_request($endpoint, $data = '', $method = self::METHOD_GET, $content_type = self::DATA_TYPE_URLENCODED, $api_version = null, $return_errors = false)
    {
        try {
            if (!$this->access_token) {
                write_log('S3 Connector is not configured');

                throw new Exception(__('S3 Connector is not configured', 'social3'));
            }

            if ($api_version === null) {
                $api_version = $this->api_version;
            }

            $url = $this->api_endpoint . 'api/' . $api_version . $endpoint;
            $request_data = array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $this->access_token,
                    'Content-Type'  => $content_type,
                    'Accept'        => 'application/json'
                ),
                'method'  => $method,
                'timeout' => 20,
                'body'    => $data,
            );

            $result = wp_remote_request($url, $request_data);

            if ($result instanceof WP_Error) {
                write_log('Request error: ' . $result->get_error_message());
                throw new Exception($result->get_error_message());
            }

            if ($result['response']['code'] == 422) {
                write_log('Failed to make a request: ' . $result['body']);

                $body = json_decode($result['body']);
                if ($return_errors) {
                    return $body;
                }

                foreach((array)$body as $item) {
                    foreach($item as $error) {
                        $this->notice->displayError($error);
                        break;
                    }
                }
                return false;
            }

            if ($result['response']['code'] >= 300) {
                $body = json_decode($result['body']);
                if ($return_errors) {
                    return $body;
                }

                if (!empty($body->error)) {
                    write_log('Failed to make a request: ' . $body->error);

                    throw new Exception($body->error);
                } elseif (!empty($body->message_alert)) {
                    write_log('Failed to make a request: ' . $body->message_alert);

                    throw new Exception($body->message_alert);
                } elseif (!empty($body->message)) {
                    write_log('Failed to make a request: ' . $body->message);

                    throw new Exception($body->message);
                }
                write_log('Failed to make a request: ' . $result['response']['code'] . '|' . $result['response']['message']);

                throw new Exception('Failed to make a request: ' . $result['response']['code'] . '|' . $result['response']['message']);
            }

            return json_decode($result['body']);
        } catch (Exception $error) {
            $this->notice->displayError('Social3: ' . $error->getMessage());
        }
    }
}
