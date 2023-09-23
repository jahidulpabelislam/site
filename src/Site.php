<?php

declare(strict_types=1);

namespace JPI;

use JPI\Utils\Singleton;
use JPI\Utils\URL;

class Site implements BrandInterface {

    use Singleton;

    protected ?string $environment = null;

    protected ?bool $useDevAssets = null;
    protected string $devAssetsKey = "dev_assets";

    protected ?URL $currentURL = null;

    protected ?array $colours = null;

    public static function asset(string $src, ?string $ver = null, string $root = PUBLIC_ROOT): URL {
        if ($ver === null) {
            $filepath = URL::removeTrailingSlash($root) . URL::addLeadingSlash($src);
            if (file_exists($filepath)) {
                $ver = date("mdYHi", filemtime($filepath));
            } else {
                $ver = "1";
            }
        }

        $src = new URL($src);

        if (!$ver) {
            return $src;
        }

        $src->setQueryParam("v", $ver);
        return $src;
    }

    public static function formatURL(string $url): string {
        $indexes = [
            "index.php",
            "index.html",
        ];
        foreach ($indexes as $index) {
            $indexLength = strlen($index);
            if (substr($url, -$indexLength) === $index) {
                return substr($url, 0, -$indexLength);
            }
        }

        return $url;
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
     * @return bool Whether the param was set by user on page view
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
     * @return string Return the local domain
     */
    public function getDomain(): string {
        return $_SERVER["SERVER_NAME"];
    }

    public function makeURL(string $path, bool $addDevAssetsParam = true, bool $isFull = false): URL {
        $url = new URL(static::formatURL($path));

        if ($isFull) {
            $url->setScheme((!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http");
            $url->setHost($this->getDomain());
        }

        if ($addDevAssetsParam && $this->useDevAssets()) {
            $url->setQueryParam($this->devAssetsKey, "");
        }

        return $url;
    }

    /**
     * Generate and return the current requested page/URL.
     *
     * @param $isFull bool
     * @return URL
     */
    public function getCurrentURL(bool $isFull = false): URL {
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
