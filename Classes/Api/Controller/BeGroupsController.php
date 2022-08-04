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
    public function allSyncBeGroupsAction(): JsonResponse
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
