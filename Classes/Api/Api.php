<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Api;

use Pluswerk\BePermissions\Builder\BeGroupFieldCollectionBuilderInterface;
use Pluswerk\BePermissions\Collection\BeGroupCollection;
use Pluswerk\BePermissions\Configuration\ExtensionConfigurationInterface;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\Identifier;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\Uri;

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
        $host = $this->extensionConfiguration->getProductionHost();
        $uri = (new Uri($host))->withHost($host)->withScheme('https')->withPath('/be-permissions-api/v1.0/begroups');

        // @todo: Try catch & response code 200?
        $response = $this->requestFactory->request((string)$uri);
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
}
