<?php

namespace Differ\Differ;

use Docopt;

use function Funct\Collection\union;
use function Funct\Collection\sortBy;
use function Differ\Parsers\parse;
use function Differ\Formatter\Stylish\renderStylish;
use function Differ\Formatter\Plain\renderPlain;
use function Differ\Formatter\Json\renderJson;


function genDiff($pathToFile1, $pathToFile2, $format = 'stylish')
{
    $beforeFile = file_get_contents($pathToFile2);
    $afterFile = file_get_contents($pathToFile1);

    $format1 = pathinfo($pathToFile1, PATHINFO_EXTENSION);
    $format2 = pathinfo($pathToFile2, PATHINFO_EXTENSION);

    $data1 = parse($beforeFile, $format1);
    $data2 = parse($afterFile, $format2);

    $tree = getTree($data1, $data2);
 
    switch ($format) {
        case 'plain':
            $result = renderPlain($tree);
            break;
        case 'json':
            $result = renderJson($tree);
            break;
        default:
            $result = renderStylish($tree);
            break;
    }

    return $result;
}

function getTree(object $objBefore, object $objAfter) : array
{
    $unicKey = union(array_keys(get_object_vars($objBefore)), array_keys(get_object_vars($objAfter)));

    $sortedUnicKey = array_values(
        sortBy(
            $unicKey,
            function ($key) {
                return $key;
            }
        )
    );

    $tree = array_map(
        function ($key) use ($objBefore, $objAfter) {
            if (!property_exists($objAfter, $key)) {
                return [
                    'name' => $key,
                    'type' => 'removed',
                    'value' => $objBefore->$key
                ];
            }
            if (!property_exists($objBefore, $key)) {
                return [
                    'name' => $key,
                    'type' => 'added',
                    'value' => $objAfter->$key
                ];
            }
            if (is_object($objBefore->$key) && is_object($objAfter->$key)) {
                return [
                    'name' => $key,
                    'type' => 'nested',
                    'children' => getTree($objBefore->$key, $objAfter->$key)
                ];
            }
            if ($objBefore->$key !== $objAfter->$key) {
                return [
                    'name' => $key,
                    'type' => 'changed',
                    'valueBefore' => $objBefore->$key,
                    'valueAfter' => $objAfter->$key
                ];
            }
            return [
                'name' => $key,
                'type' => 'unchanged',
                'value' => $objBefore->$key
            ];
        },
        $sortedUnicKey
    );
    return $tree;
}

