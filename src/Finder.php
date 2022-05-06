<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch;

class Finder implements FinderInterface
{
    /**
     * @inheritdoc
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
