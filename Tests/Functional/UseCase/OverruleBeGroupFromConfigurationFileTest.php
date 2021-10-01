<?php

declare(strict_types=1);

namespace Functional\UseCase;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Repository\BeGroupConfigurationRepository;
use Pluswerk\BePermissions\Repository\BeGroupRepository;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\NonExcludeFields;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Pluswerk\BePermissions\UseCase\OverruleBeGroupFromConfigurationFile;

/**
 * @covers \Pluswerk\BePermissions\UseCase\OverruleBeGroupFromConfigurationFile
 */
final class OverruleBeGroupFromConfigurationFileTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/be_permissions'
    ];

    /**
     * @test
     */
    public function a_connected_configuration_can_overrule_the_be_group_record(): void
    {
        $this->importDataSet(__DIR__ . '/Fixtures/be_groups.xml');

        // Prepare file
        $identifier = new Identifier('test-group');
        $config = [
            'title' => 'Some new group title',
            'non_exclude_fields' => [
                'pages' => [
                    'title'
                ],
                'tt_content' => [
                    'some_additiona_field',
                    'another_field',
                    'hidden'
                ]
            ]
        ];
        $configuration = BeGroupConfiguration::createFromConfigurationArray($identifier, Environment::getConfigPath(), $config);
        $repository = new BeGroupConfigurationRepository();
        $repository->write($configuration);

        /** @var OverruleBeGroupFromConfigurationFile $useCase */
        $useCase = GeneralUtility::makeInstance(OverruleBeGroupFromConfigurationFile::class);

        $useCase->overruleGroup('test-group');

        /** @var BeGroupRepository $repo */
        $repo = GeneralUtility::makeInstance(BeGroupRepository::class);

        $actualBeGroup = $repo->findOneByIdentifier(new Identifier('test-group'));

        $expectedBeGroup = new BeGroup($identifier, 'Some new group title', NonExcludeFields::createFromConfigurationArray([
            'pages' => [
                'title'
            ],
            'tt_content' => [
                'some_additiona_field',
                'another_field',
                'hidden'
            ]
        ]),
        ExplicitAllowDeny::createFromConfigurationArray([]));

        $this->assertEquals($expectedBeGroup, $actualBeGroup);
    }
}
