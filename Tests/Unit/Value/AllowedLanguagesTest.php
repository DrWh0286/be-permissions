<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pluswerk\BePermissions\Value\AllowedLanguages;

/**
 * @covers \Pluswerk\BePermissions\Value\AllowedLanguages
 */
final class AllowedLanguagesTest extends UnitTestCase
{
    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_array(): void
    {
        $dbValue = '0,3,5';

        $allowedLanguages = AllowedLanguages::createFromDBValue($dbValue);

        $this->assertSame([0,3,5], $allowedLanguages->asArray());
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_array_and_returned_as_database_value(): void
    {
        $confArray = [0,3,5];

        $allowedLanguages = AllowedLanguages::createFromConfigurationArray($confArray);

        $this->assertSame('0,3,5', (string)$allowedLanguages);
    }

    /**
     * @test
     */
    public function with_empty_database_field_an_empty_language_array_is_returned(): void
    {
        $allowedLanguages = AllowedLanguages::createFromDBValue('');

        $this->assertSame([], $allowedLanguages->asArray());
    }

    /**
     * @test
     */
    public function can_be_extended_by_another_allowed_languages_object(): void
    {
        $confArray = [0,3,5];

        $allowedLanguages = AllowedLanguages::createFromConfigurationArray($confArray);

        $extendAllowedLanguages = AllowedLanguages::createFromConfigurationArray([3,4,5]);

        $this->assertSame('0,3,4,5', (string)($allowedLanguages->extend($extendAllowedLanguages)));
    }
}
