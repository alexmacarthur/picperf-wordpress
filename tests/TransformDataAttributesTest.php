<?php

namespace PicPerf;

require_once './tests/setup.php';
require_once './src/utils.php';

describe('transformDataAttributes()', function () {
    it('transforms URL in data attribute on image tag', function () {
        $result = transformDataAttributes('<img data-src="http://urmom.com/something.jpg" />');

        expect($result)->toBe('<img data-src="https://picperf.io/http://urmom.com/something.jpg" />');
    });

    it('does not transform relative URLs in data attribute on image tag', function () {
        $result = transformDataAttributes('<img data-src="/something.jpg" />');

        expect($result)->toBe('<img data-src="/something.jpg" />');
    });

    it('does not transform local URLs in data attribute on image tag', function () {
        $result = transformDataAttributes('<img data-src="https://localhost.test/img.jpg" />');

        expect($result)->toBe('<img data-src="https://localhost.test/img.jpg" />');
    });

    it('does not modify data attribute on source tag if it does not contain a URL', function () {
        $result = transformDataAttributes('<source data-src="something.jpg" />');

        expect($result)->toBe('<source data-src="something.jpg" />');
    });

    it('transforms URL in data attribute on div tag', function () {
        $result = transformDataAttributes('<div data-src="http://urmom.com/something.jpg"></div>');

        expect($result)->toBe('<div data-src="https://picperf.io/http://urmom.com/something.jpg"></div>');
    });

    it('does not transform other attributes that do not have an image in them', function () {
        $result = transformDataAttributes('<div data-src="https://whatever.com/something.jpg" data-something-else="hello"></div>');

        expect($result)->toBe('<div data-src="https://picperf.io/https://whatever.com/something.jpg" data-something-else="hello"></div>');
    });

    it('sets sitemap value when provided', function () {
        $result = transformDataAttributes('<img data-src="http://urmom.com/something.jpg" />', '/some/path');

        expect($result)->toBe('<img data-src="https://picperf.io/http://urmom.com/something.jpg?sitemap_path=/some/path" />');
    });
});
