<?php

namespace PicPerf;

define('PICPERF_IMAGE_URL_PATTERN', '/(?:https?:\/)?\/[^ ,]+\.(jpg|jpeg|png|gif|webp|avif)/i');

function logError($message)
{
    error_log('PicPerf Error: '.$message);
}

function prepareDomain(string $domain): string
{
    return preg_replace('/^https?:\/\/(www\.)?/', '', rtrim($domain, '/'));
}

function isValidDomain(string $domain): bool
{
    if (empty($domain)) {
        return false;
    }

    return filter_var($domain, FILTER_VALIDATE_DOMAIN) !== false;
}

function currentUrl()
{
    $protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];

    return "$protocol://$host$uri";
}

function currentPagePath()
{
    return parse_url(currentUrl(), PHP_URL_PATH) ?? '/';
}

function pureTransform(string $url)
{
    try {
        $derivedHost = deriveHost();
        $parsedUrl = parse_url($url);

        // It already starts with the PicPerf host.
        if (strpos($url, $derivedHost) === 0) {
            return $url;
        }

        // It's probably a relative path.
        if (empty($parsedUrl['host'])) {
            return $url;
        }

        // It's probably a local image.
        if (preg_match("/localhost|\.test$/", $parsedUrl['host'])) {
            return applyLocalParameter($url);
        }

        return $derivedHost.$url;
    } catch (\Exception $e) {
        logError("Failed to parse URL: $url");

        return $url;
    }
}

function deriveHost(): string
{
    return Config::getCustomDomain() ?: PIC_PERF_HOST;
}

function applyLocalParameter(string $url): string
{
    if (strpos($url, 'picperf_local=') === false) {
        return add_query_arg([
            'picperf_local' => 'true',
        ], $url);
    }

    return $url;
}

function transformUrl(string $url, $sitemapPath = null)
{
    $transformedUrl = pureTransform($url);

    if (! empty($sitemapPath)) {
        $withSitemapPath = add_query_arg([
            'sitemap_path' => $sitemapPath,
        ], $transformedUrl);

        return str_replace('%2F', '/', $withSitemapPath);
    }

    return $transformedUrl;
}

function transformImageHtml($content, $sitemapPath = null)
{
    // Find every image tag.
    return preg_replace_callback('/(<img)[^\>]*(\>|>)/is', function ($match) use ($sitemapPath) {

        // Find every URL.
        return preg_replace_callback(PICPERF_IMAGE_URL_PATTERN, function ($subMatch) use ($sitemapPath) {
            return transformUrl($subMatch[0], $sitemapPath);
        }, $match[0]);
    }, $content);
}

function transformStyleTags($content, $sitemapPath = null)
{
    // Find every style tag.
    return preg_replace_callback('/<style.*?>(.*?)<\/style>/is', function ($match) use ($sitemapPath) {

        // Find every URL.
        return preg_replace_callback(PICPERF_IMAGE_URL_PATTERN, function ($subMatch) use ($sitemapPath) {
            return transformUrl($subMatch[0], $sitemapPath);
        }, $match[0]);
    }, $content);
}

function transformInlineStyles($content, $sitemapPath = null)
{
    // Find every inline style.
    return preg_replace_callback('/(?<=style=["\'])(.*?)(?=["\'](?:\s|>))/is', function ($match) use ($sitemapPath) {

        // Find every URL.
        return preg_replace_callback(PICPERF_IMAGE_URL_PATTERN, function ($subMatch) use ($sitemapPath) {
            return transformUrl($subMatch[0], $sitemapPath);
        }, $match[0]);
    }, $content);
}

function transformDataAttributes($content, $sitemapPath = null)
{
    return preg_replace_callback('/data-[a-zA-Z0-9]+=(?:"|\')([^"]*)(?:"|\')/is', function ($match) use ($sitemapPath) {

        return preg_replace_callback(PICPERF_IMAGE_URL_PATTERN, function ($subMatch) use ($sitemapPath) {
            return transformUrl($subMatch[0], $sitemapPath);
        }, $match[0]);
    }, $content);
}

function isPluginSettingsPage()
{
    $screen = get_current_screen();

    return $screen && $screen->id === 'settings_page_picperf-settings';
}
