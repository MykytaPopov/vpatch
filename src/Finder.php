<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch;

use MykytaPopov\VPatch\Command\GenerateCommand;

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
            ->notName('*.' . GenerateCommand::DIFF_EXT);

        return iterator_to_array($iterator);
    }
}
