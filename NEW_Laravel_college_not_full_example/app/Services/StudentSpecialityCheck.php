<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 25.12.18
 * Time: 18:59
 */

namespace App\Services;


use App\Speciality;
use App\User;

class StudentSpecialityCheck
{
    private $speciality = null;
    private $student = null;

    public function __construct($studentId, $specialityId)
    {
        $this->setSpecialityById($specialityId);
        $this->setStudentById($studentId);
    }

    /**
     * @param $id
     * @return bool
     */
    public function setSpecialityById($id)
    {
        if(isset( $this->speciality->id) )
        {
            return true;
        }

        $this->speciality = Speciality::where('id', $id)->first();
        if(!$this->speciality)
        {
            return false;
        }

        return true;
    }

    /**
     * @param $id
     * @return bool
     */
    public function setStudentById($id)
    {
        if(isset( $this->student->id) )
        {
            return true;
        }

        $this->student = User
            ::with('bcApplication')
            ->where('id', $id)
            ->first();

        if(!$this->student)
        {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function checkEnt()
    {
        return $this->student->bcApplication->ent_total >= $this->speciality->passing_ent_total;
    }
}