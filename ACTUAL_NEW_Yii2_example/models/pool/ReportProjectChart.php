<?php

namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\DataProvider;
use app\models\document\ProjectExecution;
use app\models\reference\Item;
use app\models\reference\Project;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use yii\db\Query;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @property Project $project
 */
class ReportProjectChart extends Pool
{
    public $project_id           = null;
    public $type                 = null;
    public $date                 = null;
    public $competitor_id        = null;
    public $brand_id             = null;
    public $category_id          = null;
    public $item_id              = null;
    public $series_index         = null;
    public $project_execution_id = null;
    public $last_project_execution_id = null;

    /**
     * @return string
     */
    public static function getSingularNominativeName()
    {
        return 'Детализация отчета';
    }

    /**
     * @return string
     */
    public static function getPluralNominativeName()
    {
        return 'Детализация отчета';
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'item'                      => 'Товар',
            'competitor'                => 'Конкурент',
            'price_refined'             => 'Текущая цена',
            'old_price_refined'         => 'Прошлая цена',
            'date'                      => 'Временной интервал',
            'price_calculated'          => 'Расчетная цена',
            'price_supply'              => 'Цена закупки',
            'price_recommended_retail'  => 'РРЦ',
            'price_default'             => 'ВИ МСК',
            'url'                       => 'Урл',
            'item_name'                 => 'Наименование товара',
            'margin'                    => 'Маржа',
            'item_brand_name'           => 'Бренд',
            'competitor_shop_name'      => 'Имя конкурента',


            'project_id'                => 'Проект',
            'type'                      => 'Тип',
            'competitor_id'             => 'Конкурент',
            'brand_id'                  => 'Бренд',
            'category_id'               => 'Категория',
            'item_id'                   => 'Товар',
            'series_index'              => 'Счетчик',
            'project_execution_id'      => 'Текущий расчет проекта',
            'last_project_execution_id' => 'Прошлый расчет проекта',
        ]);
    }

    public function load($data, $formName = null)
    {
        foreach ($data as $key => $val) {
            if ($this->hasProperty($key)) {
                $this->$key = $val;
            }
        }

        return true;
    }

    /**
     *
     */
    public function generateReport()
    {
        $this->load(\Yii::$app->request->get());

        /** @var ProjectChart $projectChart */
        $projectChart = ProjectChart::find()
            ->andWhere([
                'project_id' => $this->project_id,
                'type' => $this->type,
                'date' => $this->date,
            ])
            ->one();
        if (!$projectChart) {
            return false;
        }
        $this->project_execution_id = $projectChart->project_execution_id;

        $dataProvider = new ActiveDataProvider([
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        switch ($this->type) {
            case ProjectChart::TYPE_VI_COMPARE:
            break;
            case ProjectChart::TYPE_PRICE_DYNAMICS:
                $itemIds = [];
                $data = json_decode($projectChart->data, true);
                foreach ($data as $competitorId => $counts) {
                    if (isset($counts[$this->series_index])) {
                        $itemIds = array_merge($itemIds, $counts[$this->series_index]);
                    }
                }
                $yesterdayProjectExecutionId = ProjectExecution::find()
                    ->select('id')
                    ->andWhere([
                        'AND',
                        ['project_id' => $this->project_id],
                        ['!=', 'id', $projectChart->project_execution_id],
                        ['<', 'created_at', $this->date],
                    ])
                    ->orderBy('created_at DESC')
                    ->scalar();
                if (!$yesterdayProjectExecutionId) {
                    return false;
                }
                $this->last_project_execution_id = $yesterdayProjectExecutionId;
                $dataProvider->query = (new Query())
                    ->select([
                        't.item_id',
                        't.item_name',
                        't.competitor_id',
                        't.competitor_shop_name',
                        't.url',
                        't.price_refined',
                        'old.price_refined as old_price_refined',

                        't.price_calculated',
                        't.price_supply',
                        't.price_recommended_retail',
                        't.price_default',
                        't.url',
                        't.margin',
                        't.item_brand_name',
                    ])
                    ->from(['t' => LogPriceCalculation::tableName()])
                    ->andWhere([
                        't.project_execution_id' => $projectChart->project_execution_id,
                    ])
                    ->andWhere('old.id IS NOT NULL')
                    ->leftJoin(
                        [
                            'old' => LogPriceCalculation::find()
                                ->andWhere([
                                    'project_execution_id' => $yesterdayProjectExecutionId,
                                ])
                        ],
                        'old.item_id = t.item_id'
                        . ' AND old.id != t.id'
                        . ' AND old.competitor_id = t.competitor_id'
                    )
                    ->orderBy('t.item_name, t.price_refined')
                ;
                switch ($this->series_index) {
                    case 0:
                        $dataProvider->query->andWhere('t.price_refined > old.price_refined AND ((t.price_refined/old.price_refined) * 100 - 100) > 1');
                    break;
                    case 1:
                        $dataProvider->query->andWhere('t.price_refined < old.price_refined AND ((t.price_refined/old.price_refined) * 100 - 100) < -1');
                    break;
                    case 2:
                        $dataProvider->query->andWhere('((t.price_refined/old.price_refined) * 100 - 100) < 1 AND ((t.price_refined/old.price_refined) * 100 - 100) > -1');
                    break;
                    case 3:
                        $dataProvider->query->andWhere('t.price_refined != old.price_refined AND ((t.price_refined/old.price_refined) * 100 - 100) <= 1 AND ((t.price_refined/old.price_refined) * 100 - 100) >= -1');
                    break;
                }
                if ($this->item_id) {
                    $dataProvider->query->andWhere(['t.item_id' => $this->item_id]);
                }
                if ($this->brand_id) {
                    $dataProvider->query
                        ->innerJoin(['i' => Item::tableName()], 'i.id = t.item_id')
                        ->andWhere(['i.brand_id' => $this->brand_id])
                    ;
                }
                if ($this->competitor_id) {
                    $dataProvider->query->andWhere(['t.competitor_id' => $this->competitor_id]);
                }
            break;
        }

        return [
            'dataProvider' => $dataProvider,
        ];
    }

    public function crudIndexColumns()
    {
        switch ($this->type) {
            case ProjectChart::TYPE_VI_COMPARE:
                return [

                ];
            break;
            case ProjectChart::TYPE_PRICE_DYNAMICS:
                return [
                    'item' => [
                        'label' => $this->getAttributeLabel('item'),
                        'format' => 'raw',
                        'value' => function ($data) {
                            return Html::a($data['item_name'], \yii\helpers\Url::to(['/crud-item/view', 'id' => $data['item_id']]), ['target' => '_blank']);
                        }
                    ],
                    'competitor' => [
                        'label' => $this->getAttributeLabel('competitor'),
                        'format' => 'raw',
                        'value' => function ($data) {
                            return Html::a($data['competitor_shop_name'], \yii\helpers\Url::to(['/competitor/view', 'id' => $data['competitor_id']]), ['target' => '_blank']);
                        },
                    ],
                    'item_brand_name' => [
                        'attribute' => 'item_brand_name',
                        'label' => $this->getAttributeLabel('brand_id'),
                    ],
                    'price_refined' => [
                        'attribute' => 'price_refined',
                        'label' => $this->getAttributeLabel('price_refined'),
                    ],
                    'old_price_refined' => [
                        'attribute' => 'old_price_refined',
                        'label' => $this->getAttributeLabel('old_price_refined'),
                    ],

                    'price_calculated' => [
                        'attribute' => 'price_calculated',
                        'label' => $this->getAttributeLabel('price_calculated'),
                    ],
                    'price_supply' => [
                        'attribute' => 'price_supply',
                        'label' => $this->getAttributeLabel('price_supply'),
                    ],
                    'price_recommended_retail' => [
                        'attribute' => 'price_recommended_retail',
                        'label' => $this->getAttributeLabel('price_recommended_retail'),
                    ],
                    'price_default' => [
                        'attribute' => 'price_default',
                        'label' => $this->getAttributeLabel('price_default'),
                    ],
                    'margin' => [
                        'label' => 'Маржа',
                        'value' => function($data) {
                            return $data['margin'].'%';
                        }
                    ],
                    'url' => [
                        'attribute' => 'url',
                        'label' => $this->getAttributeLabel('url'),
                    ],
                ];
            break;
        }
        return [];
    }

    public static function getSeriesLabels($type = 1)
    {
        return [
            ProjectChart::TYPE_VI_COMPARE => [

            ],
            ProjectChart::TYPE_PRICE_DYNAMICS => [
                0 => 'Подняли',
                1 => 'Снизили',
                2 => 'Нулевые',
                3 => '+-1%',
            ],
        ][$type];
    }

    public function getProject()
    {
        return Project::find()
            ->andWhere(['id' => $this->project_id]);
    }
}