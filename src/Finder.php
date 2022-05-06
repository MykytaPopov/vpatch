<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch;

use Symfony\Component\Finder\SplFileInfo;

class Finder
{
    /**
     * @param string $path Where to look for files
     *
     * @return SplFileInfo[]
     */
    public function findFilesToCompare(string $path): array
    {
        $symfonyFinder = new \Symfony\Component\Finder\Finder();

        $iterator = $symfonyFinder
            ->files()
            ->in($path)
            ->notName('*.diff');

        return iterator_to_array($iterator);
    }
}
