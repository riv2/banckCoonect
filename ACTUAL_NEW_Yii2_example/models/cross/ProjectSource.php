<?php
namespace app\models\cross;

use \app\components\base\type\Cross;
use app\components\ValidationRules;
use app\models\enum\Source;
use app\models\reference\Project;

/**
 * Class ProjectCompetitorCategory
 *
 * Связь проекта с торг. площадками
 *
 * @package app\models\cross
 * @property string project_id
 * @property int source_id
 *
 * @property Project project
 * @property Source source
 */
class ProjectSource extends Cross
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Торговые площадки проекта';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Торговые площадки проекта';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            ValidationRules::ruleRequired('project_id','source_id'),
            [],
            ValidationRules::ruleEnum('source_id', Source::className())
        );
    }

    public static function relations() {
        return [
            'project',
            'source',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject() {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSource() {
        return $this->hasOne(Source::className(), ['id' => 'source_id']);
    }
}