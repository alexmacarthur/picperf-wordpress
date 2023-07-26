<?php

/**
 * Plugin Name: PicPerf
 * Plugin URI: https://picperf.dev
 * Description: Automatic image optimization for the URLs you're already using.
 * Version: 0.2.2
 * Author: Alex MacArthur
 * Author URI: https://macarthur.me
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace PicPerf;

if (!defined('WPINC')) {
    exit;
}

const PIC_PERF_HOST = 'https://picperf.dev/';

$absolutePath = realpath(dirname(__FILE__));

require_once ABSPATH . 'wp-admin/includes/plugin.php';

$pluginData = get_plugin_data(__FILE__);

define('PICPERF_PLUGIN_VERSION', $pluginData['Version']);

require "$absolutePath/src/utils.php";
require "$absolutePath/src/DomainValidator.php";
require "$absolutePath/src/hooks/plugin-meta.php";
require "$absolutePath/src/hooks/update.php";

add_filter("wp_get_attachment_image", function ($html) {
    return transformImageHtml($html);
}, 99);

add_filter('post_thumbnail_html', function ($html) {
    return transformImageHtml($html);
}, 99);

add_action('admin_notices', function () {
    $domainValidator = new DomainValidator(
        get_site_url()
    );

    if ($domainValidator->validate()) {
        return;
    }

    ?>
        <div class="notice notice-error is-dismissible">
            <p>
                Oh no! It looks like your PicPerf subscription is either inactive, or this domain hasn't been added to your account. Until you sign in and add this domain, you images won't be optimized.

                <br />
                <br />

                <a href="https://app.picperf.dev" target="_blank">Sign in to PicPerf</a>
            </p>
        </div>
    <?php
});

add_filter('the_content', function ($content) {
    return transformImageHtml($content);
}, 99);
