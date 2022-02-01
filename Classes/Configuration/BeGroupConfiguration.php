<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Configuration;

use Pluswerk\BePermissions\Collection\BeGroupFieldCollection;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\BeGroupFieldInterface;
use Pluswerk\BePermissions\Value\DeployProcessing;
use Pluswerk\BePermissions\Value\Identifier;

final class BeGroupConfiguration
{
    private Identifier $identifier;
    private string $configPath;
    /** @var BeGroupFieldCollection<BeGroupFieldInterface> */
    private BeGroupFieldCollection $beGroupFieldCollection;
    private DeployProcessing $deploymentProcessing;

    /**
     * @param Identifier $identifier
     * @param string $configPath
     * @param BeGroupFieldCollection<BeGroupFieldInterface> $beGroupFieldCollection
     */
    public function __construct(Identifier $identifier, string $configPath, BeGroupFieldCollection $beGroupFieldCollection)
    {
        $this->identifier = $identifier;
        $this->configPath = $configPath;
        $this->beGroupFieldCollection = $beGroupFieldCollection;
        $this->deploymentProcessing = DeployProcessing::createWithDefault();

        foreach ($beGroupFieldCollection as $item) {
            if ($item instanceof DeployProcessing) {
                $this->deploymentProcessing = $item;
            }
        }
    }

    public static function createFromBeGroup(BeGroup $beGroup, string $configPath): BeGroupConfiguration
    {
        return new self($beGroup->identifier(), $configPath, $beGroup->beGroupFieldCollection());
    }

    /**
     * @return BeGroupFieldCollection<BeGroupFieldInterface>
     */
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

    /**
     * @return array<mixed>
     */
    public function asArray(): array
    {
        $array = [];

        /** @var BeGroupFieldInterface $field */
        foreach ($this->beGroupFieldCollection as $field) {
            $array[$field->getFieldName()] = $field->yamlConfigurationValue();
        }

        return $array;
    }

    public function getDeploymentProcessing(): DeployProcessing
    {
        return $this->deploymentProcessing;
    }
}
