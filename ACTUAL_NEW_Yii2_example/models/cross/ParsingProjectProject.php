<?php
namespace app\models\cross;

use \app\components\base\type\Cross;
use app\components\ValidationRules;
use app\models\reference\ParsingProject;
use app\models\reference\Project;

/**
 * Class ParsingProjectProject
 *
 * Связь проекта парсинга и проекта расчета
 *
 * @package app\models\reference
 * @property string parsing_project_id
 * @property int    project_id
 *
 * @property ParsingProject parsingProject
 * @property Project project
 */
class ParsingProjectProject extends Cross
{

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Связь проекта парсинга и расчета';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Связь проектов парсинга и расчета';
    }

    public function __toString()
    {
        return $this->project->name;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            ValidationRules::ruleRequired('parsing_project_id','project_id'),
            ValidationRules::ruleUuid('parsing_project_id'),
            ValidationRules::ruleUuid('project_id'),
            [],
            []
        );
    }


    public static function relations() {
        return [
            'parsingProject',
            'project',
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
    public function getProject() {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }
}