<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Phpsu;

use Symfony\Component\Console\Output\OutputInterface;

interface PhpsuSyncAdapterInterface
{
    public function syncBeGroups(string $source, OutputInterface $output): void;

    public function checkPhpsuInstallation(): void;
}
