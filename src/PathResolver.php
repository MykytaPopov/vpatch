<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch;

class PathResolver implements PathResolverInterface
{
    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function parseRelativePath(string $path): string
    {
        preg_match('/vendor\/[^\/]+\/[^\/]+\/(?<relativePath>.*?)$/is', $path, $matches);

        return $matches['relativePath'];
    }

    /**
     * @inheritdoc
     */
    public function parseVendorPackageNames(string $path): string
    {
        preg_match('/vendor\/(?<vendorPackageName>.*?\/.*?)\//is', $path, $matches);

        return $matches['vendorPackageName'];
    }
}
