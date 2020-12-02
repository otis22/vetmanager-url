<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl\Url;

use GuzzleHttp\ClientInterface;
use Otis22\VetmanagerUrl\Url;
use Otis22\VetmanagerUrl\Url\Part\Protocol;
use Otis22\VetmanagerUrl\Url\Part\Domain;

final class FromBillingApiGateway implements Url
{
    /**
     * @var BillingApi
     */
    private $billingApiUrl;
    /**
     * @var Domain
     */
    private $domain;
    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var string
     */
    private $url;

    /**
     * UrlFromBillingApiGateway constructor.
     *
     * @param BillingApi   $billingApiUrl
     * @param Domain          $domain
     * @param ClientInterface $client
     */
    public function __construct(BillingApi $billingApiUrl, Domain $domain, ClientInterface $client)
    {
        $this->billingApiUrl = $billingApiUrl;
        $this->domain = $domain;
        $this->client = $client;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function asString(): string
    {
        if (empty($this->url)) {
            $this->url = $this->urlFromApi();
        }
        return $this->url;
    }

    private function urlFromApi(): string
    {
        try {
            $response = $this->client->request("GET", $this->hostGatewayUrl());
            $responseText = strval($response->getBody());
            $json = \json_decode($responseText);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid json response: {$responseText}");
            }
            $this->validateResponse($json);
            return $this->protocol($json)->asString()
                . $this->hostName($json);
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function hostGatewayUrl(): string
    {
        return $this->billingApiUrl->asString()
            . "/host/" . $this->domain->asString();
    }

    /**
     * @param  \stdClass $json
     * @throws \Exception
     */
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
