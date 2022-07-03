<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Controller\Backend;

use Psr\Http\Message\ResponseInterface;
use SebastianHofer\BePermissions\Repository\BeGroupRepositoryInterface;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

final class BeGroupsCompareModuleController extends ActionController
{
    protected $defaultViewObjectName = BackendTemplateView::class;
    private BeGroupRepositoryInterface $beGroupRepository;

    public function __construct(BeGroupRepositoryInterface $beGroupRepository)
    {
        $this->beGroupRepository = $beGroupRepository;
    }

    protected function initializeView(ViewInterface $view): void
    {
        parent::initializeView($view);

        /** @var BackendTemplateView $view */
        $pageRenderer = $view->getModuleTemplate()->getPageRenderer();
        $pageRenderer->addCssFile('/typo3conf/ext/be_permissions/Resources/Public/Css/bootstrap.min.css');
    }

    public function indexAction(): ResponseInterface
    {
        $beGroups = $this->beGroupRepository->findAllCodeManagedRaw();

        $this->view->assign('beGroups', $beGroups);

        return $this->htmlResponse();
    }

    public function detailAction(string $identifier): ResponseInterface
    {
        $this->view->assign('identifier', $identifier);
        return $this->htmlResponse();
    }
}
