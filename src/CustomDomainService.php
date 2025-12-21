<?php

namespace PicPerf;

class CustomDomainService
{
    public static function checkForAssociatedDomain($domain): ?string
    {
        $customDomain = prepareDomain($domain);

        if (! $customDomain) {
            return null;
        }

        $response = wp_remote_get(Config::REMOTE_HOST.'/api/domain?customHostname='.$customDomain);

        if (is_wp_error($response)) {
            logError("Failed to validate domain: {$customDomain}");

            return null;
        }

        if (wp_remote_retrieve_response_code($response) == 404) {
            logError("Domain check returned non-200 response for domain: {$customDomain}");

            return null;
        }

        $decoded = json_decode(wp_remote_retrieve_body($response), true);

        if (empty($decoded) || ! isset($decoded['domains'])) {
            logError("Domain check returned invalid response for domain: {$customDomain}");

            return null;
        }

        if (empty($decoded['domains'])) {
            return null;
        }

        return $decoded['domains'][0]['domain'];
    }
}
