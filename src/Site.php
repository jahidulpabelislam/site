<?php

namespace JPI;

use DateTime;

class Site implements Me {

    public const COLOUR = "#0078c9";

    private static $instance;

    protected $environment = null;

    protected $dateStarted;
    protected $yearStarted;

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

    public function renderFavicons() {
        ob_start();
        include_once(__DIR__ . "/../assets/favicons.php");
        $favicons = ob_get_contents();
        echo $favicons;
    }

    public function getEnvironment(): string {
        if ($this->environment == null) {
            $this->environment = getenv("APPLICATION_ENV") ?? "production";
        }

        return $this->environment;
    }

    public function isProduction(): bool {
        return $this->getEnvironment() === "production";
    }

    public function getDateStarted(): DateTime {
        if (!$this->dateStarted) {
            $this->dateStarted = new DateTime(self::START_DATE);
        }

        return $this->dateStarted;
    }

    public function getYearStarted(): string {
        if (!$this->yearStarted) {
            $dateStartedDate = $this->getDateStarted();
            $this->yearStarted = $dateStartedDate->format("Y");
        }

        return $this->yearStarted;
    }
}
