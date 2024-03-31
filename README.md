# PicPerf WordPress Plugin

[PicPerf](http://picperf.io) is a service for automatically optimizing, reformating, and aggressively caching your website's images just by prefixing the URLs you're already using. This WordPress plugin will take care of that part too, making it even easier to upgrade your image performance.

## Getting Started

1. Create an account.

In order to benefit from this plugin, you'll need to first sign up for a plan at [picperf.io](https://picperf.io). You'll automatically be given a 14-day free trial (no card required), but in order keep your images optimized beyond that, upgrade to a regular plan.

1. Add your domain.

Add your website's domain inside the PicPerf dashboard. If your WordPress site runs on a different domain from your public website, please add both domains.

## Installation

After you've created an account and added your domain, all you need to do is install the plugin and activate it. You're set!

## Usage

This plugin will automatically prefix every URL found in an image tag with the PicPerf host, allowing it to be optimized, reformatted, and globally cached. By default, it'll impact every image that's rendered in the final HTML output of your page.

### Changing Transformation Scope

If you'd like to disable universal URL transformations, you can set the `PICPERF_TRANSFORMATION_SCOPE` constant in your `wp-config.php` file to `null`. Setting it to `CONTENT` will cause only images rendered via the `the_content` filter to be transformed.

## Manually Transforming URLs

If you've disabled universal transformation and would like to manually handle images, you may use the `PicPerf/transformUrl()` function. It's available globally when the plugin is active:

```php
$transformedUrl = PicPerf\transformUrl("https://example.com/my-image.jpg");

// https://picperf.io/https://example.com/my-image.jpg
```

## Read More

For more information on PicPerf, dig into [the documentation](http://picperf.io/docs).
