<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserFilter extends QueryFilter
{
    public array $fields = [];

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->fields = [
            'user_id_or_email' => [
                'column' => 'id',
                'title' => 'E-mail/ID пользователя',
                'type' => 'search',
                'method' => 'user_id_or_email',
            ],
            'pp_id_or_email' => [
                'column' => 'pp_id',
                'title' => 'Домен/PP_ID',
                'type' => 'search',
                'method' => 'pp_id_or_email',
            ],
        ];

        if (auth()->user()->role !== 'manager') {
            unset($this->fields['pp_id_or_email']);
        }
    }

    public function user_id_or_email(string $field_name, $value): Builder
    {
        if (is_numeric($value)) {
            return $this->builder
                ->where($field_name, '=', $value);
        } else {
            return $this->builder
                ->where('email', 'like', '%' . $value . '%');
        }
    }

    public function pp_id_or_email(string $field_name, $value): Builder
    {
        if (is_numeric($value)) {
            return $this->builder
                ->where($field_name, '=', $value);
        } else {
            return $this->builder
                ->whereHas('pp', function (Builder $query) use ($value) {
                    $query->where('tech_domain', 'like', '%' . $value . '%');
                });
        }
    }
}
