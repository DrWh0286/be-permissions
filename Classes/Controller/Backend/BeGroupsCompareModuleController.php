<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Controller\Backend;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

final class BeGroupsCompareModuleController extends ActionController
{
    public function indexAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }
}
