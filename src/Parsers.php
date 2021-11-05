<?php

namespace Differ\parsers;

use Symfony\Component\Yaml\Yaml;

function parse($data, $dataFormat)
{
    switch ($dataFormat) {
        case "json":
            return json_decode($data);
        case "yml":
        case "yaml":
            return Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
        default:
            throw new \Exception("\n Wrong file format '$dataFormat' or not supported");
    }
}
