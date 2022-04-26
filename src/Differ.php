<?php
declare(strict_types=1);

namespace MykytaPopov\VPatch;

class Differ
{
    /**
     * Compare two files
     *
     * @param string $originFileName The origin file with changes to compare
     * @param string $oldFileName The old file with original content
     *
     * @return string
     */
    public function compare(string $originFileName, string $oldFileName): string
    {
        $command = "git diff --no-index {$oldFileName} {$originFileName}";

        $stdOut = shell_exec($command);

        if (empty($stdOut)) {
            return '';
        }

        return $this->clearDiff($stdOut, $originFileName, $oldFileName);
    }

    /**
     * Set proper paths in the diff, remove old file names and set related path
     *
     * @param string $diff Diff to clear
     * @param string $originFileName Origin file name to clear
     * @param string $oldFileName Old file name to clear
     *
     * @return string
     */
    private function clearDiff(string $diff, string $originFileName, string $oldFileName): string
    {
        preg_match('#vendor\/[^\/]+\/[^\/]+\/(?<localPath>.*?)$#is', $originFileName, $matches);
        $localPath = $matches['localPath'];

        return str_replace([$oldFileName, $originFileName], '/' . $localPath, $diff);
    }
}
