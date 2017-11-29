<?php

namespace codeassessor\app;

use Slim\App;

/**
 * @property-read \Slim\Http\Request $request
 * @property-read \Slim\Http\Response $response
 * @property-read \Slim\Collection $settings
 */
class Application extends App {

    const VAR_PATH = __DIR__ . '/../../var';
    const CONFIG_PATH = self::VAR_PATH . '/config/config.json';

    private static $instance;

    public function __construct(array $config = null) {
        if (self::$instance) {
            throw new \BadMethodCallException('Application can be instantiated only once. Use getInstance() instead.');
        }
        self::$instance = $this;
        if (!$config) {
            $config = require __DIR__ . '/../settings.php';
        }
        parent::__construct(['settings' => $config]);
        $this->configureServices();
    }

    private function configureServices() {
        $this->configureDb();
    }

    private function configureDb() {

    }

    public static function getInstance(): Application {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __get($property) {
        return $this->getContainer()->get($property);
    }

    public function getSetting($name, $default = null) {
        return $this->settings->get($name, $default);
    }

    public static function version(): string {
        $version = @file_get_contents(self::VAR_PATH . '/system/version');
        return $version ?: 'UNKNOWN';
    }
}
