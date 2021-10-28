<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class LockToDomain extends AbstractStringField
{
    private string $fieldName = 'lockToDomain';

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function extend(BeGroupFieldInterface $beGroupField): BeGroupFieldInterface
    {
        return new self((string)$beGroupField);
    }
}
