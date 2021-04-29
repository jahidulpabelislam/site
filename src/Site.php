<?php

namespace JPI;

class Site implements Brand {

    use URLUtilities;

    private static $instance;

    protected $environment = null;
    protected $useDevAssets = null;
    protected $devAssetsKey = "dev_assets";
    protected $domain = null;
    protected $currentURL = null;
    protected $colours = null;

    public static function get() {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function getColours(): array {
        $colours = file_get_contents(__DIR__ . "/../assets/colours.json");
        return json_decode($colours, true);
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

        return static::addParamToURL($src, "v", $ver);
    }

    public function renderFavicons(): void {
        ob_start();
        include_once __DIR__ . "/../assets/favicons.php";
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

    /**
     * @return bool Whether or not the param was set by user on page view
     */
    public function useDevAssets(): bool {
        if ($this->useDevAssets === null) {
            $this->useDevAssets = isset($_GET[$this->devAssetsKey])
                && !($_GET[$this->devAssetsKey] === "false" || $_GET[$this->devAssetsKey] === "0")
            ;
        }

        return $this->useDevAssets;
    }

    /**
     * @return string Generate and return the local domain
     */
    public function getDomain(): string {
        if ($this->domain === null) {
            $protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
            $this->domain = "$protocol://" . $_SERVER["SERVER_NAME"];
        }

        return $this->domain;
    }

    public function makeURL(string $relativeURL, bool $addDevAssetsParam = true, bool $isFull = false) : string {
        $domain = $isFull ? $this->getDomain() : "";

        $url = static::formatURL($domain, $relativeURL);

        if ($addDevAssetsParam && $this->useDevAssets()) {
            $url = static::addParamToURL($url, $this->devAssetsKey, "");
        }

        return $url;
    }

    /**
     * Generate and return the current requested page/URL.
     *
     * @param $isFull bool
     * @return string
     */
    public function getCurrentURL(bool $isFull = false): string {
        if ($this->currentURL === null) {
            $this->currentURL = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        }

        return $this->makeURL($this->currentURL, false, $isFull);
    }

}
