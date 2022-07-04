<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "form_consent".
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

namespace SebastianHofer\BePermissions\Hook;

use SebastianHofer\BePermissions\Value\Identifier;
use SebastianHofer\BePermissions\Value\InvalidIdentifierException;
use TYPO3\CMS\Core\DataHandling\DataHandler;

final class DataHandlerBeGroupsIdentifierHook
{
    /**
     * @param array<int|string> $incomingFieldArray
     * @param string $table
     * @param string $id
     * @param DataHandler $dataHandler
     */
    public function processDatamap_preProcessFieldArray(array &$incomingFieldArray, string $table, string $id, DataHandler $dataHandler): void //phpcs:ignore
    {
        if ($this->identifierNeedsUpdate($table, $incomingFieldArray)) {
            $title = $incomingFieldArray['title'];

            if (is_string($title) && !empty($title)) {
                // @todo: Cover this try catch with some test!
                // @todo: Maybe move this fallback into Identifier::buildNewFromTitle()
                try {
                    $incomingFieldArray['identifier'] = (string)Identifier::buildNewFromTitle($title);
                } catch (\Exception $e) {
                    $incomingFieldArray['identifier'] = md5((string)time());
                }
            } else {
                $incomingFieldArray['identifier'] = md5((string)time());
            }
        }
    }

    /**
     * @param string $table
     * @param array<mixed> $incomingFieldArray
     * @return bool
     */
    private function identifierNeedsUpdate(string $table, array $incomingFieldArray): bool
    {
        return ($table === 'be_groups'
            && isset($incomingFieldArray['title'])
            && isset($incomingFieldArray['identifier'])
            && empty($incomingFieldArray['identifier'])
        );
    }
}
