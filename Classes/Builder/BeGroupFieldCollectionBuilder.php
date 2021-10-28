<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Builder;

use Pluswerk\BePermissions\Collection\BeGroupFieldCollection;
use Pluswerk\BePermissions\Value\BeGroupFieldFactoryInterface;
use Pluswerk\BePermissions\Value\BeGroupFieldInterface;

final class BeGroupFieldCollectionBuilder
{
    private BeGroupFieldFactoryInterface $beGroupFieldFactory;

    /**
     * BeGroupFieldCollectionBuilder constructor.
     * @param BeGroupFieldFactoryInterface $beGroupFieldFactory
     */
    public function __construct(BeGroupFieldFactoryInterface $beGroupFieldFactory)
    {
        $this->beGroupFieldFactory = $beGroupFieldFactory;
    }

    public function buildFromDatabaseValues(array $dbValues): BeGroupFieldCollection
    {
        $collection = new BeGroupFieldCollection();

        foreach ($dbValues as $dbFieldName => $dbValue) {
            $valueObject = $this->beGroupFieldFactory->buildFromFieldNameAndDatabaseValue($dbFieldName, $dbValue);
            if ($valueObject instanceof BeGroupFieldInterface) {
                $collection->add($valueObject);
            }
        }

        return $collection;
    }

    public function buildFromConfigurationArray(array $configurationArray): BeGroupFieldCollection
    {
        $collection = new BeGroupFieldCollection();

        foreach ($configurationArray as $dbFieldName => $dbValue) {
            $valueObject = $this->beGroupFieldFactory->buildFromFieldNameAndYamlValue($dbFieldName, $dbValue);
            if ($valueObject instanceof BeGroupFieldInterface) {
                $collection->add($valueObject);
            }
        }

        return $collection;
    }
}
