<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Functional\UseCase;

use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ExportBeGroupToConfigurationFileTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/be_permissions'
    ];

    /**
     * @test
     */
    public function an_existing_be_group_can_be_exported_to_a_be_group_configuration_file(): void
    {

    }
}
