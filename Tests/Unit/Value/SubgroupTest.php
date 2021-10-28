<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use Pluswerk\BePermissions\Value\AbstractIntArrayField;
use Pluswerk\BePermissions\Value\Subgroup;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Value\Subgroup
 */
final class SubgroupTest extends UnitTestCase
{
    /**
    * @test
    */
    public function field_name_is_subgroup(): void
    {
        $pageTypesSelect = Subgroup::createFromYamlConfiguration([]);
        $this->assertSame('subgroup', $pageTypesSelect->getFieldName());
    }

    /**
     * @test
     */
    public function extends_AbstractIntArrayField(): void
    {
        $this->assertInstanceOf(AbstractIntArrayField::class, Subgroup::createFromYamlConfiguration([]));
        $this->assertInstanceOf(AbstractIntArrayField::class, Subgroup::createFromDBValue(''));
    }

}
