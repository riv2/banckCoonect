<?php
namespace app\models\reference;

use app\components\base\type\Reference;
use app\components\ValidationRules;

/**
 * Class ProjectCompetitorCategory
 *
 * Связь конкурента проекта с рубриками
 *
 * @package app\models\register
 * @property string project_id
 * @property string competitor_id
 * @property string category_id
 * @property string project_competitor_id
 * @property Project project
 * @property Competitor competitor
 * @property Category category
 */
class ProjectCompetitorCategory extends Reference
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Категория конкурента проекта';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Категории конкурентов проекта';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired('project_id','competitor_id','project_competitor_id','category_id'),
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
                'category'            => 'Категория',
            ]
        );
    }
    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if ($this->scenario != static::SCENARIO_SEARCH) {
            if (!$this->name && $this->category_id) {
                $this->name = $this->category->name;
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
            'category',
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
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
}