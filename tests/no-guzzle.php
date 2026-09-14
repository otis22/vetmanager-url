<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Url/LegacyFromJson.php';

if (interface_exists(\GuzzleHttp\ClientInterface::class)) {
    throw new RuntimeException('This smoke test requires an installation without Guzzle.');
}

$directory = sys_get_temp_dir() . '/vetmanager-url-no-guzzle-' . bin2hex(random_bytes(8));
mkdir($directory . '/host', 0700, true);
file_put_contents(
    $directory . '/host/clinic',
    '{"success":true,"protocol":"https","url":"clinic.example"}'
);

try {
    $gateway = 'file://' . $directory . '/';
    $urls = [
        \Otis22\VetmanagerUrl\create_url_from_billing_api_gateway('clinic', $gateway),
        \Otis22\VetmanagerUrl\create_url_from_billing_api_gateway('clinic', $gateway, null),
        \Otis22\VetmanagerUrl\Url\FromJson::fromDomainAndBillingApi(
            new \Otis22\VetmanagerUrl\Url\Part\Domain('clinic'),
            new \Otis22\VetmanagerUrl\Url\BillingApi($gateway)
        ),
    ];
    $urls[] = \Otis22\VetmanagerUrl\Url\LegacyFromJson::fromDomainAndBillingApi(
        new \Otis22\VetmanagerUrl\Url\Part\Domain('clinic'),
        new \Otis22\VetmanagerUrl\Url\BillingApi($gateway)
    );
    foreach ($urls as $url) {
        if ($url->asString() !== 'https://clinic.example') {
            throw new RuntimeException('Legacy URL discovery returned an unexpected result.');
        }
    }
    echo "Legacy API works without Guzzle\n";
} finally {
    unlink($directory . '/host/clinic');
    rmdir($directory . '/host');
    rmdir($directory);
}
