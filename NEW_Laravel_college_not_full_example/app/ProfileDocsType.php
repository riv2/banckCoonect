<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileDocsType extends Model
{
	use SoftDeletes;

    protected $table = 'profile_docs_type';
}
