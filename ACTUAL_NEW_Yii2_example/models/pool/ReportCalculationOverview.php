<?php
namespace app\models\pool;

use app\models\enum\Status;
use app\models\reference\Item;
use yii\bootstrap\Html;

/**
 * Class ReportCalculationOverview
 * @package app\models\pool
 *
 * @property array competitorPrices
 *
 */

class ReportCalculationOverview extends LogProjectExecution
{

    public $competitor_prices;
    public $competitor_shop_name;
    public $competitor_id;
    public $urls;
    public $price_refined;

    public static function fileImportEnabled() {
        return false;
    }

    public static function fileExportEnabled() {
        return true;
    }
    
    public static function crudCreateEnabled() {
        return false;
    }

    public static function tableName()
    {
        return LogProjectExecution::tableName();
    }

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Обзорная таблица';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Обзорная таблица';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['competitor_prices'], 'string'],
            ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'competitor_prices'         => 'Цены конкурентов',
            'competitorPrices'          => 'Цены конкурентов',
            'competitorPricesColumns'   => 'Цены конкурентов'
        ]);
    }

    public function excludeFieldsFileImportColumns()
    {
        return array_merge(parent::excludeFieldsFileImportColumns(), ['project_execution_id']);
    }

    public function fileExportFields($extra = []) {
        $all    = parent::fileExportFields($extra);
        $filter =  [
            'item_id' ,
            'item_name',
            'item_brand_name',
            'price_calculated',
            'price_supply',
            'price_recommended_retail',
            'price_default',
            'margin',
            'item_ym_url',
            'project_execution_id'  => $all['project_execution_id'],
            'competitorPricesColumns',
        ];
        return $filter;
    }

    public static function find()
    {

        $query = parent::find();
        $query->alias('t');
        $query->innerJoin([
                'lpc' => LogPriceCalculation::tableName()
            ], 'lpc.item_id = t.item_id AND lpc.project_execution_id = t.project_execution_id AND lpc.status_id = '.Status::STATUS_ACTIVE);

        $query->select([
            't.item_id',
            't.item_name',
            't.margin',
            't.item_ym_url',
            't.item_brand_name',
            't.price_calculated',
            't.price_supply',
            't.price_recommended_retail',
            't.price_default',
            't.price_weighted',
            'array_agg(lpc.competitor_shop_name ORDER BY lpc.price_refined ASC) as competitor_shop_name',
            'array_agg(lpc.competitor_id::varchar ORDER BY lpc.price_refined ASC) as competitor_id',
            'array_agg(lpc.price_refined ORDER BY lpc.price_refined ASC) as price_refined',
            'array_agg(lpc.url ORDER BY lpc.price_refined ASC) as urls',
        ]);

        $query->groupBy([
            't.item_id',
            't.item_name',
            't.margin',
            't.item_ym_url',
            't.item_brand_name',
            't.price_calculated',
            't.price_supply',
            't.price_recommended_retail',
            't.price_default',
            't.price_weighted'
        ]);
        return $query;
    }

    public function getCompetitorPricesColumns() {

        $competitorPrices = [];

        $prices = substr($this->price_refined, 1, -1);
        $csn = substr($this->competitor_shop_name, 1, -1);
        $cid = substr($this->competitor_id, 1, -1);

        $cid = str_getcsv($cid);
        $csn = str_getcsv($csn);
        $prices = str_getcsv($prices);

        foreach ($prices as $i => $price) {
            $name = isset($csn[$i]) ? $csn[$i] : null;
            if (!$name) {
                $name = isset($cid[$i]) ? $cid[$i] : null;
            }
            if (floatval($price) < $this->price_calculated) {
                $competitorPrices[] = '***' . $name;
                $competitorPrices[] = '***' . $price;
            } else {
                $competitorPrices[] = $name;
                $competitorPrices[] = $price;
            }

        }
        return $competitorPrices;
    }

    public function getCompetitorPrices() {
        $competitorPrices = [];

        $prices = substr($this->price_refined, 1, -1);
        $csn = substr($this->competitor_shop_name, 1, -1);
        $cid = substr($this->competitor_id, 1, -1);
        $urls = substr($this->urls, 1, -1);

        $cid = str_getcsv($cid);
        $csn = str_getcsv($csn);
        $prices = str_getcsv($prices);
        $urls = str_getcsv($urls);

        foreach ($prices as $i => $price) {
            $name = isset($csn[$i]) ?  $csn[$i]  : null;
            if (!$name){
                $name = isset($cid[$i]) ?  $cid[$i]  : null;
            }
            $url = isset($urls[$i]) ?  $urls[$i]  : null;
            $competitorPrices[] = ['competitor' => $name, 'price' => $price, 'url' => $url, 'lower' => floatval($price) < $this->price_calculated];
        }
        $price = array_column($competitorPrices, 'price');

        array_multisort($price, SORT_ASC, $competitorPrices);

        return $competitorPrices;
    }


    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->item_id;
    }
    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            'item_name' => [
                'attribute'     => 'item_name',
                'headerOptions' => ['style' => 'min-width:200px;'],
            ],
            'margin' => [
                'label' => 'Маржа',
                'value' => function($model) {
                    /** @var \app\models\pool\LogPriceCalculation $model */
                    return $model->margin.'%';
                }
            ],
            'competitor_prices' => [
                'label'     => 'Цены конкурентов',
                'format'    => 'raw',
                'value'     => function($model) {
                    $html = '';
                    foreach ($model->competitorPrices as  $competitorPrice) {
                        $html .=  Html::beginTag('span',['class'=>'pair-span']).
                            Html::beginTag('span',['class'=>'key-span', 'style' => $competitorPrice['lower'] ? 'background: #FFDDDD;': '']).
                            $competitorPrice['competitor'].
                            Html::endTag('span').
                            Html::a(number_format($competitorPrice['price'], 0, '.', ' '),
                                $competitorPrice['url'] ,
                                [
                                    'class'=>'value-span',
                                    'style' => $competitorPrice['lower'] ? 'background: #FFDDDD;': '',
                                    'target' => '_blank'
                                ]).

                            Html::endTag('span').
                            Html::endTag('span');
                    }
                    return $html;
                }
            ],
            'url' => [
                'label'     => 'УРЛ',
                'format'    => 'raw',
                'value'     => function($model) {
                    $url = $model->item_ym_url;
                    if (!$url) {
                        return null;
                    }
                    return '<a href="'.ANON_URL.$url.'" target="_blank">'.$url.'</a>';
                }
            ],
        ]);
    }

}