<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Builder;

use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Collection\DuplicateBeGroupFieldException;

interface BeGroupFieldCollectionBuilderInterface
{
    /**
     * @param array<string> $dbValues
     * @return BeGroupFieldCollection
     * @throws DuplicateBeGroupFieldException
     */
    public function buildFromDatabaseValues(array $dbValues): BeGroupFieldCollection;

    /**
     * @param array<string, array<int|string, array<int, string>|string>> $configurationArray
     * @return BeGroupFieldCollection
     * @throws DuplicateBeGroupFieldException
     */
    public function buildFromConfigurationArray(array $configurationArray): BeGroupFieldCollection;
}
