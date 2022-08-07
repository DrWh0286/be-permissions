<?php

declare(strict_types=1);

namespace Functional\Repoitory;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\Result;
use RuntimeException;
use SebastianHofer\BePermissions\Collection\BeGroupCollection;
use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Repository\BeGroupRepository;
use SebastianHofer\BePermissions\Value\CodeManagedGroup;
use SebastianHofer\BePermissions\Value\Identifier;
use SebastianHofer\BePermissions\Value\SubGroup;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Repository\BeGroupRepository
 */
final class BeGroupRepositoryTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/be_permissions'
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_permissions'] = [];
    }

    /**
     * @test
     */
    public function multiple_be_groups_are_stored_with_relations(): void //phpcs:ignore
    {
        $col = new BeGroupCollection();

        $fieldColA = new BeGroupFieldCollection();
        $subGroup = SubGroup::createFromYamlConfiguration(['group_b', 'group_c']);
        $fieldColA->add($subGroup);
        $fieldColA->add(CodeManagedGroup::createFromYamlConfiguration(true));
        $fieldColB = new BeGroupFieldCollection();
        $fieldColB->add(CodeManagedGroup::createFromYamlConfiguration(true));
        $fieldColC = new BeGroupFieldCollection();
        $fieldColC->add(CodeManagedGroup::createFromYamlConfiguration(true));

        $groupA = new BeGroup(new Identifier('group_a'), $fieldColA);
        $groupB = new BeGroup(new Identifier('group_b'), $fieldColB);
        $groupC = new BeGroup(new Identifier('group_c'), $fieldColC);

        $col->add($groupA);
        $col->add($groupB);
        $col->add($groupC);

        /** @var BeGroupRepository $repo */
        $repo = GeneralUtility::makeInstance(BeGroupRepository::class);

        $repo->addOrUpdateBeGroups($col);

        $allDeployedGroups = $this->getAllRecordsFromTable('be_groups', 'identifier,subgroup');

//        file_put_contents(
//            __DIR__ . '/Fixtures/multiple_be_groups_are_stored_with_relations/expected_groups.php',
//            '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($allDeployedGroups, true) . ';' . PHP_EOL
//        );

        $expectedGroups = (include(__DIR__ . '/Fixtures/multiple_be_groups_are_stored_with_relations/expected_groups.php'));

        $this->assertSame($expectedGroups, $allDeployedGroups);
    }

    /**
     * @param string $tableName
     * @param string $columns
     * @return array<int, array<string, mixed>>
     * @throws DBALException
     * @throws Exception
     */
    private function getAllRecordsFromTable(string $tableName, string $columns = '*'): array
    {
        $queryBuilder = $this->getConnectionPool()->getQueryBuilderForTable($tableName);
        $queryBuilder->getRestrictions()->removeAll();

        $result = $queryBuilder->select(...explode(',', $columns))->from($tableName)->execute();

        if (!($result instanceof Result)) {
            throw new RuntimeException(
                'Query result was not an instance of ' . Result::class,
                1_648_879_827_875
            );
        }

        return $result->fetchAllAssociative();
    }
}
