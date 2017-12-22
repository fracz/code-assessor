<?php

namespace codeassessor\model;

use Carbon\Carbon;
use DateTimeInterface;
use enform\models\encoders\ColumnEncoders;
use Illuminate\Database\Eloquent\Builder;
use Ramsey\Uuid\Uuid;

/**
 * @property string $id
 * @property Carbon $createdAt
 * @property Carbon $modifiedAt
 * @method static Builder where(array $condition)
 * @method static Builder whereIn(string $column, array $condition)
 * @method static Builder whereBetween(string $column, array $between)
 * @method static Builder select(array $columns)
 * @method static Builder groupBy(string $column)
 * @method static Builder orderBy(string $column)
 * @method static string raw(string $column)
 */
abstract class Model extends \Illuminate\Database\Eloquent\Model {
    const ID = 'id';

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('c'); // C is the ATOM format (with timezone offset)
    }
}
