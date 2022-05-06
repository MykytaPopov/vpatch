<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch;

interface PathResolverInterface
{
    /**
     * Check if current working dir is root of the project and there are vendor with composer.json
     *
     * @param string $cwd Current working dir
     *
     * @return bool
     */
    public function checkCWD(string $cwd): bool;

    /**
     * @param string $path
     *
     * @return string
     */
    public function parseRelativePath(string $path): string;

    /**
     * @param string $path
     *
     * @return string
     */
    public function parseVendorPackageNames(string $path): string;
}
