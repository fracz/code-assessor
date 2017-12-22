<?php

namespace codeassessor\app;

use codeassessor\database\EloquentExceptionHandler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\App;

/**
 * @property-read \Slim\Http\Request $request
 * @property-read \Slim\Http\Response $response
 * @property-read \Slim\Collection $settings
 * @property-read Capsule $db
 */
class Application extends App {

    const VAR_PATH = __DIR__ . '/../../var';
    const CONFIG_PATH = self::VAR_PATH . '/config/config.json';

    private static $instance;

    public function __construct() {
        if (self::$instance) {
            throw new \BadMethodCallException('Application can be instantiated only once. Use getInstance() instead.');
        }
        self::$instance = $this;
        $config = require __DIR__ . '/../settings.php';
        parent::__construct(['settings' => $config]);
        $this->configureServices();
        $this->enableCors();
    }

    private function configureServices() {
        $this->configureDb();
    }

    private function configureDb() {
        $this->getContainer()['db'] = function ($container) {
            $capsule = new Capsule;
            $capsule->addConnection($container['settings']['db']);
            $capsule->getContainer()->bind(ExceptionHandler::class, EloquentExceptionHandler::class);
//            $capsule->setEventDispatcher(new Dispatcher(new Container));
            $capsule->bootEloquent();
            $capsule->setAsGlobal();
            return $capsule;
        };
        /* get db from container to properly initialize before first use of Model elements*/
        $this->getContainer()->get('db');
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

    private function enableCors() {
        $this->options('/{routes:.+}', function ($request, $response, $args) {
            return $response;
        });

        $this->add(function ($req, $res, $next) {
            $response = $next($req, $res);
            return $response
                ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8080')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        });
    }
}
