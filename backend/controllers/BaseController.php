<?php

namespace codeassessor\controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use codeassessor\model\HasApp;
use Throwable;

abstract class BaseController {
    use HasApp;

    protected function response($content = null): Response {
        return $this->getApp()->response->withJson($content);
    }

    protected function request(): Request {
        return $this->getApp()->request;
    }

    protected function beforeAction() {
    }

    public function __call($methodName, $args) {
        if (count($args) == 3) { // request, response, args
            $action = $methodName . 'Action';
            try {
                $this->beforeAction();
                $response = call_user_func_array([&$this, $action], [$args[2]]);
            } catch (Throwable $e) {
                error_log($e->getMessage());
                throw new \RuntimeException($e);
            }
            return $response;
        }
        throw new \BadMethodCallException("There is no method $methodName.");
    }
}
