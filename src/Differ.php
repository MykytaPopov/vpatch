<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch;

class Differ implements DifferInterface
{
    private PathResolverInterface $pathResolver;

    public function __construct(PathResolverInterface $pathResolver)
    {
        $this->pathResolver = $pathResolver;
    }

    /**
     * @inheritdoc
     */
    public function compare(string $modifiedFileName, string $originFileName): string
    {
        $command = "git diff --no-index {$originFileName} {$modifiedFileName}";

        $stdOut = shell_exec($command);

        if (empty($stdOut)) {
            return '';
        }

        return $this->clearDiff($stdOut, $modifiedFileName, $originFileName);
    }

    /**
     * Set proper paths in the diff, remove old file names and set related path
     *
     * @param string $diff Diff to clear
     * @param string $modifiedFileName
     * @param string $originFileName
     *
     * @return string
     */
    private function clearDiff(string $diff, string $modifiedFileName, string $originFileName): string
    {
        $relativePath = $this->pathResolver->parseRelativePath($originFileName);

        return str_replace([$originFileName, $modifiedFileName], $relativePath, $diff);
    }
}
