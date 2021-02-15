<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\Auth;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class Syllabus
 * @package App
 * @property int id
 * @property int discipline_id
 * @property string language
 * @property string theme_number
 * @property string theme_name
 * @property string literature
 *
 * @property QuizQuestion[] quizeQuestions
 */
class Syllabus extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use SoftDeletes;

    protected $table = 'syllabus';

    protected $fillable = [
        'theme_number',
        'theme_name',
        'module_id',
        'contact_hours',
        'self_hours',
        'self_with_teacher_hours',
        'language',
        'sro_hours',
        'teoretical_description',
        'practical_description',
        'sro_description',
        'srop_description',
        'srop_hours',
        'for_test1',
    ];

    private $langs = ['ru', 'en', 'kz', 'fr', 'ar', 'de'];

    /**
     * @return string
     */
    public function getDefaultLanguage()
    {
        foreach ($this->langs as $lang) {
            if ($lang == 'ru' && isset($this->attributes['theme_name']) && $this->attributes['theme_name']) {
                return $lang;
            }

            if ($lang != 'ru') {
                if (isset($this->attributes['theme_name_' . $lang]) && $this->attributes['theme_name_' . $lang]) {
                    return $lang;
                }
            }
        }

        return false;
    }

    /**
     * @param $field
     * @param $lang
     * @return bool
     */
    public function emptyFieldByLang($field, $lang)
    {
        $suffix = $lang == 'ru' ? '' : '_' . $lang;
        $fieldName = $field . $suffix;

        return !(isset($this->attributes[$fieldName]) && $this->attributes[$fieldName]);
    }

    /**
     * @param $field
     * @return string
     */
    public function fieldByLang($field)
    {
        $lang = Auth::user()->studentProfile->education_lang ?? null;

        if (!$this->emptyFieldByLang($field, $lang)) {
            return $lang == 'ru' ? $this->attributes[$field] : $this->attributes[$field . '_' . $lang];
        }

        $lang = $this->getDefaultLanguage();

        if ($lang && !$this->emptyFieldByLang($field, $lang)) {
            return $lang == 'ru' ? $this->attributes[$field] : $this->attributes[$field . '_' . $lang];
        }

        return '';
    }

    /**
     * @return mixed
     */
    public function getThemeNumberAttribute()
    {
        return $this->fieldByLang('theme_number');
    }

    /**
     * @return mixed
     */
    public function getActualThemeNumberAttribute()
    {
        if (isset($this->attributes['theme_number']) && $this->attributes['theme_number']) {
            return $this->attributes['theme_number'];
        }

        if (isset($this->attributes['theme_number_kz']) && $this->attributes['theme_number_kz']) {
            return $this->attributes['theme_number_kz'];
        }

        if (isset($this->attributes['theme_number_en']) && $this->attributes['theme_number_en']) {
            return $this->attributes['theme_number_en'];
        }

        if (isset($this->attributes['theme_number_fr']) && $this->attributes['theme_number_fr']) {
            return $this->attributes['theme_number_fr'];
        }

        if (isset($this->attributes['theme_number_ar']) && $this->attributes['theme_number_ar']) {
            return $this->attributes['theme_number_ar'];
        }

        if (isset($this->attributes['theme_number_de']) && $this->attributes['theme_number_de']) {
            return $this->attributes['theme_number_de'];
        }
    }

    /**
     * @return mixed
     */
    /*public function getThemeNameAttribute()
    {
        return $this->fieldByLang('theme_name');
    }*/

    /**
     * @return mixed
     */
    public function getActualThemeNameAttribute()
    {
        if (isset($this->attributes['theme_name']) && $this->attributes['theme_name']) {
            return $this->attributes['theme_name'];
        }

        if (isset($this->attributes['theme_name_kz']) && $this->attributes['theme_name_kz']) {
            return $this->attributes['theme_name_kz'];
        }

        if (isset($this->attributes['theme_name_en']) && $this->attributes['theme_name_en']) {
            return $this->attributes['theme_name_en'];
        }

        if (isset($this->attributes['theme_name_fr']) && $this->attributes['theme_name_fr']) {
            return $this->attributes['theme_name_fr'];
        }

        if (isset($this->attributes['theme_name_ar']) && $this->attributes['theme_name_ar']) {
            return $this->attributes['theme_name_ar'];
        }

        if (isset($this->attributes['theme_name_de']) && $this->attributes['theme_name_de']) {
            return $this->attributes['theme_name_de'];
        }
    }

    public function getActualLiteratureAttribute()
    {
        if (isset($this->attributes['literature']) && $this->attributes['literature']) {
            return $this->attributes['literature'];
        }

        if (isset($this->attributes['literature_kz']) && $this->attributes['literature_kz']) {
            return $this->attributes['literature_kz'];
        }

        if (isset($this->attributes['literature_en']) && $this->attributes['literature_en']) {
            return $this->attributes['literature_en'];
        }

        if (isset($this->attributes['literature_fr']) && $this->attributes['literature_fr']) {
            return $this->attributes['literature_fr'];
        }

        if (isset($this->attributes['literature_ar']) && $this->attributes['literature_ar']) {
            return $this->attributes['literature_ar'];
        }

        if (isset($this->attributes['literature_de']) && $this->attributes['literature_de']) {
            return $this->attributes['literature_de'];
        }
    }

    /**
     * @return $relation
     */
    public function teoreticalMaterials()
    {
        return $this
            ->hasMany(SyllabusDocument::class, 'syllabus_id', 'id')
            ->where('material_type', SyllabusDocument::MATERIAL_TYPE_TEORETICAL);
    }

    /**
     * @return $relations
     */
    public function practicalMaterials()
    {
        return $this
            ->hasMany(SyllabusDocument::class, 'syllabus_id', 'id')
            ->where('material_type', SyllabusDocument::MATERIAL_TYPE_PRACTICAL);
    }

    /**
     * @return $relations
     */
    public function sroMaterials()
    {
        return $this
            ->hasMany(SyllabusDocument::class, 'syllabus_id', 'id')
            ->where('material_type', SyllabusDocument::MATERIAL_TYPE_SRO);
    }

    /**
     * @return $relations
     */
    public function sropMaterials()
    {
        return $this
            ->hasMany(SyllabusDocument::class, 'syllabus_id', 'id')
            ->where('material_type', SyllabusDocument::MATERIAL_TYPE_SROP);
    }

    public function documents()
    {
        return $this->hasMany(SyllabusDocument::class, 'syllabus_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function quizeQuestions()
    {
        return $this
            ->belongsToMany(
                QuizQuestion::class,
                'syllabus_quize_questions',
                'syllabus_id',
                'quize_question_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function discipline()
    {
        return $this->hasOne(Discipline::class, 'id', 'discipline_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function module()
    {
        return $this->hasOne(SyllabusModule::class, 'id', 'module_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function literature()
    {
        return $this->hasManyThrough('App\LibraryLiteratureCatalog', 'App\SyllabusLiterature', 'syllabus_id', 'id', 'id', 'literature_id');
    }

    /**
     * @param $documentList
     * @return bool
     */
    public function attachMaterials($documentList, $fileList, $materialType, $lang)
    {
        $deleteExcept = [];

        /*Insert new link docs*/
        if (isset($documentList['new']['link'])) {
            foreach ($documentList['new']['link'] as $iKey => $link) {

                $newdocId = SyllabusDocument::createLink($this->id, $materialType, [
                    'link' => $link,
                    'lang' => $lang,
                    'link_description' => !empty($documentList['new']['link_description'][$iKey]) ? $documentList['new']['link_description'][$iKey] : ''
                ]);

                if ($newdocId) {
                    $deleteExcept[] = $newdocId;
                }
            }
        }

        /*Insert new file docs*/
        if (isset($fileList['new']['file'])) {
            foreach ($fileList['new']['file'] as $file) {
                $newdocId = SyllabusDocument::createFile($this->id, $materialType, $file, $lang);

                if ($newdocId) {
                    $deleteExcept[] = $newdocId;
                }
            }
        }

        /*Update link docs*/
        if (isset($documentList['update']['link'])) {
            foreach ($documentList['update']['link'] as $klink => $link) {

                $docUpdate = SyllabusDocument::updateLink($this->id, $klink, [
                    'link' => $link,
                    'lang' => $lang,
                    'link_description' => !empty($documentList['update']['link_description'][$klink]) ? $documentList['update']['link_description'][$klink] : ''
                ]);

                if ($docUpdate) {
                    $deleteExcept[] = $klink;
                }
            }
        }

        $setLang = '';
        /*Update file docs*/
        if (isset($documentList['update']['file'])) {
            foreach ($documentList['update']['file'] as $fileId) {
                $deleteExcept[] = $fileId;
            }

            $setLang = $lang;
        }

        $forDelete = SyllabusDocument
            ::where('syllabus_id', $this->id)
            ->where('material_type', $materialType)
            ->where('lang', $lang)
            ->whereNotIn('id', $deleteExcept)
            ->get();

        if ($forDelete) {
            foreach ($forDelete as $item) {
                $item->delete();
            }
        }
    }

    /**
     * @param $questionList
     * @return bool
     */
    public function attachQuizeQuestions($questionList, $files)
    {
        $deleteExcept = [];

        /*Insert new link docs*/
        if (isset($questionList['new'])) {
            foreach ($questionList['new'] as $k => $question) {
                $newQuestionId = QuizQuestion::createQuestion($this->discipline_id, $question, $files['new'][$k] ?? null);

                if ($newQuestionId) {
                    $relation = new SyllabusQuizeQuestion();
                    $relation->syllabus_id = $this->id;
                    $relation->quize_question_id = $newQuestionId;
                    $relation->save();

                    $deleteExcept[] = $newQuestionId;
                }
            }
        }

        if (isset($questionList['update'])) {
            foreach ($questionList['update'] as $k => $question) {
                $newQuestionId = QuizQuestion::updateQuestion($k, $question, $files['update'][$k] ?? null);

                if ($newQuestionId) {
                    $deleteExcept[] = $newQuestionId;
                }
            }
        }

        /*$forDelete = SyllabusQuizeQuestion
            ::where('syllabus_id', $this->id)
            ->whereNotIn('quize_question_id', $deleteExcept)
            ->get();

        if($forDelete)
        {
            foreach ($forDelete as $item) {
                $item->deleteWithQuestion();
            }
        }*/
    }

    /**
     * @param $question
     * @param $file
     * @return bool
     * @throws \Exception
     */
    public function attachSingleQuestion($question)
    {
        $quizQuestion = null;

        if (isset($question['id'])) {
            $quizQuestion = QuizQuestion::where('id', $question['id'])->first();
        }

        if (!$quizQuestion) {
            $quizQuestion = new QuizQuestion();
            $quizQuestion->discipline_id = $this->discipline_id;
            $quizQuestion->teacher_id = 0;
        }

        $totalPoints = 0;
        foreach ($question['answers'] as $item) {
            $totalPoints = $totalPoints + $item['points'];
        }

        $quizQuestion->fill($question);
        $quizQuestion->total_points = $totalPoints;
        $quizQuestion->save();
        $this->quizeQuestions()->sync($quizQuestion->id, false);
        if (isset($question['uploadAudio'])) {
            $quizQuestion->attachAudioJson($question['uploadAudio']);
        }

        $quizQuestion->syncAnswers($question['answers']);

        return true;
    }

    /**
     * @param $disciplineId
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    static function getListForAdmin($disciplineId)
    {
        return self
            ::with('teoreticalMaterials')
            ->with('practicalMaterials')
            ->with('quizeQuestions')
            ->with('module')
            ->where('discipline_id', $disciplineId)
            ->orderBy(DB::raw('ABS(theme_number)'))
            ->get();
    }

    /**
     * @param $disciplineId
     * @param $themeId
     * @return Model|null|object|static
     */
    static function getByIdForAdmin($disciplineId, $syllabusId)
    {
        return self
            ::with(['teoreticalMaterials' => function ($query) {
                $query->orderBy('resource_type');
            }])
            ->with(['practicalMaterials' => function ($query) {
                $query->orderBy('resource_type');               
            }])
            ->with('quizeQuestions')
            ->where('discipline_id', $disciplineId)
            ->where('id', $syllabusId)
            ->first();
    }

    public static function setTest1(int $id, bool $on)
    {
        return self::where('id', $id)->update(['for_test1' => $on]);
    }

    public static function getRandomId()
    {
        $one = self
            ::select('id')
            ->inRandomOrder()
            ->first();

        return $one->id ?? null;
    }
}
