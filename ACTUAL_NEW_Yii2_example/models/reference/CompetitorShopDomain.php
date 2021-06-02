<?php
namespace app\models\reference;

use app\components\base\type\Reference;
use app\components\ValidationRules;
use app\models\enum\Region;
use app\validators\DomainValidator;

/**
 * Class CompetitorShopDomain
 * 
 * @package app\models\reference
 *
 * @property string competitor_id
 * @property int region_id
 *
 * @property Competitor competitor
 * @property Region region
 */
class CompetitorShopDomain extends Reference
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Домен конкурента';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Домены конкурентов';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleUuid('competitor_id'),
            ValidationRules::ruleEnum('region_id', Region::className()),
            [],
            [
                [
                    ['name'],
                    DomainValidator::className(),
                ],
                [['name'], 'unique', 'targetAttribute' => ['competitor_id', 'name']],
            ]
        );
    }

    public function recycle() {
        return $this->delete();
    }

    public function setAttributes($values, $safeOnly = true)
    {
        foreach ($values as $key => $value)  {
            if ($key == 'name') {
                if (substr($value,0,4) == 'www.') {
                    $value = substr($value, 4);
                }
                $values[$key] = trim($value);
            }
        }
        parent::setAttributes($values, $safeOnly);
    }

    public function setAttribute($name, $value)
    {
        if ($name == 'name') {
            if (substr($value,0,4) == 'www.') {
                $value = substr($value, 4);
            }
            $value = trim($value);
        }
        parent::setAttribute($name, $value);
    }

    public static function normalizeDomain($domain) {
        $domain = trim($domain);
        if (substr($domain,0,4) == 'www.') {
            $domain = substr($domain, 4);
        }
        return $domain;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->name = self::normalizeDomain($this->name);
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'name'                     => 'Домен',
                'competitor_id'            => 'ID Конкурента',
                'competitor'               => 'Конкурент',
                'region_id'                => 'ID Региона',
                'region'                   => 'Регион',
            ]
        );
    }

    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            'competitor',
            'name',
            'region'
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
            'region_id'
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(), [
            'competitor',
            'region',
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
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }
}