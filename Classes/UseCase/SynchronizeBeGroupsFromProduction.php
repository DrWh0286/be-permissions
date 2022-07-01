<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\UseCase;

use SebastianHofer\BePermissions\Api\Api;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Repository\BeGroupRepositoryInterface;
use SebastianHofer\BePermissions\Value\Identifier;

final class SynchronizeBeGroupsFromProduction
{
    private Api $api;
    private BeGroupRepositoryInterface $beGroupRepository;

    public function __construct(Api $api, BeGroupRepositoryInterface $beGroupRepository)
    {
        $this->api = $api;
        $this->beGroupRepository = $beGroupRepository;
    }

    public function syncBeGroups(): void
    {
        $beGroupsFromProd = $this->api->fetchAllSynchronizedBeGroups();
        $this->beGroupRepository->addOrUpdateBeGroups($beGroupsFromProd);
    }

    public function syncBeGroup(Identifier $identifier): void
    {
        $beGroup = $this->api->fetchBeGroupsByIdentifier($identifier);

        if (!$beGroup instanceof BeGroup) {
            throw new \RuntimeException('No be group found for ' . $identifier . '!');
        }

        $this->beGroupRepository->addOrUpdateBeGroup($beGroup);
    }
}
