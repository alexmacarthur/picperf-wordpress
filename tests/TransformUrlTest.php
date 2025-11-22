<?php

namespace PicPerf;

require_once './tests/setup.php';
require_once './src/Config.php';
require_once './src/utils.php';

it('transformUrl() returns same URL in local environment', function () {
    $result = transformUrl('http://localhost:3000/something.jpg');

    expect($result)->toBe('http://localhost:3000/something.jpg?picperf_local=true');
});

it('transformUrl() only adds local query parameter once', function () {
    $result = transformUrl('http://localhost:3000/something.jpg?picperf_local=true');

    expect($result)->toBe('http://localhost:3000/something.jpg?picperf_local=true');
});

it('transformUrl() returns same URL on .test domain, with query string', function () {
    $result = transformUrl('http://urmom.test/something.jpg');

    expect($result)->toBe('http://urmom.test/something.jpg?picperf_local=true');
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

describe('custom domain', function () {
    beforeEach(function () {
        global $_test_options;
        $_test_options = [
            'picperf_custom_domain' => 'custom.urmom.com',
        ];
    });

    it('transformUrl() uses custom domain when configured', function () {
        $result = transformUrl('http://example.com/image.jpg');

        expect($result)->toBe('https://custom.urmom.com/http://example.com/image.jpg');
    });

    it('transformUrl() does not transform URL already transformed with custom domain', function () {
        $result = transformUrl('https://custom.urmom.com/http://example.com/image.jpg');

        expect($result)->toBe('https://custom.urmom.com/http://example.com/image.jpg');
    });
});
