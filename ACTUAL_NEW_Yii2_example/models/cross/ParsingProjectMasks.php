<?php
namespace app\models\cross;

use \app\components\base\type\Cross;
use app\components\ValidationRules;
use app\models\reference\Masks;
use app\models\reference\ParsingProject;
use app\models\reference\Project;

/**
 * Class ParsingProjectMasks
 *
 * Связь проекта парсинга и проекта расчета
 *
 * @package app\models\reference
 * @property string parsing_project_id
 * @property int    masks_id
 *
 * @property ParsingProject parsingProject
 * @property Masks masks
 */
class ParsingProjectMasks extends Cross
{

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Связь проекта парсинга и масок';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Связь проектов парсинга и масок';
    }

    public function __toString()
    {
        return $this->masks->name;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            ValidationRules::ruleRequired('parsing_project_id','masks_id'),
            ValidationRules::ruleUuid('parsing_project_id'),
            ValidationRules::ruleUuid('masks_id'),
            [],
            []
        );
    }


    public static function relations() {
        return [
            'parsingProject',
            'masks',
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
    public function getMasks() {
        return $this->hasOne(Masks::className(), ['id' => 'masks_id']);
    }
}