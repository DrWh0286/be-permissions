<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Tests\Unit\Value;

use SebastianHofer\BePermissions\Value\AbstractStringArrayField;
use SebastianHofer\BePermissions\Value\GroupMods;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Value\GroupMods
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
