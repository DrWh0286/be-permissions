<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Model;

use JsonSerializable;
use SebastianHofer\BePermissions\Builder\BeGroupFieldCollectionBuilderInterface;
use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Value\BeGroupFieldInterface;
use SebastianHofer\BePermissions\Value\Identifier;

final class BeGroup implements JsonSerializable
{
    private Identifier $identifier;
    /** @var BeGroupFieldCollection<BeGroupFieldInterface> */
    private BeGroupFieldCollection $beGroupFieldCollection;

    public static function createFromJson(string $json, BeGroupFieldCollectionBuilderInterface $builder): BeGroup
    {
        $array = (array)json_decode($json, true);
        $identifierString = (isset($array['identifier']) && is_string($array['identifier']))
            ? $array['identifier']
            : '';

        $identifier = new Identifier($identifierString);
        unset($array['identifier']);

        $beGroupFieldCollection = (isset($array['beGroupFieldCollection']) && is_array($array['beGroupFieldCollection']))
            ? $array['beGroupFieldCollection']
            : [];

        $collection = $builder->buildFromConfigurationArray($beGroupFieldCollection);

        return new BeGroup($identifier, $collection);
    }

    /**
     * @param Identifier $identifier
     * @param BeGroupFieldCollection<BeGroupFieldInterface> $beGroupFieldCollection
     */
    public function __construct(Identifier $identifier, BeGroupFieldCollection $beGroupFieldCollection)
    {
        $this->identifier = $identifier;
        $this->beGroupFieldCollection = $beGroupFieldCollection;
    }

    public function identifier(): Identifier
    {
        return $this->identifier;
    }

    /**
     * @return BeGroupFieldCollection<BeGroupFieldInterface>
     */
    public function beGroupFieldCollection(): BeGroupFieldCollection
    {
        return $this->beGroupFieldCollection;
    }

    /**
     * @return array<string>
     */
    public function databaseValues(): array
    {
        $dbValues = [];
        $dbValues['identifier'] = (string)$this->identifier;

        /** @var BeGroupFieldInterface $field */
        foreach ($this->beGroupFieldCollection as $field) {
            $dbValues[$field->getFieldName()] = (string)$field;
        }

        return $dbValues;
    }

    /**
     * @return array<string>
     */
    public function getDatabaseValues(): array
    {
        return $this->databaseValues();
    }

    public function overruleByConfiguration(BeGroupConfiguration $configuration): BeGroup
    {
        $collection = $configuration->beGroupFieldCollection();

        return new BeGroup($this->identifier, $collection);
    }

    public function extendByConfiguration(BeGroupConfiguration $configuration): BeGroup
    {
        $collection = $this->beGroupFieldCollection->extend($configuration->beGroupFieldCollection());

        return new BeGroup(
            $this->identifier,
            $collection
        );
    }

    /**
     * @return array<string, array<string, mixed>|string>
     */
    public function jsonSerialize(): array
    {
        $jsonArray = ['identifier' => (string)$this->identifier];

        /** @var BeGroupFieldInterface $field */
        foreach ($this->beGroupFieldCollection as $field) {
            $jsonArray['beGroupFieldCollection'][$field->getFieldName()] = $field->yamlConfigurationValue();
        }

        return $jsonArray;
    }
}
