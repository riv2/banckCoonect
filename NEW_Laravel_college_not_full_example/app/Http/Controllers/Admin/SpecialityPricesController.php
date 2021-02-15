<?php

namespace App\Http\Controllers\Admin;

use App\BcApplications;
use App\Discipline;
use App\EntranceTest;
use App\Module;
use App\Profiles;
use App\SpecialityDiscipline;
use App\SpecialityPrice;
use App\Subject;
use App\Trend;
use Auth;
use App\Speciality;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;

class SpecialityPricesController extends MainAdminController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list()
    {
        $years = Speciality::getUniqueYears();
        $educationForms = Profiles::getStudyFormsArray();
        $baseEducations = BcApplications::getBaseEducationsArray();
        $priceTypes = SpecialityPrice::getTypesArray();

        return view('admin.pages.speciality_prices.list', compact('years', 'educationForms', 'baseEducations', 'priceTypes'));
    }

    /**
     * Ajax answer
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListAjax(Request $request)
    {
        $searchData = Speciality::getListForAdmin(
            $request->input('search')['value'],
            $request->input('columns')[1]['search']['value'],
            $request->input('columns')[3]['search']['value'],
            $request->input('start', 0),
            $request->input('length', 10),
            $request->input('order')[0]['column'] ?? 0,
            $request->input('order')[0]['dir'] ?? 'asc'
        );

        return Response::json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $searchData['recordsTotal'],
            'recordsFiltered' => $searchData['recordsFiltered'],
            'data' => $searchData['data']
        ]);
    }

    public function save(Request $request)
    {
        if (empty($request->input('specialities'))) {
            return Response::json(['status' => false, 'error' => __('Please, select specialities')]);
        }

        if (empty($request->input('education_form')) || empty($request->input('base_education')) || empty($request->input('price_type'))) {
            return Response::json(['status' => false, 'error' => 'Not all data submitted.']);
        }

        $price = $request->input('price') ?? 0;

        foreach ($request->input('specialities') as $specialityId) {
            SpecialityPrice::savePrice($specialityId, $request->input('education_form'), $request->input('base_education'), $request->input('price_type'), $price);
        }

        return Response::json(['status' => true]);
    }

    public function info(int $id)
    {
        $prices = SpecialityPrice::getBySpecialityId($id);

        $educationForms = [
            __(Profiles::EDUCATION_STUDY_FORM_FULLTIME),
            __(Profiles::EDUCATION_STUDY_FORM_EVENING),
            __(Profiles::EDUCATION_STUDY_FORM_ONLINE),
            __(Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL)
        ];
        $baseEducations = [
            __(BcApplications::EDUCATION_VOCATIONAL_EDUCATION),
            __(BcApplications::EDUCATION_HIGH_SCHOOL),
            __(BcApplications::EDUCATION_BACHELOR),
            __(BcApplications::EDUCATION_HIGHER)
        ];
        $priceTypes = [
            __(SpecialityPrice::TYPE_CREDIT_PRISE_FOR_RESIDENT),
            __(SpecialityPrice::TYPE_CREDIT_PRISE_FOR_NON_RESIDENT),
            __(SpecialityPrice::TYPE_REMOTE_ACCESS_RESIDENT),
            __(SpecialityPrice::TYPE_REMOTE_ACCESS_NON_RESIDENT),
            __(SpecialityPrice::TYPE_SEMESTER_CREDIT_LIMIT)
        ];

        return view('admin.pages.speciality_prices.info', compact('prices', 'priceTypes', 'educationForms', 'baseEducations'));
    }
}
