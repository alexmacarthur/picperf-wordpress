<?php

namespace PicPerf;

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if (! $length) {
        return true;
    }

    return substr($haystack, -$length) === $needle;
}

add_filter('plugin_row_meta', function ($plugin_meta, $plugin_file, $plugin_data, $status) {
    if (! endsWith($plugin_file, 'picperf.php')) {
        return $plugin_meta;
    }

    $plugin_meta[] = '<a target="_blank" rel="noopener noreferrer" href="https://picperf.io/docs">Documentation</a>';

    $plugin_meta[] = '<a href="'.menu_page_url('picperf-settings', false).'">Settings</a>';

    return $plugin_meta;
}, 10, 4);

add_action('wp_head', function () {
    echo "
    <!--
    This site's images are being automatically optimized, formatted, and aggressively cached by PicPerf.åå

    https://picperf.io
    -->\n\n";
});
