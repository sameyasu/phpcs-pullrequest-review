<?php

namespace CodeReviewer\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Psr\Log\LoggerAwareTrait;

/**
 * ロガートレイト
 */
trait DefaultLoggerTrait
{
    use LoggerAwareTrait;

    /**
     * ロガーの初期化
     * @return void
     */
    protected function initLogger(): void
    {
        $logger = new \Monolog\Logger(__CLASS__);

        $streamHandler = new StreamHandler('php://stderr', \Monolog\Logger::DEBUG);
        $formatter = new LineFormatter(
            null, // use default
            null, // use default
            true, // allowInlineLineBreaks: true
            true  // ignoreEmptyContextAndExtra: true
        );
        $streamHandler->setFormatter($formatter);

        $logger->pushHandler($streamHandler);

        $this->setLogger($logger);
    }
}
