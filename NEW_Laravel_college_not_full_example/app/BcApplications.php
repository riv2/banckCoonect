<?php

namespace App;

use App\Services\SearchCache;
use Illuminate\Database\Eloquent\Model;
use Image;
use App\ProfileDoc;
use App\Profiles;
use App\Services\Auth;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int id
 * @property int nationality_id
 * @property int citizenship_id
 * @property int user_id
 * @property string education Базовое образование
 */
class BcApplications extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;


    /** @var string Средне-специальное */
    const EDUCATION_VOCATIONAL_EDUCATION = 'vocational_education';

    /** @var string Среднее */
    const EDUCATION_HIGH_SCHOOL = 'high_school';

    /** @var string Высшее (Бакалавр) */
    const EDUCATION_BACHELOR = 'bachelor';

    /** @var string Высшее */
    const EDUCATION_HIGHER = 'higher';

    protected $table = 'bc_applications';

    protected $fillable = [
        'nationality_id',
        'citizenship_id',
        'family_status',
        'region_id',
        'city_id',
        'country_id',
        'street',
        'building_number',
        'apartment_number',
        'ikt',
        'bceducation',
        'numeducation',
        'sereducation',
        'nameeducation',
        'dateeducation',
        'cityeducation',
        'education',
        'eduspecialization',
        'typevocational',
        'edudegree',
        'kzornot',
        'nostrification',
        'residence_registration_status',
        'r086_status',
        'r063_status',
        'atteducation_status',
        'nostrification_status',
        'military_status',
        'eduspecialty',
        'ent_name_1',
        'ent_name_2',
        'ent_name_3',
        'ent_name_4',
        'ent_name_5',
        'ent_val_1',
        'ent_val_2',
        'ent_val_3',
        'ent_val_4',
        'ent_val_5',
        'ent_total',
        'ent_name_1_copy',
        'ent_name_2_copy',
        'ent_name_3_copy',
        'ent_name_4_copy',
        'ent_name_5_copy',
        'ent_val_1_copy',
        'ent_val_2_copy',
        'ent_val_3_copy',
        'ent_val_4_copy',
        'ent_val_5_copy',
    ];

    private static $adminAjaxColumnList = [
        'id',
        'fio',
        'ent_total',
        'trendName'
    ];

    public static $adminRedisTable = 'admin_ent_winners';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function nationality()
    {
        return $this->hasOne('App\Nationality', 'id', 'nationality_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function citizenship()
    {
        return $this->hasOne('App\Country', 'id', 'citizenship_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function familyStatus()
    {
        return $this->hasOne(FamilyStatus::class, 'id', 'family_status');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function region()
    {
        return $this->hasOne('App\Region', 'id', 'region_id');
    }

    public function city()
    {
        return $this->hasOne('App\City', 'id', 'city_id');
    }

    /**
     * Array for selects
     * @return array
     */
    public static function getBaseEducationsArray() : array
    {
        $array = [];

        foreach (self::getBaseEducationsArrayFlat() as $baseEducation) {
            $array[$baseEducation] = __($baseEducation);
        }

        return $array;
    }

    /**
     * Array for selects
     * @return array
     */
    public static function getBaseEducationsArrayFlat() : array
    {
        return [
            self::EDUCATION_VOCATIONAL_EDUCATION,
            self::EDUCATION_HIGH_SCHOOL,
            self::EDUCATION_BACHELOR,
            self::EDUCATION_HIGHER
        ];
    }

    /**
     * @param $file
     * @param string $side front|back
     * @return bool
     */
    public function syncR086($file = null, $side = 'front')
    {
        if ($side == 'front') {
            $this->r086_photo = null;
        }

        if ($side == 'back') {
            $this->r086_photo_back = null;
        }

        if ($file) {
            if ($side == 'front') {
                $docType = ProfileDoc::TYPE_R086;
            }

            if ($side == 'back') {
                $docType = ProfileDoc::TYPE_R086_BACK;
            }

            ProfileDoc::saveDocument($docType, $file, $side);
        }

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function syncR063($file = null)
    {

        $this->r063_photo = null;

        if ($file) {
            ProfileDoc::saveDocument(ProfileDoc::TYPE_R063, $file);
        }

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function syncResidenceRegistration($file = null)
    {
        $this->residence_registration_photo = null;

        if ($file) {
            ProfileDoc::saveDocument(ProfileDoc::TYPE_RESIDENCE_REGISTRATION, $file);
        }

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function syncMilitary($file = null)
    {
        $this->military_photo = null;

        if ($file) {
            ProfileDoc::saveDocument(ProfileDoc::TYPE_MILITARY, $file);
        }

        return true;
    }


    /**
     * @param $file
     * @param string $side front|back
     * @return bool
     */
    public function syncAttEducation($file = null, $side = 'front')
    {
        if ($side == 'front') {
            $this->atteducation_photo = null;
        }

        if ($side == 'back') {
            $this->atteducation_photo_back = null;
        }

        if ($file) {
            if ($side == 'front') {
                $docType = ProfileDoc::TYPE_ATTEDUCATION;
            }

            if ($side == 'back') {
                $docType = ProfileDoc::TYPE_ATTEDUCATION_BACK;
            }

            ProfileDoc::saveDocument($docType, $file, $side);


        }

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function syncNostrificationAttach($file = null)
    {
        $this->nostrificationattach_photo = null;

        if ($file) {
            ProfileDoc::saveDocument(ProfileDoc::TYPE_NOSTRIFICATION, $file);
        }

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function syncNostrificationAttachBack($file = null)
    {
        $this->nostrificationattach_back_photo = null;

        if ($file) {
            ProfileDoc::saveDocument(ProfileDoc::TYPE_NOSTRIFICATION_BACK, $file);
        }

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function syncConConfirm($file = null)
    {
        if ($file) {
            ProfileDoc::saveDocument(ProfileDoc::TYPE_CON_CONFIRM, $file);
        }

        return true;
    }

    /**
     * @return bool
     */
    public function attachEnt()
    {
        $profile = Profiles::where('user_id', Auth::user()->id)->first();

        $ent = $this->importEnt($this->ikt, $profile->iin);

        if (!isset($ent->errorCode)) return false;

        if ($ent->errorCode != 0) return false;

        $tmp = $ent->userBallList;

        isset($tmp[0]->subjectNameRu) ? $this->ent_name_1 = $tmp[0]->subjectNameRu : '';
        isset($tmp[0]->ball) ? $this->ent_val_1 = $tmp[0]->ball : 0;
        isset($tmp[1]->subjectNameRu) ? $this->ent_name_2 = $tmp[1]->subjectNameRu : '';
        isset($tmp[1]->ball) ? $this->ent_val_2 = $tmp[1]->ball : 0;
        isset($tmp[2]->subjectNameRu) ? $this->ent_name_3 = $tmp[2]->subjectNameRu : '';
        isset($tmp[2]->ball) ? $this->ent_val_3 = $tmp[2]->ball : 0;
        isset($tmp[3]->subjectNameRu) ? $this->ent_name_4 = $tmp[3]->subjectNameRu : '';
        isset($tmp[3]->ball) ? $this->ent_val_4 = $tmp[3]->ball : 0;
        isset($tmp[4]->subjectNameRu) ? $this->ent_name_5 = $tmp[4]->subjectNameRu : '';
        isset($tmp[4]->ball) ? $this->ent_val_5 = $tmp[4]->ball : 0;

        $this->ent_lang = $ent->langNameRu;
        $this->ent_total = $this->ent_val_1 + $this->ent_val_2 + $this->ent_val_3 + $this->ent_val_4 + $this->ent_val_5;

        return true;
    }

    /**
     * @param $idtc
     * @param $iin
     * @return mixed
     */
    public function importEnt($idtc, $iin)
    {
        $firstTest = env('ENT_FIRST_TEST_ID');

        for ($testId = $firstTest; $testId < $firstTest + 15; $testId++) {
            $ent = $this->importEntSingle($idtc, $iin, $testId);

            if (isset($ent->errorCode) && $ent->errorCode == 0) {
                $ent->testId = $testId;
                return $ent;
            }
        }
        return false;

    }

    /**
     * @param $idtc
     * @param $iin
     * @param $testId
     * @return mixed
     */
    public function importEntSingle($idtc, $iin, $testId = 32)
    {
        $ch = curl_init();
        $idtc = (int) $idtc;

        curl_setopt($ch, CURLOPT_URL, "https://res.testcenter.kz/test-result/api/userdata/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"idtc\":" . $idtc . ",\"iin\":\"" . $iin . "\",\"idTestType\":" . $testId . "}");
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = "Content-Type: application/json;charset=UTF-8";
        $headers[] = "Connection: keep-alive";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_decode(trim($result));
    }

    /**
     * @param int $year
     * @param string|null $search
     * @param int $start
     * @param int $length
     * @param int $orderColumn
     * @param string $orderDirection
     * @return array
     */
    public static function getListForAdmin(int $year, ?string $search = '', int $start = 0, int $length = 10, int $orderColumn = 0, string $orderDirection = 'desc')
    {
        $orderColumnName = self::$adminAjaxColumnList[$orderColumn] ?? 'ent_total';

        // Get all
        $tableData = SearchCache::getJsonData(self::$adminRedisTable . '_' . $year);

        $recordsTotal = !empty($tableData) ? count($tableData) : 0;

        // Search string $search
        if ( !empty($search) && !empty($tableData) ) {
            $tableData = SearchCache::filterDataByString($tableData, $search);
        }

        $recordsFiltered = !empty($tableData) ? count($tableData) : 0;

        // Sorting
        usort($tableData, function ($a, $b) use ($orderColumnName, $orderDirection) {
            if ($orderDirection == 'asc') {
                if (is_int($a[$orderColumnName]) && is_int($b[$orderColumnName])) {
                    return $a[$orderColumnName] > $b[$orderColumnName];
                }

                return strcmp($a[$orderColumnName], $b[$orderColumnName]);
            } else {
                if (is_int($a[$orderColumnName]) && is_int($b[$orderColumnName])) {
                    return $b[$orderColumnName] > $a[$orderColumnName];
                }

                return strcmp($b[$orderColumnName], $a[$orderColumnName]);
            }
        });

        // take slice
        $entList = !empty($tableData) ? array_slice($tableData, $start, $length) : [];

        $data = [];
        foreach ($entList as $item) {
            $data[] = [
                $item['id'],
                $item['fio'],
                $item['ent_total'],
                $item['trendName'],
                __($item['status']),
                ''
            ];
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }
}