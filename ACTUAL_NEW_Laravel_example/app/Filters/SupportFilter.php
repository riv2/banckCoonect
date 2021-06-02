<?php

namespace App\Filters;

use App\Lists\SupportCasesList;
use Illuminate\Http\Request;

class SupportFilter extends QueryFilter
{
    public array $fields = [
        'user_id_or_email' => [
            'column' => 'id',
            'title' => 'E-mail/ID партнера',
            'type' => 'text',
            'method' => 'user_id_or_email',
        ],
        'case_type' => [
            'column' => 'case_type',
            'title' => 'Тип вопроса',
            'type' => 'select',
            'method' => 'equals',
        ],
        'datetime' => [
            'column' => 'created_at',
            'title' => 'Дата',
            'type' => 'date',
            'method' => 'equals',
        ],
    ];

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->fields["case_type"]["options"] = SupportCasesList::getList();
    }
}
