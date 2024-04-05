<?php

namespace PicPerf;

class Config
{
    public static function getAddSitemapPath(): string
    {
        // 'ALL' or 'CONTENT'
        return defined('PICPERF_ADD_SITEMAP_PATH') ? constant("PICPERF_ADD_SITEMAP_PATH") : '';
    }

    public static function getTransformationScope(): string
    {
        // 'ALL' or 'CONTENT'
        return defined('PICPERF_TRANSFORMATION_SCOPE') ? constant("PICPERF_TRANSFORMATION_SCOPE") : 'ALL';
    }

    public static function shouldDisableSitemap(): bool
    {
        return defined('PICPERF_DISABLE_SITEMAP') && constant("PICPERF_DISABLE_SITEMAP") === true;
    }
}
