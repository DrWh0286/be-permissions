<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Controller\Backend;

use Jfcherng\Diff\DiffHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SebastianHofer\BePermissions\Diff\BeGroupDiffCreator;
use SebastianHofer\BePermissions\Repository\BeGroupRepositoryInterface;
use SebastianHofer\BePermissions\UseCase\ExportBeGroupsToConfigurationFile;
use SebastianHofer\BePermissions\UseCase\ExtendOrCreateBeGroupByConfigurationFile;
use SebastianHofer\BePermissions\UseCase\OverruleOrCreateBeGroupFromConfigurationFile;
use SebastianHofer\BePermissions\UseCase\SynchronizeBeGroupsFromProduction;
use SebastianHofer\BePermissions\Value\Identifier;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

final class BeGroupsCompareModuleController
{
    private BeGroupRepositoryInterface $beGroupRepository;
    private OverruleOrCreateBeGroupFromConfigurationFile $overruleOrCreateBeGroupFromConfigurationFile;
    private ExtendOrCreateBeGroupByConfigurationFile $extendOrCreateBeGroupByConfigurationFile;
    private ExportBeGroupsToConfigurationFile $exportBeGroupsToConfigurationFile;
    private SynchronizeBeGroupsFromProduction $synchronizeBeGroupsFromProduction;
    private BeGroupDiffCreator $beGroupDiffCreator;
    private ModuleTemplateFactory $moduleTemplateFactory;
    private PageRenderer $pageRenderer;
    private ModuleTemplate $moduleTemplate;
    private StandaloneView $view;

    public function __construct(
        BeGroupRepositoryInterface $beGroupRepository,
        OverruleOrCreateBeGroupFromConfigurationFile $overruleOrCreateBeGroupFromConfigurationFile,
        ExtendOrCreateBeGroupByConfigurationFile $extendOrCreateBeGroupByConfigurationFile,
        ExportBeGroupsToConfigurationFile $exportBeGroupsToConfigurationFile,
        SynchronizeBeGroupsFromProduction $synchronizeBeGroupsFromProduction,
        BeGroupDiffCreator $beGroupDiffCreator,
        PageRenderer $pageRenderer,
        ModuleTemplateFactory $moduleTemplateFactory
    ) {
        $this->beGroupRepository = $beGroupRepository;
        $this->overruleOrCreateBeGroupFromConfigurationFile = $overruleOrCreateBeGroupFromConfigurationFile;
        $this->extendOrCreateBeGroupByConfigurationFile = $extendOrCreateBeGroupByConfigurationFile;
        $this->exportBeGroupsToConfigurationFile = $exportBeGroupsToConfigurationFile;
        $this->synchronizeBeGroupsFromProduction = $synchronizeBeGroupsFromProduction;
        $this->beGroupDiffCreator = $beGroupDiffCreator;
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->pageRenderer = $pageRenderer;

        /** @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view = $view;
    }

    /**
     * Injects the request object for the current request, and renders the overview of all redirects
     */
    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $actionName = $request->getQueryParams()['action'] ?? 'index';
        $this->moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->pageRenderer->addCssInlineBlock('diffStyles', DiffHelper::getStyleSheet(), true);
        $this->pageRenderer->addCssFile('EXT:be_permissions/Resources/Public/Css/BeGroupCompare.css');
        $this->getLanguageService()->includeLLFile('EXT:be_permissions/Resources/Private/Language/locallang_mod.xlf');
        $this->initializeView($actionName);
        $result = $this->{$actionName . 'Action'}($request);

        if ($result instanceof ResponseInterface) {
            return $result;
        }

        $this->moduleTemplate->setContent($this->view->render());
        return new HtmlResponse($this->moduleTemplate->renderContent());
    }

    protected function initializeView(string $templateName): void
    {
        $this->view->setTemplate($templateName);
        $this->view->setTemplateRootPaths(['EXT:be_permissions/Resources/Private/Templates']);
        $this->view->setPartialRootPaths(['EXT:be_permissions/Resources/Private/Partials']);
        $this->view->setLayoutRootPaths(['EXT:be_permissions/Resources/Private/Layouts']);
        $this->view->getRenderingContext()->setControllerName('BeGroupsCompare');
    }

    public function indexAction(ServerRequestInterface $request): void
    {
        $beGroups = $this->beGroupRepository->findAllCodeManagedRaw();

        $this->view->assign('beGroups', $beGroups);
    }

    public function detailAction(ServerRequestInterface $request): void
    {
        $identifierName = $request->getQueryParams()['identifier'] ?? '';
        $identifier = new Identifier($identifierName);
        $configPath = Environment::getConfigPath();

        $beGroup = $this->beGroupRepository->findOneByIdentifierRaw($identifier);

        $yamlToRecordResult = $this->beGroupDiffCreator->createYamlToRecordDiff($identifier);

        $recordToYamlResult = $this->beGroupDiffCreator->createRecordToYamlDiff($identifier);

        $this->view->assign('identifier', $identifier);
        $this->view->assign('yamlToRecordResult', $yamlToRecordResult);
        $this->view->assign('recordToYamlResult', $recordToYamlResult);
        $this->view->assign('group', $beGroup);
        $this->view->assign('configPath', $configPath);
    }

    public function overruleAction(ServerRequestInterface $request): ResponseInterface
    {
        $identifierName = $request->getQueryParams()['identifier'] ?? '';
        $identifier = new Identifier($identifierName);

        $this->overruleOrCreateBeGroupFromConfigurationFile->overruleGroup($identifier);

        return new RedirectResponse($this->buildActionUriWithIdentifier($identifier, 'detail'));
    }

    public function extendAction(ServerRequestInterface $request): ResponseInterface
    {
        $identifierName = $request->getQueryParams()['identifier'] ?? '';
        $identifier = new Identifier($identifierName);

        $this->extendOrCreateBeGroupByConfigurationFile->extendGroup($identifier);

        return new RedirectResponse($this->buildActionUriWithIdentifier($identifier, 'detail'));
    }

    public function exportAction(ServerRequestInterface $request): ResponseInterface
    {
        $identifierName = $request->getQueryParams()['identifier'] ?? '';
        $identifier = new Identifier($identifierName);

        $this->exportBeGroupsToConfigurationFile->exportGroup($identifier);

        return new RedirectResponse($this->buildActionUriWithIdentifier($identifier, 'detail'));
    }

    public function synchronizeRemoteAction(ServerRequestInterface $request): ResponseInterface
    {
        $identifierName = $request->getQueryParams()['identifier'] ?? '';
        $identifier = new Identifier($identifierName);

        $this->synchronizeBeGroupsFromProduction->syncBeGroup($identifier);

        return new RedirectResponse($this->buildActionUriWithIdentifier($identifier, 'detail'));
    }

    public function exportAllAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->exportBeGroupsToConfigurationFile->exportGroups();

        return new RedirectResponse($this->buildActionUriWithIdentifier(null, 'index'));
    }

    public function synchronizeAllRemoteAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->synchronizeBeGroupsFromProduction->syncBeGroups();

        return new RedirectResponse($this->buildActionUriWithIdentifier(null, 'index'));
    }

    private function buildActionUriWithIdentifier(?Identifier $identifier, string $actionName): string
    {
        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $parameters = [
            'action' => $actionName
        ];

        if ($identifier instanceof Identifier) {
            $parameters['identifier'] = (string)$identifier;
        }

        return (string)$uriBuilder->buildUriFromRoute('system_bepermissions', $parameters);
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
