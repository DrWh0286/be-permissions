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
use Pluswerk\BePermissions\UseCase\ExtendBeGroupByConfigurationFile;
use Pluswerk\BePermissions\Value\AllowedLanguages;
use Pluswerk\BePermissions\Value\BeGroupFieldFactory;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\NonExcludeFields;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \Pluswerk\BePermissions\UseCase\ExtendBeGroupByConfigurationFile
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
final class ExtendBeGroupByConfigurationFileTest extends FunctionalTestCase
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
    public function a_connected_configuration_can_extend_the_be_group_record(): void //phpcs:ignore
    {
        $this->importDataSet(__DIR__ . '/Fixtures/be_groups.xml');

        // Prepare file
        $identifier = new Identifier('test-group');

        $collection = new BeGroupFieldCollection();
        $collection->add(NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title',
                    'abstract'
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
        $factory = new BeGroupFieldFactory($extConfig);
        $builder = new BeGroupFieldCollectionBuilder($factory);
        $repository = new BeGroupConfigurationRepository($builder);
        $repository->write($configuration);

        /** @var ExtendBeGroupByConfigurationFile $useCase */
        $useCase = GeneralUtility::makeInstance(ExtendBeGroupByConfigurationFile::class);

        $useCase->extendGroup('test-group');

        /** @var BeGroupRepository $repo */
        $repo = GeneralUtility::makeInstance(BeGroupRepository::class);

        $actualBeGroup = $repo->findOneByIdentifier(new Identifier('test-group'));

        $expectedCollection = new BeGroupFieldCollection();

        $expectedCollection->add(NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title',
                    'hidden',
                    'abstract'
                ],
                'tt_content' => [
                    'hidden',
                    'some_additiona_field',
                    'another_field'
                ]
            ]
        ));
        $expectedCollection->add(ExplicitAllowDeny::createFromYamlConfiguration([]));
        $expectedCollection->add(AllowedLanguages::createFromYamlConfiguration([]));
        $expectedBeGroup = new BeGroup(
            $identifier,
            'Some group title',
            $expectedCollection
        );

        $this->assertEquals($expectedBeGroup, $actualBeGroup);
    }
}
