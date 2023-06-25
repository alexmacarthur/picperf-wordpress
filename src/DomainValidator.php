<?php

namespace PicPerf;

class DomainValidator
{
    private $url;

    private $transientName;

    const REMOTE_HOST = 'https://picperf-optimization.fly.dev/';

    const ONE_DAY_IN_SECONDS = 86400;

    public function __construct($url)
    {
        $this->url = $url;
        $this->transientName = "picperf_domain_validation_{$this->getDomain()}";
    }

    public function validate()
    {
        $cachedValidationResult = get_transient($this->transientName);

        if ($cachedValidationResult !== false) {
            return $this->isActiveFromJson($cachedValidationResult);
        }

        $rawValidationResult = $this->rawValidation();

        if (empty($rawValidationResult)) {
            return true;
        }

        set_transient($this->transientName, $rawValidationResult, self::ONE_DAY_IN_SECONDS);

        return $this->isActiveFromJson($rawValidationResult);
    }

    private function rawValidation()
    {
        $response = wp_remote_get(self::REMOTE_HOST.$this->getDomain());

        if (is_wp_error($response)) {
            logError("Failed to validate domain: {$this->getDomain()}");

            return null;
        }

        return $response['body'];
    }

    private function isActiveFromJson($json): bool
    {
        $result = json_decode($json);

        if (empty($result)) {
            logError("Failed to parse JSON: $json");

            return true;
        }

        return $result->isActive;
    }

    private function getDomain()
    {
        $parsedUrl = parse_url($this->url);

        return $parsedUrl['host'];
    }
}
