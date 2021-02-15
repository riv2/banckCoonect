<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProjectSection
 *
 * @property int id
 * @property string url
 * @property string name_ru
 * @property string name_kz
 * @property string name_en
 * @property string project
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class ProjectSection extends Model
{
    const PROJECT_ADMIN = 'admin';
    const PROJECT_STUDENT = 'student';
    const PROJECT_TEACHER = 'teacher';

    protected $table = 'project_section';
}
