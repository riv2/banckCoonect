<?php
namespace app\models\enum;

use app\components\base\type\Enum;
use app\components\ValidationRules;
use app\models\cross\ParsingProjectRegion;
use app\models\reference\ParsingProject;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * Регион
 *
 * Class Region
 *
 * @package app\models\reference
 * @property string parent_id
 * @property bool is_ours
 */
class Region extends Enum
{
    const DEFAULT_REGION = 1;
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Регион';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Регионы';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['is_ours'], 'boolean'],
            ],
            ValidationRules::ruleEnum('parent_id', Region::className())
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
                'is_ours'          => 'Наш регион',
            ]
        );
    }

    /**
     *
     */
    /**
     * @inheritdoc
     */
    public function crudIndexColumns()
    {
        return array_merge(parent::crudIndexColumns(), [
            'id',
            'name',
            'is_ours' => [
                'attribute' => 'is_ours',
                'format'    => 'raw',
                'value'     => function($model) {
                    /** @var Region $model */
                    $id = 'is_ours_'.$model->id;
                    return Html::label(Html::checkbox($id, $model->is_ours, [
                        'id' => $id,
                        'onchange' => new JsExpression("$.ajax({url:'".Url::to(['/region/update-ajax', 'id' => $model->id])."', type:'post', dataType:'json', data: {is_ours: $(this).prop('checked') ? 1 : 0 }})")
                    ]),$id, [
                        'style' => 'width: 100%'
                    ]);
                }
            ]
        ]);
    }

    public static function ourRegions() {
        return self::find()
            ->andWhere(['is_ours' => true])
            ->select('id')
            ->column();

    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$insert && isset($changedAttributes['is_ours'])) {
            //$ourRegions = self::ourRegions();
            $projectsWithOurs = ParsingProject::find()
                ->andWhere(['is_our_regions' => true])
                ->select('id')
                ->column();

            if (!$this->is_ours) {
                ParsingProjectRegion::deleteAll([
                    'parsing_project_id'    => $projectsWithOurs,
                    'region_id'             => $this->id,
                    'cookies'               => null,
                ]);
            } else {
                foreach ($projectsWithOurs as $ppId) {
                    try {
                        $ppr = new ParsingProjectRegion;
                        $ppr->parsing_project_id = $ppId;
                        $ppr->region_id = $this->id;
                        $ppr->save();
                    } catch (\Exception $e) {

                    }
                }
            }
//            ParsingProjectRegion::deleteAll(['and',
//                [
//                    'not',
//                    ['region_id'  => $ourRegions]
//                ],
//                [
//                    'parsing_project_id' => $projectsWithOurs
//                ]
//            ]);

        }
    }
}