<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;

/**
 * @covers \Pluswerk\BePermissions\Value\ExplicitAllowDeny
 */
final class ExplicitAllowDenyTest extends UnitTestCase
{
    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_array(): void
    {
        $dbValue = 'tt_content:CType:header:ALLOW,tt_content:CType:text:ALLOW,tt_content:CType:textpic:ALLOW,tt_content:list_type:some_plugina:ALLOW,tt_content:list_type:another_pluginb:ALLOW';

        $explicitAllowDeny = ExplicitAllowDeny::createFromDBValue($dbValue);

        $this->assertSame(
            [
                'tt_content' => [
                    'CType' => [
                        'header' => 'ALLOW',
                        'text' => 'ALLOW',
                        'textpic' => 'ALLOW'
                    ],
                    'list_type' => [
                        'some_plugina' => 'ALLOW',
                        'another_pluginb' => 'ALLOW'
                    ]
                ]
            ],
            $explicitAllowDeny->asArray()
        );
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_array_and_returned_as_database_value(): void
    {
        $configArray = [
            'tt_content' => [
                'CType' => [
                    'header' => 'ALLOW',
                    'text' => 'ALLOW',
                    'textpic' => 'ALLOW'
                ],
                'list_type' => [
                    'some_plugina' => 'ALLOW',
                    'another_pluginb' => 'ALLOW'
                ]
            ]
        ];

        $explicitAllowDeny = ExplicitAllowDeny::createFromConfigurationArray($configArray);

        $this->assertSame(
            'tt_content:CType:header:ALLOW,tt_content:CType:text:ALLOW,tt_content:CType:textpic:ALLOW,tt_content:list_type:some_plugina:ALLOW,tt_content:list_type:another_pluginb:ALLOW',
            (string)$explicitAllowDeny
        );
    }

    /**
     * @test
     */
    public function can_be_extended_by_another_explicit_allow_deny_object(): void
    {
        $baseConfigArray = [
            'tt_content' => [
                'CType' => [
                    'header' => 'ALLOW',
                    'text' => 'ALLOW',
                    'textpic' => 'ALLOW'
                ],
                'list_type' => [
                    'some_plugina' => 'ALLOW',
                    'another_pluginb' => 'ALLOW'
                ]
            ]
        ];

        $baseExplicitAllowDeny = ExplicitAllowDeny::createFromConfigurationArray($baseConfigArray);

        $extendingConfigArray = [
            'tt_content' => [
                'CType' => [
                    'header' => 'ALLOW',
                    'text' => 'ALLOW',
                    'additionia_field' => 'ALLOW'
                ],
                'list_type' => [
                    'some_plugina' => 'ALLOW',
                    'another_pluginb' => 'DENY',
                    'z_plugin' => 'ALLOW'
                ]
            ]
        ];

        $extendingExplicitAllowDeny = ExplicitAllowDeny::createFromConfigurationArray($extendingConfigArray);

        $expectedConfigArray = [
            'tt_content' => [
                'CType' => [
                    'additionia_field' => 'ALLOW',
                    'header' => 'ALLOW',
                    'text' => 'ALLOW',
                    'textpic' => 'ALLOW'
                ],
                'list_type' => [
                    'another_pluginb' => 'DENY',
                    'some_plugina' => 'ALLOW',
                    'z_plugin' => 'ALLOW'
                ]
            ]
        ];

        $expectedExplicitAllowDeny = ExplicitAllowDeny::createFromConfigurationArray($expectedConfigArray);

        $this->assertEquals($expectedExplicitAllowDeny, $baseExplicitAllowDeny->extend($extendingExplicitAllowDeny));
    }
}
