<?php

namespace PicPerf;

require_once './tests/setup.php';
require_once './src/utils.php';

$originalHttpHost = null;
$originalRequestUri = null;
$originalHttps = null;

beforeEach(function () use (&$originalHttpHost, &$originalRequestUri, &$originalHttps) {
    $originalHttpHost = $_SERVER['HTTP_HOST'] ?? null;
    $originalRequestUri = $_SERVER['REQUEST_URI'] ?? null;
    $originalHttps = $_SERVER['HTTPS'] ?? null;
});

afterEach(function () use ($originalHttpHost, $originalRequestUri, $originalHttps) {
    $_SERVER['HTTP_HOST'] = $originalHttpHost;
    $_SERVER['REQUEST_URI'] = $originalRequestUri;
    $_SERVER['HTTPS'] = $originalHttps;
});

describe("currentUrl()", function () {
    it("returns the current URL", function () {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/some/path';

        $result = currentUrl();

        expect($result)->toBe('http://localhost/some/path');
    });

    it('returns correct URL when SSL is enabled', function () {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/some/path';
        $_SERVER['HTTPS'] = 'on';

        $result = currentUrl();

        expect($result)->toBe('https://localhost/some/path');
    });

    it('returns correct URL when no path is set', function () {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '';

        $result = currentUrl();

        expect($result)->toBe('http://localhost');
    });
});

describe("currentPagePath()", function () {
    it("returns the current page path", function () {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/some/path';
        $_SERVER['HTTPS'] = 'on';

        $result = currentPagePath();

        expect($result)->toBe('/some/path');
    });

    it("returns the current page path when no path is set", function () {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '';
        $_SERVER['HTTPS'] = 'on';

        $result = currentPagePath();

        expect($result)->toBe('/');
    });

    it("returns the current page with root trailing slash", function () {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['HTTPS'] = 'on';

        $result = currentPagePath();

        expect($result)->toBe('/');
    });
});
