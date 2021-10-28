<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use Pluswerk\BePermissions\Value\AbstractStringArrayField;
use Pluswerk\BePermissions\Value\GroupMods;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Value\GroupMods
 */
final class GroupModsTest extends UnitTestCase
{
    /**
     * @test
     */
    public function field_name_is_groupMods(): void //phpcs:ignore
    {
        $groupMods = GroupMods::createFromYamlConfiguration([]);
        $this->assertSame('groupMods', $groupMods->getFieldName());
    }

    /**
     * @test
     */
    public function extends_AbstractStringArrayField(): void //phpcs:ignore
    {
        $groupMods = GroupMods::createFromYamlConfiguration([]);
        $this->assertInstanceOf(AbstractStringArrayField::class, $groupMods);
    }
}
