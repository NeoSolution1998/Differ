<?php

namespace Differ\Formatter\Json;

function renderJson($tree)
{
    return json_encode($tree, JSON_PRETTY_PRINT);
}
