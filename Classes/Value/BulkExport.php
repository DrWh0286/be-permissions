<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class BulkExport extends AbstractBooleanField
{
    private string $fieldName = 'bulk_export';

    public static function createFromDBValue(string $dbValue): BulkExport
    {
        $createValue = (bool)((int)$dbValue);

        return new self($createValue);
    }

    public function extend(BeGroupFieldInterface $beGroupField): BulkExport
    {
        if (!$beGroupField instanceof BulkExport) {
            throw new \RuntimeException('Wrong be_groups field is given. ' . get_class($beGroupField) . ' given instead of expected ' . get_class($this));
        }

        return clone $beGroupField;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public static function createFromYamlConfiguration(bool $configValue): BulkExport
    {
        return new self($configValue);
    }
}
