<?php

namespace Differ\Formatter\Plain;

use function Funct\Collection\flattenAll;

function renderPlain($tree)
{
    $result = function ($node, $path = '') use (&$result) {
        return array_map(
            function ($item) use ($result, $path) {
                $name = $item['name'];
                $type = $item['type'];
                $propertyName = "{$path}{$name}";

                switch ($type) {
                    case 'added':
                        $value = getValuePlain($item['value']);
                        return "Property '{$propertyName}' was added with value: {$value}";
                        break;

                    case 'removed':
                        $value = getValuePlain($item['value']);
                        return "Property '{$propertyName}' was removed";
                        break;

                    case 'changed':
                        $oldValue = getValuePlain($item['valueBefore']);
                        $newValue = getValuePlain($item['valueAfter']);
                        return "Property '{$propertyName}' was updated. From {$oldValue} to {$newValue}";
                        break;

                    case 'unchanged':
                        return [];
                        break;

                    case 'nested':
                        return $result($item['children'], "{$path}{$name}.");
                        break;
                }
            },
            $node
        );
    };
    $flattened = flattenAll($result($tree));
    return implode("\n", $flattened);
}

function getValuePlain($value)
{
    if (is_array($value) || is_object($value)) {
        return '[complex value]';
    }

    if (is_bool($value) & $value == true) {
        return 'true';
    } elseif (is_bool($value) & $value == false) {
        return 'false';
    } elseif ($value === null) {
        return 'null';
    } elseif ($value === 0) {
        return 0;
    }
    return "'$value'";
}
