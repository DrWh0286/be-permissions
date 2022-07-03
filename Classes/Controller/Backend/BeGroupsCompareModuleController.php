<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Controller\Backend;

use Jfcherng\Diff\Differ;
use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Renderer\RendererConstant;
use Psr\Http\Message\ResponseInterface;
use SebastianHofer\BePermissions\Repository\BeGroupConfigurationRepositoryInterface;
use SebastianHofer\BePermissions\Repository\BeGroupRepositoryInterface;
use SebastianHofer\BePermissions\Value\Identifier;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

final class BeGroupsCompareModuleController extends ActionController
{
    protected $defaultViewObjectName = BackendTemplateView::class;
    private BeGroupRepositoryInterface $beGroupRepository;
    private BeGroupConfigurationRepositoryInterface $beGroupConfigurationRepository;

    public function __construct(BeGroupRepositoryInterface $beGroupRepository, BeGroupConfigurationRepositoryInterface $beGroupConfigurationRepository)
    {
        $this->beGroupRepository = $beGroupRepository;
        $this->beGroupConfigurationRepository = $beGroupConfigurationRepository;
    }

    protected function initializeView(ViewInterface $view): void
    {
        parent::initializeView($view);

        /** @var BackendTemplateView $view */
        $pageRenderer = $view->getModuleTemplate()->getPageRenderer();
        $pageRenderer->addCssFile('/typo3conf/ext/be_permissions/Resources/Public/Css/bootstrap.min.css');
        $pageRenderer->addCssInlineBlock('diffStyles', DiffHelper::getStyleSheet(), true);
    }

    public function indexAction(): ResponseInterface
    {
        $beGroups = $this->beGroupRepository->findAllCodeManagedRaw();

        $this->view->assign('beGroups', $beGroups);

        return $this->htmlResponse();
    }

    public function detailAction(string $identifier): ResponseInterface
    {
        $identifier = new Identifier($identifier);
        $configPath = Environment::getConfigPath();

        $configurationFileYaml = $this->beGroupConfigurationRepository->loadYamlString($identifier, $configPath);
        $beGroupYaml = $this->beGroupRepository->loadYamlString($identifier);
        $beGroup = $this->beGroupRepository->findOneByIdentifierRaw($identifier);

        $diffOptions = [
            // show how many neighbor lines
            // Differ::CONTEXT_ALL can be used to show the whole file
            'context' => Differ::CONTEXT_ALL,
            // ignore case difference
            'ignoreCase' => false,
            // ignore whitespace difference
            'ignoreWhitespace' => false,
        ];

        $rendererOptions = [
            // how detailed the rendered HTML is? (none, line, word, char)
            'detailLevel' => 'line',
            // renderer language: eng, cht, chs, jpn, ...
            // or an array which has the same keys with a language file
            'language' => 'eng',
            // show line numbers in HTML renderers
            'lineNumbers' => true,
            // show a separator between different diff hunks in HTML renderers
            'separateBlock' => true,
            // show the (table) header
            'showHeader' => true,
            // the frontend HTML could use CSS "white-space: pre;" to visualize consecutive whitespaces
            // but if you want to visualize them in the backend with "&nbsp;", you can set this to true
            'spacesToNbsp' => false,
            // HTML renderer tab width (negative = do not convert into spaces)
            'tabSize' => 4,
            // this option is currently only for the Combined renderer.
            // it determines whether a replace-type block should be merged or not
            // depending on the content changed ratio, which values between 0 and 1.
            'mergeThreshold' => 0.8,
            // this option is currently only for the Unified and the Context renderers.
            // RendererConstant::CLI_COLOR_AUTO = colorize the output if possible (default)
            // RendererConstant::CLI_COLOR_ENABLE = force to colorize the output
            // RendererConstant::CLI_COLOR_DISABLE = force not to colorize the output
            'cliColorization' => RendererConstant::CLI_COLOR_AUTO,
            // this option is currently only for the Json renderer.
            // internally, ops (tags) are all int type but this is not good for human reading.
            // set this to "true" to convert them into string form before outputting.
            'outputTagAsString' => false,
            // this option is currently only for the Json renderer.
            // it controls how the output JSON is formatted.
            // see available options on https://www.php.net/manual/en/function.json-encode.php
            'jsonEncodeFlags' => \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE,
            // this option is currently effective when the "detailLevel" is "word"
            // characters listed in this array can be used to make diff segments into a whole
            // for example, making "<del>good</del>-<del>looking</del>" into "<del>good-looking</del>"
            // this should bring better readability but set this to empty array if you do not want it
            'wordGlues' => [' ', '-'],
            // change this value to a string as the returned diff if the two input strings are identical
            'resultForIdenticals' => null,
            // extra HTML classes added to the DOM of the diff container
            'wrapperClasses' => ['diff-wrapper'],
        ];

        $inlineResult = DiffHelper::calculate(
            $beGroupYaml,
            $configurationFileYaml,
            'Inline',
            $diffOptions,
            ['detailLevel' => 'none'] + $rendererOptions,
        );

        $this->view->assign('identifier', $identifier);
        $this->view->assign('result', $inlineResult);
        $this->view->assign('group', $beGroup);
        $this->view->assign('configPath', $configPath);
        return $this->htmlResponse();
    }
}
