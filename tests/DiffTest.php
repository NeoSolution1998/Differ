<?php

namespace Differ\tests\gendiffTests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

    class DiffTest extends TestCase
{
    public function testGendiff(): void
    {
	$path1 = "/home/neosolution/php-project-lvl2/tests/fixtures/before.json";
	$path2 = '/home/neosolution/php-project-lvl2/tests/fixtures/after.json';
	$result = genDiff($path1, $path2);
	$this->assertEquals($result, genDiff($path1, $path2));
    }
}
