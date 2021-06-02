<?php
namespace app\models\register;

use app\components\base\Entity;
use app\components\base\type\Register;
use app\components\ValidationRules;
use app\models\enum\ErrorType;
use app\models\enum\Status;
use app\models\reference\ParsingProject;
use yii;
use yii\helpers\Html;

/**
 * Class AntiCaptchaTask
 * @package app\models\register
 *
 * @property string anti_captcha_task_id
 * @property string answer
 * @property string img_src
 * @property string url
 * @property int parsing_id
 * @property int parsing_project_id
 * @property float cost
 * @property string error
 * @property Parsing parsing
 * @property ParsingProject parsingProject
 *
 *
 */
class AntiCaptchaTask extends Register
{

    public $labelAttribute = 'url';

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Антикапча';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Антикапча';
    }

    /**
     * @inheritdoc
     */
    public static function noCount() {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            ValidationRules::ruleUuid('parsing_id'),
            ValidationRules::ruleUuid('parsing_project_id'),
            parent::rules(),
            [
                [['answer','answer','img_src','url'], 'string'],
                [['error'], 'safe'],
                [['cost'], 'number'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'anti_captcha_task_id'      => 'Task Id',
                'answer'                    => 'Решение',
                'img_src'                   => 'Урл  картинки',
                'url'                       => 'Урл',
                'parsing_id'                => 'Парсинг',
                'parsing_project_id'        => 'Проет парсинга',
                'parsing'                => 'Парсинг',
                'parsingProject'        => 'Проет парсинга',
                'cost'                      => 'Цена',
                'error'                     => 'Ошибка',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(),[
            'parsing',
            'parsingProject',
        ]);
    }
    /**
     * @inheritdoc
     */
    public function crudIndexSearchRelations()
    {
        return array_merge(
            parent::crudIndexSearchRelations(),
            [
                'parsing',
                'parsingProject',
            ]
        );
    }


    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            'created_at',
            'anti_captcha_task_id',
            'img_body'=> [
                'label' => 'captcha',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::img('data:image/gif;base64,'.$model->img_body);
                }
            ],
            'answer',
            'cost' => [
                'attribute' => 'cost',
                'value' => function($model) {
                    return round($model->cost, 3);
                }
            ],
            'parsingProject' ,
            'parsing' ,
            'error',
            'url',
        ]);
    }

    public function __toString()
    {
        return $this->url;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParsing() {
        return $this->hasOne(Parsing::className(), ['parsing_project_id' => 'parsing_project_id', 'id' => 'parsing_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParsingProject() {
        return $this->hasOne(ParsingProject::className(), ['id' => 'parsing_project_id']);
    }

}