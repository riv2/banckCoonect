<?php
namespace app\models\reference;

use app\components\base\type\Reference;
use app\components\ValidationRules;
use app\models\enum\Region;
use app\models\enum\Source;

/**
 * Class CompetitorShopIndex
 * 
 * @package app\models\reference
 *
 * @property string competitor_id
 * @property int source_id
 *
 * @property Competitor competitor
 * @property Region region
 */
class CompetitorShopIndex extends Reference
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'YMID конкурента';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'YMID конкурентов';
    }

    public function recycle() {
        return $this->delete();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'name'                     => 'YMID',
                'competitor_id'            => 'Конкурент',
                'competitor'               => 'Конкурент',
                'source_id'                => 'Торговая площадка',
                'source'                   => 'Торговая площадка',
            ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleUuid('competitor_id'),
            ValidationRules::ruleDefault('source_id', Source::SOURCE_YANDEX_MARKET),
            ValidationRules::ruleEnum('source_id', Source::className()),
            [],
            [
                [['name'], 'unique', 'targetAttribute' => ['competitor_id', 'name', 'source_id']]
            ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)){
            if (!$this->source_id) {
                $this->source_id = Source::SOURCE_YANDEX_MARKET;
            }
            return true;
        }
        return false;
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
            'source_id',
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(), [
            'competitor',
            'source',
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
    public function getSource()
    {
        return $this->hasOne(Source::className(), ['id' => 'source_id']);
    }
}