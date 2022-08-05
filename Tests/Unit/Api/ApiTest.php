<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Tests\Unit\Api;

use SebastianHofer\BePermissions\Builder\BeGroupFieldCollectionBuilderInterface;
use SebastianHofer\BePermissions\Collection\BeGroupCollection;
use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Configuration\ExtensionConfigurationInterface;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Value\AllowedLanguages;
use SebastianHofer\BePermissions\Value\ExplicitAllowDeny;
use SebastianHofer\BePermissions\Value\Identifier;
use SebastianHofer\BePermissions\Value\NonExcludeFields;
use SebastianHofer\BePermissions\Value\Title;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use SebastianHofer\BePermissions\Api\Api;

/**
 * @covers \SebastianHofer\BePermissions\Api\Api
 */
final class ApiTest extends UnitTestCase
{
    /**
     * @test
     */
    public function all_code_managed_be_groups_are_fetched(): void //phpcs:ignore
    {
        $requestFactory = $this->createMock(RequestFactory::class);
        $extensionConfiguration = $this->createMock(ExtensionConfigurationInterface::class);
        $apiResponse = $this->createMock(ResponseInterface::class);
        $builder = $this->createMock(BeGroupFieldCollectionBuilderInterface::class);
        $testBeGroups = $this->getBeGroups();

        $stream = $this->createMock(StreamInterface::class);

        $api = new Api($requestFactory, $extensionConfiguration, $builder);

        $extensionConfiguration->expects($this->once())->method('getApiUri')->willReturn(new Uri('https://prod.host/'));
        $requestFactory->expects($this->once())
            ->method('request')
            ->with('https://prod.host/be-permissions-api/v1.0/begroups')
            ->willReturn($apiResponse);
        $apiResponse->expects($this->once())->method('getBody')->willReturn($stream);
        $stream->expects($this->once())->method('getContents')->willReturn(json_encode($testBeGroups));

        /** @var BeGroup $beGroup0 */
        $beGroup0 = $testBeGroups->getBeGroup(0);
        /** @var BeGroup $beGroup1 */
        $beGroup1 = $testBeGroups->getBeGroup(1);

        $builder->expects($this->exactly(2))->method('buildFromConfigurationArray')
            ->willReturnOnConsecutiveCalls($beGroup0->beGroupFieldCollection(), $beGroup1->beGroupFieldCollection());

        $beGroups = $api->fetchAllCodeManagedBeGroups();

        $expectedBeGroups = $this->getBeGroups();

        $this->assertEquals($expectedBeGroups, $beGroups);
    }

    private function getBeGroups(): BeGroupCollection
    {
        $beGroups = new BeGroupCollection();

        $identifier = new Identifier('some-identifier');
        $title = Title::createFromYamlConfiguration('[PERM] Basic permissions');
        $nonExcludeFields = NonExcludeFields::createFromYamlConfiguration([
            'pages' => ['media', 'hidden'],
            'tt_content' => ['pages', 'date']
        ]);

        $explicitAllowDeny = ExplicitAllowDeny::createFromYamlConfiguration(
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
            ]
        );
        $allowedLanguages = AllowedLanguages::createFromYamlConfiguration([0,3,5]);

        $beGroupFieldCollection = new BeGroupFieldCollection();
        $beGroupFieldCollection->add($title);
        $beGroupFieldCollection->add($nonExcludeFields);
        $beGroupFieldCollection->add($explicitAllowDeny);
        $beGroupFieldCollection->add($allowedLanguages);

        $beGroup = new BeGroup($identifier, $beGroupFieldCollection);

        $beGroups->add($beGroup);
        $beGroups->add(clone $beGroup);

        return $beGroups;
    }
}
