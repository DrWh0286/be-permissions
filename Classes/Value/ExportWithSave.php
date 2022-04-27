<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class ExportWithSave extends AbstractBooleanField
{
    private string $fieldName = 'export_with_save';

    public static function createFromDBValue(string $dbValue): ExportWithSave
    {
        $createValue = (bool)((int)$dbValue);

        return new self($createValue);
    }

    public function extend(BeGroupFieldInterface $beGroupField): BeGroupFieldInterface
    {
        if (!$beGroupField instanceof ExportWithSave) {
            throw new \RuntimeException('Wrong be_groups field is given. ' . get_class($beGroupField) . ' given instead of expected ' . get_class($this));
        }

        return clone $beGroupField;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public static function createFromYamlConfiguration(bool $configValue): BooleanBasedFieldInterface
    {
        return new self($configValue);
    }
}
