<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Tests\Unit\Value;

use InvalidArgumentException;
use SebastianHofer\BePermissions\Value\AbstractStringField;
use SebastianHofer\BePermissions\Value\DeployProcessing;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Value\DeployProcessing
 */
final class DeployProcessingTest extends UnitTestCase
{
    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_value(): void //phpcs:ignore
    {
        $dbValue = 'extend';

        $deployProcessing = DeployProcessing::createFromDBValue($dbValue);

        $this->assertSame($dbValue, $deployProcessing->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_value_and_returned_as_database_value(): void //phpcs:ignore
    {
        $yamlValue = 'overrule';

        $deployProcessing = DeployProcessing::createFromYamlConfiguration($yamlValue);

        $this->assertSame($yamlValue, (string)$deployProcessing);
    }

    /**
     * @test
     */
    public function with_empty_database_field_an_default_deploy_process_extend_is_returned(): void //phpcs:ignore
    {
        $deployProcessing = DeployProcessing::createFromDBValue('');

        $this->assertSame('extend', $deployProcessing->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function field_name_is_deploy_processing(): void //phpcs:ignore
    {
        $deployProcessing = DeployProcessing::createFromYamlConfiguration('extend');
        $this->assertSame('deploy_processing', $deployProcessing->getFieldName());
    }

    /**
     * @test
     */
    public function implements_AbstractStringField(): void //phpcs:ignore
    {
        $deployProcessing = DeployProcessing::createFromYamlConfiguration('extend');
        $this->assertInstanceOf(AbstractStringField::class, $deployProcessing);
    }

    /**
     * @test
     */
    public function deploy_processing_is_immutable_overruled_by_extend(): void //phpcs:ignore
    {
        $deployProcessing = DeployProcessing::createFromDBValue('extend');
        $extendDeployProcessing = DeployProcessing::createFromYamlConfiguration('overrule');

        $actualTitle = $deployProcessing->extend($extendDeployProcessing);

        $this->assertSame('overrule', (string)$actualTitle);
        $this->assertNotSame($deployProcessing, $actualTitle);
        $this->assertNotSame($extendDeployProcessing, $actualTitle);
    }

    /**
     * @test
     * @dataProvider validValuesProvider
     */
    public function can_be_created_with_valid_values_extend_and_overrule(string $value): void //phpcs:ignore
    {
        $fromDb = DeployProcessing::createFromDBValue($value);
        $fromYaml = DeployProcessing::createFromYamlConfiguration($value);

        $this->assertSame($value, (string)$fromDb);
        $this->assertSame($value, (string)$fromYaml);
    }

    /**
     * @return string[][]
     */
    public function validValuesProvider(): array
    {
        return [
            'extend' => [
                'value' => 'extend'
            ],
            'overrule' => [
                'value' => 'extend'
            ]
        ];
    }

    /**
     * @test
     * @dataProvider invalidValuesProvider
     */
    public function can_not_be_created_with_invalid_database_values(string $value): void //phpcs:ignore
    {
        $this->expectException(InvalidArgumentException::class);
        DeployProcessing::createFromDBValue($value);
    }

    /**
     * @test
     * @dataProvider invalidValuesProvider
     */
    public function can_not_be_created_with_invalid_yaml_values(string $value): void //phpcs:ignore
    {
        $this->expectException(InvalidArgumentException::class);
        DeployProcessing::createFromYamlConfiguration($value);
    }

    /**
     * @return string[][]
     */
    public function invalidValuesProvider(): array
    {
        return [
            'invalid a' => [
                'value' => 'some_value_a'
            ],
            'invalid b' => [
                'value' => 'another_value_b'
            ]
        ];
    }

    /**
     * @test
     */
    public function can_be_checked_if_it_is_extend(): void //phpcs:ignore
    {
        $extend = DeployProcessing::createFromDBValue('extend');

        $this->assertTrue($extend->isExtend());
        $this->assertFalse($extend->isOverrule());
    }

    /**
     * @test
     */
    public function can_be_checked_if_it_is_overrule(): void //phpcs:ignore
    {
        $extend = DeployProcessing::createFromDBValue('extend');

        $this->assertTrue($extend->isExtend());
        $this->assertFalse($extend->isOverrule());
    }

    /**
     * @test
     */
    public function tca_value_array_can_be_created(): void //phpcs:ignore
    {
        $tcaItems = DeployProcessing::tcaItems('LLL:path.xlf:key.');

        $this->assertSame(
            [
                ['LLL:path.xlf:key.extend', 'extend'],
                ['LLL:path.xlf:key.overrule', 'overrule']
            ],
            $tcaItems
        );
    }

    /**
     * @test
     */
    public function default_value_can_be_crated(): void //phpcs:ignore
    {
        $extend = DeployProcessing::createWithDefault();

        $this->assertSame('extend', (string)$extend);
    }
}
