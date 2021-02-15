<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ManualResult
 * @package App
 * @property int teacher_id
 * @property int discipline_id
 * @property int study_group_id
 * @property int student_id
 * @property int student_discipline_id
 * @property int sro_old
 * @property int sro_new
 * @property int exam_old
 * @property int exam_new
 * @property Carbon created_at
 */
class ManualResult extends Model
{
    protected $table = 'manual_results';
}
