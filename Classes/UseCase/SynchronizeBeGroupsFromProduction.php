<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\UseCase;

use Pluswerk\BePermissions\Api\Api;
use Pluswerk\BePermissions\Repository\BeGroupRepositoryInterface;

final class SynchronizeBeGroupsFromProduction
{
    private Api $api;
    private BeGroupRepositoryInterface $beGroupRepository;

    public function __construct(Api $api, BeGroupRepositoryInterface $beGroupRepository)
    {
        $this->api = $api;
        $this->beGroupRepository = $beGroupRepository;
    }

    public function syncAndExport(): void
    {
        $beGroupsFromProd = $this->api->fetchAllSynchronizedBeGroups();
        $this->beGroupRepository->addOrUpdateBeGroups($beGroupsFromProd);
    }
}
