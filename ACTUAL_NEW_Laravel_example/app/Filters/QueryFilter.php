<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\View;
use LogicException;

/**
 * Класс для фильтрации через $request
 *
 * @author Anton Vanin <vaninanton@gmail.com>
 * @version 1.0
 */
abstract class QueryFilter
{
    protected Request $request;
    protected Builder $builder;

    /** @var array Обязательные поля в настройке фильтра */
    protected $required_keys = ['column', 'title', 'type', 'method'];

    /** @var array Настройка фильтра */
    public array $fields;

    public function __construct(Request $request)
    {
        if (!isset($this->fields)) {
            throw new LogicException(get_class($this) . ' must have a $fields');
        }

        $this->checkFields();

        $this->request = $request;
        View::share('filter_fields', $this->fields);
    }

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        // Проходим по всем параметрам из фильтра
        foreach ($this->fields as $field_name => $field_params) {
            $method = $field_params['method'];
            $column = $field_params['column'];
            $default = $field_params['default'] ?? null;

            if ($this->request->filled($field_name)) {
                /** @var mixed введенное пользователем значение */
                $value = $this->request->get($field_name, $default);

                // Если значение - массив не задано, пропускаем
                if (is_array($value)) {
                    $value = array_filter($value);
                    if (empty($value)) {
                        continue;
                    }
                }

                call_user_func_array([$this, $method], [$column, $value]);
            }
        }

        return $this->builder;
    }

    /**
     * Проверяет корректность настройки фильтра
     *
     * @throws LogicException
     * @return void
     */
    private function checkFields()
    {
        foreach ($this->fields as $field_name => $field_params) {
            // Проверяем наличие всех обязательных ключей
            foreach ($this->required_keys as $required_key) {
                if (!array_key_exists($required_key, $field_params)) {
                    throw new LogicException('В фильтре ' . static::class . '->fields["'.$field_name.'"] отсутствует обязательный ключ "' . $required_key . '"');
                }
            }

            if (!method_exists($this, $field_params['method'])) {
                throw new LogicException('Отсутствует ' . $field_params['method'] . ' в классе ' . __CLASS__);
            }
        }
    }

    public function equals(string $field_name, $value): Builder
    {
        return $this->builder
            ->when($value, function (Builder $query) use ($field_name, $value) {
                return $query->where($field_name, '=', $value);
            });
    }

    public function boolean(string $field_name, $value): Builder
    {
        return $this->builder
            ->when($value === 'null', function (Builder $query) use ($field_name) {
                $query->whereNull($field_name);
            })
            ->when($value === 'true', function (Builder $query) use ($field_name) {
                $query->where($field_name, '=', 1);
            })
            ->when($value === 'false', function (Builder $query) use ($field_name) {
                $query->where($field_name, '=', 0);
            });
    }

    public function incomma(string $field_name, $value): Builder
    {
        $values = explode(',', $value);
        return $this->builder
            ->whereIn($field_name, $values);
    }

    public function jsonContains(string $field_name, $value): Builder
    {
        return $this->builder
            ->whereJsonContains($field_name, $value);
    }

    public function lt(string $field_name, $value): Builder
    {
        return $this->builder
            ->where($field_name, '<', $value);
    }

    public function gt(string $field_name, $value): Builder
    {
        return $this->builder
            ->where($field_name, '>', $value);
    }

    public function lte(string $field_name, $value): Builder
    {
        return $this->builder
            ->where($field_name, '<=', $value);
    }

    public function gte(string $field_name, $value): Builder
    {
        return $this->builder
            ->where($field_name, '>=', $value);
    }

    public function like(string $field_name, $value): Builder
    {
        return $this->builder
            ->where($field_name, 'like', '%' . $value . '%');
    }

    public function yearmonth(string $field_name, $value): Builder
    {
        $date = Date::createFromFormat('Y-m', $value);
        $year = (string) $date->year;
        $month = (string) $date->month;

        return $this->builder
            ->whereYear($field_name, '=', $year)
            ->whereMonth($field_name, '=', $month);
    }

    public function daterange(string $field_name, $value): Builder
    {
        request()->validate([
            $field_name . '.gt' => 'nullable|date',
            $field_name . '.lt' => 'nullable|date',
        ]);
        return $this->builder
            ->when($value['gt'] ?? null, function (Builder $query, $value) use ($field_name) {
                $value = Date::createFromFormat('Y-m-d', $value);
                return $query->whereDate($field_name, '>=', $value->toDateString());
            })
            ->when($value['lt'] ?? null, function (Builder $query, $value) use ($field_name) {
                $value = Date::createFromFormat('Y-m-d', $value);
                return $query->whereDate($field_name, '<=', $value->toDateString());
            });
    }

    public function user_id_or_email(string $field_name, $value): Builder
    {
        if (is_numeric($value)) {
            return $this->builder
                ->where($field_name, '=', $value);
        } else {
            return $this->builder
                ->whereHas('partner', function (Builder $query) use ($value) {
                    $query->where('email', 'like', '%' . $value . '%');
                });
        }
    }

    public function filledData(string $field_name, $value): Builder
    {
        return $this->builder
            ->when($value === 'true', function (Builder $query) use ($field_name) {
                $query->where($field_name, '!=', null);
            })
            ->when($value === 'false', function (Builder $query) use ($field_name) {
                $query->where($field_name, '=', null);
            });
    }
}
