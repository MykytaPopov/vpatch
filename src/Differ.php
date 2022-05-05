<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch;

class Differ
{
    /**
     * Compare two files
     *
     * @param string $modefiedFileName Modified file name
     * @param string $originFileName Origin file name from the vendor
     *
     * @return string
     */
    public function compare(string $modefiedFileName, string $originFileName): string
    {
        $command = "git diff --no-index {$originFileName} {$modefiedFileName}";

        $stdOut = shell_exec($command);

        if (empty($stdOut)) {
            return '';
        }

        return $this->clearDiff($stdOut, $modefiedFileName, $originFileName);
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
        preg_match('#vendor\/[^\/]+\/[^\/]+\/(?<localPath>.*?)$#is', $originFileName, $matches);
        $localPath = $matches['localPath'];

        return str_replace([$originFileName, $modifiedFileName], $localPath, $diff);
    }
}
