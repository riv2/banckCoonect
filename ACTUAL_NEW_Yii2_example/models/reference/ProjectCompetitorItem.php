<?php
namespace app\models\reference;

use app\components\base\type\Reference;
use app\components\ValidationRules;

/**
 * Class ProjectCompetitorCategory
 *
 * Связь товаров с рубриками
 *
 * @package app\models\register
 * @property string project_id
 * @property string competitor_id
 * @property string item_id
 * @property string project_competitor_id
 * @property Project project
 * @property Competitor competitor
 * @property Category category
 */
class ProjectCompetitorItem extends Reference
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Исключенные товары конкурента проекта';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Исключенные товары конкурента проекта';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired('project_id','competitor_id','project_competitor_id','item_id'),
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
                'item'                => 'Товар',
            ]
        );
    }
    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if ($this->scenario != static::SCENARIO_SEARCH) {
            if (!$this->name && $this->item_id) {
                $this->name = $this->item->name;
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
            'item',
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
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }
}