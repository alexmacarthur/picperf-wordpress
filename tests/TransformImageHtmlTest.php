<?php

namespace PicPerf;

require_once './tests/setup.php';
require_once './src/utils.php';

const PIC_PERF_HOST = 'https://picperf.io/';

const IMAGE_MARKUP = '<img decoding="async" width="684" height="1024" src="https://urmom.com/wp-content/uploads/2023/06/trees-684x1024.png" alt="" class="wp-image-111" srcset="https://urmom.com/wp-content/uploads/2023/06/trees-684x1024.png 684w, https://urmom.com/wp-content/uploads/2023/06/trees-200x300.png 200w, https://urmom.com/wp-content/uploads/2023/06/trees-768x1150.png 768w, https://urmom.com/wp-content/uploads/2023/06/trees.png 800w" sizes="(max-width: 684px) 100vw, 684px">';

it('transformImageHtml() transforms all URLs', function () {
    $result = transformImageHtml(IMAGE_MARKUP);

    expect($result)->toBe('<img decoding="async" width="684" height="1024" src="https://picperf.io/https://urmom.com/wp-content/uploads/2023/06/trees-684x1024.png" alt="" class="wp-image-111" srcset="https://picperf.io/https://urmom.com/wp-content/uploads/2023/06/trees-684x1024.png 684w, https://picperf.io/https://urmom.com/wp-content/uploads/2023/06/trees-200x300.png 200w, https://picperf.io/https://urmom.com/wp-content/uploads/2023/06/trees-768x1150.png 768w, https://picperf.io/https://urmom.com/wp-content/uploads/2023/06/trees.png 800w" sizes="(max-width: 684px) 100vw, 684px">');
});

it('transformImageHtml() transforms normal image tags', function () {
    $result = transformImageHtml("<img src='http://urmom.com/something.jpg' />");

    expect($result)->toBe("<img src='https://picperf.io/http://urmom.com/something.jpg' />");
});

it('transformImageHtml() does not transform relative images', function () {
    $result = transformImageHtml("<img src='/something.jpg' />");

    expect($result)->toBe("<img src='/something.jpg' />");
});

it('transformImageHtml() does not transform local images', function () {
    $result = transformImageHtml("<img src='https://localhost.test/img.jpg' />");

    expect($result)->toBe("<img src='https://localhost.test/img.jpg?picperf_local=true' />");
});

describe('setting sitemap path', function () {
    it('transforms URLs with sitemap path', function () {
        $result = transformImageHtml(IMAGE_MARKUP, '/some/path');

        expect($result)->toBe('<img decoding="async" width="684" height="1024" src="https://picperf.io/https://urmom.com/wp-content/uploads/2023/06/trees-684x1024.png?sitemap_path=/some/path" alt="" class="wp-image-111" srcset="https://picperf.io/https://urmom.com/wp-content/uploads/2023/06/trees-684x1024.png?sitemap_path=/some/path 684w, https://picperf.io/https://urmom.com/wp-content/uploads/2023/06/trees-200x300.png?sitemap_path=/some/path 200w, https://picperf.io/https://urmom.com/wp-content/uploads/2023/06/trees-768x1150.png?sitemap_path=/some/path 768w, https://picperf.io/https://urmom.com/wp-content/uploads/2023/06/trees.png?sitemap_path=/some/path 800w" sizes="(max-width: 684px) 100vw, 684px">');
    });
});
