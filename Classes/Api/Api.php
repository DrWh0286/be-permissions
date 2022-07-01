<?php

declare(strict_types=1);

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
