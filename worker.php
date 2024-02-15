<?php
declare(strict_types=1);

use Katsarov\MailerliteTest\Core\Cache;
use Katsarov\MailerliteTest\Core\Config;

include_once __DIR__ . '/vendor/autoload.php';

$config = new Config();
$redis = new Redis();
$worker = new \Katsarov\MailerliteTest\Core\Queue\Worker(
    new Cache($redis, $config),
    $config
);
$worker->work();