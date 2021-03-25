<?php

namespace JPI;

class Site
{
    public const NAME = "Jahidul Pabel Islam";

    public const ROLE = "Full Stack Developer";

    public const COLOUR = "#0078c9";

    public static function asset(string $src, string $ver = null, string $root = SITE_ROOT): string {
        if ($ver === null) {
            $ver = "1"; // Default

            $filepath = $root . $src;
            if (file_exists($filepath)) {
                $ver = date("mdYHi", filemtime($filepath));
            }
        }

        if (empty($ver)) {
            return $src;
        }

        $query = parse_url($src, PHP_URL_QUERY);
        if (empty($query)) {
            return "{$src}?v={$ver}";
        }

        return "{$src}&v={$ver}";
    }

    public function renderFavicons() {
        include_once(__DIR__ . "/../assets/favicons.php");
    }
}
