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
    public function compare(string $vendorFilePath, string $maskFilePath): string
    {
        $command = "git diff --no-index {$maskFilePath} {$vendorFilePath}";

        $stdOut = shell_exec($command);

        if (empty($stdOut)) {
            return '';
        }

        return $this->clearDiff($stdOut, $vendorFilePath, $maskFilePath);
    }

    /**
     * Set proper paths in the diff, remove old file names and set related path
     *
     * @param string $diff Diff to clear
     * @param string $vendorFilePath
     * @param string $maskFilePath
     *
     * @return string
     */
    private function clearDiff(string $diff, string $vendorFilePath, string $maskFilePath): string
    {
        $relativePath = $this->pathResolver->parseRelativePath($vendorFilePath);

        $diff = str_replace('\ No newline at end of file' . PHP_EOL, '', $diff);
        return str_replace([$maskFilePath, $vendorFilePath], $relativePath, $diff);
    }
}
