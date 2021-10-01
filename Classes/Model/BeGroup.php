<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Model;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\NonExcludeFields;

final class BeGroup
{
    private Identifier $identifier;
    private string $title;
    private NonExcludeFields $nonExcludeFields;
    private ExplicitAllowDeny $explicitAllowDeny;

    public function __construct(Identifier $identifier, string $title, NonExcludeFields $nonExcludeFields, ExplicitAllowDeny $explicitAllowDeny)
    {
        $this->title = $title;
        $this->identifier = $identifier;
        $this->nonExcludeFields = $nonExcludeFields;
        $this->explicitAllowDeny = $explicitAllowDeny;
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

        return new self(
            new Identifier($dbValues['identifier']),
            $dbValues['title'],
            $nonExcludeFields,
            $explicitAllowDeny
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

    public function nonExcludeFields(): NonExcludeFields
    {
        return $this->nonExcludeFields;
    }

    public function explicitAllowDeny(): ExplicitAllowDeny
    {
        return $this->explicitAllowDeny;
    }

    public function databaseValues(): array
    {
        return [
            'identifier' => (string)$this->identifier,
            'title' => $this->title,
            'non_exclude_fields' => (string)$this->nonExcludeFields,
            'explicit_allowdeny' => (string)$this->explicitAllowDeny
        ];
    }

    public function overruleByConfiguration(BeGroupConfiguration $configuration): BeGroup
    {
        return new BeGroup(
            $this->identifier,
            $configuration->title(),
            $configuration->nonExcludeFields(),
            $configuration->explicitAllowDeny()
        );
    }

    public function extendByConfiguration(BeGroupConfiguration $configuration): BeGroup
    {
        $nonExcludeFields = $this->nonExcludeFields->extend($configuration->nonExcludeFields());
        $explicitAllowDeny = $this->explicitAllowDeny->extend($configuration->explicitAllowDeny());

        return new BeGroup(
            $this->identifier,
            $this->title,
            $nonExcludeFields,
            $explicitAllowDeny
        );
    }
}
