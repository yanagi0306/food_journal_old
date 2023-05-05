<?php

namespace App\Logging;

use App\Constants\Common;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;

class ActionLog
{
    public function __invoke(array $config): Logger
    {
        $date       = date('ymd');
        $logPath    = $config['logPath'] ?? Common::LOGS_DIR . "/app_{$date}.log";
        $userId     = $config['userId'] ?? null;
        $userName   = $config['userName'] ?? null;
        $methodName = $config['methodName'] ?? null;
        $infoFormat = ($userId !== null) ? "id:{$userId} name:{$userName}" : '';
        $format     = "[%datetime%][{$infoFormat}][%level_name%:{$methodName}]%message%[%extra.class%:%extra.line%]" . PHP_EOL;
        $dateFormat = 'Y-m-d H:i:s';

        $lineFormatter = new LineFormatter($format, $dateFormat, true, true);

        $handler = new StreamHandler($logPath, Logger::DEBUG);
        $handler->setFormatter($lineFormatter);

        $handler->pushProcessor(new IntrospectionProcessor(Logger::DEBUG, [
            'App\Logging\ActionLog',
            'Monolog\Logger',
            'Monolog\Handler\StreamHandler',
            'Illuminate\Log\Writer',
            'Illuminate\Log\Logger',
            'Illuminate\Log\LogManager',
            'Illuminate\Support\Facades\Facade',
        ]));


        $logger = new Logger('action');
        $logger->pushHandler($handler);

        return $logger;
    }
}


