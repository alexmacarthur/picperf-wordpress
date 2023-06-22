<?php

namespace PicPerf;

require './src/utils.php';

const PIC_PERF_HOST = 'https://picperf.dev/';

it('transformUrl() returns same URL in local environment', function () {
    $result = transformUrl('http://localhost:3000/something.jpg');

    expect($result)->toBe('http://localhost:3000/something.jpg');
});

it('transformUrl() returns same URL on .test domain.', function () {
    $result = transformUrl('http://urmom.test/something.jpg');

    expect($result)->toBe('http://urmom.test/something.jpg');
});

it('transformUrl() transforms URL correctly', function () {
    $result = transformUrl('http://urmom.com/something.jpg');

    expect($result)->toBe('https://picperf.dev/http://urmom.com/something.jpg');
});

const IMAGE_MARKUP = '<img decoding="async" width="684" height="1024" src="https://urmom.com/wp-content/uploads/2023/06/trees-684x1024.png" alt="" class="wp-image-111" srcset="https://urmom.com/wp-content/uploads/2023/06/trees-684x1024.png 684w, https://urmom.com/wp-content/uploads/2023/06/trees-200x300.png 200w, https://urmom.com/wp-content/uploads/2023/06/trees-768x1150.png 768w, https://urmom.com/wp-content/uploads/2023/06/trees.png 800w" sizes="(max-width: 684px) 100vw, 684px">';

it('transformImageHtml() transforms all URLs', function () {
    $result = transformImageHtml(IMAGE_MARKUP);

    expect($result)->toBe('<img decoding="async" width="684" height="1024" src="https://picperf.dev/https://urmom.com/wp-content/uploads/2023/06/trees-684x1024.png" alt="" class="wp-image-111" srcset="https://picperf.dev/https://urmom.com/wp-content/uploads/2023/06/trees-684x1024.png 684w, https://picperf.dev/https://urmom.com/wp-content/uploads/2023/06/trees-200x300.png 200w, https://picperf.dev/https://urmom.com/wp-content/uploads/2023/06/trees-768x1150.png 768w, https://picperf.dev/https://urmom.com/wp-content/uploads/2023/06/trees.png 800w" sizes="(max-width: 684px) 100vw, 684px">');
});

it('transformImageHtml() transforms normal image tags', function () {
    $result = transformImageHtml("<img src='http://urmom.com/something.jpg' />");

    expect($result)->toBe("<img src='https://picperf.dev/http://urmom.com/something.jpg' />");
});

it('transformImageHtml() does not transform relative images', function () {
    $result = transformImageHtml("<img src='/something.jpg' />");

    expect($result)->toBe("<img src='/something.jpg' />");
});

it('transformImageHtml() does not transform local images', function () {
    $result = transformImageHtml("<img src='https://localhost.test/img.jpg' />");

    expect($result)->toBe("<img src='https://localhost.test/img.jpg' />");
});
