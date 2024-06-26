<?php

namespace PicPerf;

require_once './tests/setup.php';
require_once './src/utils.php';

it('transformInlineStyles() converts image URLs', function () {
    $result = transformInlineStyles('<div style="background: url(\'https://whatever/img.jpeg\');">');

    expect($result)->toBe('<div style="background: url(\'https://picperf.io/https://whatever/img.jpeg\');">');
});

it('transformInlineStyles() does not convert unknown file extensions.', function () {
    $result = transformInlineStyles('<div style="background: url(\'https://whatever/img.does-not-exist\');">');

    expect($result)->toBe('<div style="background: url(\'https://whatever/img.does-not-exist\');">');
});

it('transformInlineStyles() does not mangle elements with no style attribute', function () {
    $result = transformInlineStyles('<div id="el">hey!</div>');

    expect($result)->toBe('<div id="el">hey!</div>');
});

describe('setting sitemap path', function () {
    it('transforms URLs with sitemap path', function () {
        $result = transformInlineStyles('<div style="background: url(\'https://whatever/img.jpeg\');">', '/some/path');

        expect($result)->toBe('<div style="background: url(\'https://picperf.io/https://whatever/img.jpeg?sitemap_path=/some/path\');">');

    });
});
