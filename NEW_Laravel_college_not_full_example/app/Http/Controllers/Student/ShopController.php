<?php

namespace App\Http\Controllers\Student;

use App\Course;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function otherList()
    {
        $courseList = Course::getListForShopOther();

    	return view('student.shop.other', [
    	    'courseList' => $courseList
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function disciplineList(Request $request)
    {
        $courseList = Course::getListForShopDisciplines();

        return view('student.shop.disciplines', [
            'courseList' => $courseList
        ]);
    }

    public function details(Request $request, $id)
    {
        $course = Course::getForShopDetails($id);

        if(!$course)
        {
            abort(404);
        }

        if($course->lectures) {
            foreach ($course->lectures as $k => $lecture) {
                $course->lectures[$k]->getReserveRoomInfo();
            }
        }

        return view('student.shop.details', ['course' => $course]);
    }
}
