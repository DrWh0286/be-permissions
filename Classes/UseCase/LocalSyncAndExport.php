<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\UseCase;

use Pluswerk\BePermissions\Phpsu\PhpsuSyncAdapterInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LocalSyncAndExport
{
    private BulkExportBeGroupsToConfigurationFiles $bulkExportBeGroupsToConfigurationFiles;
    private DeployBeGroups $deployBeGroups;
    private PhpsuSyncAdapterInterface $phpsuSyncAdapter;

    public function __construct(
        BulkExportBeGroupsToConfigurationFiles $bulkExportBeGroupsToConfigurationFiles,
        DeployBeGroups $deployBeGroups,
        PhpsuSyncAdapterInterface $phpsuSyncAdapter
    ) {
        $this->bulkExportBeGroupsToConfigurationFiles = $bulkExportBeGroupsToConfigurationFiles;
        $this->deployBeGroups = $deployBeGroups;
        $this->phpsuSyncAdapter = $phpsuSyncAdapter;
    }

    public function syncAndExport(string $source, OutputInterface $output): void
    {
        $this->phpsuSyncAdapter->checkPhpsuInstallation();

        $this->bulkExportBeGroupsToConfigurationFiles->exportGroups();
        $this->phpsuSyncAdapter->syncBeGroups($source, $output);
        $this->deployBeGroups->deployGroups();
        $this->bulkExportBeGroupsToConfigurationFiles->exportGroups();
    }
}
