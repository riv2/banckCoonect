<?php
namespace app\models\cross;

use \app\components\base\type\Cross;
use app\components\ValidationRules;
use app\models\reference\PriceFormerType;
use app\models\reference\Project;

/**
 * Class ProjectPriceFormerType
 *
 * Связь проекта и типа цены прафйсформера
 *
 * @package app\models\reference
 * @property string price_former_type_id
 * @property int    project_id
 *
 * @property PriceFormerType priceFormerType
 * @property Project project
 */
class ProjectPriceFormerType extends Cross
{

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Связь проекта и типа цены прафйсформера';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Связь проекта и типа цены прафйсформера';
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
            ValidationRules::ruleRequired('price_former_type_id','project_id'),
            ValidationRules::ruleUuid('price_former_type_id'),
            ValidationRules::ruleUuid('project_id'),
            [],
            []
        );
    }


    public static function relations() {
        return [
            'priceFormerType',
            'project',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceFormerType() {
        return $this->hasOne(PriceFormerType::className(), ['id' => 'price_former_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject() {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }
}