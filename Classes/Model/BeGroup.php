<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Model;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Value\Identifier;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class BeGroup
{
    private Identifier $identifier;
    private string $title;
    private array $nonExcludeFields;

    public function __construct(Identifier $identifier, string $title, array $nonExcludeFields)
    {
        $this->title = $title;
        $this->identifier = $identifier;
        $this->nonExcludeFields = $nonExcludeFields;
    }

    public static function createFromDBValues(array $dbValues): BeGroup
    {
        $nonExcludeFields = self::explodeNonExcludeFields($dbValues['non_exclude_fields']);

        return new self(
            new Identifier($dbValues['identifier']),
            $dbValues['title'],
            $nonExcludeFields
        );
    }

    private static function explodeNonExcludeFields(string $nonExcludeFields): array
    {
        $basicArray = GeneralUtility::trimExplode(',', $nonExcludeFields);

        $nonExcludeFieldsArray = [];

        foreach ($basicArray as $entry) {
            $entryArray = GeneralUtility::trimExplode(':', $entry);
            $nonExcludeFieldsArray[$entryArray[0]][] = $entryArray[1];
        }

        return $nonExcludeFieldsArray;
    }

    public function identifier(): Identifier
    {
        return $this->identifier;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function nonExcludeFields(): array
    {
        return $this->nonExcludeFields;
    }

    public function databaseValues(): array
    {
        return [
            'identifier' => (string)$this->identifier,
            'title' => $this->title,
            'non_exclude_fields' => $this->implodeExcludeFields()
        ];
    }

    private function implodeExcludeFields(): string
    {
        $nonExcludeFieldsArray = [];

        foreach ($this->nonExcludeFields as $nonExcludeFieldsTable => $nonExcludeFields) {
            foreach ($nonExcludeFields as $nonExcludeField) {
                $nonExcludeFieldsArray[] = $nonExcludeFieldsTable . ':' . $nonExcludeField;
            }
        }

        return implode(',', $nonExcludeFieldsArray);
    }

    public function overruleByConfiguration(BeGroupConfiguration $configuration): BeGroup
    {
        return new BeGroup(
            $this->identifier,
            $configuration->rawConfiguration()['title'],
            $configuration->rawConfiguration()['non_exclude_fields']
        );
    }

    public function extendByConfiguration(BeGroupConfiguration $configuration): BeGroup
    {
        $nonExcludeFields = array_merge_recursive(
            $this->nonExcludeFields,
            $configuration->rawConfiguration()['non_exclude_fields'] ?? []
        );

        return new BeGroup(
            $this->identifier,
            $this->title,
            $nonExcludeFields
        );
    }
}
