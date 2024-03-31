<?php

namespace PicPerf;

if (!function_exists('add_query_arg')) {
    function add_query_arg($args, $url)
    {
        $query = http_build_query($args);

        return $url . '?' . $query;
    }
}
