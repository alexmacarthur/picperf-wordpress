## PicPerf WordPress Plugin

[PicPerf](http://picperf.io/) is a service for automatically optimizing, reformating, and aggressively caching your website's images just by prefixing the URLs you're already using. This WordPress plugin will take care of that part too, making it even easier to upgrade your image performance.

### Getting Started

1. Create an account.

In order to benefit from this plugin, you'll need to first create an account at [app.picperf.io](https://app.picperf.io/). You'll automatically be given a 14-day free trial, but in order keep your images optimized beyond that, upgrade to a regular plan.

2. Add your domain.

Add your website's domain inside the PicPerf dashboard. If your WordPress site runs on a different domain from your public website, please add both domains.

### Installation

After you've created an account and added your domain, all you need to do is install the plugin and activate it. You're set!

### What It Does

This plugin will automatically prefix every URL found in an image tag with the PicPerf host, allowing it to be optimized, reformatted, and cached. Currently, it does _not_ affect image URLs that are not exposed through WordPress's `the_content` filter.

Because of this, you're highly encouraged to manually change image URLs outside this scope. For convenience, you may use the `PicPerf/transformUrl()` function available globally when the plugin is active:

```php
    $transformedUrl = PicPerf\transformUrl("https://example.com/my-image.jpg");

    // https://picperf.io/https://example.com/my-image.jpg
```

## Read More

For more information on PicPerf, dig into [the documentation](http://picperf.io/docs).
