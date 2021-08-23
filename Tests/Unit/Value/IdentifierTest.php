<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use Pluswerk\BePermissions\Value\InvalidIdentifierException;
use Pluswerk\BePermissions\Value\Identifier;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class IdentifierTest extends UnitTestCase
{
    /**
     * @test
     */
    public function an_identifier_must_not_contain_spaces(): void
    {
        $this->expectException(InvalidIdentifierException::class);
        $this->expectExceptionMessage('Spaces are not allowed within an identifier string!');
        new Identifier('some identifier');
    }

    /**
     * @test
     */
    public function can_be_casted_to_string(): void
    {
        $id = new Identifier('some-identifier');

        $this->assertSame('some-identifier', (string)$id);
    }
}
