<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Configuration;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Configuration\ConfigurationFileMissingException;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\Identifier;
use Symfony\Component\Yaml\Yaml;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
 */
final class BeGroupConfigurationTest extends UnitTestCase
{
    protected $resetSingletonInstances = true;
    private string $basePath;

    protected function setUp(): void
    {
        $this->basePath = __DIR__ . '/Fixtures';
    }

    /**
     * @test
     */
    public function can_be_created_from_be_group_model(): void
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('from-be-group');
        $beGroup = new BeGroup(
            $identifier,
            'Group title',
            [
            'pages' => [
                'title',
                'media'
            ]
        ]);

        $config = BeGroupConfiguration::createFromBeGroup($beGroup, $configPath);

        $expectedConfig = new BeGroupConfiguration(
            $identifier,
            $configPath,
            [
                'title' => 'Group title',
                'non_exclude_fields' => [
                    'pages' => [
                        'title',
                        'media'
                    ]
                ]
            ]
        );

        $this->assertEquals($expectedConfig, $config);
    }

    /**
     * @test
     */
    public function non_exclude_fields_can_be_fetched(): void
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('some-identifier');
        $config = [
            'non_exclude_fields' => [
                'pages' => [
                    'title',
                    'media'
                ]
            ]
        ];

        $config = new BeGroupConfiguration($identifier, $configPath, $config);

        $this->assertSame(
            [
                'pages' => [
                    'title',
                    'media'
                ]
            ],
            $config->nonExcludeFields()
        );
    }

    private function cleanup(Identifier $identifier)
    {
        @unlink($this->basePath . '/config/be_groups/' . $identifier . '/be_group.yaml');
        rmdir($this->basePath . '/config/be_groups/' . $identifier);
    }
}
