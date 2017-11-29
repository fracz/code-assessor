<?php

namespace codeassessor\controllers;

use codeassessor\app\Application;

class CodeController extends BaseController {
    public function getRandomAction() {
        $id = rand(1, 4);
        return $this->response([
            'id' => $id,
            'diff' => file_get_contents(Application::VAR_PATH . "/diffs/0000$id.txt"),
        ]);
    }
}
