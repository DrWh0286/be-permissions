<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "be_permissions".
 *
 * Copyright (C) 2022 Sebastian Hofer <sebastian.hofer@s-hofer.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace SebastianHofer\BePermissions\Configuration;

use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Value\BeGroupFieldInterface;
use SebastianHofer\BePermissions\Value\DeployProcessing;
use SebastianHofer\BePermissions\Value\Identifier;

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

    public function getIdentifier(): Identifier
    {
        return $this->identifier();
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

        ksort($array);

        return $array;
    }

    /**
     * @return array<mixed>
     */
    public function getAsArray(): array
    {
        return $this->asArray();
    }

    public function getDeploymentProcessing(): DeployProcessing
    {
        return $this->deploymentProcessing;
    }
}
