<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int user_id
 * @property float value
 */
class StudentGpa extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'student_gpi';

    public static function getListForAdmin()
    {
        $gpaNewList = self::select(['student_gpi.user_id AS id', 'fio', 'value'])
            ->leftJoin('profiles', 'student_gpi.user_id', '=', 'profiles.user_id')
            ->orderBy('value', 'desc')
            ->limit(6)
            ->get();

        foreach ($gpaNewList as $key => $gpa) {
            if ($key < 3) {
                $gpa->discountSize = 100;
            } else {
                $gpa->discountSize = 50;
            }
        }

        return $gpaNewList;
    }

    public static function getActual(int $userId) : float
    {
        $gpi = self::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->first();

        return $gpi->value ?? 0.0;
    }
}
