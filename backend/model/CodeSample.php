<?php

namespace codeassessor\model;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $filename
 */
class CodeSample extends Model {
    const TABLE_NAME = 'code_samples';
    const FILENAME = 'filename';
    protected $fillable = [self::FILENAME];

    public function assessments(): HasMany {
        return $this->hasMany(CodeSampleAssessment::class, CodeSampleAssessment::SAMPLE_ID);
    }
}
