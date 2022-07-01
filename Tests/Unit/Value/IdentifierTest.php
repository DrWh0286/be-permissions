<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Tests\Unit\Value;

use SebastianHofer\BePermissions\Value\InvalidIdentifierException;
use SebastianHofer\BePermissions\Value\Identifier;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Value\Identifier
 */
final class IdentifierTest extends UnitTestCase
{
    /**
     * @test
     */
    public function an_identifier_must_not_contain_spaces(): void //phpcs:ignore
    {
        $this->expectException(InvalidIdentifierException::class);
        $this->expectExceptionMessage('Spaces are not allowed within an identifier string!');
        new Identifier('some identifier');
    }

    /**
     * @test
     */
    public function can_be_casted_to_string(): void //phpcs:ignore
    {
        $id = new Identifier('some-identifier');

        $this->assertSame('some-identifier', (string)$id);
    }

    /**
     * @test
     * @dataProvider titleProvider
     */
    public function a_new_unique_identifier_can_be_built_from_title_string(string $title, string $expectedIdentifierString): void //phpcs:ignore
    {
        $identifier = Identifier::buildNewFromTitle($title);

        $this->assertSame($expectedIdentifierString, (string)$identifier);
    }

    /**
     * @return string[][]
     */
    public function titleProvider(): array
    {
        return [
            'title one' => [
                'title' => 'Das wäre ein Gruppentitel',
                'expectedIdentifierString' => 'das_waere_ein_gruppentitel'
            ],
            'title two' => [
                'title' => 'Gruppentitel äöüßÄÖÜ',
                'expectedIdentifierString' => 'gruppentitel_aeoeuessaeoeue'
            ],
            'title three' => [
                'title' => '[ACCESS] Gruppentitel',
                'expectedIdentifierString' => 'access_gruppentitel'
            ],
            'title four' => [
                'title' => '[ACCESS] Gruppen-Titel',
                'expectedIdentifierString' => 'access_gruppen-titel'
            ]
        ];
    }
}
