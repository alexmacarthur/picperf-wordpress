<?php

namespace PicPerf;

require_once './src/SitemapService.php';

it("does not serve sitemap when request path is not '/picperf/sitemap'.", function () {
    $sitemapService = $this->createPartialMock(SitemapService::class, ['fetchSitemap']);
    $sitemapService->expects($this->never())->method('fetchSitemap');

    $sitemapService->serveSitemap('/picperf/sitemap/other', 'https://example.com', false);

    expect(ob_get_clean())->toBe('');
});

it("serves sitemap when request path is '/picperf/sitemap'.", function () {
    $sitemapService = $this->createPartialMock(SitemapService::class, ['fetchSitemap', 'die']);
    $sitemapService
        ->expects($this->once())
        ->method('fetchSitemap')
        ->with('example.com')
        ->willReturn('sitemap content');

    $sitemapService
        ->expects($this->once())
        ->method('die');

    $sitemapService->serveSitemap('/picperf/sitemap', 'https://example.com', false);

    expect(ob_get_clean())->toBe('sitemap content');
});

it('does not serve sitemap when sitemap is disabled.', function () {
    $sitemapService = $this->createPartialMock(SitemapService::class, ['fetchSitemap']);
    $sitemapService->expects($this->never())->method('fetchSitemap');

    $sitemapService->serveSitemap('/picperf/sitemap', 'https://example.com', true);

    expect(ob_get_clean())->toBe('');
});
