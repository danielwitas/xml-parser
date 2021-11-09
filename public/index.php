#!/usr/bin/php
<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Service\Container;
use App\Service\FeedReader;
use App\Service\InfoPrinter;

$container = Container::getInstance();
/** @var InfoPrinter $infoPrinter */
$infoPrinter = $container->get(InfoPrinter::class);

if(!isset($argv[1])) {
    echo $infoPrinter->colorLog('Missing argument: <filename>', 'w') . PHP_EOL;
    exit;
}
$file = $argv[1];
$path = __DIR__ . '/../feeds/' . $file;
if(!file_exists($path)) {
    echo $infoPrinter->colorLog(
            sprintf('File %s does not exist in feeds directory', $file),
            'w') . PHP_EOL;
    exit;
}
/** @var FeedReader $feedReader */
$feedReader = $container->get(FeedReader::class);
try {
    $feedReader->parse($path);
    echo $infoPrinter->colorLog('Success. Task Completed', 's') . PHP_EOL;
} catch (Exception $exception) {
    echo $infoPrinter->colorLog(
            sprintf('Task failed. Reason: %s', $exception->getMessage()),
            'e'
        ) . PHP_EOL;
}


