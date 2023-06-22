<?php

namespace PicPerf;

function logError($message)
{
    error_log('PicPerf Error: '.$message);
}

function transformUrl($url)
{
    try {
        $parsedUrl = parse_url($url);

        // It's probably a relative path.
        if (empty($parsedUrl['host'])) {
            return $url;
        }

        //It's probably a local image.
        if (preg_match("/localhost|\.test$/", $parsedUrl['host'])) {
            return $url;
        }

        return PIC_PERF_HOST.$url;
    } catch (\Exception $e) {
        logError("Failed to parse URL: $url");

        return $url;
    }
}

function transformImageHtml($content)
{
    return preg_replace_callback('/(<img)[^\>]*(\>|>)/i', function ($match) {
        return preg_replace_callback('/(https?:\/\/[^\'"\s]+)(?:\s+\d+)?/i', function ($subMatch) {
            return transformUrl($subMatch[0]);
        }, $match[0]);
    }, $content);
}
