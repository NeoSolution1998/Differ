<?php

namespace Differ\tests\gendiffTests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

    class DiffTest extends TestCase
{
    public function testGendiff(): void
    {
        $result = genDiff('/home/neosolution/php-project-lvl2/tests/fixtures/before.json', '/home/neosolution/php-project-lvl2/tests/fixtures/after.json');
	$this->assertEquals($result, genDiff('/home/neosolution/php-project-lvl2/tests/fixtures/before.json', '/home/neosolution/php-project-lvl2/tests/fixtures/after.json'));	
     }
}
