<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class SpecialityPrice
 * @package App
 * @property int id
 * @property int speciality_id
 * @property string study_form
 * @property string base_education
 * @property int price_type
 * @property int price
 */
class SpecialityPrice extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'speciality_prices';

    /** @var string Price for 1 credit for resident */
    const TYPE_CREDIT_PRISE_FOR_RESIDENT = 'credit_price_resident';

    /** @var string Price for 1 credit for non resident */
    const TYPE_CREDIT_PRISE_FOR_NON_RESIDENT = 'credit_price_non_resident';

    /** @var string Price for 1 credit for test remote access for resident  */
    const TYPE_REMOTE_ACCESS_RESIDENT = 'test_remote_resident';

    /** @var string Price for 1 credit for test remote access for non resident */
    const TYPE_REMOTE_ACCESS_NON_RESIDENT = 'test_remote_non_resident';

    /** @var string Semester credits limit */
    const TYPE_SEMESTER_CREDIT_LIMIT = 'semester_credit_limit';

    /**
     * Array for selects
     * @return array
     */
    public static function getTypesArray() : array
    {
        return [
            self::TYPE_CREDIT_PRISE_FOR_RESIDENT => __(self::TYPE_CREDIT_PRISE_FOR_RESIDENT),
            self::TYPE_CREDIT_PRISE_FOR_NON_RESIDENT => __(self::TYPE_CREDIT_PRISE_FOR_NON_RESIDENT),
            self::TYPE_REMOTE_ACCESS_RESIDENT => __(self::TYPE_REMOTE_ACCESS_RESIDENT),
            self::TYPE_REMOTE_ACCESS_NON_RESIDENT => __(self::TYPE_REMOTE_ACCESS_NON_RESIDENT),
            self::TYPE_SEMESTER_CREDIT_LIMIT => __(self::TYPE_SEMESTER_CREDIT_LIMIT),
        ];
    }

    /**
     * Save price or add new
     * @param int $specialityId
     * @param string $educationForm
     * @param string $baseEducation
     * @param string $priceType
     * @param int $price
     * @return bool
     */
    public static function savePrice(int $specialityId, string $educationForm, string $baseEducation, string $priceType, int $price) : bool
    {
        $specialityPrice = self::getOne($specialityId, $educationForm, $baseEducation, $priceType);

        if (!empty($specialityPrice)) {
            $specialityPrice->price = $price;
            return $specialityPrice->save();
        } else {
            return self::add($specialityId, $educationForm, $baseEducation, $priceType, $price);
        }
    }

    public static function createNewAsset(int $specialityId)
    {
        $educationForms = [
            Profiles::EDUCATION_STUDY_FORM_FULLTIME,
            Profiles::EDUCATION_STUDY_FORM_EVENING,
            Profiles::EDUCATION_STUDY_FORM_ONLINE,
            Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL
        ];
        $baseEducations = [
            BcApplications::EDUCATION_VOCATIONAL_EDUCATION,
            BcApplications::EDUCATION_HIGH_SCHOOL,
            BcApplications::EDUCATION_BACHELOR,
            BcApplications::EDUCATION_HIGHER
        ];
        $priceTypes = [
            self::TYPE_CREDIT_PRISE_FOR_RESIDENT,
            self::TYPE_CREDIT_PRISE_FOR_NON_RESIDENT,
            self::TYPE_REMOTE_ACCESS_RESIDENT,
            self::TYPE_REMOTE_ACCESS_NON_RESIDENT,
            self::TYPE_SEMESTER_CREDIT_LIMIT
        ];

        foreach ($educationForms as $educationForm) {
            foreach ($baseEducations as $baseEducation) {
                foreach ($priceTypes as $priceType) {
                    if ($priceType == self::TYPE_SEMESTER_CREDIT_LIMIT) {
                        $price = 33;
                    } else {
                        $price = 0;
                    }

                    self::addIfNotExists($specialityId, $educationForm, $baseEducation, $priceType, $price);
                }
            }
        }
    }

    public static function getBySpecialityId(int $id)
    {
        return self::where('speciality_id', $id)
            ->orderBy('study_form')
            ->orderBy('base_education')
            ->orderBy('price_type')
            ->get();
    }

    public static function getCreditPrice(int $specialityId, string $studyForm, string $baseEducation, bool $isResident) : ?int
    {
        $type = ($isResident) ? self::TYPE_CREDIT_PRISE_FOR_RESIDENT : self::TYPE_CREDIT_PRISE_FOR_NON_RESIDENT;

        $price = self::select('price')
            ->where('speciality_id', $specialityId)
            ->where('study_form', $studyForm)
            ->where('base_education', $baseEducation)
            ->where('price_type', $type)
            ->first();

        return $price->price ?? null;
    }

    public static function add(int $specialityId, string $educationForm, string $baseEducation, string $priceType, int $price) : bool
    {
        $specialityPrice = new self;
        $specialityPrice->speciality_id = $specialityId;
        $specialityPrice->study_form = $educationForm;
        $specialityPrice->base_education = $baseEducation;
        $specialityPrice->price_type = $priceType;
        $specialityPrice->price = $price;
        return $specialityPrice->save();
    }

    private static function addIfNotExists(int $specialityId, $educationForm, $baseEducation, $priceType, int $price)
    {
        $specialityPrice = self::getOne($specialityId, $educationForm, $baseEducation, $priceType);

        if (!empty($specialityPrice)) {
            return true;
        } else {
            return self::add($specialityId, $educationForm, $baseEducation, $priceType, $price);
        }
    }

    private static function getOne(int $specialityId, string $educationForm, string $baseEducation, string $priceType) : ?self
    {
        return self::where('speciality_id', $specialityId)
            ->where('study_form', $educationForm)
            ->where('base_education', $baseEducation)
            ->where('price_type', $priceType)
            ->first();
    }

    public static function getSemesterCreditsLimit(int $specialityId, string $studyForm, string $baseEducation) : int
    {
        $price = self::select('price')
            ->where('speciality_id', $specialityId)
            ->where('study_form', $studyForm)
            ->where('base_education', $baseEducation)
            ->where('price_type', self::TYPE_SEMESTER_CREDIT_LIMIT)
            ->first();

        return $price->price ?? 0;
    }

    public static function getRemoteAccessPrice(int $specialityId, string $studyForm, string $baseEducation, bool $isResident) : ?int
    {
        $type = ($isResident) ? self::TYPE_REMOTE_ACCESS_RESIDENT : self::TYPE_REMOTE_ACCESS_NON_RESIDENT;

        $price = self::select('price')
            ->where('speciality_id', $specialityId)
            ->where('study_form', $studyForm)
            ->where('base_education', $baseEducation)
            ->where('price_type', $type)
            ->first();

        return $price->price ?? null;
    }
}
