<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractStringArrayField implements ArrayBasedFieldInterface
{
    /** @var array<string> */
    private array $values;

    /** @return array<string> */
    protected static function createFromDBValueHelper(string $dbValue): array
    {
        return ($dbValue !== '') ? GeneralUtility::trimExplode(',', $dbValue) : [];
    }

    /**
     * @param array<string> $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function yamlConfigurationValue(): array
    {
        return $this->values;
    }

    public function __toString(): string
    {
        return implode(',', $this->values);
    }

    /** @return array<string> */
    protected function extendHelper(BeGroupFieldInterface $tablesSelect): array
    {
        $tablesSelectArray = array_unique(array_merge($this->values, $tablesSelect->yamlConfigurationValue()));
        asort($tablesSelectArray);

        return array_values($tablesSelectArray);
    }
}
