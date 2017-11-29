<?php

namespace codeassessor\controllers;

use Assert\Assertion;
use suplascripts\models\HasSuplaApi;
use suplascripts\models\scene\SceneExecutor;
use suplascripts\models\supla\SuplaApiException;

class CodeController extends BaseController {

    public function getRandomAction($params) {
        return $this->response($this->getApi()->getChannelWithState($params['id']));
    }
}
