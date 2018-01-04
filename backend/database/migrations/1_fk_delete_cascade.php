<?php

use codeassessor\database\migrations\Migration;
use codeassessor\model\CodeSample;
use codeassessor\model\CodeSampleAssessment;

class FkDeleteCascade extends Migration {
    public function change() {
        $this->table(CodeSampleAssessment::TABLE_NAME)
            ->dropForeignKey(CodeSampleAssessment::SAMPLE_ID)
            ->update();
        $this->table(CodeSampleAssessment::TABLE_NAME)
            ->addForeignKey(CodeSampleAssessment::SAMPLE_ID, CodeSample::TABLE_NAME, CodeSample::ID, ['delete' => 'CASCADE'])
            ->update();
    }
}
