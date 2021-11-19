<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string $data, string $dataFormat): object
{
    switch ($dataFormat) {
        case "json":
            return json_decode($data);
        case "yml":
        case "yaml":
            return Yaml::parse($data, YAML::PARSE_OBJECT_FOR_MAP);
        default:
            throw new \Exception("\n Wrong file format '$dataFormat' or not supported");
    }
}
