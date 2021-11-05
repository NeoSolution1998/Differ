<?php

namespace Differ\Differ;

use Docopt;

use function Funct\Collection\union;
use function Differ\parsers\parse;

function genDiff($pathToFile1, $pathToFile2)
{
    $beforeFile = file_get_contents($pathToFile1);
    $afterFile = file_get_contents($pathToFile2);
    echo " Данные из файла 1\n";
    print_r($beforeFile);
    echo "\n";
    $format1 = pathinfo($pathToFile1, PATHINFO_EXTENSION);
    $format2 = pathinfo($pathToFile2, PATHINFO_EXTENSION);

    echo  "Формат из файла 1\n";
    print_r($format1);
    echo  "\n";
    echo "\n";
    $fileParse1 = parse($beforeFile, $format1);
    $fileParse2 = parse($afterFile, $format2);

    echo "ФАЙЛ 1 ПОСЛЕ ПАРСИНГА \n";
    print_r($fileParse1);

    $file1 = (array) $fileParse1;
    $file2 = (array) $fileParse2;
    echo "ФАЙЛ 1 ПОСЛЕ ARRAY \n";
    print_r($file1);

    $keys1 = array_keys((array)$file1);
    $keys2 = array_keys((array)$file2);
    $keys = union($keys1, $keys2);
    echo "Ключи общие";
    print_r($keys);
    return array_reduce(
        $keys,
        function ($acc, $key) use ($file1, $file2) {
            if (array_key_exists($key, $file1) && array_key_exists($key, $file2)) {
                if ($file1[$key] === $file2[$key]) {
                        $result = "{$key} {$file2[$key]}\n";
                } else {
                        $result = "- {$key} {$file1[$key]}\n";
                        $result .= "+ {$key} {$file2[$key]}\n";
                }
            } elseif (array_key_exists($key, $file1) && !array_key_exists($key, $file2)) {
                $result = "- {$key} {$file1[$key]}\n";
            } else {
                $result = "+ {$key} {$file2[$key]}\n";
            }
            $acc .= $result;
            return $acc;
        },
    );
}
