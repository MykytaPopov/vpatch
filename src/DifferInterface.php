<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch;

interface DifferInterface
{
    /**
     * Compare two files
     *
     * @param string $modifiedFileName Modified file name
     * @param string $originFileName Origin file name from the vendor
     *
     * @return string
     */
    public function compare(string $modifiedFileName, string $originFileName): string;
}
