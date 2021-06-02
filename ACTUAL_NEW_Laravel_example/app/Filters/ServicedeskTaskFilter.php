<?php

namespace App\Filters;

use App\Models\ServicedeskTask;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ServicedeskTaskFilter extends QueryFilter
{
    public array $fields = [];

    public function __construct(Request $request)
    {
        $this->fields = [
            'id' => [
                'column' => 'id',
                'title' => __('servicedeskTaskFilter.id'),
                'type' => 'number',
                'method' => 'equals',
            ],
            'type' => [
                'column' => 'type',
                'title' => __('servicedeskTaskFilter.type'),
                'type' => 'select',
                'method' => 'equals',
                'options' => [],
            ],
            'status' => [
                'column' => 'status',
                'title' => __('servicedeskTaskFilter.status'),
                'type' => 'select',
                'method' => 'incomma',
                'default' => 'new,pending',
                'options' => [],
            ],
            'created_at' => [
                'column' => 'created_at',
                'title' => __('servicedeskTaskFilter.created_at'),
                'type' => 'daterange',
                'method' => 'daterange',
            ],
            'creator_user_id_or_email' => [
                'column' => 'creator_user_id',
                'title' => __('servicedeskTaskFilter.creator_user_id_or_email'),
                'type' => 'text',
                'method' => 'user_id_or_email',
            ],
            'doer_user_id' => [
                'column' => 'doer_user_id',
                'title' => __('servicedeskTaskFilter.doer_user_id'),
                'type' => 'select',
                'method' => 'equals',
                'options' => [],
            ],
            'not_closed' => [
                'column' => 'not_closed',
                'title' => __('servicedeskTaskFilter.not_closed'),
                'type' => 'select',
                'method' => 'boolean',
                'options' => [
                    'false' => __('servicedeskTaskFilter.no'),
                    'true' => __('servicedeskTaskFilter.yes'),
                ],
            ],
            'deadline_at' => [
                'column' => 'deadline_at',
                'title' => __('servicedeskTaskFilter.deadline_at'),
                'type' => 'select',
                'method' => 'deadline_at',
                'options' => [
                    'false' => __('servicedeskTaskFilter.no'),
                    'true' => __('servicedeskTaskFilter.yes'),
                ],
            ],
            'subject' => [
                'column' => 'subject',
                'title' => __('servicedeskTaskFilter.subject'),
                'type' => 'text',
                'method' => 'like',
            ],
        ];
        $this->fields['type']['options'] = ServicedeskTask::getTaskTypesList();
        $this->fields['status']['options'] = ServicedeskTask::getTaskStatusList();
        $this->fields['status']['options']['new,pending,closed'] = __('servicedeskTaskFilter.any');
        $this->fields['status']['options']['new,pending'] = __('servicedeskTaskFilter.except_closed');
        $this->fields['doer_user_id']['options'] = User::whereIn('user_id', ServicedeskTask::$doers)->get()->pluck('email', 'user_id');
        parent::__construct($request);
    }

    public function user_id_or_email(string $field_name, $value): Builder
    {
        if (is_numeric($value)) {
            return $this->builder
                ->where($field_name, '=', $value);
        } else {
            return $this->builder
                ->whereHas('creator', function (Builder $query) use ($value) {
                    $query->where('email', 'like', '%' . $value . '%');
                });
        }
    }

    public function deadline_at(string $field_name, $value): Builder
    {
        return $this->builder
            ->when($value === 'true', function (Builder $query) use ($field_name) {
                $query->where($field_name, '<=', now()->toDateTimeString());
            })
            ->when($value === 'false', function (Builder $query) use ($field_name) {
                $query->where($field_name, '>', now()->toDateTimeString());
            });
    }
}
