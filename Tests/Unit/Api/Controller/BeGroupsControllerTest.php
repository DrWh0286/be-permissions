<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Api\Controller;

use Pluswerk\BePermissions\Collection\BeGroupCollection;
use Pluswerk\BePermissions\Collection\BeGroupFieldCollection;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Repository\BeGroupRepositoryInterface;
use Pluswerk\BePermissions\Value\AllowedLanguages;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\NonExcludeFields;
use Pluswerk\BePermissions\Value\Title;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pluswerk\BePermissions\Api\Controller\BeGroupsController;

/**
 * @covers \Pluswerk\BePermissions\Api\Controller\BeGroupsController
 */
final class BeGroupsControllerTest extends UnitTestCase
{
    /**
     * @test
     */
    public function all_be_groups_can_be_fetched_as_json_from_api(): void //phpcs:ignore
    {
        $beGroupRepo = $this->createMock(BeGroupRepositoryInterface::class);
        $controller = new BeGroupsController($beGroupRepo);

        $beGroups = $this->getBeGroups();

        $beGroupRepo->expects($this->once())->method('findAllForBulkExport')->willReturn($beGroups);

        $response = $controller->allSyncBeGroupsAction();

        $json = $response->getBody()->getContents();

        $this->assertSame(json_encode($beGroups), $json);
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
