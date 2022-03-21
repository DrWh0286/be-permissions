<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Hook;

use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\InvalidIdentifierException;
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
