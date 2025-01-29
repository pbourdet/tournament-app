<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Laravel\Dusk\Console\DuskCommand as BaseDuskCommand;
use Symfony\Component\Process\Process;

class DuskCommand extends BaseDuskCommand
{
    protected function setupDuskEnvironment(): void
    {
        parent::setupDuskEnvironment();
        $this->rebuildAssets();
    }

    protected function restoreEnvironment(): void
    {
        parent::restoreEnvironment();
        $this->rebuildAssets();
    }

    private function rebuildAssets(): void
    {
        new Process(['npm', 'run', 'build'])->run();
    }
}
