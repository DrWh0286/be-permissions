<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "form_consent".
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

namespace SebastianHofer\BePermissions\Api;

use GuzzleHttp\RequestOptions;
use SebastianHofer\BePermissions\Builder\BeGroupFieldCollectionBuilderInterface;
use SebastianHofer\BePermissions\Collection\BeGroupCollection;
use SebastianHofer\BePermissions\Configuration\ExtensionConfigurationInterface;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Value\Identifier;
use TYPO3\CMS\Core\Http\RequestFactory;

final class Api implements ApiInterface
{
    private RequestFactory $requestFactory;
    private ExtensionConfigurationInterface $extensionConfiguration;
    private BeGroupFieldCollectionBuilderInterface $builder;

    public function __construct(RequestFactory $requestFactory, ExtensionConfigurationInterface $extensionConfiguration, BeGroupFieldCollectionBuilderInterface $builder)
    {
        $this->requestFactory = $requestFactory;
        $this->extensionConfiguration = $extensionConfiguration;
        $this->builder = $builder;
    }

    public function fetchAllSynchronizedBeGroups(): BeGroupCollection
    {
        $uri = $this->extensionConfiguration->getApiUri();
        $uri = $uri->withPath('/be-permissions-api/v1.0/begroups');

        // @todo: Try catch & response code 200?
        $response = $this->requestFactory->request(
            (string)$uri,
            'GET',
            [RequestOptions::HEADERS => ['apiToken' => $this->extensionConfiguration->getApiToken()]]
        );
        $content = $response->getBody()->getContents();

        $jsonAnswer = json_decode($content, true);

        $groups = new BeGroupCollection();

        if (is_array($jsonAnswer)) {
            foreach ($jsonAnswer as $jsonBeGroup) {
                $identifier = new Identifier($jsonBeGroup['identifier']);
                unset($jsonBeGroup['identifier']);

                $collection = $this->builder->buildFromConfigurationArray($jsonBeGroup['beGroupFieldCollection']);

                $groups->add(new BeGroup($identifier, $collection));
            }
        }

        return $groups;
    }

    public function fetchBeGroupsByIdentifier(Identifier $identifier): ?BeGroup
    {
        $uri = $this->extensionConfiguration->getApiUri();
        $uri = $uri->withPath('/be-permissions-api/v1.0/begroup/' . $identifier);

        // @todo: Try catch & response code 200?
        $response = $this->requestFactory->request(
            (string)$uri,
            'GET',
            [RequestOptions::HEADERS => ['apiToken' => $this->extensionConfiguration->getApiToken()]]
        );
        $content = $response->getBody()->getContents();

        $jsonBeGroup = json_decode($content, true);

        if (is_array($jsonBeGroup)) {
            $identifier = new Identifier($jsonBeGroup['identifier']);
            unset($jsonBeGroup['identifier']);

            $collection = $this->builder->buildFromConfigurationArray($jsonBeGroup['beGroupFieldCollection']);

            return new BeGroup($identifier, $collection);
        }

        return null;
    }
}
