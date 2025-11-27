<?php

namespace PicPerf;

const PIC_PERF_HOST = 'https://picperf.io/';

if (! function_exists('add_query_arg')) {
    function add_query_arg($args, $url)
    {
        $query = http_build_query($args);

        return $url.'?'.$query;
    }
}

if (! function_exists('get_option')) {
    function get_option($option_name, $default = false)
    {
        global $_test_options;

        if (isset($_test_options[$option_name])) {
            return $_test_options[$option_name];
        }

        // For picperf_custom_domain, return empty string by default to match WordPress behavior
        if ($option_name === 'picperf_custom_domain') {
            return '';
        }

        return $default;
    }
}

if (! function_exists('get_site_url')) {
    function get_site_url()
    {
        return 'http://example.com';
    }
}
