<?php

namespace PicPerf;

require_once './tests/setup.php';
require_once './src/utils.php';

it('transformStyleTags() transforms all URLs', function () {
    $result = transformStyleTags('<style>
    body {
        background: url("https://urmom.com/something.jpg");
    }

    @font-face {
        font-family: "Test"; src: url("trickster-outline.woff") format("woff");
    }
</style>');

    expect($result)->toBe('<style>
    body {
        background: url("https://picperf.io/https://urmom.com/something.jpg");
    }

    @font-face {
        font-family: "Test"; src: url("trickster-outline.woff") format("woff");
    }
</style>');
});

it('transformStyleTags() transforms all despite single or double quotes', function () {
    $result = transformStyleTags("<style>body { background: url('https://urmom.com/something.jpg'); }</style>");

    expect($result)->toBe("<style>body { background: url('https://picperf.io/https://urmom.com/something.jpg'); }</style>");
});

it('transformStyleTags() transforms URLs even when there are no quotes', function () {
    $result = transformStyleTags("<style>body { background: url(https://urmom.com/something.jpg); }</style>");

    expect($result)->toBe("<style>body { background: url(https://picperf.io/https://urmom.com/something.jpg); }</style>");
});

it('transformStyleTags() preserves query params on images', function () {
    $result = transformStyleTags("<style>body { background: url(https://urmom.com/something.jpg?hey=true); }</style>");

    expect($result)->toBe("<style>body { background: url(https://picperf.io/https://urmom.com/something.jpg?hey=true); }</style>");
});

describe('setting sitemap path', function () {
    it('transformStyleTags() transforms all URLs', function () {
        $result = transformStyleTags('<style>
    body {
        background: url("https://urmom.com/something.jpg");
    }

    @font-face {
        font-family: "Test"; src: url("trickster-outline.woff") format("woff");
    }
</style>', '/some/path');

        expect($result)->toBe('<style>
    body {
        background: url("https://picperf.io/https://urmom.com/something.jpg?sitemap_path=/some/path");
    }

    @font-face {
        font-family: "Test"; src: url("trickster-outline.woff") format("woff");
    }
</style>');
    });
});
