<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class PayDocumentStudentDiscipline
 * @package App
 * @property int pay_document_id
 * @property int student_discipline_id
 */
class PayDocumentStudentDiscipline extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'pay_documents_student_disciplines';

    public static function add(int $payDocumentId, int $studentDisciplineId) : bool
    {
        $payDocumentStudentDisc = new self;
        $payDocumentStudentDisc->pay_document_id = $payDocumentId;
        $payDocumentStudentDisc->student_discipline_id = $studentDisciplineId;
        return $payDocumentStudentDisc->save();
    }
}
