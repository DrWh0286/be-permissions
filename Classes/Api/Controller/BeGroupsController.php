<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Api\Controller;

use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Repository\BeGroupRepositoryInterface;
use SebastianHofer\BePermissions\Value\Identifier;
use SebastianHofer\BePermissions\Value\InvalidIdentifierException;
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
    public function getAllCodeManagedBeGroupsAction(): JsonResponse
    {
        $beGroups = $this->beGroupRepository->findAllCodeManaged();
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
