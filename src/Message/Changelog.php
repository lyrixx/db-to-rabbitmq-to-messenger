<?php

namespace App\Message;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class Changelog
{
    public function __construct(
        public string $action,
        #[SerializedName('table_name')]
        public string $tableName,
        #[SerializedName('transaction_id')]
        public int $transactionId,
        public \DateTimeImmutable $timestamp,
        #[SerializedName('old_data')]
        public ?array $oldData,
        #[SerializedName('new_data')]
        public ?array $newData,
    ) {
    }
}
