<?php

declare(strict_types=1);

namespace Functional\UseCase;

use Pluswerk\BePermissions\Builder\BeGroupFieldCollectionBuilder;
use Pluswerk\BePermissions\Collection\BeGroupFieldCollection;
use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Configuration\ExtensionConfiguration;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Repository\BeGroupConfigurationRepository;
use Pluswerk\BePermissions\Repository\BeGroupRepository;
use Pluswerk\BePermissions\Value\AllowedLanguages;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\NonExcludeFields;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Pluswerk\BePermissions\UseCase\OverruleBeGroupFromConfigurationFile;

/**
 * @covers \Pluswerk\BePermissions\UseCase\OverruleBeGroupFromConfigurationFile
 * @uses \Pluswerk\BePermissions\Configuration\BeGroupConfiguration
 * @uses \Pluswerk\BePermissions\Model\BeGroup
 * @uses \Pluswerk\BePermissions\Repository\BeGroupConfigurationRepository
 * @uses \Pluswerk\BePermissions\Repository\BeGroupRepository
 * @uses \Pluswerk\BePermissions\Value\AllowedLanguages
 * @uses \Pluswerk\BePermissions\Value\ExplicitAllowDeny
 * @uses \Pluswerk\BePermissions\Value\Identifier
 * @uses \Pluswerk\BePermissions\Value\NonExcludeFields
 * @uses \TYPO3\CMS\Core\Core\Environment
 * @uses \TYPO3\CMS\Core\Utility\GeneralUtility
 */
final class OverruleBeGroupFromConfigurationFileTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/be_permissions'
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_permissions'] = [];
    }

    /**
     * @test
     */
    public function a_connected_configuration_can_overrule_the_be_group_record(): void
    {
        $this->importDataSet(__DIR__ . '/Fixtures/be_groups.xml');

        // Prepare file
        $identifier = new Identifier('test-group');

        $collection = new BeGroupFieldCollection();
        $collection->add(NonExcludeFields::createFromConfigurationArray(
            [
                'pages' => [
                    'title'
                ],
                'tt_content' => [
                    'some_additiona_field',
                    'another_field',
                    'hidden'
                ]
            ]
        ));

        $configuration = new BeGroupConfiguration($identifier, Environment::getConfigPath(), 'Some new group title', $collection);
        $extConfig = new ExtensionConfiguration();
        $builder = new BeGroupFieldCollectionBuilder($extConfig);
        $repository = new BeGroupConfigurationRepository($builder);
        $repository->write($configuration);

        /** @var OverruleBeGroupFromConfigurationFile $useCase */
        $useCase = GeneralUtility::makeInstance(OverruleBeGroupFromConfigurationFile::class);

        $useCase->overruleGroup('test-group');

        /** @var BeGroupRepository $repo */
        $repo = GeneralUtility::makeInstance(BeGroupRepository::class);

        $actualBeGroup = $repo->findOneByIdentifier(new Identifier('test-group'));

        $collection = new BeGroupFieldCollection();
        $collection->add(NonExcludeFields::createFromConfigurationArray(
            [
                'pages' => [
                    'title'
                ],
                'tt_content' => [
                    'some_additiona_field',
                    'another_field',
                    'hidden'
                ]
            ]
        ));
        $collection->add(ExplicitAllowDeny::createFromConfigurationArray([]));
        $collection->add(AllowedLanguages::createFromConfigurationArray([]));

        $expectedBeGroup = new BeGroup(
            $identifier,
            'Some new group title',
            $collection
        );

        $this->assertEquals($expectedBeGroup, $actualBeGroup);
    }
}
