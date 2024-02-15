<?php

declare(strict_types=1);

namespace Katsarov\MailerliteTest\Jobs;

final readonly class StoreSubscriber
{
    public function __construct(
        private string $email,
        private string $firstName,
        private string $lastName,
        private string $status
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
