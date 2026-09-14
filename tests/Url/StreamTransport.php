<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl\Url;

final class StreamTransport
{
    public static ?string $response = null;

    /** @var string[] */
    public static array $requests = [];
}

/** @return string|false */
function file_get_contents(string $filename)
{
    if (StreamTransport::$response === null) {
        return \file_get_contents($filename);
    }
    StreamTransport::$requests[] = $filename;
    return StreamTransport::$response;
}
