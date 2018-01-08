<?php

use codeassessor\database\migrations\Migration;
use codeassessor\model\CodeSampleAssessment;
use codeassessor\model\Respondent;

class AddRespondents extends Migration {
    public function change() {
        $this->table(Respondent::TABLE_NAME)
            ->addColumn(Respondent::EXPERIENCE, 'integer')
            ->addTimestamps()
            ->create();
        $this->table(CodeSampleAssessment::TABLE_NAME)
            ->addColumn(CodeSampleAssessment::RESPONDENT_ID, 'integer', ['null' => true])
            ->addForeignKey(CodeSampleAssessment::RESPONDENT_ID, Respondent::TABLE_NAME, Respondent::ID, ['delete' => 'SET NULL'])
            ->update();
    }
}
