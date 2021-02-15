<?php

namespace App\Models\StudentRequest;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentRequestType extends Model
{
	use SoftDeletes;

    protected $table = 'student_request_types';

    const DOCS_TYPE_PREFIX = 'student_request_';
    const DOCS_TEMPLATE = 'requests_templates';

    
}


