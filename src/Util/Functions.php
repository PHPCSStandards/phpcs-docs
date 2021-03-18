<?php
declare(strict_types=1);

namespace App\Util;

class Functions
{

    /**
     * Normalizes all slashes in a file path to forward slashes.
     *
     * @param string $path File path.
     *
     * @return string The file path with normalized slashes.
     */
    public static function normalizeSlashes(string $path)
    {
        return str_replace('\\', '/', $path);
    }
}
