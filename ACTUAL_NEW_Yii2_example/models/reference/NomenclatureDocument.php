<?php


namespace app\models\reference;

use app\components\base\type\Reference;
use app\components\ValidationRules;
use app\models\pool\NomenclatureDocumentItem;
use yii\db\Query;
use yii\helpers\Html;

/**
 *
 */
class NomenclatureDocument extends Reference
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Документ номенклатур';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Документы номенклатур';
    }

    /**
     * @inheritDoc
     */
    public function crudIndexColumns()
    {
        return [
            'name' => [
                'label' => 'Наименование',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Html::decode((string)$model), ['update', 'id' => $model->id], ['data-pjax' => '0']);
                }
            ],
            'itemsCount' => [
                'label' => 'Количество номенклатур',
                'format' => 'raw',
                'value' => function ($model) {
                    return NomenclatureDocumentItem::find()
                        ->andWhere(['nomenclature_document_id' => $model->id])
                        ->count();
                }
            ]
        ];
    }
}