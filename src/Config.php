<?php

namespace PicPerf;

class Config
{
    public static function getAddSitemapPath()
    {
        // 'ALL' or 'CONTENT'
        return defined('PICPERF_ADD_SITEMAP_PATH') ? constant("PICPERF_ADD_SITEMAP_PATH") : '';
    }

    public static function getTransformationScope()
    {
        // 'ALL' or 'CONTENT'
        return defined('PICPERF_TRANSFORMATION_SCOPE') ? constant("PICPERF_TRANSFORMATION_SCOPE") : 'ALL';
    }
}
