<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class AppealQuizAnswer
 * @package App
 *
 * @property int id
 * @property int appeal_id
 * @property int result_id
 * @property string snapshot
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class AppealQuizAnswer extends Model
{
    protected $casts = [
        'snapshot' => 'array',
    ];

}