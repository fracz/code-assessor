<?php

use codeassessor\database\migrations\Migration;
use codeassessor\model\CodeSampleAssessment;

class CodeSampleAssessmentTime extends Migration {
    public function change() {
        $this->table(CodeSampleAssessment::TABLE_NAME)
            ->addColumn(CodeSampleAssessment::TIME, 'integer', ['null' => true])
            ->update();
    }
}
