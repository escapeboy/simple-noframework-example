<?php
declare(strict_types=1);

use Katsarov\MailerliteTest\Core\Cache;
use Katsarov\MailerliteTest\Core\Config;
use Katsarov\MailerliteTest\SubscriptionService;

include_once __DIR__ . '/vendor/autoload.php';
$config = new Config();

$id = (int)$_GET['id'];

$service = new SubscriptionService(
    $config,
    new Cache(new Redis(), $config)
);
$service->deleteSubscriber($id);

header('Status: 200');
echo 'Subscriber removed';