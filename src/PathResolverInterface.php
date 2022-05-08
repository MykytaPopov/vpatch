<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch;

interface PathResolverInterface
{
    /**
     * Parse relative path relatively to vendor package name
     *
     * @param string $path
     *
     * @return string
     */
    public function parseRelativePath(string $path): string;

    /**
     * Parse vendor package name form the provided path
     *
     * @param string $path
     *
     * @return string
     */
    public function parseVendorPackageName(string $path): string;
}
