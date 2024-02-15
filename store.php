<?php
declare(strict_types=1);

use Katsarov\MailerliteTest\Core\Cache;
use Katsarov\MailerliteTest\Core\Config;
use Katsarov\MailerliteTest\SubscriptionService;

include_once __DIR__ . '/vendor/autoload.php';

$email = $_POST['email'];
$firstName = $_POST['first_name'];
$lastName = $_POST['last_name'];
$status = $_POST['status'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Status: 400', response_code: 400);
    echo 'Invalid email address';
    return;
}

$cache = new Cache(new Redis(), new Config());
$service = new SubscriptionService(new Config(), $cache);
if($service->checkIfEmailExists($email)){
    header('Status: 400', response_code: 400);
    echo 'Email already exists';
    return;
}
$service->storeSubscriber($email, $firstName, $lastName, $status);

header('Status: 200', response_code: 200);
echo 'Subscriber stored successfully';