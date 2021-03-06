<?php

namespace codeassessor\model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $sampleId
 * @property int $score
 */
class CodeSampleAssessment extends Model {
    const TABLE_NAME = 'code_sample_assessments';
    const SAMPLE_ID = 'sample_id';
    const RESPONDENT_ID = 'respondentId';
    const TIME = 'time';
    const SCORE = 'score';
    protected $fillable = [self::SCORE, self::RESPONDENT_ID, self::TIME];

    public function type(): BelongsTo {
        return $this->belongsTo(CodeSample::class);
    }
}
