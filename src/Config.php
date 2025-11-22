<?php

namespace PicPerf;

class Config
{
    public static function getAddSitemapPath(): string
    {
        // 'ALL' or 'CONTENT'
        return defined('PICPERF_ADD_SITEMAP_PATH') ? constant('PICPERF_ADD_SITEMAP_PATH') : '';
    }

    public static function getTransformationScope(): string
    {
        // 'ALL' or 'CONTENT'
        return defined('PICPERF_TRANSFORMATION_SCOPE') ? constant('PICPERF_TRANSFORMATION_SCOPE') : 'ALL';
    }

    public static function shouldDisableSitemap(): bool
    {
        return defined('PICPERF_DISABLE_SITEMAP') && constant('PICPERF_DISABLE_SITEMAP') === true;
    }

    public static function getCustomDomain(): ?string
    {
        $customDomain = get_option('picperf_custom_domain', '');

        return $customDomain ? 'https://'.trim($customDomain).'/' : null;
    }

    public static function getProxyDomain(): string
    {
        $domain = get_option('picperf_proxy_domain', '');

        if ($domain) {
            return trim($domain);
        }

        $parsedUrl = parse_url(get_site_url());
        $withWww = $parsedUrl['host'];

        return preg_replace('/^www\./', '', $withWww);
    }
}
