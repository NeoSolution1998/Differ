<?php

namespace Differ\Formatter\Stylish;

use function Funct\Collection\flattenAll;

function renderStylish(array $tree) : string
{
    $array = json_decode(json_encode($tree), true);
    $jsonArray = render($array);
    $jsonStr = getJsonStr($jsonArray);
    return $jsonStr;
}

function render($tree)
{
    $arrayJson = array_reduce(
        $tree,
        function ($acc, $item) {
            $names = $item['name'];
            $type = $item['type'];

            switch ($type) {
                case 'added':
                    $name = "  + {$names}:";
                    $acc[] = getValue($item['value'], $name);

                    break;

                case 'removed':
                    $name = "  - {$names}:";
                    $acc[] = getValue($item['value'], $name);
                    break;

                case 'unchanged':
                    $name = "    {$names}:";
                    $acc[] = getValue($item['value'], $name);
                    break;

                case 'changed':
                    $name = "  - {$names}:";
                    $acc[] = getValue($item['valueBefore'], $name);
                    $name = "  + {$names}:";
                    $acc[] = getValue($item['valueAfter'], $name);
                    break;

                case 'nested':
                    $acc[] = "  {$names}: {";
                    $acc[] = render($item['children']);
                    $acc[] = '}';
                    break;
            }
            return $acc;
        },
        []
    );
    return $arrayJson;
}

function getValue($value, $name)
{
    if (is_bool($value) && $value == true) {
        return $name . ' true';
    } elseif (is_bool($value) && $value == false) {
        return $name . ' false';
    } elseif (is_null($value)) {
        return $name . ' null';
    } elseif ($value == "") {
        return "{$name} {$value}";
    } elseif (is_string($value) || is_int($value)) {
        return "{$name} {$value}";
    }

    $value = json_encode($value, JSON_PRETTY_PRINT);
    $arr = ['"', ','];
    $value = str_replace($arr, '', $value);
    $value = explode("\n", $value);
    $result = ["{$name} {"];
    foreach ($value as $key) {
        if ($key == '{' || $key == '}') {
            continue;
        }
        $result[] = $key;
    }
    $result[] = '}';
    return $result;
}

function getSpace($int, $operand)
{
    $arr = ['-', '+'];
    if ($int == 0) {
        return str_repeat(" ", 4);
    }
    if ($int == 1) {
        if ($operand == '}') {
            return str_repeat(" ", 8);
        }
        return in_array($operand, $arr) ? '  ' : "    ";
    }
    if ($int == 2) {
        if ($operand == '}') {
            return str_repeat(" ", 12);
        }
        return in_array($operand, $arr) ? str_repeat(" ", 6) : str_repeat(" ", 8);
    }
    if ($int == 3) {
        return in_array($operand, $arr) ? str_repeat(" ", 10) : str_repeat(" ", 12);
    }
    if ($int == 4) {
        return in_array($operand, $arr) ? str_repeat(" ", 14) : str_repeat(" ", 16);
    }
    if ($int == 5) {
        return in_array($operand, $arr) ? str_repeat(" ", 18) : str_repeat(" ", 20);
    }
}

function getJsonStr($arrayJson)
{
    $arrayJson = flattenAll($arrayJson);
    $arrayJson = array_map(function ($item) { return trim($item); }, $arrayJson);
    $index = 0;
    $result = array_reduce(
        $arrayJson,
        function ($acc, $item) use (&$index) {
            if ($item[-1] == '{') {
                $index++;
                $a = getSpace($index, $item[0]);
                $acc[] = "{$a}{$item}";
                return $acc;
            } elseif ($item[-1] == '}') {
                $index--;
                $a = getSpace($index, $item[0]);
                $acc[] = "{$a}{$item}";
                return $acc;
            }

            $i = $index + 1;
            $a = getSpace($i, $item[0]);
            $acc[] = "{$a}{$item}";
            return $acc;
        },
        ['{']
    );
    $result[] = "}";
    return implode("\n", $result);
}
