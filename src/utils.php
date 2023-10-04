<?php

namespace PicPerf;

define('PICPERF_IMAGE_URL_PATTERN', '/(https?:\/\/[^\'"\s]+\.(png|jpe?g|gif|webp|avif))(?:\s+\d+)?/i');

function logError($message)
{
    error_log('PicPerf Error: ' . $message);
}

function transformUrl($url)
{
    try {
        $parsedUrl = parse_url($url);

        // It already starts with the PicPerf host.
        if (strpos($url, PIC_PERF_HOST) === 0) {
            return $url;
        }

        // It's probably a relative path.
        if (empty($parsedUrl['host'])) {
            return $url;
        }

        // It's probably a local image.
        if (preg_match("/localhost|\.test$/", $parsedUrl['host'])) {
            return $url;
        }

        return PIC_PERF_HOST . $url;
    } catch (\Exception $e) {
        logError("Failed to parse URL: $url");

        return $url;
    }
}

function transformImageHtml($content)
{
    // Find every image tag.
    return preg_replace_callback('/(<img)[^\>]*(\>|>)/is', function ($match) {

        // Find every URL.
        return preg_replace_callback('/(https?:\/\/[^\'"\s]+)(?:\s+\d+)?/i', function ($subMatch) {
            return transformUrl($subMatch[0]);
        }, $match[0]);
    }, $content);
}

function transformStyleTags($content)
{
    // Find every style tag.
    return preg_replace_callback('/<style.*?>(.*?)<\/style>/is', function ($match) {

        // Find every URL.
        return preg_replace_callback(PICPERF_IMAGE_URL_PATTERN, function ($subMatch) {
            return transformUrl($subMatch[0]);
        }, $match[0]);
    }, $content);
}

function transformInlineStyles($content)
{
    // Find every inline style.
    return preg_replace_callback('/style=([\'"])(.*?)\1/is', function ($match) {

        // Find every URL.
        return preg_replace_callback(PICPERF_IMAGE_URL_PATTERN, function ($subMatch) {
            return transformUrl($subMatch[0]);
        }, $match[0]);
    }, $content);
}
