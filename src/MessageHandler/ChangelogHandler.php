<?php

namespace App\MessageHandler;

use App\Message\Changelog;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ChangelogHandler
{
    public function __invoke(Changelog $message): void
    {
        dump($message);
    }
}
