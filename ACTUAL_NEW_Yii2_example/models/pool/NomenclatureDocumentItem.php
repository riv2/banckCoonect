<?php
namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\ValidationRules;
use app\models\reference\Competitor;
use app\models\reference\Item;
use app\models\reference\NomenclatureDocument;
use app\models\reference\Vpn;
use app\models\register\Parsing;
use yii;
use yii\helpers\Json;

/**
 * Модель связки документ номенклатуры к номенклатуре
 *
 * @property string item_id
 * @property string nomenclature_document_id
 * @property string min_margin
 * @property string rrp_regulations
 * @property string price_variation_modifier
 *
 * @property NomenclatureDocument nomenclatureDocument
 * @property Item item
 */
class NomenclatureDocumentItem extends Pool
{
    /**
     * @inheritDoc
     */
    public static function noCount()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public static function getSingularNominativeName()
    {
        return 'Номеклатура документа';
    }

    /**
     * @inheritDoc
     */
    public static function getPluralNominativeName()
    {
        return 'Номеклатуры документа';
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'nomenclatureDocument' => 'Документ номенклатуры',
            'nomenclature_document_id' => 'Документ номенклатуры',
            'item_id' => 'Товар',
            'min_margin' => 'Мин. наценка',
            'price_variation_modifier' => 'Процент отклонения',
            'rrp_regulations' => 'Регламент РРЦ',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired(['item_id', 'nomenclature_document_id']),
            ValidationRules::ruleUuid(['id', 'item_id', 'nomenclature_document_id']),
            [
                [['price_variation_modifier'], 'filter', 'filter' => function ($value) {
                    return floatval($value);
                } ],
                [['price_variation_modifier'], 'number'],
                [['min_margin'], 'number'],
                [['rrp_regulations'], 'boolean'],
                [['rrp_regulations'], 'default', 'value' => false],
                [['item_id'], 'unique', 'targetAttribute' => ['nomenclature_document_id', 'item_id'], 'except' => self::SCENARIO_SEARCH],
            ]
        );
    }

    public static function relations()
    {
        return array_merge(parent::relations(), [
            'nomenclatureDocument',
            'item',
        ]);
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getNomenclatureDocument()
    {
        return $this->hasOne(NomenclatureDocument::className(), ['id' => 'nomenclature_document_id']);
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }

    /**
     * @inheritdoc
     */
    public function excludeFieldsFileImportColumns()
    {
        return array_merge(parent::excludeFieldsFileImportColumns(),[
            'id',
            'nomenclature_document_id'
        ]);
    }
}
