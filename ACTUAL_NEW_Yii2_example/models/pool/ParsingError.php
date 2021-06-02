<?php
namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\ValidationRules;
use app\models\reference\Competitor;
use app\models\reference\Item;
use app\models\enum\Region;
use app\models\reference\Masks;
use app\models\reference\ParsingProject;
use app\models\reference\Robot;
use app\models\register\Parsing;
use yii\helpers\ArrayHelper;

/**
 * 
 * Class ParsingError
 * @package app\models\pool
 *
 * @property string hash1               Хэш1
 * @property string hash2               Хэш2
 * @property string url                 УРЛ
 *
 * @property int regions              Регионы
 * @property string item_id             Товар
 * @property string competitor_id       Конкурнет
 * @property string robot_id            Робот
 * @property string parsing_project_id  Проект парсинга
 * @property string parsing_id          Парсинг
 * @property string message             Ошибка
 * @property string masks_id            Маски
 * @property string item
 * @property string proxy
 * @property string user_agent
 *
 *
 */

class ParsingError extends Pool
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Ошибки парсинга';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Ошибки парсинга';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleUuid('parsing_project_id'),
            ValidationRules::ruleUuid('parsing_id'),
            ValidationRules::ruleUuid('item_id'),
            ValidationRules::ruleUuid('competitor_id'),
            ValidationRules::ruleUuid('masks_id'),
            [
                [['region_id'], 'number'],
                [['proxy','user_agent','url','hash1','hash2','robot_id','item','message','regions','info'], 'string'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'region_id'             => 'Регион',
                'item_id'               => 'Товар',
                'competitor_id'         => 'Конкурент',
                'robot_id'              => 'Конкурент',
                'proxy'                 => 'Прокси',
                'user_agent'            => 'User Agent',
                'url'                   => 'URL товара',

                'region'                => 'Регион',
                'regions'               => 'Регионы',
                'item'                  => 'Товар',
                'robot'                 => 'Робот',
                'competitor'            => 'Конкурент',

                'parsing_project_id'    => 'Проект парсинга',
                'parsing_id'            => 'Парсинг',
                'message'               => 'Ошибка',
                'info'               => 'Информация',
            ]
        );
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)){
            $this->hash1 = md5($this->message.$this->parsing_id);
            $this->hash2 = md5($this->message.$this->robot_id.$this->proxy);
            return true;
        }
        return false;
    }

    public function excludeFieldsFileImportColumns()
    {
        return array_merge(parent::excludeFieldsFileImportColumns(),[
            'id',
        ]);
    }


    /**
     * @inheritdoc
     */
    public function crudIndexSearchRelations() {
        return [
            //'parsing',
            //'parsingProject',
            //'competitor',
            'robot',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(),[
            'item',
            'parsing',
            'parsingProject',
            'competitor',
            'robot',
        ]);
    }


    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            'created_at',
            'message',
            'proxy',
            'parsing',
            'parsingProject',
            'masks',
            'url',
            'robot',
            'regions',
        ]);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRobot()
    {
        return $this->hasOne(Robot::className(), ['id' => 'robot_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasks()
    {
        return $this->hasOne(Masks::className(), ['id' => 'masks_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompetitor()
    {
        return $this->hasOne(Competitor::className(), ['id' => 'competitor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParsingProject()
    {
        return $this->hasOne(ParsingProject::className(), ['id' => 'parsing_project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParsing()
    {
        return $this->hasOne(Parsing::className(), ['parsing_project_id' => 'parsing_project_id', 'id' => 'parsing_id']);
    }


    public function getName() {
        return $this->message;
    }
}