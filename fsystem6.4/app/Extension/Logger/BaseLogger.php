<?php

declare(strict_types=1);

namespace App\Extension\Logger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class BaseLogger
{
    /**
     * * @var Monolog\Logger
     */
    protected $logger;

    /**
     * @return void
     */
    protected function __construct($output)
    {
        $this->logger = new Logger(config('app.env'), [
            new StreamHandler($output, config('app.log_level'), true, 0777)
        ]);
    }

    /**
     * ログインスタンスを取得
     *
     * @return \Monolog\Logger
     */
    public function log()
    {
        return $this->logger;
    }

    /**
     * DEBUGレベルでログレコードを追加
     *
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function debug($message, array $context = [])
    {
        $this->logger->addDebug($message, $context);
    }

    /**
     * INFOレベルでログレコードを追加
     *
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function info($message, array $context = [])
    {
        $this->logger->addInfo($message, $context);
    }

    /**
     * NOTICEレベルでログレコードを追加
     *
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function notice($message, array $context = [])
    {
        $this->logger->addNotice($message, $context);
    }

    /**
     * WARNINGレベルでログレコードを追加
     *
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function warning($message, array $context = [])
    {
        $this->logger->addWarning($message, $context);
    }

    /**
     * ERRORレベルでログレコードを追加
     *
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function error($message, array $context = [])
    {
        $this->logger->addError($message, $context);
    }

    /**
     * CRITICALレベルでログレコードを追加
     *
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function critical($message, array $context = [])
    {
        $this->logger->addCritical($message, $context);
    }

    /**
     * ALERTレベルでログレコードを追加
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function alert($message, array $context = [])
    {
        $this->logger->addAlert($message, $context);
    }

    /**
     * EMERGENCYレベルでログレコードを追加
     *
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function emergency($message, array $context = [])
    {
        $this->logger->addEmergency($message, $context);
    }
}
