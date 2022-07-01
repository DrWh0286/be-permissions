<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class CodeManagedGroup extends AbstractBooleanField
{
    private string $fieldName = 'code_managed_group';

    public static function createFromDBValue(string $dbValue): CodeManagedGroup
    {
        $createValue = (bool)((int)$dbValue);

        return new self($createValue);
    }

    public function extend(BeGroupFieldInterface $beGroupField): CodeManagedGroup
    {
        if (!$beGroupField instanceof CodeManagedGroup) {
            throw new \RuntimeException('Wrong be_groups field is given. ' . get_class($beGroupField) . ' given instead of expected ' . get_class($this));
        }

        return clone $beGroupField;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public static function createFromYamlConfiguration(bool $configValue): CodeManagedGroup
    {
        return new self($configValue);
    }
}
