<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use InvalidArgumentException;
use Pluswerk\BePermissions\Configuration\ExtensionConfigurationInterface;
use Pluswerk\BePermissions\Configuration\NoValueObjectConfiguredException;
use Pluswerk\BePermissions\Value\ArrayBasedFieldInterface;
use Pluswerk\BePermissions\Value\StringBasedFieldInterface;
use Pluswerk\BePermissions\Value\TablesSelect;
use Pluswerk\BePermissions\Value\Title;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pluswerk\BePermissions\Value\BeGroupFieldFactory;

/**
 * @covers \Pluswerk\BePermissions\Value\BeGroupFieldFactory
 */
final class BeGroupFieldFactoryTest extends UnitTestCase
{
    /**
     * @test
     */
    public function array_based_field_can_be_created(): void
    {
        $configuration = $this->getMockBuilder(ExtensionConfigurationInterface::class)->getMock();
        $factory = new BeGroupFieldFactory($configuration);
        $fieldName = 'tables_selected';

        $configuration->expects($this->once())->method('getClassNameByFieldName')->with($fieldName)->willReturn(TablesSelect::class);

        /** @var ArrayBasedFieldInterface $field */
        $field = $factory->buildFromFieldNameAndYamlValue($fieldName, ['pages', 'tt_content']);

        $this->assertInstanceOf(ArrayBasedFieldInterface::class, $field);
        $this->assertSame('tables_select', $field->getFieldName());
        $this->assertSame(['pages', 'tt_content'], $field->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function a_string_based_field_can_be_created(): void
    {
        $configuration = $this->getMockBuilder(ExtensionConfigurationInterface::class)->getMock();
        $factory = new BeGroupFieldFactory($configuration);
        $fieldName = 'title';

        $configuration->expects($this->once())->method('getClassNameByFieldName')->with($fieldName)->willReturn(Title::class);

        /** @var StringBasedFieldInterface $field */
        $field = $factory->buildFromFieldNameAndYamlValue($fieldName, 'group title');

        $this->assertInstanceOf(StringBasedFieldInterface::class, $field);
        $this->assertSame('title', $field->getFieldName());
        $this->assertSame('group title', $field->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function null_is_returned_if_no_class_is_configured(): void
    {
        $configuration = $this->getMockBuilder(ExtensionConfigurationInterface::class)->getMock();
        $factory = new BeGroupFieldFactory($configuration);
        $fieldName = 'non_existing';

        $configuration->expects($this->once())->method('getClassNameByFieldName')->with($fieldName)->willThrowException(new NoValueObjectConfiguredException());

        $this->assertNull($factory->buildFromFieldNameAndYamlValue($fieldName, ''));
    }

    /**
     * @test
     */
    public function if_an_array_based_field_is_requested_the_value_must_be_an_array(): void
    {
        $configuration = $this->getMockBuilder(ExtensionConfigurationInterface::class)->getMock();
        $factory = new BeGroupFieldFactory($configuration);
        $fieldName = 'tables_selected';

        $configuration->expects($this->once())->method('getClassNameByFieldName')->with($fieldName)->willReturn(TablesSelect::class);

        $this->expectException(InvalidArgumentException::class);
        $factory->buildFromFieldNameAndYamlValue($fieldName, '');
    }

    /**
     * @test
     */
    public function if_a_string_based_field_is_requested_the_value_must_be_a_string(): void
    {
        $configuration = $this->getMockBuilder(ExtensionConfigurationInterface::class)->getMock();
        $factory = new BeGroupFieldFactory($configuration);

        $fieldName = 'title';

        $configuration->expects($this->once())->method('getClassNameByFieldName')->with($fieldName)->willReturn(Title::class);

        $this->expectException(InvalidArgumentException::class);
        $factory->buildFromFieldNameAndYamlValue($fieldName, ['pages', 'tt_content']);
    }
    
    /**
     * @test
     */
    public function can_be_created_from_database_values(): void
    {
        $configuration = $this->getMockBuilder(ExtensionConfigurationInterface::class)->getMock();
        $factory = new BeGroupFieldFactory($configuration);
        $fieldName = 'title';

        $configuration->expects($this->once())->method('getClassNameByFieldName')->with($fieldName)->willReturn(Title::class);

        /** @var StringBasedFieldInterface $field */
        $field = $factory->buildFromFieldNameAndDatabaseValue($fieldName, 'group title');

        $this->assertInstanceOf(StringBasedFieldInterface::class, $field);
        $this->assertSame('title', $field->getFieldName());
        $this->assertSame('group title', (string)$field);
    }
}
