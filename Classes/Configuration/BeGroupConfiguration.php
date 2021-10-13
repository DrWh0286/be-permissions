<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Configuration;

use Pluswerk\BePermissions\Collection\BeGroupFieldCollection;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\BeGroupFieldInterface;
use Pluswerk\BePermissions\Value\Identifier;

final class BeGroupConfiguration
{
    private Identifier $identifier;
    private string $configPath;
    private string $title;
    private BeGroupFieldCollection $beGroupFieldCollection;

    public function __construct(Identifier $identifier, string $configPath, string $title, BeGroupFieldCollection $beGroupFieldCollection)
    {
        $this->identifier = $identifier;
        $this->configPath = $configPath;
        $this->title = $title;
        $this->beGroupFieldCollection = $beGroupFieldCollection;
    }

    public static function createFromBeGroup(BeGroup $beGroup, string $configPath): BeGroupConfiguration
    {
        return new self($beGroup->identifier(), $configPath, $beGroup->title(), $beGroup->beGroupFieldCollection());
    }

    public function title(): string
    {
        return $this->title;
    }

    public function beGroupFieldCollection(): BeGroupFieldCollection
    {
        return $this->beGroupFieldCollection;
    }

    /**
     * @return Identifier
     */
    public function identifier(): Identifier
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function configPath(): string
    {
        return $this->configPath;
    }

    public function asArray(): array
    {
        $array = [];
        $array['title'] = $this->title;

        /** @var BeGroupFieldInterface $field */
        foreach ($this->beGroupFieldCollection as $field) {
            $array[$field->getFieldName()] = $field->asArray();
        }

        return array_filter($array);
    }
}
