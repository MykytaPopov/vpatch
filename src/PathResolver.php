<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch;

class PathResolver implements PathResolverInterface
{
    /**
     * @inheritdoc
     */
    public function parseRelativePath(string $path): string
    {
        preg_match('/vendor(?!.*vendor)\/[^\/]+\/[^\/]+\/(?<relativePath>.*?)$/is', $path, $matches);

        if (empty($matches['relativePath'])) {
            throw new \Exception('can\'t resolve vendor path');
        }

        return $matches['relativePath'];
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function parseVendorPackageName(string $path): string
    {
        preg_match('/vendor(?!.*vendor)\/(?<vendorPackageName>.*?\/.*?)\//is', $path, $matches);

        if (empty($matches['vendorPackageName'])) {
            throw new \Exception('can\'t resolve vendor package name');
        }

        return $matches['vendorPackageName'];
    }
}
