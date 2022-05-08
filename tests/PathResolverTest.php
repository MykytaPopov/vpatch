<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch\Tests;

use MykytaPopov\VPatch\PathResolver;
use PHPUnit\Framework\TestCase;

class PathResolverTest extends TestCase
{
    public function testCheckCWD()
    {
        $this->assertTrue(true);
    }

    public function testParseVendorPackageNames()
    {
        $this->fail();
    }

    public function testParseRelativePath()
    {
        $this->fail();
    }
}
