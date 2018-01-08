<?php

namespace codeassessor\model;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $experience
 */
class Respondent extends Model {
    const TABLE_NAME = 'respondents';
    const EXPERIENCE = 'experience';
    protected $fillable = [self::EXPERIENCE];

    public function codeSamples(): HasMany {
        return $this->hasMany(CodeSample::class, CodeSample::SAMPLE_ID);
    }
}
