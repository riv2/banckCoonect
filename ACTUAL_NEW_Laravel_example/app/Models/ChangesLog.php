<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ChangesLog
 *
 * @property int $log_id
 * @property int $user_id
 * @property string $record_type
 * @property string $changes_type
 * @property array $raw_data_old
 * @property array $raw_data_new
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ChangesLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangesLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangesLog query()
 * @mixin \Eloquent
 */
class ChangesLog extends Model
{
    protected $table = 'changes_log';
    protected $primaryKey = 'log_id';

    protected $casts = [
        'user_id' => 'int',
        'raw_data_old' => 'json',
        'raw_data_new' => 'json'
    ];

    protected $fillable = [
        'user_id',
        'record_type',
        'changes_type',
        'raw_data_old',
        'raw_data_new'
    ];
}
