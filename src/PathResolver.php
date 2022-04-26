<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch;

class PathResolver
{
    /**
     * Check if current working dir is root of the project and there are vendor with composer.json
     *
     * @param string $cwd Current working dir
     *
     * @return bool
     */
    public function checkCWD(string $cwd): bool
    {
        $vendorPath = $cwd . '/vendor';
        $vendor = file_exists($vendorPath) && !is_file($vendorPath);

        $composerJsonPath = $cwd . '/composer.json';
        $composerJson = file_exists($composerJsonPath) && is_file($composerJsonPath);

        return $vendor && $composerJson;
    }

    /**
     * Get destination path to save patches
     *
     * @param string $cwd Current working dir
     * @param string $oldFilePath old fine path
     *
     * @return string
     */
    public function getDestination(string $cwd, string $oldFilePath): string
    {
        $destinationPath = $cwd . '/patches';
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath);
        }

        return $destinationPath . '/' . pathinfo($oldFilePath, PATHINFO_FILENAME) . '.patch';
    }
}
