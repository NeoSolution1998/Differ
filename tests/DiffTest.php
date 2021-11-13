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

        $result1 = genDiff($path1, $path2, 'json');
        $this->assertEquals($result1, genDiff($path1, $path2, 'json'));

        $result2 = genDiff($path1, $path2, 'plain');
        $this->assertEquals($result2, genDiff($path1, $path2, 'plain'));

        $result3 = genDiff($path1, $path2, 'stylish');
        $this->assertEquals($result3, genDiff($path1, $path2, 'stylish'));

        $result4 = genDiff($path1, $path2);
        $this->assertEquals($result4, genDiff($path1, $path2));
    }
}
