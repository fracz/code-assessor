<?php

namespace codeassessor\controllers;

use codeassessor\model\Respondent;

class RespondentsController extends BaseController {
    public function createNewAction() {
        $exp = $this->request()->getParsedBody()['experience'] ?? -1;
        $respondent = new Respondent([Respondent::EXPERIENCE => $exp]);
        $respondent->save();
        return $this->response($respondent);
    }
}
