<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch;

interface DifferInterface
{
    /**
     * Compare two files
     *
     * @param string $vendorFilePath Modified file name
     * @param string $maskFilePath Origin file name from the vendor
     *
     * @return string
     */
    public function compare(string $vendorFilePath, string $maskFilePath): string;
}
