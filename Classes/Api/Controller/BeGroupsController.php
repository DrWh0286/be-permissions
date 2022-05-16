<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Api\Controller;

use Pluswerk\BePermissions\Repository\BeGroupRepositoryInterface;
use Symfony\Component\Routing\Annotation\Route;
use TYPO3\CMS\Core\Http\JsonResponse;

final class BeGroupsController
{
    private BeGroupRepositoryInterface $beGroupRepository;

    public function __construct(BeGroupRepositoryInterface $beGroupRepository)
    {
        $this->beGroupRepository = $beGroupRepository;
    }

    /**
     * @Route("/be-permissions-api/v1.0/begroups", methods={"GET"})
     * @return JsonResponse
     */
    public function allSyncBeGroupsAction(): JsonResponse
    {
        $beGroups = $this->beGroupRepository->findAllForBulkExport();
        return new JsonResponse($beGroups->jsonSerialize());
    }
}
