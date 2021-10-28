<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Model;

use Pluswerk\BePermissions\Collection\BeGroupFieldCollection;
use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Value\AllowedLanguages;
use Pluswerk\BePermissions\Value\BeGroupFieldInterface;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\NonExcludeFields;

final class BeGroup
{
    private Identifier $identifier;
    private string $title;
    private BeGroupFieldCollection $beGroupFieldCollection;

    public function __construct(Identifier $identifier, string $title, BeGroupFieldCollection $beGroupFieldCollection)
    {
        $this->title = $title;
        $this->identifier = $identifier;
        $this->beGroupFieldCollection = $beGroupFieldCollection;
    }

    public static function createFromDBValues(array $dbValues): BeGroup
    {
        if (empty($dbValues['title'])) {
            throw new \RuntimeException('A ' . __CLASS__ . ' needs a title!');
        }
        if (empty($dbValues['identifier'])) {
            throw new \RuntimeException('A ' . __CLASS__ . ' needs an identifier!');
        }

        $nonExcludeFields = NonExcludeFields::createFromDBValue($dbValues['non_exclude_fields'] ?? '');
        $explicitAllowDeny = ExplicitAllowDeny::createFromDBValue($dbValues['explicit_allowdeny'] ?? '');
        $allowedLanguages = AllowedLanguages::createFromDBValue($dbValues['allowed_languages'] ?? '');

        $beGroupFieldCollection = new BeGroupFieldCollection();
        $beGroupFieldCollection->add($nonExcludeFields);
        $beGroupFieldCollection->add($explicitAllowDeny);
        $beGroupFieldCollection->add($allowedLanguages);

        return new self(
            new Identifier($dbValues['identifier']),
            $dbValues['title'],
            $beGroupFieldCollection
        );
    }

    public function identifier(): Identifier
    {
        return $this->identifier;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function beGroupFieldCollection(): BeGroupFieldCollection
    {
        return $this->beGroupFieldCollection;
    }

    public function databaseValues(): array
    {
        $dbValues = [];
        $dbValues['identifier'] = (string)$this->identifier;
        $dbValues['title'] = $this->title;

        /** @var BeGroupFieldInterface $field */
        foreach ($this->beGroupFieldCollection as $field) {
            $dbValues[$field->getFieldName()] = (string)$field;
        }

        return $dbValues;
    }

    public function overruleByConfiguration(BeGroupConfiguration $configuration): BeGroup
    {
        $collection = $configuration->beGroupFieldCollection();

        return new BeGroup($this->identifier, $configuration->title(), $collection);
    }

    public function extendByConfiguration(BeGroupConfiguration $configuration): BeGroup
    {
        $collection = $this->beGroupFieldCollection->extend($configuration->beGroupFieldCollection());

        return new BeGroup(
            $this->identifier,
            $this->title,
            $collection
        );
    }
}
