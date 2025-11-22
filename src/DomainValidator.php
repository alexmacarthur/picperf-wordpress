<?php

namespace PicPerf;

class DomainValidator
{
    private $domain;

    private $transientName;

    const REMOTE_HOST = 'https://go.picperf.io';

    const ONE_DAY_IN_SECONDS = 86400;

    public function __construct()
    {
        $this->domain = Config::getProxyDomain() ?? $this->getDefaultDomain();
        $this->transientName = "picperf_domain_validation_{$this->domain}";
    }

    public function isActive(): bool
    {
        $validationResult = $this->validate();

        return $validationResult->domainExists && $validationResult->isSubscribed;
    }

    public function domainExists(): bool
    {
        return $this->validate()->domainExists;
    }

    public function isSubscribed(): bool
    {
        return $this->validate()->isSubscribed;
    }

    private function validate(): object
    {
        $cachedValidationResult = get_transient($this->transientName);

        if ($cachedValidationResult !== false) {
            return json_decode($cachedValidationResult);
        }

        $rawValidationResult = $this->rawValidation();

        set_transient($this->transientName, $rawValidationResult, self::ONE_DAY_IN_SECONDS);

        return json_decode($rawValidationResult);
    }

    private function rawValidation()
    {
        $response = wp_remote_get(self::REMOTE_HOST.'/api/validate/domain/'.$this->domain);

        if (is_wp_error($response)) {
            logError("Failed to validate domain: {$this->domain}");

            return null;
        }

        return $response['body'];
    }

    private function getDefaultDomain()
    {
        $parsedUrl = parse_url(get_site_url());
        $withWww = $parsedUrl['host'];

        return preg_replace('/^www\./', '', $withWww);
    }
}
