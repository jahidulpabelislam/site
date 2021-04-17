<?php

namespace JPI;

trait URLUtilities {

    protected static function addParamToURL(string $url, string $key, string $value): string {
        $query = parse_url($url, PHP_URL_QUERY);
        if (empty($query)) {
            return "$url?$key=$value";
        }

        return "$url&$key=$value";
    }

    public static function removeSlashes(string $value): string {
        return trim($value, "/");
    }

    public static function removeTrailingSlash(string $value): string {
        return rtrim($value, "/");
    }

    public static function removeLeadingSlash(string $value): string {
        return ltrim($value, "/");
    }

    public static function addLeadingSlash(string $url): string {
        $url = static::removeLeadingSlash($url);
        return "/$url";
    }

    public static function addTrailingSlash(string $value): string {
        $value = static::removeTrailingSlash($value);

        // If the last bit includes a full stop, assume its a file...
        // so don't add trailing slash
        $withoutProtocol = str_replace(["https://", "http://"], "", $value);
        $splitPaths = explode("/", $withoutProtocol);
        $count = count($splitPaths);
        if ($count > 1 && !is_dir($value)) {
            $lastPath = $splitPaths[$count - 1] ?? null;
            if ($lastPath && strpos($lastPath, ".")) {
                return $value;
            }
        }

        return "$value/";
    }

    public static function addSlashes(string $url): string {
        return static::addTrailingSlash(static::addLeadingSlash($url));
    }

    public static function makeURL(string $domain, string $relativeURL): string {
        // Just strip these off
        $indexes = [
            "index.php",
            "index.html",
        ];
        foreach ($indexes as $index) {
            $indexLength = strlen($index);
            if (substr($relativeURL, -$indexLength) === $index) {
                $relativeURL = substr($relativeURL, 0, -$indexLength);
                break;
            }
        }

        $domain = static::removeTrailingSlash($domain);
        $relativeURL = static::addSlashes($relativeURL);
        return $domain . $relativeURL;
    }

}
