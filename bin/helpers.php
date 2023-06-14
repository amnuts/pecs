<?php

namespace Hamidrezaniazi\Pecs\Bin;

use Monolog\Formatter\NormalizerFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

function get_logger(): Logger
{
    $handler = new StreamHandler('php://stdout');
    $handler->setFormatter(new class () extends NormalizerFormatter {
        public function format(array $record): string
        {
            return $record['message'] . PHP_EOL;
        }
    });
    return new Logger(name: 'cli', handlers: [$handler]);
}
