<?php

namespace PicPerf;

require_once __DIR__.'/src/utils.php';

const DASHBOARD_PROXIED_URL = 'https://go.picperf.io/proxied/domains';

/**
 * Add PicPerf settings page to WordPress admin menu
 */
add_action('admin_menu', function () {
    add_options_page(
        'PicPerf Settings',
        'PicPerf ⚡',
        'manage_options',
        'picperf-settings',
        'PicPerf\renderSettingsPage'
    );
});

/**
 * Register PicPerf settings
 */
add_action('admin_init', function () {
    register_setting('picperf_settings', 'picperf_custom_domain', [
        'type' => 'string',
        'sanitize_callback' => 'PicPerf\validateCustomDomain',
        'default' => '',
    ]);

    register_setting('picperf_settings', 'picperf_proxy_domain', [
        'type' => 'string',
        'sanitize_callback' => 'PicPerf\validateCustomDomain',
        'default' => '',
    ]);
});

/**
 * Validate custom domain setting
 */
function validateCustomDomain($value)
{
    $originalValue = $value;
    $preparedDomain = prepareDomain($value);

    if (empty($preparedDomain)) {
        return '';
    }

    if (! isValidDomain($preparedDomain)) {
        add_settings_error(
            'picperf_custom_domain',
            'invalid_domain',
            sprintf('Invalid domain format: "%s". Please enter a valid domain name (e.g., cdn.your-domain.com).', esc_html($originalValue)),
            'error'
        );

        return get_option('picperf_custom_domain', '');
    }

    return $preparedDomain;
}

function getStatusText(): string
{
    $domainValidator = new DomainValidator;

    $isValidDomain = $domainValidator->domainExists();

    if ($isValidDomain && $domainValidator->isSubscribed()) {
        return '✅ Active and optimizing images!';
    }

    if (! $isValidDomain) {
        return "
            ⚠️ A valid domain isn't set! 

            <span class='picperf-description'>
                Please make sure this domain has been added to the <a href='".DASHBOARD_PROXIED_URL."' target='_blank'>PicPerf Dashboard</a>, and that your subscription is active.
            </span>
            ";
    }

    return "
        ⚠️ Your subscription is inactive! 

        <span class='picperf-description'>
            We couldn't find an active subscription for the domain. Please make sure your subscription is active in the <a href='https://go.picperf.io/profile' target='_blank'>PicPerf Dashboard</a>.
        </span>
        ";
}

function renderSettingsPage()
{
    $statusText = getStatusText();

    ?>

    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <div class="card-wrapper">
        
            <div class="card">
                <h2>Plugin Information</h2>
                <p>
                    <strong>Version:</strong> <?php echo esc_html(PICPERF_PLUGIN_VERSION); ?>
                </p>
                
                <p>
                    <strong>Status:</strong> <?php echo $statusText; ?>
                </p>
            </div>

            <div class="card">
                <h2>General Settings</h2>
                <form method="post" action="options.php">
                    <?php settings_fields('picperf_settings'); ?>
                    <?php do_settings_sections('picperf_settings'); ?>

                    <div class="form-field-wrapper">
                    
                    <div class="form-field">
                        <label for="picperf_proxy_domain"><strong>Proxy Domain (required)</strong></label>
                        <input 
                            type="text" 
                            id="picperf_proxy_domain" 
                            name="picperf_proxy_domain" 
                            value="<?php echo esc_attr(Config::getProxyDomain()); ?>" 
                            class="regular-text"                            
                            style="margin-top: 8px; width: 100%; max-width: 400px;"
                        />
                        <span class="picperf-description">
                            This is the domain your images are normally served from. By default, it's set to your site's configured domain. When overriding, leave off the "https://www."
                        </span>
                    </div>

                    <div class="form-field">
                        <label for="picperf_custom_domain"><strong>Custom Domain</strong></label>
                        <input 
                            type="text" 
                            id="picperf_custom_domain" 
                            name="picperf_custom_domain" 
                            value="<?php echo esc_attr(get_option('picperf_custom_domain', '')); ?>" 
                            class="regular-text"
                            placeholder="cdn.your-domain.com"
                            style="margin-top: 8px; width: 100%; max-width: 400px;"
                        />
                        <span class="picperf-description">
                            Only enter a domain if you've added one in the PicPerf dashboard and updated your DNS records. Learn more here. <a href="https://picperf.io/docs/custom-domain" target="_blank">Learn more here.</a>
                        </span>
                    </div>

                    </div>
                    
                    <?php submit_button('Save Settings'); ?>
                </form>
            </div>

            <div class="card">
                <h2>Code-Driven Configuration</h2>
                <span class="picperf-description">
                    The following settings can currently only be changed by setting constants in your <code>wp-config.php</code> file.
                </span>

                <p><strong>Current Transformation Scope:</strong> <?php echo esc_html(Translation::getTransformationScope()); ?>
                <span class="picperf-description">
                        Which images are being optimized on your site. <a href="https://picperf.io/docs/wordpress#changing-transformation-scope" target="_blank">Learn more here.</a>
                    </span>
            </p>
                <p><strong>Current Sitemap Scope:</strong> <?php echo esc_html(Translation::getSitemapScope()); ?>
                    <span class="picperf-description">
                        Which images are being added to your site's image sitemap. <a href="https://picperf.io/docs/wordpress#using-an-auto-generated-image-sitemap" target="_blank">Learn more here.</a>
                    </span>
            </p>
            </div>

            <div class="card">
                <h2>Manage Your Account</h2>
                <p>To manage your PicPerf subscription and view analytics:</p>
                <p><a href="<?php echo DASHBOARD_PROXIED_URL; ?>" target="_blank" class="button button-primary">Visit PicPerf Dashboard</a></p>

                <h2 style="margin-top: 30px;">Need Help?</h2>
                <p>Don't spin your wheels! Send an email to <a href="mailto:support@picperf.io">support@picperf.io</a>.</p>
            </div>
        </div>
    </div>

    <style>
        .card-wrapper {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 520px));
            margin-top: 12px;

            @media screen and (max-width: 782px) {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 20px;
            margin: 0;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
        }
        .card h2 {
            margin-top: 0;
        }
        .card ul {
            margin-left: 20px;
        }
        .picperf-description {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 13px;
            line-height: 1.4;
        }

        .form-field-wrapper {
            display: flex;;
            flex-direction: column;
            gap: 20px;
        }
    </style>
    <?php
}
