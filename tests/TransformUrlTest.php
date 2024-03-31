<?php

namespace PicPerf;

require_once './tests/setup.php';
require_once './src/utils.php';

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

    expect($result)->toBe('https://picperf.io/http://urmom.com/something.jpg');
});

it('transformUrl() does not transform a URL that is already transformed', function () {
    $result = transformUrl('https://picperf.io/http://urmom.com/something.jpg');

    expect($result)->toBe('https://picperf.io/http://urmom.com/something.jpg');
});

it('transformUrl() adds sitemap path', function () {
    $result = transformUrl('http://urmom.com/something.jpg', '/some/path');

    expect($result)->toBe('https://picperf.io/http://urmom.com/something.jpg?sitemap_path=/some/path');
});
