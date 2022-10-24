<?php

namespace JPI;

class Site implements BrandInterface {

    use URLUtilities;

    private static $instance;

    protected $environment = null;
    protected $useDevAssets = null;
    protected $devAssetsKey = "dev_assets";
    protected $domain = null;
    protected $currentURL = null;
    protected $colours = null;

    public static function get() {
        if (is_null(static::$instance)) {
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

        return static::addParamToURL($src, "v", $ver);
    }

    public function renderFavicons(): void {
        ob_start();
        include_once __DIR__ . "/../partials/favicons.php";
        $favicons = ob_get_contents();
        ob_end_clean();
        echo $favicons;
    }

    public function getEnvironment(): string {
        if (is_null($this->environment)) {
            $this->environment = getenv("APPLICATION_ENV") ?? "production";
        }

        return $this->environment;
    }

    public function isProduction(): bool {
        return $this->getEnvironment() === "production";
    }

    public function isDevelopment(): bool {
        return $this->getEnvironment() === "development";
    }

    /**
     * @return bool Whether or not the param was set by user on page view
     */
    public function useDevAssets(): bool {
        if (is_null($this->useDevAssets)) {
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
        if (is_null($this->domain)) {
            $protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
            $this->domain = "$protocol://" . $_SERVER["SERVER_NAME"];
        }

        return $this->domain;
    }

    public function makeURL(string $relativeURL, bool $addDevAssetsParam = true, bool $isFull = false): string {
        $domain = $isFull ? $this->getDomain() : "";

        $url = static::formatURL($domain, $relativeURL);

        if ($addDevAssetsParam && $this->useDevAssets()) {
            $url = static::addParamToURL($url, $this->devAssetsKey);
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
        if (is_null($this->currentURL)) {
            $this->currentURL = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        }

        return $this->makeURL($this->currentURL, false, $isFull);
    }

    public function getColours(): array {
        if (is_null($this->colours)) {
            $colours = file_get_contents(__DIR__ . "/../config/colours.json");
            $this->colours = json_decode($colours, true);
        }

        return $this->colours;
    }

    public function getBrandColour(): string {
        return $this->getColours()['brand'];
    }
}
