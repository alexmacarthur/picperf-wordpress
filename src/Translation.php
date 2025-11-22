<?php

namespace PicPerf;

class Translation
{
    public static function getTransformationScope(): string
    {
        $scope = Config::getTransformationScope();

        if ($scope === 'ALL') {
            return __('All Images', 'picperf-wordpress');
        } elseif ($scope === 'CONTENT') {
            return __('Content Images Only', 'picperf-wordpress');
        } else {
            return __('No Images', 'picperf-wordpress');
        }
    }

    public static function getSitemapScope(): string
    {
        $scope = Config::getAddSitemapPath();

        if ($scope === 'ALL') {
            return __('All Images', 'picperf-wordpress');
        } elseif ($scope === 'CONTENT') {
            return __('Content Images Only', 'picperf-wordpress');
        } else {
            return __('No Images', 'picperf-wordpress');
        }
    }
}
