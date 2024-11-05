<?php

namespace App\Messenger\Serializer;

use App\Message\Changelog;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

class ChangelogSerializer implements SerializerInterface
{
    public function __construct(
        private SymfonySerializerInterface $serializer,
    ) {
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        $stamps = [];

        $count = 0;
        $lastTime = null;
        foreach ($encodedEnvelope['headers']['x-death'] ?? [] as $xDeath) {
            $count += $xDeath['count'];
            $lastTime = $xDeath['time'];
        }
        if ($count && $lastTime) {
            $stamps[] = new RedeliveryStamp(
                $count,
                new \DateTimeImmutable('@' . $lastTime),
            );
        }

        try {
            $changelog = $this->serializer->deserialize($encodedEnvelope['body'], Changelog::class, 'json');
        } catch (ExceptionInterface $e) {
            throw new MessageDecodingFailedException($e->getMessage(), $e->getCode(), $e);
        }

        return new Envelope($changelog, $stamps);
    }

    public function encode(Envelope $envelope): array
    {
        return [
            'body' => $this->serializer->serialize($envelope->getMessage(), 'json'),
            'headers' => ['type' => 'application/json'],
        ];
    }
}
