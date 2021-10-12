<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

use TYPO3\CMS\Core\Utility\GeneralUtility;

final class DbMountpoints implements BeGroupFieldInterface
{
    private array $dbMountpoints;
    private string $fieldName = 'db_mountpoints';

    public static function createFromDBValue(string $dbValue): DbMountpoints
    {
        $dbMountpoints = ($dbValue === '') ? [] : GeneralUtility::intExplode(',', $dbValue);

        return new self($dbMountpoints);
    }

    public function __construct(array $dbMountpoints)
    {
        $this->dbMountpoints = $dbMountpoints;
    }

    public static function createFromConfigurationArray(array $confArray): DbMountpoints
    {
        return new self($confArray);
    }

    public function asArray(): array
    {
        return $this->dbMountpoints;
    }

    public function __toString(): string
    {
        return implode(',', $this->dbMountpoints);
    }

    public function extend(BeGroupFieldInterface $extendDbMountpoints): DbMountpoints
    {
        $array = array_unique(array_merge($this->dbMountpoints, $extendDbMountpoints->asArray()));
        asort($array);

        return new self(array_values($array));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
