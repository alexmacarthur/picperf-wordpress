<?php

namespace PicPerf;

add_filter('plugin_row_meta', function ($plugin_meta, $plugin_file, $plugin_data, $status) {
    if ($plugin_file !== 'picperf/picperf.php') {
        return $plugin_meta;
    }

    $plugin_meta[] = '<a target="_blank" rel="noopener noreferrer" href="https://picperf.io/docs">View PicPerf\'s Documentation</a>';

    return $plugin_meta;
}, 10, 4);

add_action('wp_head', function () {
    echo "
    <!--
    This site's images are being automatically optimized, formatted, and aggressively cached by PicPerf.

    https://picperf.io
    -->\n\n";
});
