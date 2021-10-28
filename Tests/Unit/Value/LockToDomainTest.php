<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use Pluswerk\BePermissions\Value\LockToDomain;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Value\LockToDomain
 */
final class LockToDomainTest extends UnitTestCase
{
    /**
     * @test
     */
    public function field_name_is_lockToDomain(): void //phpcs:ignore
    {
        $lockToDomain = LockToDomain::createFromYamlConfiguration('');
        $this->assertSame('lockToDomain', $lockToDomain->getFieldName());
    }

    /**
     * @test
     */
    public function LockToDomain_is_changed_by_extend(): void //phpcs:ignore
    {
        $lockToDomain = LockToDomain::createFromDBValue('domain.a');
        $extendLockToDomain = LockToDomain::createFromYamlConfiguration('domain.b');

        $actualLockToDomain = $lockToDomain->extend($extendLockToDomain);

        $this->assertSame('domain.b', (string)$actualLockToDomain);
        $this->assertNotSame($lockToDomain, $actualLockToDomain);
        $this->assertNotSame($extendLockToDomain, $actualLockToDomain);
    }
}
