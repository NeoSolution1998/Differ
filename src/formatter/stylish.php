<?php

namespace Differ\Formatter\Stylish;

///////////////////////////////////////
// Получает дерево и дает json строку//
///////////////////////////////////////
function renderStylish(array $tree): string
{
    $array = json_decode(json_encode($tree), true);
    $jsonArray = render($array);
    $jsonStr = getJsonStr($jsonArray);
    return $jsonStr;
}

/////////////////////////////////////////////////////////////////////////////////////
// Получает массив-дерево с описанием элементов и преобразует в массив json формата//
/////////////////////////////////////////////////////////////////////////////////////

function render($array)
{
    $jsonArray = array_reduce(
        $array,
        function ($acc, $key) {
            if ($key['type'] == 'added') {
                if (is_array($key['oldValue'])) {
                    $acc[] = "- {$key['key']}: {";
                    $acc[] = getJsonString($key['oldValue']);
                    $acc[] = "  }";
                    return $acc;
                } elseif (is_array($key['newValue'])) {
                    $acc[] = "- {$key['key']}: {";
                    $acc[] = getJsonString($key['newValue']);
                    $acc[] = "  }";
                    return $acc;
                }
                $acc[] = "- {$key['key']}: {$key['newValue']}";
                return $acc;
            } elseif ($key['type'] == 'unchanged') {
                if (is_array($key['oldValue'])) {
                    $acc[] = "+ {$key['key']}: {";
                    $acc[] = getJsonString($key['oldValue']);
                    $acc[] = "  }";
                    return $acc;
                } elseif (is_array($key['newValue'])) {
                    $acc[] = "+ {$key['key']}: {";
                    $acc[] = getJsonString($key['newValue']);
                    $acc[] = "  }";
                    return $acc;
                }
                $acc[] = "  {$key['key']}: {$key['oldValue']}";
                return $acc;
            } elseif ($key['type'] == 'changed') {
                if (is_array($key['oldValue'])) {
                    $acc[] = "- {$key['key']}: {$key['newValue']}";
                    $acc[] = "+ {$key['key']}: {";
                    $acc[] = getJsonString($key['oldValue']);
                    $acc[] = "  }";
                    return $acc;
                } elseif (is_array($key['newValue'])) {
                    $acc[] = "- {$key['key']}: {";
                    $acc[] = getJsonString($key['newValue']);
                    $acc[] = "  }";
                    $acc[] = "+ {$key['key']}: {$key['oldValue']}";
                    return $acc;
                }
                $acc[] = "- {$key['key']}: {$key['newValue']}";
                $acc[] = "+ {$key['key']}: {$key['oldValue']}";
                return $acc;
            } elseif ($key['type'] == 'deleted') {
                if (is_array($key['oldValue'])) {
                    $acc[] = "+ {$key['key']}: {";
                    $acc[] = getJsonString($key['oldValue']);
                    $acc[] = "  }";
                    return $acc;
                } elseif (is_array($key['newValue'])) {
                    $acc[] = "+ {$key['key']}: {";
                    $acc[] = getJsonString($key['newValue']);
                    $acc[] = "  }";
                    return $acc;
                }
                $acc[] = "+ {$key['key']}: {$key['oldValue']}";
                return $acc;
            } elseif ($key['type'] == 'nested') {
                $acc[] = "  {$key['key']}: {";
                $acc[] = render($key['children']);
                $acc[] = "}";
                return $acc;
            }
            $acc[] = $key;
            return $acc;
        },
        []
    );

    return $jsonArray;
}

///////////////////////////////////////////////////////////////////////////////////
// Получает вложенный массив и преобразует его в одномерный массив в формате json//
// Работает вместе с Render                                                      //
///////////////////////////////////////////////////////////////////////////////////

function getJsonString(array $array): array
{
    $deleteSymbol = ['"', ','];
    $array = json_encode($array, JSON_PRETTY_PRINT);
    $str = str_replace($deleteSymbol, '', $array);
    $result = explode("\n", $str);
    $deleteFirstEl = array_shift($result);
    $deleteLastEl = array_pop($result);
    return array_filter($result);
}

/////////////////////////////////////////////////////
// Получает массив и преобразует его в json строку //
/////////////////////////////////////////////////////

function getJsonStr(array $arr): string
{
    $jsonStr = json_encode($arr, JSON_PRETTY_PRINT);
    $deleteSymbol = ['"', ',', '[', ']'];
    $jsonStr = str_replace($deleteSymbol, "", $jsonStr);
    return getResult($jsonStr);
}

//////////////////////////////////////////////////////
// Получает json строку и правильно подбирает оступы//
//////////////////////////////////////////////////////

function getResult(string $jsonStr): string
{
    $array = explode("\n", $jsonStr);

    $jsonArr = [];
    foreach ($array as $item) {
        $item = trim($item);
        $jsonArr[] = $item;
    }
    $jsonArr = array_filter($jsonArr);

    $index = 0;
    $result = array_reduce(
        $jsonArr,
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

//////////////////////////////////////////////////////////
// Получает индекс и операнд и выдает правильные отступы//
//////////////////////////////////////////////////////////

function getSpace(int $int, string $symbol): string
{
    $operands = ['-', '+'];

    if ($int == 0) {
        return '    ';
    }
    if ($int == 1) {
        if ($symbol == '}') {
            return "        ";
        }
        return in_array($symbol, $operands) ? '  ' : "    ";
    } elseif ($int == 2) {
        if ($symbol == '}') {
            return "            ";
        }
        return in_array($symbol, $operands) ? '      ' : "        ";
    }

    if ($int == 3) {
        return in_array($symbol, $operands) ? '          ' : "            ";
    } elseif ($int == 4) {
        return in_array($symbol, $operands) ? '              ' : "                ";
    } elseif ($int == 5) {
        return in_array($symbol, $operands) ? '                  ' : "                    ";
    }
}
