<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\UseCase;

use Pluswerk\BePermissions\Api\Api;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Repository\BeGroupRepositoryInterface;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\Source;

final class SynchronizeBeGroupsFromProduction
{
    private Api $api;
    private BeGroupRepositoryInterface $beGroupRepository;

    public function __construct(Api $api, BeGroupRepositoryInterface $beGroupRepository)
    {
        $this->api = $api;
        $this->beGroupRepository = $beGroupRepository;
    }

    public function syncBeGroups(Source $source): void
    {
        $beGroupsFromProd = $this->api->fetchAllSynchronizedBeGroups($source);
        $this->beGroupRepository->addOrUpdateBeGroups($beGroupsFromProd);
    }

    public function syncBeGroup(Source $source, Identifier $identifier): void
    {
        $beGroup = $this->api->fetchBeGroupsByIdentifier($source, $identifier);

        if (!$beGroup instanceof BeGroup) {
            throw new \RuntimeException('No be group found for ' . $identifier . '!');
        }

        $this->beGroupRepository->addOrUpdateBeGroup($beGroup);
    }
}
