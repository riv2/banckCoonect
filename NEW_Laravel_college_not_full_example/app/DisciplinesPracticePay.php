<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DisciplinesPracticePay extends Model
{
    protected $table = 'disciplines_practice_pay';

    const STATUS_PROCESS = "process";
    const STATUS_OK      = "ok";

    protected $fillable = [
        'discipline_id',
        'user_id',
        'status',
        'payed_sum'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function discipline()
    {
        return $this->hasOne(Discipline::class, 'id', 'discipline_id');
    }
}
