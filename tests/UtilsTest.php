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

describe('currentUrl()', function () {
    it('returns the current URL', function () {
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

describe('currentPagePath()', function () {
    it('returns the current page path', function () {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/some/path';
        $_SERVER['HTTPS'] = 'on';

        $result = currentPagePath();

        expect($result)->toBe('/some/path');
    });

    it('returns the current page path when no path is set', function () {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '';
        $_SERVER['HTTPS'] = 'on';

        $result = currentPagePath();

        expect($result)->toBe('/');
    });

    it('returns the current page with root trailing slash', function () {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['HTTPS'] = 'on';

        $result = currentPagePath();

        expect($result)->toBe('/');
    });
});

describe('prepareDomain()', function () {
    it('removes protocol from domain', function () {
        $result = prepareDomain('https://example.com/');

        expect($result)->toBe('example.com');
    });

    it('removes trailing slash from domain', function () {
        $result = prepareDomain('example.com/');

        expect($result)->toBe('example.com');
    });

    it('handles domain without protocol or trailing slash', function () {
        $result = prepareDomain('example.com');

        expect($result)->toBe('example.com');
    });

    it('handles with www prefix', function () {
        $result = prepareDomain('http://www.example.com/');

        expect($result)->toBe('example.com');
    });

    it('handles subdomains', function () {
        $result = prepareDomain('https://sub.example.com/');

        expect($result)->toBe('sub.example.com');
    });
});

describe('isValidDomain()', function () {
    it('returns true for valid domain', function () {
        $result = isValidDomain('example.com');

        expect($result)->toBeTrue();
    });

    it('returns false for invalid domain', function () {
        $result = isValidDomain('invalid_domain.. d dd..');

        expect($result)->toBeFalse();
    });

    it('returns true for subdomain', function () {
        $result = isValidDomain('sub.example.com');

        expect($result)->toBeTrue();
    });

    it('returns false for empty string', function () {
        $result = isValidDomain('');

        expect($result)->toBeFalse();
    });
});

describe('removeDomain()', function () {
    it('removes http domain from URL', function () {
        $result = removeDomain('example.com', 'http://example.com/path/to/image.jpg');

        expect($result)->toBe('/path/to/image.jpg');
    });

    it('removes https domain from URL', function () {
        $result = removeDomain('example.com', 'https://example.com/path/to/image.jpg');

        expect($result)->toBe('/path/to/image.jpg');
    });

    it('removes domain with www prefix from URL', function () {
        $result = removeDomain('example.com', 'https://www.example.com/path/to/image.jpg');

        expect($result)->toBe('/path/to/image.jpg');
    });

    it('handles domain input with protocol', function () {
        $result = removeDomain('https://example.com', 'https://example.com/path/to/image.jpg');

        expect($result)->toBe('/path/to/image.jpg');
    });

    it('handles domain input with trailing slash', function () {
        $result = removeDomain('example.com/', 'https://example.com/path/to/image.jpg');

        expect($result)->toBe('/path/to/image.jpg');
    });

    it('does not match when domain has www prefix but URL does not', function () {
        $result = removeDomain('www.example.com', 'https://example.com/path/to/image.jpg');

        expect($result)->toBe('https://example.com/path/to/image.jpg');
    });

    it('handles domain input with www prefix when URL also has www', function () {
        $result = removeDomain('www.example.com', 'https://www.example.com/path/to/image.jpg');

        expect($result)->toBe('/path/to/image.jpg');
    });

    it('preserves URL when domain does not match', function () {
        $result = removeDomain('example.com', 'https://other.com/path/to/image.jpg');

        expect($result)->toBe('https://other.com/path/to/image.jpg');
    });

    it('preserves URL when domain appears in path but not at start', function () {
        $result = removeDomain('example.com', 'https://other.com/example.com/image.jpg');

        expect($result)->toBe('https://other.com/example.com/image.jpg');
    });

    it('handles subdomain in URL', function () {
        $result = removeDomain('cdn.example.com', 'https://cdn.example.com/image.jpg');

        expect($result)->toBe('/image.jpg');
    });

    it('does not remove parent domain when subdomain is specified', function () {
        $result = removeDomain('cdn.example.com', 'https://example.com/image.jpg');

        expect($result)->toBe('https://example.com/image.jpg');
    });

    it('preserves query parameters', function () {
        $result = removeDomain('example.com', 'https://example.com/image.jpg?width=100&height=200');

        expect($result)->toBe('/image.jpg?width=100&height=200');
    });

    it('preserves fragment identifiers', function () {
        $result = removeDomain('example.com', 'https://example.com/page#section');

        expect($result)->toBe('/page#section');
    });

    it('handles URL with port number', function () {
        $result = removeDomain('example.com', 'https://example.com:8080/image.jpg');

        expect($result)->toBe('/image.jpg');
    });

    it('handles root URL', function () {
        $result = removeDomain('example.com', 'https://example.com');

        expect($result)->toBe('');
    });

    it('handles root URL with trailing slash', function () {
        $result = removeDomain('example.com', 'https://example.com/');

        expect($result)->toBe('/');
    });

    it('returns original URL when given relative path', function () {
        $result = removeDomain('example.com', '/path/to/image.jpg');

        expect($result)->toBe('/path/to/image.jpg');
    });

    it('handles empty URL', function () {
        $result = removeDomain('example.com', '');

        expect($result)->toBe('');
    });
});
