<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Listener;

use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Repository\BeGroupRepository;
use TYPO3\CMS\Backend\Controller\Event\AfterFormEnginePageInitializedEvent;
use TYPO3\CMS\Core\Core\ApplicationContext;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class BeGroupsEditListener
{
    private BeGroupRepository $beGroupRepository;
    private ApplicationContext $applicationContext;

    public function __construct(BeGroupRepository $beGroupRepository, ApplicationContext $applicationContext)
    {
        $this->beGroupRepository = $beGroupRepository;
        $this->applicationContext = $applicationContext;
    }

    public function __invoke(AfterFormEnginePageInitializedEvent $event): void
    {
        if (!$this->applicationContext->isProduction()) {
            return;
        }

        /** @var array<string, array<int, string>> $parsedBody */
        $parsedBody = $event->getRequest()->getParsedBody();
        $queryParams = $event->getRequest()->getQueryParams();
        $editConf = $parsedBody['edit'] ?? $queryParams['edit'] ?? [];

        if ($this->showMessageForBeGroup($editConf)) {
            /** @var FlashMessage $message */
            $message = GeneralUtility::makeInstance(
                FlashMessage::class,
                'You are editing a code managed group! Only go on if you know what you are doing!',
                'New Warning',
                AbstractMessage::WARNING
            );

            /** @var FlashMessageService $flashMessageService */
            $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
            $queue = $flashMessageService->getMessageQueueByIdentifier();
            $queue->addMessage($message);
        }
    }

    /**
     * @param array<string, array<int, string>> $editConfig
     * @return bool
     */
    private function showMessageForBeGroup(array $editConfig): bool
    {
        if (array_key_exists('be_groups', $editConfig) && is_array($editConfig['be_groups'])) {
            $uid = array_key_first($editConfig['be_groups']);

            if ($editConfig['be_groups'][$uid] === 'edit') {
                $beGroup = $this->beGroupRepository->findOneByUid((int)$uid);

                if ($beGroup instanceof BeGroup && $beGroup->isCodeManaged()) {
                    return true;
                }
            }
        }

        return false;
    }
}
