<?php

namespace Differ\Differ;

use function Funct\Collection\union;

function genDiff($pathToFile1, $pathToFile2)
{
    $file1 = json_decode(file_get_contents($pathToFile1), true);
    $file2 = json_decode(file_get_contents($pathToFile2), true);

    $keys1 = array_keys($file1);
    $keys2 = array_keys($file2);
    $keys = union($keys1, $keys2);

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
       	''
    );
}
