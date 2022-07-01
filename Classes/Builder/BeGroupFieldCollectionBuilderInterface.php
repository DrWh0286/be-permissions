<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Builder;

use Pluswerk\BePermissions\Collection\BeGroupFieldCollection;
use Pluswerk\BePermissions\Collection\DuplicateBeGroupFieldException;

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
