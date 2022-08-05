<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Diff;

use Jfcherng\Diff\Differ;
use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Renderer\RendererConstant;
use SebastianHofer\BePermissions\Configuration\ConfigurationFileMissingException;
use SebastianHofer\BePermissions\Repository\BeGroupConfigurationRepositoryInterface;
use SebastianHofer\BePermissions\Repository\BeGroupRepositoryInterface;
use SebastianHofer\BePermissions\Value\Identifier;
use TYPO3\CMS\Core\Core\Environment;

final class BeGroupDiffCreator
{
    private string $configPath;

    /**
     * @var array<string, mixed>
     */
    private array $diffOptions = [
        // show how many neighbor lines
        // Differ::CONTEXT_ALL can be used to show the whole file
        'context' => Differ::CONTEXT_ALL,
        // ignore case difference
        'ignoreCase' => false,
        // ignore whitespace difference
        'ignoreWhitespace' => false,
    ];

    /**
     * @var array<string, mixed>
     */
    private array $rendererOptions = [
        // how detailed the rendered HTML is? (none, line, word, char)
        'detailLevel' => 'word',
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
    private BeGroupConfigurationRepositoryInterface $beGroupConfigurationRepository;
    private BeGroupRepositoryInterface $beGroupRepository;
    private string $configurationFileYaml = '';
    private string $beGroupYaml = '';

    public function __construct(
        BeGroupRepositoryInterface $beGroupRepository,
        BeGroupConfigurationRepositoryInterface $beGroupConfigurationRepository
    ) {
        $this->configPath = Environment::getConfigPath();
        $this->beGroupConfigurationRepository = $beGroupConfigurationRepository;
        $this->beGroupRepository = $beGroupRepository;
    }

    public function createYamlToRecordDiff(Identifier $identifier): string
    {
        try {
            $configurationFileYaml = $this->getConfigurationFileYaml($identifier);
        } catch (ConfigurationFileMissingException $e) {
            $configurationFileYaml = '';
        }

        $beGroupYaml = $this->getBeGroupYaml($identifier);

        return DiffHelper::calculate(
            $beGroupYaml,
            $configurationFileYaml,
            'SideBySide',
            $this->diffOptions,
            ['detailLevel' => 'none'] + $this->rendererOptions,
        );
    }

    public function createRecordToYamlDiff(Identifier $identifier): string
    {
        try {
            $configurationFileYaml = $this->getConfigurationFileYaml($identifier);
        } catch (ConfigurationFileMissingException $e) {
            $configurationFileYaml = '';
        }

        $beGroupYaml = $this->getBeGroupYaml($identifier);

        return DiffHelper::calculate(
            $configurationFileYaml,
            $beGroupYaml,
            'SideBySide',
            $this->diffOptions,
            ['detailLevel' => 'none'] + $this->rendererOptions
        );
    }

    /**
     * @throws ConfigurationFileMissingException
     */
    private function getConfigurationFileYaml(Identifier $identifier): string
    {
        if (empty($this->configurationFileYaml)) {
            $this->configurationFileYaml = $this->beGroupConfigurationRepository->loadYamlString($identifier, $this->configPath);
        }

        return $this->configurationFileYaml;
    }

    private function getBeGroupYaml(Identifier $identifier): string
    {
        if (empty($this->beGroupYaml)) {
            $this->beGroupYaml = $this->beGroupRepository->loadYamlString($identifier);
        }

        return $this->beGroupYaml;
    }
}
