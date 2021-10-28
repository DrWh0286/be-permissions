<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

use Pluswerk\BePermissions\Configuration\ExtensionConfigurationInterface;
use Pluswerk\BePermissions\Configuration\NoValueObjectConfiguredException;

final class BeGroupFieldFactory implements BeGroupFieldFactoryInterface
{
    private ExtensionConfigurationInterface $extensionConfiguration;

    public function __construct(ExtensionConfigurationInterface $extensionConfiguration)
    {
        $this->extensionConfiguration = $extensionConfiguration;
    }

    public function buildFromFieldNameAndYamlValue(string $fieldName, $value): ?BeGroupFieldInterface
    {
        try {
            $valueClass = $this->extensionConfiguration->getClassNameByFieldName($fieldName);
        } catch (NoValueObjectConfiguredException $exception) {
            // @todo: Log info here.
            return null;
        }

        $implementArray = class_implements($valueClass);

        if (in_array(ArrayBasedFieldInterface::class, $implementArray) && !is_array($value)) {
            throw new \InvalidArgumentException('Value for field ' . $fieldName . ' must be of type array!');
        }

        if (in_array(StringBasedFieldInterface::class, $implementArray) && !is_string($value)) {
            throw new \InvalidArgumentException('Value for field ' . $fieldName . ' must be of type string!');
        }

        return $valueClass::createFromYamlConfiguration($value);
    }

    public function buildFromFieldNameAndDatabaseValue(string $fieldName, string $value): ?BeGroupFieldInterface
    {
        try {
            $valueClass = $this->extensionConfiguration->getClassNameByFieldName($fieldName);
        } catch (NoValueObjectConfiguredException $exception) {
            // @todo: Log info here.
            return null;
        }

        return $valueClass::createFromDBValue($value);
    }
}
