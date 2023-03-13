<?php

namespace Otis22\VetmanagerUrl\Url;

use Otis22\VetmanagerUrl\Url\Part\Domain;
use Otis22\VetmanagerUrl\Url\Part\Protocol;

class FromJson implements \Otis22\VetmanagerUrl\Url
{
    private string $jsonText;

    /**
     * @param string $jsonText
     */
    public function __construct(string $jsonText)
    {
        $this->jsonText = $jsonText;
    }

    /**
     * @inheritDoc
     */
    public function asString(): string
    {
        try {
            $json = \json_decode($this->jsonText);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid json response: {$this->jsonText}");
            }
            $this->validateResponse($json);
            return $this->protocol($json)->asString()
                . $this->hostName($json);
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    public static function fromDomainAndBillingApi(Domain $domain, BillingApi $billingApi): self
    {
        $billingUrl = $billingApi->asString() . "/host/" . $domain->asString();
        $jsonText = file_get_contents($billingUrl);
        if ($jsonText === false) {
            throw new \Exception('Can`t create FromJson object. Invalid server response.');
        }
        return new self($jsonText);
    }

    private function validateResponse(\stdClass $json): void
    {
        if (!filter_var($json->success, FILTER_VALIDATE_BOOLEAN)) {
            throw new \Exception("Unsuccessful request");
        }
        if (empty($json->url)) {
            throw new \Exception('Url is empty');
        }
        if (empty($json->protocol)) {
            throw new \Exception('Protocol is empty');
        }
    }

    private function protocol(\stdClass $json): Protocol
    {
        return new Protocol($json->protocol);
    }

    private function hostName(\stdClass $json): string
    {
        return $json->url;
    }
}
