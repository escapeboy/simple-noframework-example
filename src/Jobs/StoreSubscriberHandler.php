<?php

declare(strict_types=1);

namespace Katsarov\MailerliteTest\Jobs;

final readonly class StoreSubscriberHandler
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function handle(StoreSubscriber $command): void
    {
        $query = $this->pdo->prepare('INSERT INTO subscribers 
            (email, first_name, last_name, status) 
            VALUES (:email, :first_name, :last_name, :status)');
        $query->execute([
            'email' => $command->getEmail(),
            'first_name' => $command->getFirstName(),
            'last_name' => $command->getLastName(),
            'status' => $command->getStatus(),
        ]);
    }
}
