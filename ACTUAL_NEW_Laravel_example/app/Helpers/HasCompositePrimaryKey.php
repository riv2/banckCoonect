<?php
/**
 * Project laravel
 * Created by danila 05.02.20 @ 11:05
 */

namespace App\Helpers;

use Exception;
use Illuminate\Database\Eloquent\Builder;

trait HasCompositePrimaryKey
{
    /**
     * Get the primary key for the model.
     *
     * @return array|string
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    protected function setKeysForSaveQuery(Builder $query)
    {
        foreach ($this->getKeyName() as $key) {
            if (isset($this->$key)) {
                $query->where($key, '=', $this->$key);
            } else {
                throw new Exception(__METHOD__ . 'Missing part of the primary key: ' . $key);
            }
        }

        return $query;
    }
}
