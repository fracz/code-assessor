<?php

use codeassessor\database\migrations\Migration;
use codeassessor\model\CodeSample;
use codeassessor\model\CodeSampleAssessment;

class InitialStructure extends Migration {
    public function change() {
        $this->table(CodeSample::TABLE_NAME)
            ->addColumn(CodeSample::FILENAME, 'string')
            ->addTimestamps()
            ->addIndex(CodeSample::FILENAME, ['unique' => true])
            ->create();
        $this->table(CodeSampleAssessment::TABLE_NAME)
            ->addColumn(CodeSampleAssessment::SAMPLE_ID, 'integer')
            ->addColumn(CodeSampleAssessment::SCORE, 'integer')
            ->addTimestamps()
            ->addForeignKey(CodeSampleAssessment::SAMPLE_ID, CodeSample::TABLE_NAME)
            ->create();
    }
}
