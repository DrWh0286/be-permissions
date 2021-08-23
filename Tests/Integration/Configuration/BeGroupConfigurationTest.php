<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Integration\Configuration;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\Identifier;
use Symfony\Component\Yaml\Yaml;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
 * @covers \Pluswerk\BePermissions\Model\BeGroup;
 */
final class BeGroupConfigurationTest extends UnitTestCase
{
    private string $basePath;

    protected function setUp(): void
    {
        $this->basePath = __DIR__ . '/Fixtures';
    }

    /**
     * @test
     */
    public function be_group_can_be_written_to_configuration_file(): void
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
            ]
        );

        $config = BeGroupConfiguration::createFromBeGroup($beGroup, $configPath);
        $config->write();

        $expectedFilename = $configPath . '/be_groups/' . $identifier . '/be_group.yaml';
        $this->assertFileExists($expectedFilename);
        $expectedValue = [
            'title' => 'Group title',
            'non_exclude_fields' => [
                'pages' => [
                    'title',
                    'media'
                ]
            ]
        ];

        $actualContent = Yaml::parse(file_get_contents($expectedFilename));

        $this->assertSame($expectedValue, $actualContent);

        $this->cleanup($identifier);
    }

    private function cleanup(Identifier $identifier)
    {
        @unlink($this->basePath . '/config/be_groups/' . $identifier . '/be_group.yaml');
        rmdir($this->basePath . '/config/be_groups/' . $identifier);
        rmdir($this->basePath . '/config/be_groups');
        rmdir($this->basePath . '/config');
        rmdir($this->basePath);
    }
}
