<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Api\Controller;

use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Repository\BeGroupRepositoryInterface;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\InvalidIdentifierException;
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

    /**
     * @Route("/be-permissions-api/v1.0/begroup/{identifier}", methods={"GET"})
     * @param string $identifier
     * @return JsonResponse
     */
    public function beGroupsAction(string $identifier): JsonResponse
    {
        try {
            $beGroup = $this->beGroupRepository->findOneByIdentifier(new Identifier($identifier));
        } catch (InvalidIdentifierException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 500);
        }

        if ($beGroup instanceof BeGroup) {
            return new JsonResponse($beGroup->jsonSerialize());
        }

        return new JsonResponse([], 404);
    }
}
