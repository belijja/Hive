<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 11.7.17.
 * Time: 12.17
 */
declare(strict_types = 1);

namespace Helpers\LogHelpers;

use Monolog\Formatter\HtmlFormatter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Helpers\ConfigHelpers\ConfigManager;

class LogManager
{
    /**
     * @param string $logLevel
     * @param bool $isLineFormatter
     * @param string $errorText
     * @return Logger
     */
    public function log(string $logLevel, bool $isLineFormatter, string $errorText): Logger
    {
        $reflection = new \ReflectionClass('Monolog\Logger');
        $constants = $reflection->getConstants();
        $log = new Logger('Service');
        $handler = new StreamHandler(ConfigManager::getLog('logDir') . $logLevel . '(' . date('Y-m-d') . ')' . ($isLineFormatter ? '.log' : '.html'), $constants[strtoupper($logLevel)], false, 0777);
        $handler->setFormatter($isLineFormatter ? new LineFormatter(null, null, false, true) : new HtmlFormatter());
        $log->pushHandler($handler);
        $log->$logLevel($errorText);
        return $log;
    }
}