<?php
declare(strict_types=1);

use Katsarov\MailerliteTest\Core\Cache;
use Katsarov\MailerliteTest\Core\Config;
use Katsarov\MailerliteTest\SubscriptionService;

include_once __DIR__ . '/vendor/autoload.php';

$config = new Config();
$service = new SubscriptionService(
    $config,
    new Cache(new Redis(), $config)
);
$page = array_key_exists('page', $_GET) ? (int)$_GET['page'] : 1;
$perPage = 10;
$subscribers = $service->listSubscriptions($page, $perPage);
$totalSubscribers = $service->getSubscribersCount();
header('Content-Type: application/json' );
echo json_encode([
    'data' => $subscribers,
    'perPage' => $perPage,
    'page' => $page,
    'total' => $totalSubscribers,
], JSON_THROW_ON_ERROR);
