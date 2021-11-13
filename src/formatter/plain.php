<?php

namespace Differ\Formatter\Plain;

use function Funct\Collection\flattenAll;

///////////////////////////////////////////////////////////////////////
// Получает дерево и дает информацию в формате Plain (плоский вывод) //
///////////////////////////////////////////////////////////////////////
function renderPlain($tree)
{
    $iter = function ($node, $path = '') use (&$iter) {
        return array_map(
            function ($item) use ($iter, $path) {
                ['key' => $key, 'type' => $type, 'oldValue' => $oldValue, 'newValue' => $newValue] = $item;
                $propertyName = "{$path}{$key}";
                $newValue = getValue($newValue);
                $oldValue = getvalue($oldValue);
                if ($type == 'changed') {
                    return "Property '{$propertyName}' was updated. From {$newValue} to {$oldValue}";
                } elseif ($type == 'deleted') {
                    return "Property '{$propertyName}' was added with value: {$oldValue}";
                } elseif ($type == 'added') {
                    return "Property '{$propertyName}' was removed";
                } elseif ($type == 'unchanged') {
                    return [];
                } elseif ($type == 'nested') {
                    return $iter($item['children'], "{$path}{$key}.");
                }
            },
            $node
        );
    };
    $flattened = flattenAll($iter($tree));
    return implode("\n", $flattened);
}

////////////////////////////////////////////////////
// Получает значение и формирует строку для вывода//
////////////////////////////////////////////////////

function getValue($value)
{
    if (is_array($value) || is_object($value)) {
        return '[complex value]';
    }

    if (is_bool($value)) {
        return $value ? true : false;
    }

    return "'$value'";
}
