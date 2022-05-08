<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch\Tests;

use MykytaPopov\VPatch\PathResolver;
use PHPUnit\Framework\TestCase;

class PathResolverTest extends TestCase
{
    private PathResolver $pathResolver;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->pathResolver = new PathResolver();
    }

    public function testParseVendorPackageNames(): void
    {
        $vendorPackageNames = 'mykytapopov/vpatch';
        $path = '/var/www/mysite/vendor/some/package/vendor/' . $vendorPackageNames . '/bin/vpatch.php';

        $result = $this->pathResolver->parseVendorPackageName($path);

        $this->assertEquals($vendorPackageNames, $result, 'can\'t resolve relative path');
    }

    public function testParseVendorPackageNamesUnableToParse(): void
    {
        $path = '/some/wrong/path/index.php';

        $this->expectExceptionMessage('can\'t resolve vendor package name');
        $this->pathResolver->parseVendorPackageName($path);
    }

    public function testParseRelativePath(): void
    {
        $relativePath = 'bin/vpatch.php';
        $path = '/var/www/mysite/vendor/some/package/vendor/mykytapopov/vpatch/' . $relativePath;

        $result = $this->pathResolver->parseRelativePath($path);

        $this->assertEquals($relativePath, $result);
    }

    public function testParseRelativePathUnableToParse(): void
    {
        $path = '/some/wrong/path/index.php';

        $this->expectExceptionMessage('can\'t resolve vendor path');
        $this->pathResolver->parseRelativePath($path);
    }
}
