<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PayDocumentLecture extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'pay_documents_lectures';
}
