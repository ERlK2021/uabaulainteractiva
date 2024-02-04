<?php

function buildUrl($query=null, $params=null) : string
{
    $header = "/index.php";

    $params = empty($params)? [] : $params;

    if (!empty($query)) {
        $params = array_merge(array('query' => $query), $params);
    }
    $paramsString = '?' . http_build_query($params);
    $paramsString = $paramsString === '?'? '' : $paramsString;

    return "$header$paramsString";
}

function redirectLocation($query=null, $params=null) : void
{
    $url = buildUrl($query, $params);

    header("Location:$url");
}
