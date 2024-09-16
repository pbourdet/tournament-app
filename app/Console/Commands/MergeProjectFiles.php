<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class MergeProjectFiles extends Command
{
    /** @var string */
    protected $signature = 'app:merge-project-files';

    /** @var string */
    protected $description = 'Command description';

    public function handle(): void
    {
        $phpFiles = $this->getPhpFiles();
        $configFiles = $this->getConfigFiles();
        $frontendFiles = $this->getFrontendFiles();

        foreach (['php' => $phpFiles, 'config' => $configFiles, 'frontend' => $frontendFiles] as $type => $files) {
            $this->mergeFiles($type, $files);
        }
    }

    /** @return SplFileInfo[] */
    private function getPhpFiles(): array
    {
        return array_merge($this->getFilesFromDirectories(['app', 'database', 'tests']), File::files(base_path('bootstrap')));
    }

    /** @return SplFileInfo[] */
    private function getConfigFiles(): array
    {
        $files = $this->getFilesFromDirectories(['config', 'routes']);
        $jsConfigFiles = File::glob(base_path('*.config.js'));

        foreach ($jsConfigFiles as $jsConfigFile) {
            $files[] = new SplFileInfo($jsConfigFile, '', $jsConfigFile);
        }

        return $files;
    }

    /** @return SplFileInfo[] */
    private function getFrontendFiles(): array
    {
        return $this->getFilesFromDirectories(['resources/js', 'resources/views', 'resources/css']);
    }

    /**
     * @param string[] $directories
     *
     * @return SplFileInfo[]
     */
    private function getFilesFromDirectories(array $directories): array
    {
        $files = [];

        foreach ($directories as $directory) {
            $files = array_merge($files, File::allFiles(base_path($directory)));
        }

        return $files;
    }

    /** @param SplFileInfo[] $files */
    private function mergeFiles(string $type, array $files): void
    {
        $mergedContent = '';

        foreach ($files as $file) {
            $mergedContent .= sprintf("//%s\n%s\n\n", Str::replaceStart('/var/www/html/', '', $file->getPathname()), $file->getContents());
        }

        File::put(base_path(sprintf('tmp/merged-%s-files.txt', $type)), $mergedContent);
    }
}
