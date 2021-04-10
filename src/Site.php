<?php

namespace JPI;

class Site {

    public const COLOUR = "#0078c9";

    private static $instance;

    protected $environment = null;

    public static function get() {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public static function asset(string $src, string $ver = null, string $root = PUBLIC_ROOT): string {
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

    public function renderFavicons(): void {
        ob_start();
        include_once(__DIR__ . "/../assets/favicons.php");
        $favicons = ob_get_contents();
        echo $favicons;
    }

    public function getEnvironment(): string {
        if ($this->environment === null) {
            $this->environment = getenv("APPLICATION_ENV") ?? "production";
        }

        return $this->environment;
    }

    public function isProduction(): bool {
        return $this->getEnvironment() === "production";
    }

}
