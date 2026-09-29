<?php

declare(strict_types=1);

namespace Otis22\VetmanagerUrl\Url;

final class StreamTransport
{
    public static ?string $response = null;

    public static bool $fail = false;

    /** @var string[] */
    public static array $warnings = [];

    /** @var string[] */
    public static array $requests = [];

    public static function reset(): void
    {
        self::$response = null;
        self::$fail = false;
        self::$warnings = [];
        self::$requests = [];
    }
}

/** @return string|false */
function file_get_contents(string $filename)
{
    if (StreamTransport::$fail) {
        StreamTransport::$requests[] = $filename;
        foreach (StreamTransport::$warnings as $warning) {
            trigger_error($warning, E_USER_WARNING);
        }
        return false;
    }
    if (StreamTransport::$response === null) {
        return \file_get_contents($filename);
    }
    StreamTransport::$requests[] = $filename;
    return StreamTransport::$response;
}
