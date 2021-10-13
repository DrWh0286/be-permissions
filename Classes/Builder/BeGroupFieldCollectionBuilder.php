<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Builder;

use Pluswerk\BePermissions\Collection\BeGroupFieldCollection;
use Pluswerk\BePermissions\Configuration\ExtensionConfiguration;
use Pluswerk\BePermissions\Configuration\NoValueObjectConfiguredException;
use Pluswerk\BePermissions\Value\BeGroupFieldInterface;

final class BeGroupFieldCollectionBuilder
{
    private ExtensionConfiguration $config;

    /**
     * BeGroupFieldCollectionBuilder constructor.
     * @param ExtensionConfiguration $config
     */
    public function __construct(ExtensionConfiguration $config)
    {
        $this->config = $config;
    }

    public function buildFromDatabaseValues(array $dbValues): BeGroupFieldCollection
    {
        $collection = new BeGroupFieldCollection();

        foreach ($dbValues as $dbFieldName => $dbValue) {

            try {
                $valueClass = $this->config->getClassNameByFieldName($dbFieldName);
            } catch (NoValueObjectConfiguredException $exception) {
                continue;
            }

            // @todo: Move to a BeGroupFieldFactory
            $implementArray = class_implements($valueClass);
            if (in_array(BeGroupFieldInterface::class, $implementArray)) {
                $valueObject = $valueClass::createFromDBValue($dbValue);
                $collection->add($valueObject);
            }
        }

        return $collection;
    }

    public function buildFromConfigurationArray(array $configurationArray): BeGroupFieldCollection
    {
        $collection = new BeGroupFieldCollection();

        foreach ($configurationArray as $fieldName => $valueArray) {
            try {
                $valueClass = $this->config->getClassNameByFieldName($fieldName);
            } catch (NoValueObjectConfiguredException $exception) {
                continue;
            }

            $implementArray = class_implements($valueClass);
            if (in_array(BeGroupFieldInterface::class, $implementArray)) {
                $valueObject = $valueClass::createFromConfigurationArray($valueArray);
                $collection->add($valueObject);
            }
        }

        return $collection;
    }
}
