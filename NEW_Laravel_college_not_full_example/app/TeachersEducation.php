<?php
/**
 * User: dadicc
 * Date: 30.07.19
 * Time: 7:22
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeachersEducation extends Model
{

    const TYPE_BACHELOR = 'bachelor';
    const TYPE_SPECIALIST = 'specialist';
    const TYPE_MAGISTRACY = 'magistracy';
    const TYPE_CANDIDATE_SCIENCES = 'scientific_degree';
    const TYPE_DOCTORATE = 'doctorate';
    const TYPE_ACADEMIC_RANK = 'academic_status';
    const TYPE_LANGUAGE_ABILITY = 'language_ability';
    const TYPE_ADDITIONAL_SKILL = 'additional_skill';

    const ACADEMIC_TITLE_NO_TITLE = 'no_title';
    const ACADEMIC_TITLE_ASSOCIATE_PROFESSOR = 'associate_professor';
    const ACADEMIC_TITLE_PROFESSOR = 'professor';

    protected $table = 'teachers_education';

}