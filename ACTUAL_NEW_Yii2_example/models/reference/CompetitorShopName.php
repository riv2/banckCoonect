<?php
namespace app\models\reference;

use app\components\base\type\Reference;
use app\components\ValidationRules;

/**
 * Class CompetitorShopName
 * @package app\models\reference
 *
 * @property string competitor_id
 *
 * @property Competitor competitor
 */
class CompetitorShopName extends Reference
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Название конкурента';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Названия конкурентов';
    }


    public function recycle() {
        return $this->delete();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleUuid('competitor_id'),
            [],
            [
                [['name'], 'unique', 'targetAttribute' => ['competitor_id', 'name']]
            ]
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
                'name'                     => 'Название',
                'competitor_id'            => 'Конкурент',
                'competitor'               => 'Конкурент',
            ]
        );
    }

    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            'competitor',
            'name',
        ]);
    }


    public function fileImportPresetColumns()
    {
        return array_merge(parent::fileImportPresetColumns(),[
        ]);
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
    public static function relations()
    {
        return array_merge(parent::relations(), [
            'competitor'
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompetitor()
    {
        return $this->hasOne(Competitor::className(), ['id' => 'competitor_id']);
    }
}