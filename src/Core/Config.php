<?php

declare(strict_types=1);

namespace Katsarov\MailerliteTest\Core;

final class Config
{
    public function get(string $key): ?string
    {
        return parse_ini_file('.env')[$key] ?? null;
    }
}
