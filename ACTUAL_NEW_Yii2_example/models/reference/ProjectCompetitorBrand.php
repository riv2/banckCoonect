<?php
namespace app\models\reference;

use app\components\base\type\Reference;
use app\components\ValidationRules;

/**
 * Class ProjectCompetitorBrand
 *
 * Связь конкурента проекта с брендами
 *
 * @package app\models\register
 * @property string project_id
 * @property string competitor_id
 * @property string brand_id
 * @property string project_competitor_id
 *
 * @property Project project
 * @property Competitor competitor
 * @property Brand brand
 */
class ProjectCompetitorBrand extends Reference
{    
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Бренд конкурента проекта';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Бренды конкурентов проекта';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired('project_id','competitor_id','project_competitor_id','brand_id'),
            [],
            [],
            [],
            []
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
                'competitor'          => 'Конкурент',
                'project'             => 'Проект',
                'projectCompetitor'   => 'Конкурент проекта',
                'brand'               => 'Бренд',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if ($this->scenario != static::SCENARIO_SEARCH) {
            if (!$this->name && $this->brand_id) {
                $this->name = $this->brand->name;
            }
        }
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(), [
            'competitor',
            'project',
            'projectCompetitor',
            'brand',
        ]);
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
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectCompetitor()
    {
        return $this->hasOne(ProjectCompetitor::className(), ['id' => 'project_competitor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id']);
    }
}