<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch;

use Symfony\Component\Finder\SplFileInfo;

interface FinderInterface
{
    /**
     * @param string $path Where to look for files
     *
     * @return SplFileInfo[]
     */
    public function findFilesToCompare(string $path): array;
}
