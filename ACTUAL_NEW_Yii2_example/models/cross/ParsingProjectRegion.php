<?php
namespace app\models\cross;

use \app\components\base\type\Cross;
use app\components\ValidationRules;
use app\models\enum\Region;
use app\models\reference\ParsingProject;

/**
 * Class ParsingProjectRegion
 *
 * Связь проекта парсинга с регионом
 *
 * @package app\models\reference
 * @property string parsing_project_id
 * @property int    region_id
 * @property string cookies
 * @property string proxies
 * @property string url_replace_from
 * @property string url_replace_to
 * @property int sort
 *
 * @property ParsingProject parsingProject
 * @property Region region
 */
class ParsingProjectRegion extends Cross
{

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Регион парсинга';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Регионы парсинга';
    }

    public function __toString()
    {
        return $this->region->name;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['cookies', 'proxies','url_replace_from','url_replace_to'], 'string'],
                [['sort'], 'number'],
            ],
            ValidationRules::ruleUuid('parsing_project_id'),
            ValidationRules::ruleEnum('region_id', Region::className()),
            ValidationRules::ruleRequired('parsing_project_id','region_id'),
            []
        );
    }


    public static function relations() {
        return [
            'parsingProject',
            'region',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParsingProject() {
        return $this->hasOne(ParsingProject::className(), ['id' => 'parsing_project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion() {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }
}