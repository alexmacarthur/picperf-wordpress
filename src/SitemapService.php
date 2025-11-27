<?php

namespace PicPerf;

class SitemapService
{
    const SITEMAP_PATH = '/picperf/sitemap';

    public function serveSitemap(
        string $requestPath,
        bool $shouldDisableSitemap
    ) {
        if ($shouldDisableSitemap) {
            return;
        }

        if ($requestPath !== self::SITEMAP_PATH) {
            return;
        }

        header('Content-Type: application/xml; charset=utf-8');
        echo $this->fetchSitemap(Config::getProxyDomain());

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
