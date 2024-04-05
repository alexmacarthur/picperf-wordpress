<?php

/**
 * Plugin Name: PicPerf
 * Plugin URI: https://picperf.io
 * Description: Automatic image optimization for the URLs you're already using.
 * Version: 0.5.0
 * Author: Alex MacArthur
 * Author URI: https://macarthur.me
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace PicPerf;

if (!defined('WPINC')) {
    exit;
}

const PIC_PERF_HOST = 'https://picperf.io/';

$absolutePath = realpath(dirname(__FILE__));

require_once ABSPATH . 'wp-admin/includes/plugin.php';

$pluginData = get_plugin_data(__FILE__);

define('PICPERF_PLUGIN_VERSION', $pluginData['Version']);

require "$absolutePath/src/Config.php";
require "$absolutePath/src/SitemapService.php";
require "$absolutePath/src/utils.php";
require "$absolutePath/src/DomainValidator.php";
require "$absolutePath/src/hooks/plugin-meta.php";
require "$absolutePath/src/hooks/update.php";
require "$absolutePath/src/hooks/sitemap.php";

/**
 * Capturing the output buffer allows us to transform the HTML before
 * it's sent to the browser. It was intended to replace the following
 * type of hooks:
 * - wp_get_attachment_image_src
 * - wp_get_attachment_image
 * - post_thumbnail_html
 * - the_content
 */

function buffer_callback($buffer)
{
    $sitemapPath = Config::getAddSitemapPath() === 'ALL' ? currentPagePath() : null;
    $buffer = transformStyleTags($buffer, $sitemapPath);
    $buffer = transformImageHtml($buffer, $sitemapPath);
    $buffer = transformInlineStyles($buffer, $sitemapPath);

    return $buffer;
}

/**
 * These hooks perform a global image URL transformation.
 */
add_action('wp_head', function () {
    if (Config::getTransformationScope() === 'ALL') {
        ob_start("PicPerf\buffer_callback");
    }
}, PHP_INT_MIN);

add_action('wp_footer', function () {
    if (Config::getTransformationScope() === 'ALL') {
        ob_end_flush();
    }
}, PHP_INT_MAX);

add_filter('the_content', function ($content) {
    if (Config::getTransformationScope() !== 'CONTENT' && Config::getTransformationScope() !== 'ALL') {
        return $content;
    }

    $sitemapPath = Config::getAddSitemapPath() === 'CONTENT' || Config::getAddSitemapPath() === 'ALL' ? currentPagePath() : null;

    return transformImageHtml($content, $sitemapPath);
});

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

                <a href="https://app.picperf.io" target="_blank">Sign in to PicPerf</a>
            </p>
        </div>
    <?php
});

add_action('admin_notices', function () {
    if (!is_plugin_active('picperf-lite/picperf-lite.php')) {
        return;
    }

    ?>
        <div class="notice notice-error is-dismissible">
            <p>
                You're using the premium PicPerf plugin, but "PicPerf Lite" is still active. Please deactivate it to to prevent issues.
            </p>
        </div>
    <?php
});
