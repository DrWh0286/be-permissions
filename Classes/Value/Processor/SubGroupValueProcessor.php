<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Value\Processor;

use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Repository\BeGroupRepositoryInterface;
use SebastianHofer\BePermissions\Repository\SubGroupNotFoundException;
use SebastianHofer\BePermissions\Value\BeGroupFieldInterface;
use SebastianHofer\BePermissions\Value\Identifier;
use SebastianHofer\BePermissions\Value\InvalidIdentifierException;
use SebastianHofer\BePermissions\Value\SubGroup;

class SubGroupValueProcessor
{
    private BeGroupRepositoryInterface $beGroupRepository;

    public function __construct(BeGroupRepositoryInterface $beGroupRepository)
    {
        $this->beGroupRepository = $beGroupRepository;
    }

    /**
     * @throws SubGroupNotFoundException
     */
    public function processValuesFromDatabase(string $value): string
    {
        $explodedValue = explode(',', $value);

        $identifierList = [];
        foreach ($explodedValue as $uid) {
            $beGroup = $this->beGroupRepository->findOneByUid((int)$uid);
            if ($beGroup instanceof BeGroup) {
                $identifierList[] = (string)$beGroup->identifier();
                continue;
            }

            throw new SubGroupNotFoundException('The group with uid ' . $uid . ' does not exist!', (string)$uid);
        }

        return implode(',', $identifierList);
    }

    /**
     * @throws SubGroupNotFoundException
     * @throws InvalidIdentifierException
     */
    public function processValuesForDatabase(BeGroupFieldInterface $beGroupField): string
    {
        if (!$beGroupField instanceof SubGroup) {
            throw new \RuntimeException(get_class($beGroupField) . ' can not be processed by ' . __CLASS__ . '!');
        }

        $identifierStrings = array_filter(explode(',', (string)$beGroupField));

        $uids = [];
        $someSubGroupsWereNotFound = false;
        foreach ($identifierStrings as $identifierString) {
            $identifier = new Identifier($identifierString);
            $uid = $this->beGroupRepository->findUidByIdentifier($identifier);

            if ($uid === null) {
                $someSubGroupsWereNotFound = true;
                continue;
            }

            $uids[] = $uid;
        }

        if ($someSubGroupsWereNotFound) {
            throw new SubGroupNotFoundException('Some subgroups could not be found!', implode(',', $uids));
        }

        return implode(',', $uids);
    }
}
