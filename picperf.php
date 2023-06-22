<?php

/**
 * Plugin Name: PicPerf
 * Plugin URI: https://picperf.dev
 * Description: Automatic image optimization for the URLs you're already using.
 * Version: 0.0.1
 * Author: Alex MacArthur
 * Author URI: https://macarthur.me
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace PicPerf;

const PIC_PERF_HOST = 'https://picperf.dev/';

$absolutePath = realpath(dirname(__FILE__));

require "$absolutePath/src/utils.php";

add_filter('the_content', function ($content) {
    return transformImageHtml($content);
}, 99);
