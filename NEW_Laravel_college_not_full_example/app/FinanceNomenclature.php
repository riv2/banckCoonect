<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Support\Collection;
use Auth;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class FinanceNomenclature
 * @package App
 * @property string code
 * @property int cost
 * @property int id
 * @property int only_one
 * @property int only_one_per_semester
 * @property string name_en
 * @property string name
 */
class FinanceNomenclature extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    const TYPE_FEE = 'fee';
    const TYPE_OTHER = 'other';
    const TYPE_REFERENCE = 'reference';
    const TYPE_HELPS = 'helps';

    const STATUS_HIDDEN = 1;
    const STATUS_NOT_HIDDEN = 0;

    const TRANSIT_CLASS_ATTENDANCE_ID = 22;

    const ENQUIRE_NAME_TR    = 'Transcript enquire';
    const ENQUIRE_NAME_GCVP4  = 'Reference to the state pension payment center type 4';
    const ENQUIRE_NAME_GCVP21  = 'Reference to the state pension payment center type 21';
    const ENQUIRE_NAME_GCVP6  = 'Reference to the state pension payment center type 6';
    const ENQUIRE_NAME_MILITARY   = 'Reference to the military commissariat';
    const ENQUIRE_NAME_ENTER = 'Reference for submission upon request';

    const ID_TEST1_TRIAL = 2;
    const ID_EXAM_TRIAL = 2;
    const ID_REMOTE_ACCESS = 6;

    const HIDDEN_OR_NO  = 0;
    const HIDDEN_OR_YES = 1;

    protected $table = 'finance_nomenclatures';

    protected $fillable = [
        'code',
        'name',
        'name_kz',
        'name_en',
        'type',
        'cost',
        'hidden',
        'or_hidden'
    ];

    public static function getForStudyPage(string $category)
    {
        $list = self::where('hidden', self::STATUS_NOT_HIDDEN)
            ->where('type', '!=', self::TYPE_REFERENCE)
            ->where(function (Builder $query) use ($category) {
                $query->whereNull('profile_category')
                    ->orWhere('profile_category', '')
                    ->orWhere('profile_category', $category);
            })
            ->where('id',9)
            ->whereNull('deleted_at')
            ->orderBy('name', 'ASC')
            ->get();
        return self::getForStudyPageFilter($list);
    }

    public static function getForStudyPageFilter($list)
    {
        $profile = Auth::user()->studentProfile;
        $filtered = [];
        foreach ($list as $item) {
            if( $item->name_en == self::ENQUIRE_NAME_MILITARY && !$profile->sex) {
                continue;
            }
            //for distant student hiding all docs to buy
            if( $profile->education_study_form == $profile::EDUCATION_STUDY_FORM_ONLINE){
                if( $item->name_en == self::ENQUIRE_NAME_ENTER ) {
                    continue;
                }
            }
            if( $profile->education_study_form != $profile::EDUCATION_STUDY_FORM_FULLTIME ) {
                if( $item->name_en == self::ENQUIRE_NAME_MILITARY ||
                    $item->name_en == self::ENQUIRE_NAME_GCVP6  ||
                    $item->name_en == self::ENQUIRE_NAME_GCVP21  ||
                    $item->name_en == self::ENQUIRE_NAME_GCVP4 ) {
                    continue;
                }
            }
            $filtered[] = $item;
        }
        return $filtered;
    }


    /**
     * @param int $id
     * @return self|null
     */
    public static function getById(int $id): ?self
    {
        return self::where('id', $id)->first();
    }

    public static function getReferences() : Collection
    {
        return self::where('hidden', self::STATUS_NOT_HIDDEN)
            ->where('type', self::TYPE_REFERENCE)
            ->orderBy('name', 'ASC')
            ->get();
    }

    /**
     * @return static|null
     * @codeCoverageIgnore
     */
    public static function getTest1Trial() : ?self
    {
        return self::getById(self::ID_TEST1_TRIAL);
    }

    /**
     * @return static|null
     * @codeCoverageIgnore
     */
    public static function getExamTrial() : ?self
    {
        return self::getById(self::ID_EXAM_TRIAL);
    }

    public static function getRemoteAccess(int $credits, int $price) : self
    {
        $self = self::getById(self::ID_REMOTE_ACCESS);
        $self->cost = $price * $credits;
        return $self;
    }

    public static function getRandomId() : int
    {
        /** @var self $student */
        $n = self
            ::select(['id'])
            ->inRandomOrder()
            ->first();

        return $n->id;
    }
}
