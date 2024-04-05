<?php

namespace PicPerf;

add_action('template_redirect', function () {
    $requestPath = explode('?', $_SERVER['REQUEST_URI'])[0];

    (new SitemapService())->serveSitemap(
        $requestPath,
        get_site_url(),
        Config::shouldDisableSitemap()
    );
}, 0);

add_action('wp_head', function () {
    $url = get_site_url();
    $sitemapUrl = $url . SitemapService::SITEMAP_PATH;

    if (Config::shouldDisableSitemap()) {
        return;
    }

    echo "<link rel='sitemap' type='application/xml' href='$sitemapUrl' />\n";
}, 0);
