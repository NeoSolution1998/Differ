<?php

namespace Differ\Differ;

use Docopt;

use function Funct\Collection\union;
use function Differ\parsers\parse;
use function Differ\Formatter\Stylish\renderStylish;
use function Differ\Formatter\Plain\renderPlain;
use function Differ\Formatter\Json\renderJson;

function genDiff($pathToFile1, $pathToFile2, $format = 'stylish')
{
    $beforeFile = file_get_contents($pathToFile1);
    $afterFile = file_get_contents($pathToFile2);

    $format1 = pathinfo($pathToFile1, PATHINFO_EXTENSION);
    $format2 = pathinfo($pathToFile2, PATHINFO_EXTENSION);

    $data1 = parse($beforeFile, $format1);
    $data2 = parse($afterFile, $format2);

    $tree = buildDiff($data1, $data2);

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

    return "{$result}\n";
}

function node($key, $type, $oldValue, $newValue, $children = [])
{
    return [
        'key' => $key,
        'type' => $type,
        'oldValue' => $oldValue,
        'newValue' => $newValue,
        'children' => $children,
    ];
}

function buildDiff($data1, $data2)
{
    $keys1 = array_keys((array) $data1);
    $keys2 =  array_keys((array) $data2);
    $keys = union($keys1, $keys2);
    sort($keys);
    return array_map(function ($key) use ($data1, $data2) {
        if (!property_exists($data1, $key)) {
            return node($key, 'added', null, $data2->$key);
        }
        if (!property_exists($data2, $key)) {
            return node($key, 'deleted', $data1->$key, null);
        }
        if (is_object($data1->$key) && is_object($data2->$key)) {
            return node($key, 'nested', null, null, buildDiff($data1->$key, $data2->$key));
        }
        if ($data1->$key == $data2->$key) {
            return node($key, 'unchanged', $data1->$key, null);
        }
            return node($key, 'changed', $data1->$key, $data2->$key);
    }, $keys);
}
