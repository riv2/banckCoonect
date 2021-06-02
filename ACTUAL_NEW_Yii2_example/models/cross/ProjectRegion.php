<?php
namespace app\models\cross;

use \app\components\base\type\Cross;
use app\components\ValidationRules;
use app\models\enum\Region;
use app\models\reference\Project;

/**
 * Class ProjectCompetitorCategory
 *
 * Связь проекта с торг. площадками
 *
 * @package app\models\cross
 * @property string project_id
 * @property int region_id
 *
 * @property Project project
 * @property Region region
 */
class ProjectRegion extends Cross
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Регион проекта';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Регоины проекта';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            ValidationRules::ruleRequired('project_id','region_id'),
            [],
            ValidationRules::ruleEnum('region_id', Region::className())
        );
    }

    public static function relations() {
        return [
            'project',
            'region',
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
    public function getRegion() {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }
}