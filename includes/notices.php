<?php

/**
 * Created by PhpStorm.
 * User: dvasilevskiy
 * Date: 08.12.17
 * Time: 11:43
 */
class S3_AdminNotice
{
    private static $instance;
    const NOTICE_FIELD = 's3_admin_notice_message';

    protected function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function displayAdminNotice()
    {
        $option      = get_option(self::NOTICE_FIELD);
        $message     = isset($option['message']) ? $option['message'] : false;
        $noticeLevel = ! empty($option['notice-level']) ? $option['notice-level'] : 'notice-error';

        if ($message) {
            echo "<div class='notice {$noticeLevel} is-dismissible'><p>{$message}</p></div>";
            delete_option(self::NOTICE_FIELD);
        }
    }

    public function displayError($message)
    {
        $this->updateOption($message, 'notice-error');
    }

    public function displayWarning($message)
    {
        $this->updateOption($message, 'notice-warning');
    }

    public function displayInfo($message)
    {
        $this->updateOption($message, 'notice-info');
    }

    public function displaySuccess($message)
    {
        $this->updateOption($message, 'notice-success');
    }

    protected function updateOption($message, $noticeLevel) {
        update_option(self::NOTICE_FIELD, array(
            'message'       => $message,
            'notice-level'  => $noticeLevel
        ));
    }
}