<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl;

use Otis22\VetmanagerUrl\Url\BillingApi;
use Otis22\VetmanagerUrl\Url\LegacyFromJson;
use Otis22\VetmanagerUrl\Url\Part\Domain;
use Otis22\VetmanagerUrl\Url\StreamTransport;
use PHPUnit\Framework\TestCase;

final class LegacyFactoryTest extends TestCase
{
    public function testExistingOverrideCanLoadAndDelegateToParent(): void
    {
        StreamTransport::$response = '{"success":true,"protocol":"https","url":"clinic.example"}';
        StreamTransport::$requests = [];
        try {
            $result = LegacyFromJson::fromDomainAndBillingApi(
                new Domain('Clinic'),
                new BillingApi('https://billing.example/')
            );
            self::assertInstanceOf(LegacyFromJson::class, $result);
            self::assertSame('https://clinic.example', $result->asString());
            self::assertSame(['https://billing.example//host/clinic'], StreamTransport::$requests);
        } finally {
            StreamTransport::$response = null;
            StreamTransport::$requests = [];
        }
    }
}
