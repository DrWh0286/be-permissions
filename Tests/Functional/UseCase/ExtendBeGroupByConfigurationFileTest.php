<?php

declare(strict_types=1);

namespace Functional\UseCase;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Repository\BeGroupConfigurationRepository;
use Pluswerk\BePermissions\Repository\BeGroupRepository;
use Pluswerk\BePermissions\UseCase\ExtendBeGroupByConfigurationFile;
use Pluswerk\BePermissions\Value\Identifier;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \Pluswerk\BePermissions\UseCase\ExtendBeGroupByConfigurationFile
 */
final class ExtendBeGroupByConfigurationFileTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/be_permissions'
    ];

    /**
     * @test
     */
    public function a_connected_configuration_can_extend_the_be_group_record(): void
    {
        $this->importDataSet(__DIR__ . '/Fixtures/be_groups.xml');

        // Prepare file
        $identifier = new Identifier('test-group');
        $config = [
            'title' => 'Some new group title',
            'non_exclude_fields' => [
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
        ];

        $configuration = new BeGroupConfiguration($identifier, Environment::getConfigPath(), $config);
        $repository = new BeGroupConfigurationRepository();
        $repository->write($configuration);

        /** @var ExtendBeGroupByConfigurationFile $useCase */
        $useCase = GeneralUtility::makeInstance(ExtendBeGroupByConfigurationFile::class);

        $useCase->extendGroup('test-group');

        /** @var BeGroupRepository $repo */
        $repo = GeneralUtility::makeInstance(BeGroupRepository::class);

        $actualBeGroup = $repo->findOneByIdentifier(new Identifier('test-group'));

        $expectedBeGroup = new BeGroup($identifier, 'Some group title', [
            'pages' => [
                'abstract',
                'hidden',
                'title'
            ],
            'tt_content' => [
                'another_field',
                'hidden',
                'some_additiona_field'
            ]
        ]);

        $this->assertEquals($expectedBeGroup, $actualBeGroup);
    }
}
