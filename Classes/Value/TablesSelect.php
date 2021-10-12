<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

use TYPO3\CMS\Core\Utility\GeneralUtility;

final class TablesSelect
{
    private array $tablesSelect;

    public static function createFromDBValue(string $dbValue): TablesSelect
    {
        $tablesSelect = ($dbValue !== '') ? GeneralUtility::trimExplode(',', $dbValue) : [];

        return new self($tablesSelect);
    }

    public static function createFromConfigurationArray(array $confArray): TablesSelect
    {
        return new self($confArray);
    }

    public function __construct(array $tablesSelect)
    {
        $this->tablesSelect = $tablesSelect;
    }

    public function asArray(): array
    {
        return $this->tablesSelect;
    }

    public function __toString(): string
    {
        return implode(',', $this->tablesSelect);
    }

    public function extend(TablesSelect $tablesSelect): TablesSelect
    {
        $tablesSelectArray = array_unique(array_merge($this->tablesSelect, $tablesSelect->asArray()));
        asort($tablesSelectArray);

        return new self(array_values($tablesSelectArray));
    }
}
