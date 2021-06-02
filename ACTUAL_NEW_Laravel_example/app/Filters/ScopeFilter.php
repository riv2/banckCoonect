<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use App\Filters\QueryFilter;

trait ScopeFilter
{
    /**
     * Scope for request filtering
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \App\Filters\QueryFilter $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $builder, QueryFilter $filters): Builder
    {
        return $filters->apply($builder);
    }
}
