<?php
function groupByParentId($array, $key)
{
    $return = array();
    foreach ($array as $val) {
        $return[$val[$key]][] = $val;
    }
    return $return;
}