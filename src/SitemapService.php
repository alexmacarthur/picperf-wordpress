<?php

namespace PicPerf;

class SitemapService
{
    const SITEMAP_PATH = '/picperf/sitemap';

    public function serveSitemap(
        string $requestPath,
        string $url,
        bool $shouldDisableSitemap
    ) {
        if ($shouldDisableSitemap) {
            return;
        }

        if ($requestPath !== self::SITEMAP_PATH) {
            return;
        }

        if (! $url) {
            return;
        }

        $host = parse_url($url)['host'];

        header('Content-Type: application/xml; charset=utf-8');
        echo $this->fetchSitemap($host);

        $this->die();
    }

    public function fetchSitemap(string $host): string
    {
        $response = wp_remote_get("https://picperf.io/sitemap/$host");

        return wp_remote_retrieve_body($response);
    }

    public function die()
    {
        exit();
    }
}
